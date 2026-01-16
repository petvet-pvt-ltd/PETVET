<?php
require_once __DIR__ . '/../config/connect.php';

$pdo = db();
$stmt = $pdo->query('DESCRIBE appointments');
echo "APPOINTMENTS TABLE COLUMNS:\n";
echo str_repeat("-", 50) . "\n";
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
    echo "{$col['Field']} - {$col['Type']}\n";
}
