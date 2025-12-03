<?php
require_once __DIR__ . '/../config/connect.php';

$date = '2025-12-01';
$time = '10:00';
$vetId = 0; // Any available vet
$clinicId = 1;

$db = db();

$query = "SELECT appointment_time as time, vet_id, duration_minutes FROM appointments WHERE appointment_date = ? AND status NOT IN ('declined', 'cancelled')";
$params = [$date];

if ($clinicId > 0) {
    $query .= " AND clinic_id = ?";
    $params[] = $clinicId;
}

$stmt = $db->prepare($query);
$stmt->execute($params);
$existingAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo 'Existing appointments:' . PHP_EOL;
print_r($existingAppointments);

function timeToMinutes($time) {
    $parts = explode(':', $time);
    return (int)$parts[0] * 60 + (int)$parts[1];
}

$defaultSlotDuration = 20;
$requestedTimeMinutes = timeToMinutes($time);
$requestedEndMinutes = $requestedTimeMinutes + $defaultSlotDuration;

echo PHP_EOL . 'Requested time: ' . $time . ' (' . $requestedTimeMinutes . ' minutes)' . PHP_EOL;
echo 'Requested end: ' . $requestedEndMinutes . ' minutes' . PHP_EOL . PHP_EOL;

foreach ($existingAppointments as $appointment) {
    echo 'Checking appointment:' . PHP_EOL;
    echo '  Time: ' . $appointment['time'] . PHP_EOL;
    echo '  Vet ID: ' . $appointment['vet_id'] . PHP_EOL;
    
    if ($vetId > 0) {
        if ($appointment['vet_id'] != $vetId && $appointment['vet_id'] != null) {
            echo '  -> SKIPPED (different vet)' . PHP_EOL;
            continue;
        }
    }
    
    $slotDuration = isset($appointment['duration_minutes']) ? (int)$appointment['duration_minutes'] : $defaultSlotDuration;
    $appointmentTimeMinutes = timeToMinutes($appointment['time']);
    $appointmentEndMinutes = $appointmentTimeMinutes + $slotDuration;
    
    echo '  Start minutes: ' . $appointmentTimeMinutes . PHP_EOL;
    echo '  End minutes: ' . $appointmentEndMinutes . PHP_EOL;
    
    $overlap1 = ($requestedTimeMinutes >= $appointmentTimeMinutes && $requestedTimeMinutes < $appointmentEndMinutes);
    $overlap2 = ($requestedEndMinutes > $appointmentTimeMinutes && $requestedEndMinutes <= $appointmentEndMinutes);
    $overlap3 = ($requestedTimeMinutes <= $appointmentTimeMinutes && $requestedEndMinutes >= $appointmentEndMinutes);
    
    echo '  Overlap check 1 (requested start within existing): ' . ($overlap1 ? 'YES' : 'NO') . PHP_EOL;
    echo '  Overlap check 2 (requested end within existing): ' . ($overlap2 ? 'YES' : 'NO') . PHP_EOL;
    echo '  Overlap check 3 (requested wraps existing): ' . ($overlap3 ? 'YES' : 'NO') . PHP_EOL;
    
    if ($overlap1 || $overlap2 || $overlap3) {
        echo '  -> CONFLICT DETECTED!' . PHP_EOL;
    } else {
        echo '  -> NO CONFLICT' . PHP_EOL;
    }
}
