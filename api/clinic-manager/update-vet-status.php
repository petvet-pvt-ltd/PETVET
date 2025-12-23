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
    $validStatuses = ['Active', 'On Leave', 'Inactive'];
    if (!in_array($status, $validStatuses)) {
        throw new Exception('Invalid status value');
    }
    
    // Convert status to available flag (Active = 1, others = 0)
    $available = ($status === 'Active') ? 1 : 0;
    
    // Get clinic manager's clinic_id
    $stmt = $pdo->prepare("SELECT clinic_id FROM clinic_manager_profiles WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $profile = $stmt->fetch();
    
    if (!$profile) {
        throw new Exception('Clinic manager profile not found');
    }
    
    $clinicId = $profile['clinic_id'];
    
    // Update vet availability in vets table (only for vets in this clinic)
    $stmt = $pdo->prepare("
        UPDATE vets 
        SET available = ?,
            updated_at = CURRENT_TIMESTAMP
        WHERE user_id = ? 
        AND clinic_id = ?
    ");
    
    $stmt->execute([$available, $userId, $clinicId]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('Vet not found or no permission to update');
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
