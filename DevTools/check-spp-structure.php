<?php
require_once __DIR__ . '/../config/connect.php';

$pdo = db();
$stmt = $pdo->query('DESCRIBE service_provider_profiles');
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "service_provider_profiles table structure:\n\n";
foreach ($columns as $col) {
    echo $col['Field'] . " - " . $col['Type'] . " - " . $col['Null'] . " - " . $col['Key'] . "\n";
}
