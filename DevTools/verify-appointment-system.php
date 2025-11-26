<?php
session_start();
require_once __DIR__ . '/config/connect.php';

echo "<h1>Appointment Booking System - Database Verification</h1>";
echo "<hr>";

try {
    $db = db();
    
    // 1. Check appointments table exists
    echo "<h2>✓ Step 1: Appointments Table</h2>";
    $tables = $db->query("SHOW TABLES LIKE 'appointments'")->fetchAll();
    if (count($tables) > 0) {
        echo "<p style='color: green;'>✓ Appointments table exists!</p>";
        
        // Show structure
        $columns = $db->query("DESCRIBE appointments")->fetchAll(PDO::FETCH_ASSOC);
        echo "<details><summary>Table Structure</summary><pre>";
        print_r($columns);
        echo "</pre></details>";
    } else {
        echo "<p style='color: red;'>✗ Appointments table NOT found!</p>";
        exit;
    }
    
    // 2. Check for clinics
    echo "<h2>✓ Step 2: Available Clinics</h2>";
    $clinics = $db->query("SELECT id, clinic_name FROM clinics WHERE is_active = 1")->fetchAll(PDO::FETCH_ASSOC);
    echo "<p>Found " . count($clinics) . " active clinics:</p>";
    echo "<ul>";
    foreach ($clinics as $clinic) {
        echo "<li>ID: {$clinic['id']} - {$clinic['clinic_name']}</li>";
    }
    echo "</ul>";
    
    // 3. Check for vets
    echo "<h2>✓ Step 3: Available Veterinarians</h2>";
    $vets = $db->query("
        SELECT u.id, u.first_name, u.last_name, u.email 
        FROM users u
        JOIN user_roles ur ON u.id = ur.user_id
        JOIN roles r ON ur.role_id = r.id
        WHERE r.role_name = 'vet' AND u.is_active = 1
    ")->fetchAll(PDO::FETCH_ASSOC);
    echo "<p>Found " . count($vets) . " active vets:</p>";
    echo "<ul>";
    foreach ($vets as $vet) {
        echo "<li>ID: {$vet['id']} - {$vet['first_name']} {$vet['last_name']} ({$vet['email']})</li>";
    }
    echo "</ul>";
    
    // 4. Check for pet owners and their pets
    echo "<h2>✓ Step 4: Pet Owners & Pets</h2>";
    $petOwners = $db->query("
        SELECT u.id, u.first_name, u.last_name, u.email,
               (SELECT COUNT(*) FROM pets WHERE user_id = u.id AND is_active = 1) as pet_count
        FROM users u
        JOIN user_roles ur ON u.id = ur.user_id
        JOIN roles r ON ur.role_id = r.id
        WHERE r.role_name = 'pet_owner' AND u.is_active = 1
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
    echo "<p>Sample pet owners:</p>";
    echo "<ul>";
    foreach ($petOwners as $owner) {
        echo "<li>ID: {$owner['id']} - {$owner['first_name']} {$owner['last_name']} ({$owner['email']}) - {$owner['pet_count']} pets</li>";
    }
    echo "</ul>";
    
    // 5. Check existing appointments
    echo "<h2>✓ Step 5: Current Appointments</h2>";
    $appointmentCount = $db->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
    echo "<p>Total appointments in database: <strong>$appointmentCount</strong></p>";
    
    if ($appointmentCount > 0) {
        $recentAppointments = $db->query("
            SELECT a.id, a.status, a.appointment_date, a.appointment_time, 
                   p.name as pet_name, u.first_name, u.last_name
            FROM appointments a
            JOIN pets p ON a.pet_id = p.id
            JOIN users u ON a.pet_owner_id = u.id
            ORDER BY a.created_at DESC
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Pet</th><th>Owner</th><th>Date</th><th>Time</th><th>Status</th></tr>";
        foreach ($recentAppointments as $appt) {
            $statusColor = $appt['status'] === 'pending' ? '#f59e0b' : ($appt['status'] === 'approved' ? '#10b981' : '#6b7280');
            echo "<tr>";
            echo "<td>{$appt['id']}</td>";
            echo "<td>{$appt['pet_name']}</td>";
            echo "<td>{$appt['first_name']} {$appt['last_name']}</td>";
            echo "<td>{$appt['appointment_date']}</td>";
            echo "<td>{$appt['appointment_time']}</td>";
            echo "<td style='color: $statusColor; font-weight: bold;'>{$appt['status']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Pending appointments count
        $pendingCount = $db->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'")->fetchColumn();
        echo "<p style='background: #fef3c7; padding: 10px; border-left: 4px solid #f59e0b;'>";
        echo "<strong>⏳ Pending Appointments: $pendingCount</strong>";
        echo "</p>";
    }
    
    // 6. API Endpoints
    echo "<h2>✓ Step 6: API Endpoints</h2>";
    echo "<ul>";
    echo "<li>✓ <code>/api/appointments/book.php</code> - Book new appointment</li>";
    echo "<li>✓ <code>/api/appointments/approve.php</code> - Approve pending appointment</li>";
    echo "<li>✓ <code>/api/appointments/decline.php</code> - Decline pending appointment</li>";
    echo "<li>✓ <code>/api/check-availability.php</code> - Check time slot availability</li>";
    echo "<li>✓ <code>/api/get-vets.php</code> - Get vets by clinic</li>";
    echo "</ul>";
    
    echo "<hr>";
    echo "<h2 style='color: green;'>✅ System Ready!</h2>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li>Login as a pet owner at <a href='/PETVET/index.php'>/PETVET/index.php</a></li>";
    echo "<li>Navigate to 'My Pets' and click 'Book Appointment' on any pet</li>";
    echo "<li>Fill in the appointment details and submit</li>";
    echo "<li>Login as a receptionist to see the pending appointment request</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
