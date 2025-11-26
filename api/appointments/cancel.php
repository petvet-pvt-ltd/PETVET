<?php
require_once '../../config/connect.php';
require_once '../../config/auth_helper.php';

header('Content-Type: application/json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is authenticated
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Check if user is receptionist or clinic manager
$userRole = currentRole();
$allowedRoles = ['receptionist', 'clinic_manager'];
if (!in_array($userRole, $allowedRoles)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['appointment_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Appointment ID is required']);
    exit;
}

$appointmentId = intval($input['appointment_id']);
$cancelReason = $input['reason'] ?? 'Cancelled by staff';

try {
    $pdo = db();
    
    // Update appointment status to cancelled
    $stmt = $pdo->prepare("
        UPDATE appointments 
        SET status = 'cancelled',
            updated_at = NOW()
        WHERE id = ?
    ");
    
    $success = $stmt->execute([$appointmentId]);
    
    if ($success && $stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Appointment cancelled successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Appointment not found or already cancelled'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
