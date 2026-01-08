<?php
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../models/PetOwner/MedicalRecordsModel.php';

$pdo = db();
$petId = 26;
$ownerId = 2;

echo "<h2>Testing Appointments Display for Pet 26</h2>";

$model = new MedicalRecordsModel();
$clinic_visits = $model->getClinicVisitsByPetId($petId);

echo "<h3>Total Clinic Visits/Appointments: " . count($clinic_visits) . "</h3>";

// Categorize like the view does
$upcoming = [];
$ongoing = [];
$completed = [];

foreach ($clinic_visits as $visit) {
    $status = strtolower($visit['status'] ?? '');
    if (in_array($status, ['pending', 'approved'])) {
        $upcoming[] = $visit;
    } elseif ($status === 'ongoing') {
        $ongoing[] = $visit;
    } elseif ($status === 'completed') {
        $completed[] = $visit;
    }
}

echo "<h3>Categorized:</h3>";
echo "Upcoming (pending/approved): " . count($upcoming) . "<br>";
echo "Ongoing: " . count($ongoing) . "<br>";
echo "Completed: " . count($completed) . "<br>";
echo "<hr>";

echo "<h3>UPCOMING APPOINTMENTS:</h3>";
if (!empty($upcoming)) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Date</th><th>Time</th><th>Type</th><th>Vet</th><th>Status</th><th>Symptoms</th></tr>";
    foreach ($upcoming as $v) {
        echo "<tr>";
        echo "<td>{$v['id']}</td>";
        echo "<td>" . ($v['appointment_date'] ?? 'N/A') . "</td>";
        echo "<td>" . ($v['appointment_time'] ?? 'N/A') . "</td>";
        echo "<td>" . ($v['appointment_type'] ?? 'N/A') . "</td>";
        echo "<td>" . ($v['vet_name'] ?? 'N/A') . "</td>";
        echo "<td>" . ($v['status'] ?? 'N/A') . "</td>";
        echo "<td>" . ($v['symptoms'] ?? '-') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No upcoming appointments</p>";
}

echo "<h3>ONGOING APPOINTMENTS:</h3>";
if (!empty($ongoing)) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Date</th><th>Time</th><th>Type</th><th>Vet</th><th>Status</th><th>Diagnosis</th><th>Treatment</th></tr>";
    foreach ($ongoing as $v) {
        echo "<tr>";
        echo "<td>{$v['id']}</td>";
        echo "<td>" . ($v['appointment_date'] ?? 'N/A') . "</td>";
        echo "<td>" . ($v['appointment_time'] ?? 'N/A') . "</td>";
        echo "<td>" . ($v['appointment_type'] ?? 'N/A') . "</td>";
        echo "<td>" . ($v['vet_name'] ?? 'N/A') . "</td>";
        echo "<td>" . ($v['status'] ?? 'N/A') . "</td>";
        echo "<td>" . ($v['diagnosis'] ?? '-') . "</td>";
        echo "<td>" . ($v['treatment'] ?? '-') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No ongoing appointments</p>";
}

echo "<h3>COMPLETED APPOINTMENTS:</h3>";
if (!empty($completed)) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Date</th><th>Time</th><th>Type</th><th>Vet</th><th>Diagnosis</th><th>Treatment</th></tr>";
    foreach ($completed as $v) {
        echo "<tr>";
        echo "<td>{$v['id']}</td>";
        echo "<td>" . ($v['appointment_date'] ?? 'N/A') . "</td>";
        echo "<td>" . ($v['appointment_time'] ?? 'N/A') . "</td>";
        echo "<td>" . ($v['appointment_type'] ?? 'N/A') . "</td>";
        echo "<td>" . ($v['vet_name'] ?? 'N/A') . "</td>";
        echo "<td>" . ($v['diagnosis'] ?? '-') . "</td>";
        echo "<td>" . ($v['treatment'] ?? '-') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No completed appointments</p>";
}

echo "<hr>";
echo "<h3>First 3 Raw Records from Query:</h3>";
echo "<pre>";
print_r(array_slice($clinic_visits, 0, 3));
echo "</pre>";
