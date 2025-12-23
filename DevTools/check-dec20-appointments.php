<?php
require_once '../config/connect.php';

$date = '2025-12-20';

$query = "SELECT 
    a.*,
    CONCAT(u.first_name, ' ', u.last_name) as pet_owner_name,
    v.name as vet_name,
    p.name as pet_name
FROM appointments a
LEFT JOIN users u ON a.pet_owner_id = u.id
LEFT JOIN vets v ON a.vet_id = v.vet_id
LEFT JOIN pets p ON a.pet_id = p.pet_id
WHERE a.appointment_date = ?
ORDER BY a.appointment_date, a.appointment_time";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 's', $date);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

echo "=== Appointments for December 20, 2025 ===\n\n";

$appointments = [];
while($row = mysqli_fetch_assoc($result)) {
    $appointments[] = $row;
}

if (empty($appointments)) {
    echo "No appointments found for this date.\n";
} else {
    foreach ($appointments as $apt) {
        echo "ID: " . $apt['id'] . "\n";
        echo "Time: " . date('g:i A', strtotime($apt['appointment_time'])) . "\n";
        echo "Pet Owner: " . $apt['pet_owner_name'] . "\n";
        echo "Pet: " . ($apt['pet_name'] ?? 'N/A') . "\n";
        echo "Vet: " . ($apt['vet_name'] ?? 'N/A') . "\n";
        echo "Status: " . $apt['status'] . "\n";
        echo "Service: " . $apt['appointment_type'] . "\n";
        echo "Created: " . $apt['created_at'] . "\n";
        echo "---\n\n";
    }
    
    echo "\nTotal appointments: " . count($appointments) . "\n";
    
    // Check for duplicates
    $duplicates = [];
    foreach ($appointments as $apt) {
        $key = $apt['appointment_time'] . '_' . $apt['pet_owner_id'];
        if (!isset($duplicates[$key])) {
            $duplicates[$key] = [];
        }
        $duplicates[$key][] = $apt['id'];
    }
    
    echo "\n=== Checking for duplicate time slots ===\n";
    foreach ($duplicates as $key => $ids) {
        if (count($ids) > 1) {
            echo "DUPLICATE FOUND: " . $key . " has " . count($ids) . " appointments (IDs: " . implode(', ', $ids) . ")\n";
        }
    }
}
?>
