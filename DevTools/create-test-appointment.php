<?php
session_start();
require_once __DIR__ . '/config/connect.php';

// Simulate logged-in pet owner
$_SESSION['user_id'] = 10; // petowner user ID

$db = db();

// Check if user has pets
$petsStmt = $db->prepare("SELECT id, name, user_id FROM pets WHERE user_id = ?");
$petsStmt->execute([10]);
$pets = $petsStmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Manual Appointment Creation Test</h2>";

if (empty($pets)) {
    echo "<p style='color: red;'>User 10 has no pets. Let me find a user with pets...</p>";
    
    $userWithPetsStmt = $db->query("
        SELECT DISTINCT p.user_id, u.email, u.first_name, u.last_name, COUNT(p.id) as pet_count
        FROM pets p 
        JOIN users u ON p.user_id = u.id
        WHERE p.is_active = 1
        GROUP BY p.user_id
        LIMIT 5
    ");
    $usersWithPets = $userWithPetsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Users with pets:</h3>";
    echo "<pre>" . print_r($usersWithPets, true) . "</pre>";
    
    if (!empty($usersWithPets)) {
        $_SESSION['user_id'] = $usersWithPets[0]['user_id'];
        echo "<p>Switching to user: {$usersWithPets[0]['email']} (ID: {$usersWithPets[0]['user_id']})</p>";
        
        $petsStmt->execute([$_SESSION['user_id']]);
        $pets = $petsStmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (!empty($pets)) {
    echo "<h3>Creating test appointment...</h3>";
    echo "<p>User ID: {$_SESSION['user_id']}</p>";
    echo "<p>Pet: {$pets[0]['name']} (ID: {$pets[0]['id']})</p>";
    
    // Create appointment directly
    $insertStmt = $db->prepare("
        INSERT INTO appointments (
            pet_id, pet_owner_id, clinic_id, vet_id, 
            appointment_type, symptoms, appointment_date, appointment_time, 
            status, duration_minutes
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', 20)
    ");
    
    $result = $insertStmt->execute([
        $pets[0]['id'],      // pet_id
        $_SESSION['user_id'], // pet_owner_id
        1,                    // clinic_id (Happy Paws)
        3,                    // vet_id (vet@gmail.com)
        'routine',            // appointment_type
        'Test appointment - manual creation',
        '2025-11-27',        // appointment_date
        '10:00:00'           // appointment_time
    ]);
    
    if ($result) {
        $appointmentId = $db->lastInsertId();
        echo "<p style='color: green; font-weight: bold;'>✅ SUCCESS! Appointment ID: $appointmentId</p>";
        
        // Verify it was created
        $verifyStmt = $db->prepare("
            SELECT a.*, p.name as pet_name, CONCAT(u.first_name, ' ', u.last_name) as owner_name, c.clinic_name
            FROM appointments a
            JOIN pets p ON a.pet_id = p.id
            JOIN users u ON a.pet_owner_id = u.id
            JOIN clinics c ON a.clinic_id = c.id
            WHERE a.id = ?
        ");
        $verifyStmt->execute([$appointmentId]);
        $appointment = $verifyStmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<h3>Created Appointment Details:</h3>";
        echo "<pre>" . print_r($appointment, true) . "</pre>";
        
        echo "<hr>";
        echo "<h3>Now check the receptionist dashboard!</h3>";
        echo "<p>Login as: receptionist@petvet.com / password123</p>";
        echo "<p>This appointment should appear in the 'Pending Requests' section.</p>";
        
    } else {
        echo "<p style='color: red;'>❌ Failed to create appointment</p>";
        print_r($insertStmt->errorInfo());
    }
} else {
    echo "<p style='color: red;'>No pets available for testing!</p>";
}
?>
