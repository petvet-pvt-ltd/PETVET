<?php
require_once '../../../config/connect.php';
require_once '../../../config/auth_helper.php';

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

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Appointment ID is required']);
    exit;
}

$appointmentId = intval($input['id']);
$userId = currentUserId();

try {
    $pdo = db();
    
    // Verify the appointment belongs to the current user
    $stmt = $pdo->prepare("
        SELECT a.id, a.pet_id, a.status 
        FROM appointments a
        JOIN pets p ON a.pet_id = p.id
        WHERE a.id = ? AND p.user_id = ?
    ");
    $stmt->execute([$appointmentId, $userId]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$appointment) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Appointment not found or access denied']);
        exit;
    }
    
    // Update appointment status to cancelled
    $stmt = $pdo->prepare("
        UPDATE appointments 
        SET status = 'cancelled',
            updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$appointmentId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Appointment cancelled successfully'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
