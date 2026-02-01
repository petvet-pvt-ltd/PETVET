<?php
/**
 * Pet Owner Request Reschedule API
 * Pet owner can request to reschedule an appointment
 * Receptionist/clinic manager must approve the reschedule
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/connect.php';

session_start();

$pet_owner_id = $_SESSION['user_id'] ?? null;

if (!$pet_owner_id) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Unauthorized'
    ]);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['appointment_id']) || !isset($input['date']) || !isset($input['time'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Missing required fields'
    ]);
    exit;
}

$appointmentId = intval($input['appointment_id']);
$newDate = $input['date'];
$newTime = $input['time'];

try {
    $pdo = db();
    
    // Verify the appointment belongs to this pet owner
    $checkStmt = $pdo->prepare("
        SELECT id, status 
        FROM appointments 
        WHERE id = ? AND pet_owner_id = ?
    ");
    $checkStmt->execute([$appointmentId, $pet_owner_id]);
    $appointment = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$appointment) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Appointment not found'
        ]);
        exit;
    }
    
    // Check if appointment can be rescheduled
    if (!in_array($appointment['status'], ['pending', 'approved'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Only pending or approved appointments can be rescheduled'
        ]);
        exit;
    }
    
    // Store reschedule request data in a JSON column or separate table
    // For now, we'll update the appointment status to 'rescheduled' 
    // and store the requested date/time for receptionist approval
    
    $updateStmt = $pdo->prepare("
        UPDATE appointments 
        SET 
            status = 'rescheduled',
            reschedule_requested_date = ?,
            reschedule_requested_time = ?,
            updated_at = NOW()
        WHERE id = ?
    ");
    
    $success = $updateStmt->execute([$newDate, $newTime, $appointmentId]);
    
    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Reschedule request submitted successfully. Waiting for clinic confirmation.'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to submit reschedule request'
        ]);
    }
    
} catch (Exception $e) {
    error_log('Request reschedule error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to submit reschedule request'
    ]);
}
