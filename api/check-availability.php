<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/connect.php';

$date = isset($_GET['date']) ? $_GET['date'] : '';
$time = isset($_GET['time']) ? $_GET['time'] : '';
$vetId = isset($_GET['vet_id']) ? (int)$_GET['vet_id'] : 0;
$clinicId = isset($_GET['clinic_id']) ? (int)$_GET['clinic_id'] : 0;

if (empty($date) || empty($time)) {
    echo json_encode(['error' => 'Date and time are required']);
    exit;
}

try {
    $db = db();
    
    // Get existing appointments for the date at the clinic
    $query = "
        SELECT appointment_time as time, vet_id, duration_minutes 
        FROM appointments 
        WHERE appointment_date = ? 
        AND status NOT IN ('declined', 'cancelled')
    ";
    
    $params = [$date];
    
    // If clinic is specified, filter by clinic
    if ($clinicId > 0) {
        $query .= " AND clinic_id = ?";
        $params[] = $clinicId;
    }
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $existingAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Availability check error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Failed to check availability']);
    exit;
}

// Convert input time to minutes for comparison
function timeToMinutes($time) {
    $parts = explode(':', $time);
    return (int)$parts[0] * 60 + (int)$parts[1];
}

$defaultSlotDuration = 20; // minutes
$requestedTimeMinutes = timeToMinutes($time);
$requestedEndMinutes = $requestedTimeMinutes + $defaultSlotDuration;

$isAvailable = true;
$conflictingAppointment = null;

// Check for conflicts
foreach ($existingAppointments as $appointment) {
    // If vet_id is 0, it means "any available" was selected - only check if specific vet selected
    if ($vetId > 0 && $appointment['vet_id'] != $vetId && $appointment['vet_id'] != null) {
        continue; // Different vet, no conflict
    }
    
    $slotDuration = isset($appointment['duration_minutes']) ? (int)$appointment['duration_minutes'] : $defaultSlotDuration;
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
    'slotDuration' => $defaultSlotDuration
]);
