<?php
require_once __DIR__ . '/../config/connect.php';

$pdo = db();
$petId = 26;

echo "<h2>Database Table Structures & Sample Data</h2>";

// Medical Records
echo "<h3>Medical Records Table Structure:</h3>";
$cols = $pdo->query("DESCRIBE medical_records")->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
foreach ($cols as $col) {
    echo $col['Field'] . " (" . $col['Type'] . ")\n";
}
echo "</pre>";

echo "<h3>Sample Medical Records for Pet 26:</h3>";
$stmt = $pdo->prepare("
    SELECT mr.*, 
           a.appointment_date,
           a.pet_id,
           p.name as pet_name,
           CONCAT(u.first_name, ' ', u.last_name) as owner_name,
           CONCAT(uv.first_name, ' ', uv.last_name) as vet_name
    FROM medical_records mr
    JOIN appointments a ON a.id = mr.appointment_id
    JOIN pets p ON p.id = a.pet_id
    JOIN users u ON u.id = p.user_id
    LEFT JOIN users uv ON uv.id = a.vet_id
    WHERE a.pet_id = ?
    ORDER BY mr.id DESC
");
$stmt->execute([$petId]);
$medicalRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Count: " . count($medicalRecords) . "<br>";
echo "<pre>";
print_r($medicalRecords);
echo "</pre>";

// Prescriptions
echo "<h3>Prescriptions Table Structure:</h3>";
$cols = $pdo->query("DESCRIBE prescriptions")->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
foreach ($cols as $col) {
    echo $col['Field'] . " (" . $col['Type'] . ")\n";
}
echo "</pre>";

echo "<h3>Sample Prescriptions for Pet 26:</h3>";
$stmt = $pdo->prepare("
    SELECT pr.*, 
           a.appointment_date,
           a.pet_id,
           p.name as pet_name,
           CONCAT(u.first_name, ' ', u.last_name) as owner_name,
           CONCAT(uv.first_name, ' ', uv.last_name) as vet_name
    FROM prescriptions pr
    JOIN appointments a ON a.id = pr.appointment_id
    JOIN pets p ON p.id = a.pet_id
    JOIN users u ON u.id = p.user_id
    LEFT JOIN users uv ON uv.id = a.vet_id
    WHERE a.pet_id = ?
    ORDER BY pr.id DESC
");
$stmt->execute([$petId]);
$prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Count: " . count($prescriptions) . "<br>";
echo "<pre>";
print_r($prescriptions);
echo "</pre>";

// Vaccinations
echo "<h3>Vaccinations Table Structure:</h3>";
$cols = $pdo->query("DESCRIBE vaccinations")->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
foreach ($cols as $col) {
    echo $col['Field'] . " (" . $col['Type'] . ")\n";
}
echo "</pre>";

echo "<h3>Sample Vaccinations for Pet 26:</h3>";
$stmt = $pdo->prepare("
    SELECT v.*, 
           a.appointment_date,
           a.pet_id,
           p.name as pet_name,
           CONCAT(u.first_name, ' ', u.last_name) as owner_name,
           CONCAT(uv.first_name, ' ', uv.last_name) as vet_name
    FROM vaccinations v
    JOIN appointments a ON a.id = v.appointment_id
    JOIN pets p ON p.id = a.pet_id
    JOIN users u ON u.id = p.user_id
    LEFT JOIN users uv ON uv.id = a.vet_id
    WHERE a.pet_id = ?
    ORDER BY v.id DESC
");
$stmt->execute([$petId]);
$vaccinations = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Count: " . count($vaccinations) . "<br>";
echo "<pre>";
print_r($vaccinations);
echo "</pre>";
