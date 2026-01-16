<?php
require_once __DIR__ . '/../config/connect.php';

$pdo = db();
$stmt = $pdo->query('DESCRIBE users');
echo "USERS TABLE COLUMNS:\n";
echo str_repeat("-", 50) . "\n";
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
    echo "{$col['Field']} - {$col['Type']}\n";
}

echo "\n\nPETS TABLE COLUMNS:\n";
echo str_repeat("-", 50) . "\n";
$stmt = $pdo->query('DESCRIBE pets');
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
    echo "{$col['Field']} - {$col['Type']}\n";
}
