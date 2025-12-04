<?php
require_once '../config/connect.php';

echo "=== CHECKING 3:00 PM APPOINTMENTS ===\n\n";

$date = '2025-12-05'; // Tomorrow
$time = '15:00:00';
$clinicId = 1; // Happy Paws

$query = "
SELECT a.* 
FROM appointments a
WHERE a.clinic_id = ?
AND a.appointment_date = ?
AND a.appointment_time = ?
AND a.status NOT IN ('declined', 'cancelled')
ORDER BY a.id
";

$stmt = $conn->prepare($query);
$stmt->bind_param("iss", $clinicId, $date, $time);
$stmt->execute();
$result = $stmt->get_result();

$count = 0;
while ($row = $result->fetch_assoc()) {
    $count++;
    echo "Appointment #$count:\n";
    echo "  ID: " . $row['id'] . "\n";
    echo "  Vet ID: " . ($row['vet_id'] ?? 'ANY/NULL') . "\n";
    echo "  Pet ID: " . $row['pet_id'] . "\n";
    echo "  Status: " . $row['status'] . "\n";
    echo "  Type: " . $row['appointment_type'] . "\n\n";
}

if ($count === 0) {
    echo "No appointments found at 3:00 PM on $date\n\n";
}

echo "\n=== CHECKING VETS AT HAPPY PAWS ===\n";
$vetsQuery = "
SELECT u.id 
FROM users u
JOIN user_roles ur ON u.id = ur.user_id
JOIN clinics c ON ur.clinic_id = c.id
WHERE ur.role = 'vet' 
AND c.id = ?
";

$stmt = $conn->prepare($vetsQuery);
$stmt->bind_param("i", $clinicId);
$stmt->execute();
$result = $stmt->get_result();

$totalVets = 0;
while ($row = $result->fetch_assoc()) {
    $totalVets++;
    echo "Vet ID: " . $row['id'] . "\n";
}

echo "\nTotal Vets: $totalVets\n";
echo "Appointments at 3PM: $count\n";
echo "Available slots: " . ($totalVets - $count) . "\n";
