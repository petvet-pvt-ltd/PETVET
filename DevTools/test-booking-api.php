<?php
// Test if the book appointment API is working
session_start();

// Simulate a logged-in user
$_SESSION['user_id'] = 10; // peterpoker@gmail.com user ID

// Simulate booking data
$testData = [
    'pet_id' => 1, // You'll need to check if this pet exists
    'clinic_id' => 1, // Happy Paws
    'vet_id' => 3, // vet@gmail.com
    'appointment_type' => 'routine',
    'symptoms' => 'Test appointment booking',
    'appointment_date' => '2025-11-27',
    'appointment_time' => '10:00'
];

echo "<h2>Testing Appointment Booking API</h2>";
echo "<h3>Test Data:</h3>";
echo "<pre>" . print_r($testData, true) . "</pre>";

// Check if user has pets
require_once __DIR__ . '/config/connect.php';
$db = db();

$petsStmt = $db->prepare("SELECT id, name FROM pets WHERE user_id = ? LIMIT 5");
$petsStmt->execute([10]);
$pets = $petsStmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>Available Pets for user 10:</h3>";
if (empty($pets)) {
    echo "<p style='color: red;'>NO PETS FOUND! User must have pets to book appointments.</p>";
    echo "<p>Let me check all users with pets:</p>";
    
    $allPetsStmt = $db->query("SELECT p.id, p.name, p.user_id, u.email FROM pets p JOIN users u ON p.user_id = u.id LIMIT 10");
    $allPets = $allPetsStmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>" . print_r($allPets, true) . "</pre>";
} else {
    echo "<pre>" . print_r($pets, true) . "</pre>";
    
    // Use the first pet for testing
    $testData['pet_id'] = $pets[0]['id'];
    
    echo "<h3>Making API Call...</h3>";
    
    // Make the actual API call
    $ch = curl_init('http://localhost/PETVET/api/appointments/book.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Cookie: ' . session_name() . '=' . session_id()
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "<h3>Response (HTTP $httpCode):</h3>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    $result = json_decode($response, true);
    if ($result && isset($result['success']) && $result['success']) {
        echo "<p style='color: green; font-weight: bold;'>✅ SUCCESS! Appointment created.</p>";
        
        // Check database
        $checkStmt = $db->query("SELECT * FROM appointments ORDER BY id DESC LIMIT 1");
        $appointment = $checkStmt->fetch(PDO::FETCH_ASSOC);
        echo "<h3>Created Appointment:</h3>";
        echo "<pre>" . print_r($appointment, true) . "</pre>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ FAILED!</p>";
    }
}
?>
