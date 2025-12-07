<?php
require 'config/connect.php';

$result = $conn->query("
    SELECT 
        a.*,
        CONCAT(u.first_name, ' ', u.last_name) as vet_name
    FROM appointments a 
    JOIN users u ON a.vet_id = u.id 
    WHERE appointment_date = '2025-12-17' 
    ORDER BY appointment_time
");

echo "Appointments on December 17, 2025:\n\n";
while($row = $result->fetch_assoc()) {
    echo "ID: {$row['id']}\n";
    echo "Vet: {$row['vet_name']} (ID: {$row['vet_id']})\n";
    echo "Clinic ID: {$row['clinic_id']}\n";
    echo "Time: {$row['appointment_time']}\n";
    echo "Duration: {$row['duration_minutes']} minutes\n";
    echo "Status: {$row['status']}\n";
    echo "---\n";
}
