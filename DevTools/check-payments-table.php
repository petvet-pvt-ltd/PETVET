<?php
require_once __DIR__ . '/../config/connect.php';

$pdo = db();
$stmt = $pdo->query('DESCRIBE payments');
echo "PAYMENTS TABLE STRUCTURE:\n";
echo str_repeat("-", 80) . "\n";
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
    echo "{$col['Field']} - {$col['Type']}\n";
}

echo "\n\nPAYMENTS TABLE DATA:\n";
echo str_repeat("-", 80) . "\n";
$stmt = $pdo->query("SELECT * FROM payments");
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Total records: " . count($payments) . "\n\n";
foreach ($payments as $payment) {
    print_r($payment);
}
