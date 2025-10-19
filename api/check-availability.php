<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../models/PetOwner/MyPetsModel.php';

$date = isset($_GET['date']) ? $_GET['date'] : '';
$time = isset($_GET['time']) ? $_GET['time'] : '';
$vetId = isset($_GET['vet_id']) ? (int)$_GET['vet_id'] : 0;

if (empty($date) || empty($time)) {
    echo json_encode(['error' => 'Date and time are required']);
    exit;
}

$model = new MyPetsModel();
$existingAppointments = $model->getExistingAppointments($date);

// Convert input time to minutes for comparison
function timeToMinutes($time) {
    $parts = explode(':', $time);
    return (int)$parts[0] * 60 + (int)$parts[1];
}

$slotDuration = 20; // minutes
$requestedTimeMinutes = timeToMinutes($time);
$requestedEndMinutes = $requestedTimeMinutes + $slotDuration;

$isAvailable = true;
$conflictingAppointment = null;

// Check for conflicts
foreach ($existingAppointments as $appointment) {
    // If vet_id is 0, it means "any available" was selected - only check if specific vet selected
    if ($vetId > 0 && $appointment['vet_id'] != $vetId) {
        continue; // Different vet, no conflict
    }
    
    $appointmentTimeMinutes = timeToMinutes($appointment['time']);
    $appointmentEndMinutes = $appointmentTimeMinutes + $slotDuration;
    
    // Check if time slots overlap
    if (
        ($requestedTimeMinutes >= $appointmentTimeMinutes && $requestedTimeMinutes < $appointmentEndMinutes) ||
        ($requestedEndMinutes > $appointmentTimeMinutes && $requestedEndMinutes <= $appointmentEndMinutes) ||
        ($requestedTimeMinutes <= $appointmentTimeMinutes && $requestedEndMinutes >= $appointmentEndMinutes)
    ) {
        $isAvailable = false;
        $conflictingAppointment = $appointment;
        break;
    }
}

echo json_encode([
    'success' => true,
    'available' => $isAvailable,
    'conflictingAppointment' => $conflictingAppointment,
    'slotDuration' => $slotDuration
]);
