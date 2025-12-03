<?php
/**
 * Test script to verify appointments table and data retrieval
 */

session_start();
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../models/PetOwner/AppointmentsModel.php';

echo "<h1>Appointments System Test</h1>";
echo "<hr>";

try {
    $db = db();
    
    // Check if appointments table exists
    echo "<h2>1. Checking if appointments table exists...</h2>";
    $stmt = $db->query("SHOW TABLES LIKE 'appointments'");
    $tableExists = $stmt->fetch();
    
    if ($tableExists) {
        echo "✅ Appointments table exists<br>";
        
        // Show table structure
        echo "<h3>Table Structure:</h3>";
        $stmt = $db->query("DESCRIBE appointments");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Count appointments
        echo "<h3>Appointment Count:</h3>";
        $stmt = $db->query("SELECT COUNT(*) as count FROM appointments");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Total appointments: <strong>{$count['count']}</strong><br>";
        
        if ($count['count'] > 0) {
            // Show recent appointments
            echo "<h3>Recent Appointments:</h3>";
            $stmt = $db->query("
                SELECT 
                    a.id,
                    a.pet_id,
                    a.appointment_date,
                    a.appointment_time,
                    a.appointment_type,
                    a.status,
                    p.name as pet_name,
                    u.username as owner_username
                FROM appointments a
                LEFT JOIN pets p ON a.pet_id = p.id
                LEFT JOIN users u ON a.pet_owner_id = u.id
                ORDER BY a.created_at DESC
                LIMIT 10
            ");
            $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Pet</th><th>Owner</th><th>Date</th><th>Time</th><th>Type</th><th>Status</th></tr>";
            foreach ($appointments as $appt) {
                echo "<tr>";
                echo "<td>{$appt['id']}</td>";
                echo "<td>{$appt['pet_name']}</td>";
                echo "<td>{$appt['owner_username']}</td>";
                echo "<td>{$appt['appointment_date']}</td>";
                echo "<td>{$appt['appointment_time']}</td>";
                echo "<td>{$appt['appointment_type']}</td>";
                echo "<td>{$appt['status']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        // Test the model
        echo "<h2>2. Testing AppointmentsModel...</h2>";
        
        // Get first pet owner from database
        $stmt = $db->query("
            SELECT u.id, u.username 
            FROM users u 
            WHERE u.role = 'pet-owner' 
            LIMIT 1
        ");
        $owner = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($owner) {
            echo "Testing with Pet Owner: <strong>{$owner['username']}</strong> (ID: {$owner['id']})<br><br>";
            
            // Check if session user exists
            session_start();
            if (isset($_SESSION['user_id'])) {
                echo "⚠️ Session User ID: {$_SESSION['user_id']}<br>";
                echo "Testing with session user instead...<br><br>";
                $owner['id'] = $_SESSION['user_id'];
            }
            
            $model = new PetOwnerAppointmentsModel();
            $result = $model->getUpcomingAppointments($owner['id']);
            
            echo "<h3>Upcoming Appointments:</h3>";
            echo "Pets count: " . count($result['pets']) . "<br>";
            echo "Appointments count: " . count($result['appointments']) . "<br><br>";
            
            if (!empty($result['appointments'])) {
                echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
                echo "<tr><th>Pet</th><th>Date</th><th>Time</th><th>Type</th><th>Vet</th><th>Status</th></tr>";
                foreach ($result['appointments'] as $appt) {
                    $pet = $result['pets'][$appt['pet_id']] ?? ['name' => 'Unknown'];
                    echo "<tr>";
                    echo "<td>{$pet['name']}</td>";
                    echo "<td>{$appt['date']}</td>";
                    echo "<td>{$appt['time']}</td>";
                    echo "<td>{$appt['type']}</td>";
                    echo "<td>{$appt['vet']}</td>";
                    echo "<td>{$appt['status']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No upcoming appointments found for this owner.<br>";
            }
        } else {
            echo "⚠️ No pet owners found in database<br>";
        }
        
    } else {
        echo "❌ Appointments table does NOT exist<br>";
        echo "<h3>Create the table by running:</h3>";
        echo "<code>database/migrations/create_appointments_table.sql</code>";
    }
    
    echo "<hr>";
    echo "<h2>✅ Test Complete</h2>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error:</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h1 { color: #2563eb; }
    h2 { color: #334155; margin-top: 30px; }
    h3 { color: #475569; }
    table { margin: 10px 0; }
    th { background: #f1f5f9; text-align: left; }
    code { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; }
</style>
