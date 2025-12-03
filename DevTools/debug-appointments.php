<?php
session_start();
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../models/PetOwner/AppointmentsModel.php';

echo "<h1>Debug Appointments</h1>";
echo "<hr>";

if (!isset($_SESSION['user_id'])) {
    echo "❌ Not logged in. Session user_id not set.<br>";
    exit;
}

$userId = $_SESSION['user_id'];
echo "✅ Logged in as User ID: <strong>$userId</strong><br><br>";

try {
    $model = new PetOwnerAppointmentsModel();
    
    echo "<h2>Raw Query Results:</h2>";
    $db = db();
    $stmt = $db->prepare("
        SELECT 
            a.id,
            a.pet_id,
            a.appointment_date,
            a.appointment_time,
            a.appointment_type,
            a.status,
            p.name as pet_name
        FROM appointments a
        INNER JOIN pets p ON a.pet_id = p.id
        WHERE a.pet_owner_id = ?
        AND a.appointment_date >= CURDATE()
        AND a.status NOT IN ('cancelled', 'declined', 'completed')
        ORDER BY a.appointment_date ASC, a.appointment_time ASC
    ");
    $stmt->execute([$userId]);
    $rawResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($rawResults) . " appointments<br><br>";
    
    if (count($rawResults) > 0) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Pet</th><th>Date</th><th>Time</th><th>Type</th><th>Status</th></tr>";
        foreach ($rawResults as $appt) {
            echo "<tr>";
            echo "<td>{$appt['id']}</td>";
            echo "<td>{$appt['pet_name']}</td>";
            echo "<td>{$appt['appointment_date']}</td>";
            echo "<td>{$appt['appointment_time']}</td>";
            echo "<td>{$appt['appointment_type']}</td>";
            echo "<td><strong>{$appt['status']}</strong></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "⚠️ No results from raw query<br>";
    }
    
    echo "<h2>Model Results:</h2>";
    $result = $model->getUpcomingAppointments($userId);
    
    echo "Pets: " . count($result['pets']) . "<br>";
    echo "Appointments: " . count($result['appointments']) . "<br><br>";
    
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    
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
    table { margin: 10px 0; }
    th { background: #f1f5f9; text-align: left; }
</style>
