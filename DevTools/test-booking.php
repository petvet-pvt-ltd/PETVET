<?php
// Test booking API with sample data
session_start();

// Simulate logged-in user (adjust user_id as needed)
$_SESSION['user_id'] = 1; // Change this to your actual pet owner user_id

echo "=== TESTING BOOKING API ===\n\n";

// Sample booking data
$bookingData = [
    'pet_id' => 1,  // Change to your actual pet ID
    'clinic_id' => 1,  // Happy Paws
    'vet_id' => 1,  // Change to actual vet ID or '0' for any vet
    'appointment_type' => 'checkup',
    'symptoms' => '',  // Optional/empty
    'appointment_date' => '2025-12-05',  // Tomorrow (not Saturday)
    'appointment_time' => '10:00'
];

echo "Request Data:\n";
echo json_encode($bookingData, JSON_PRETTY_PRINT) . "\n\n";

// Make API request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/petvet/api/appointments/book.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($bookingData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Cookie: ' . session_name() . '=' . session_id()
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

echo "HTTP Status: $httpCode\n\n";
echo "Response Headers:\n$headers\n";
echo "Response Body:\n";

$jsonResponse = json_decode($body, true);
if ($jsonResponse) {
    echo json_encode($jsonResponse, JSON_PRETTY_PRINT) . "\n";
} else {
    echo $body . "\n";
}

// Check if booking was created
if ($httpCode === 200) {
    require_once '../config/connect.php';
    $lastAppointment = mysqli_query($conn, "SELECT * FROM appointments ORDER BY id DESC LIMIT 1");
    if ($row = mysqli_fetch_assoc($lastAppointment)) {
        echo "\n=== LAST APPOINTMENT IN DATABASE ===\n";
        echo "ID: " . $row['id'] . "\n";
        echo "Pet ID: " . $row['pet_id'] . "\n";
        echo "Clinic ID: " . $row['clinic_id'] . "\n";
        echo "Vet ID: " . ($row['vet_id'] ?? 'NULL') . "\n";
        echo "Type: " . $row['appointment_type'] . "\n";
        echo "Date: " . $row['appointment_date'] . "\n";
        echo "Time: " . $row['appointment_time'] . "\n";
        echo "Status: " . $row['status'] . "\n";
    }
}
