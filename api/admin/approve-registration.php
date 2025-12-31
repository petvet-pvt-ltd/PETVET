<?php
/**
 * Approve User Registration Request API
 * Admin approves pending user role registration
 */

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/connect.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Admin access required.']);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $db = db();
    $requestId = $_POST['request_id'] ?? null;
    $notes = $_POST['notes'] ?? '';
    
    if (!$requestId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Request ID is required']);
        exit;
    }
    
    // Update user_roles verification status
    $stmt = $db->prepare("
        UPDATE user_roles 
        SET verification_status = 'approved',
            verification_notes = :notes,
            verified_by = :admin_id,
            verified_at = NOW(),
            is_active = 1
        WHERE id = :request_id
    ");
    
    $stmt->execute([
        ':notes' => $notes,
        ':admin_id' => $_SESSION['user_id'],
        ':request_id' => $requestId
    ]);
    
    if ($stmt->rowCount() > 0) {
        // Check if this is a clinic_manager role and activate the clinic
        $stmt = $db->prepare("
            SELECT ur.user_id, r.role_name
            FROM user_roles ur
            JOIN roles r ON ur.role_id = r.id
            WHERE ur.id = :request_id
        ");
        $stmt->execute([':request_id' => $requestId]);
        $roleInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // If clinic manager, activate their clinic
        if ($roleInfo && $roleInfo['role_name'] === 'clinic_manager') {
            $stmt = $db->prepare("
                UPDATE clinics c
                JOIN clinic_manager_profiles cmp ON c.id = cmp.clinic_id
                SET c.verification_status = 'approved',
                    c.is_active = 1
                WHERE cmp.user_id = :user_id
            ");
            $stmt->execute([':user_id' => $roleInfo['user_id']]);
        }
        
        // Get user details for response
        $stmt = $db->prepare("
            SELECT u.id, u.email, CONCAT(u.first_name, ' ', u.last_name) as name, r.role_display_name
            FROM user_roles ur
            JOIN users u ON ur.user_id = u.id
            JOIN roles r ON ur.role_id = r.id
            WHERE ur.id = :request_id
        ");
        $stmt->execute([':request_id' => $requestId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Registration approved successfully',
            'user' => $user
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Request not found']);
    }
    
} catch (PDOException $e) {
    error_log("Database error in approve-registration.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Error in approve-registration.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred',
        'error' => $e->getMessage()
    ]);
}
