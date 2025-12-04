<?php
require_once '../config/connect.php';

echo "=== TESTING AVAILABLE VETS API ===\n\n";

// Test parameters
$clinicId = 1; // Happy Paws
$appointmentDate = '2025-12-04'; // Today
$appointmentTime = '17:00:00'; // 5:00 PM

echo "Test Parameters:\n";
echo "Clinic ID: $clinicId\n";
echo "Date: $appointmentDate\n";
echo "Time: $appointmentTime\n\n";

// Check current bookings at this time
$query = "
SELECT a.id, a.vet_id, CONCAT(u.first_name, ' ', u.last_name) as vet_name
FROM appointments a
LEFT JOIN users u ON a.vet_id = u.id
WHERE a.clinic_id = ?
AND a.appointment_date = ?
AND a.appointment_time = ?
AND a.status NOT IN ('declined', 'cancelled')
";

$stmt = $conn->prepare($query);
$stmt->bind_param("iss", $clinicId, $appointmentDate, $appointmentTime);
$stmt->execute();
$result = $stmt->get_result();

echo "=== CURRENT BOOKINGS AT THIS TIME ===\n";
$bookingCount = 0;
while ($row = $result->fetch_assoc()) {
    $bookingCount++;
    echo "Appointment ID: " . $row['id'] . "\n";
    echo "Vet ID: " . ($row['vet_id'] ?? 'NULL/ANY') . "\n";
    echo "Vet Name: " . ($row['vet_name'] ?? 'Any Available') . "\n\n";
}

if ($bookingCount === 0) {
    echo "No bookings at this time\n\n";
}

// Test the API
$url = "http://localhost/petvet/api/appointments/get-available-vets.php?clinic_id=$clinicId&appointment_date=$appointmentDate&appointment_time=$appointmentTime";
echo "=== API TEST ===\n";
echo "URL: $url\n\n";

$response = file_get_contents($url);
$data = json_decode($response, true);

echo "API Response:\n";
echo json_encode($data, JSON_PRETTY_PRINT);
