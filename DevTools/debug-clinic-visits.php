<?php
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../models/PetOwner/MedicalRecordsModel.php';

$pdo = db();
$petId = 26; // RoMky's pet ID

echo "<h2>Testing Clinic Visits Query for Pet ID: $petId</h2>";

// Test 0: Check clinics table structure
echo "<h3>Clinics Table Structure:</h3>";
$cols = $pdo->query("DESCRIBE clinics")->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
foreach ($cols as $col) {
    echo $col['Field'] . " - " . $col['Type'] . "\n";
}
echo "</pre>";

// Test 1: Check appointments table structure
echo "<h3>Appointments Table Structure:</h3>";
$cols = $pdo->query("DESCRIBE appointments")->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
foreach ($cols as $col) {
    echo $col['Field'] . " - " . $col['Type'] . "\n";
}
echo "</pre>";

// Test 2: Check raw appointments for this pet
echo "<h3>Raw Appointments for Pet $petId:</h3>";
$stmt = $pdo->prepare("SELECT * FROM appointments WHERE pet_id = ? LIMIT 5");
$stmt->execute([$petId]);
$rawAppts = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($rawAppts);
echo "</pre>";

// Test 3: Test the model query
echo "<h3>Model Query Result:</h3>";
$model = new MedicalRecordsModel();
$visits = $model->getClinicVisitsByPetId($petId);
echo "Count: " . count($visits) . "<br>";
echo "<pre>";
print_r($visits);
echo "</pre>";

// Test 4: Test the FIXED query from the model
echo "<h3>Direct Query Test (FIXED - with clinic_name):</h3>";
$sql = "SELECT 
            a.*,
            a.appointment_date,
            a.appointment_time,
            a.appointment_type,
            a.status,
            a.symptoms,
            CONCAT(uv.first_name, ' ', uv.last_name) AS vet_name,
            c.clinic_name,
            mr.diagnosis,
            mr.treatment
        FROM appointments a
        LEFT JOIN users uv ON uv.id = a.vet_id
        LEFT JOIN clinics c ON c.id = a.clinic_id
        LEFT JOIN medical_records mr ON mr.appointment_id = a.id
        WHERE a.pet_id = ?
        ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$petId]);
$direct = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Count: " . count($direct) . "<br>";
echo "<strong>First 3 results:</strong><br>";
echo "<pre>";
print_r(array_slice($direct, 0, 3));
echo "</pre>";

echo "<h3>What the view will display:</h3>";
foreach (array_slice($direct, 0, 3) as $visit) {
    echo "<strong>Appointment ID {$visit['id']}:</strong><br>";
    echo "Date: " . ($visit['appointment_date'] ?? 'N/A') . "<br>";
    echo "Time: " . ($visit['appointment_time'] ?? 'N/A') . "<br>";
    echo "Type: " . ($visit['appointment_type'] ?? 'N/A') . "<br>";
    echo "Status: " . ($visit['status'] ?? 'N/A') . "<br>";
    echo "Vet: " . ($visit['vet_name'] ?? 'N/A') . "<br>";
    echo "Clinic: " . ($visit['clinic_name'] ?? 'N/A') . "<br>";
    echo "Symptoms: " . ($visit['symptoms'] ?? 'N/A') . "<br>";
    echo "Diagnosis: " . ($visit['diagnosis'] ?? 'N/A') . "<br>";
    echo "Treatment: " . ($visit['treatment'] ?? 'N/A') . "<br>";
    echo "<hr>";
}
