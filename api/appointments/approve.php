<?php
header('Content-Type: application/json');
session_start();

// TODO: Add authentication check
// if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'receptionist') {
//     http_response_code(403);
//     echo json_encode(['success' => false, 'error' => 'Unauthorized']);
//     exit;
// }

require_once __DIR__ . '/../../models/SharedAppointmentsModel.php';

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['appointment_id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Missing appointment ID'
    ]);
    exit;
}

$appointmentId = intval($data['appointment_id']);

try {
    $model = new SharedAppointmentsModel();
    $result = $model->approveAppointment($appointmentId);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Appointment approved successfully',
            'appointment_id' => $appointmentId
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to approve appointment'
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
