<?php
/**
 * API Endpoint: Update Vet Status
 * Updates vet availability status in vets table
 */

session_start();
header('Content-Type: application/json');

// Check if user is logged in and is a clinic manager
if (!isset($_SESSION['user_id']) || !isset($_SESSION['current_role']) || $_SESSION['current_role'] !== 'clinic_manager') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

require_once __DIR__ . '/../../config/connect.php';

try {
    $pdo = db();
    
    // Get request data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['user_id']) || !isset($data['status'])) {
        throw new Exception('Missing required fields');
    }
    
    $userId = (int)$data['user_id'];
    $status = $data['status'];
    
    // Validate status
    // Note: Frontend uses 'Inactive' for suspension toggle.
    $validStatuses = ['Active', 'On Leave', 'Inactive'];
    if (!in_array($status, $validStatuses, true)) {
        throw new Exception('Invalid status value');
    }
    
    // Get clinic manager's clinic_id
    $stmt = $pdo->prepare("SELECT clinic_id FROM clinic_manager_profiles WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $profile = $stmt->fetch();
    
    if (!$profile) {
        throw new Exception('Clinic manager profile not found');
    }
    
    $clinicId = $profile['clinic_id'];
    
    // Load current flags to preserve On Leave when suspending/activating
    $stmt = $pdo->prepare("SELECT available, is_suspended, is_on_leave FROM vets WHERE user_id = ? AND clinic_id = ? LIMIT 1");
    $stmt->execute([$userId, $clinicId]);
    $current = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$current) {
        throw new Exception('Vet not found or no permission to update');
    }

    $isSuspended = (int)($current['is_suspended'] ?? 0) === 1;
    $isOnLeave = (int)($current['is_on_leave'] ?? 0) === 1;

    if ($status === 'Inactive') {
        // Suspend: keep leave state as-is, but always make unavailable.
        $stmt = $pdo->prepare(
            "UPDATE vets
             SET is_suspended = 1,
                 suspended_at = NOW(),
                 available = 0,
                 updated_at = CURRENT_TIMESTAMP
             WHERE user_id = ? AND clinic_id = ?"
        );
        $stmt->execute([$userId, $clinicId]);
    } elseif ($status === 'On Leave') {
        // On Leave: only allowed if not suspended.
        if ($isSuspended) {
            throw new Exception('Cannot change leave status while vet is suspended');
        }
        $stmt = $pdo->prepare(
            "UPDATE vets
             SET is_on_leave = 1,
                 available = 0,
                 updated_at = CURRENT_TIMESTAMP
             WHERE user_id = ? AND clinic_id = ?"
        );
        $stmt->execute([$userId, $clinicId]);
    } else {
        // Active
        if ($isSuspended) {
            // Activating from suspension: unsuspend, but preserve leave state.
            $available = $isOnLeave ? 0 : 1;
            $stmt = $pdo->prepare(
                "UPDATE vets
                 SET is_suspended = 0,
                     suspended_at = NULL,
                     available = ?,
                     updated_at = CURRENT_TIMESTAMP
                 WHERE user_id = ? AND clinic_id = ?"
            );
            $stmt->execute([$available, $userId, $clinicId]);
        } else {
            // Mark Active (from leave toggle): clear leave and make available.
            $stmt = $pdo->prepare(
                "UPDATE vets
                 SET is_on_leave = 0,
                     available = 1,
                     updated_at = CURRENT_TIMESTAMP
                 WHERE user_id = ? AND clinic_id = ?"
            );
            $stmt->execute([$userId, $clinicId]);
        }
    }

    $rowCount = $stmt->rowCount();

    if ($rowCount === 0) {
        throw new Exception('No changes applied');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Vet status updated successfully',
        'status' => $status
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
