<?php
require_once __DIR__ . '/../config/connect.php';

echo "=== APPOINTMENTS TABLE STRUCTURE ===\n\n";
$cols = db()->query('DESCRIBE appointments')->fetchAll();
foreach($cols as $c) {
    echo "{$c['Field']} ({$c['Type']})\n";
}

echo "\n=== SAMPLE APPOINTMENTS ===\n";
$appointments = db()->query("SELECT * FROM appointments LIMIT 3")->fetchAll();
foreach($appointments as $apt) {
    echo "ID: {$apt['id']}, Clinic: {$apt['clinic_id']}, Vet: {$apt['vet_id']}, Date: {$apt['appointment_date']}, Time: {$apt['appointment_time']}\n";
}

echo "\n=== CHECK VET SCHEDULE/AVAILABILITY ===\n";
$tables = db()->query("SHOW TABLES LIKE '%vet%'")->fetchAll(PDO::FETCH_COLUMN);
foreach($tables as $table) {
    echo "- $table\n";
}

echo "\n=== CHECK CLINIC TABLES ===\n";
$tables = db()->query("SHOW TABLES LIKE '%clinic%'")->fetchAll(PDO::FETCH_COLUMN);
foreach($tables as $table) {
    echo "- $table\n";
}
?>
