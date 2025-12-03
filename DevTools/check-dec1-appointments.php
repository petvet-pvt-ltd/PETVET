<?php
require_once __DIR__ . '/../config/connect.php';
$db = db();
$stmt = $db->prepare('SELECT id, appointment_date, appointment_time, vet_id, clinic_id, duration_minutes, status FROM appointments WHERE appointment_date = ? AND status NOT IN (\'declined\', \'cancelled\') ORDER BY appointment_time');
$stmt->execute(['2025-12-01']);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo 'Appointments on Dec 1, 2025:' . PHP_EOL;
print_r($appointments);

