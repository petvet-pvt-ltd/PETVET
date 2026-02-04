<?php
require_once __DIR__ . '/../config/connect.php';

$pdo = db();
$stmt = $pdo->query('DESCRIBE vets');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo $r['Field'] . "\t" . $r['Type'] . "\n";
}
