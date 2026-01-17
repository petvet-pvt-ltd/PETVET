<?php
session_start();
require_once '../../config/connect.php';
header('Content-Type: application/json');

// Check admin authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $clinicId = intval($data['clinic_id']);
    $status = mysqli_real_escape_string($conn, $data['status']); // 'approved' or 'rejected'
    
    if (!in_array($status, ['approved', 'rejected'])) {
        throw new Exception('Invalid status');
    }
    
    // When approving, also set is_active to 1
    if ($status === 'approved') {
        $query = "UPDATE clinics SET 
            verification_status = ?,
            is_active = 1,
            updated_at = NOW()
            WHERE id = ?";
    } else {
        // When rejecting, keep is_active as 0
        $query = "UPDATE clinics SET 
            verification_status = ?,
            is_active = 0,
            updated_at = NOW()
            WHERE id = ?";
    }
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $status, $clinicId);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception(mysqli_error($conn));
    }
    
    echo json_encode([
        'success' => true,
        'message' => "Clinic $status successfully"
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
