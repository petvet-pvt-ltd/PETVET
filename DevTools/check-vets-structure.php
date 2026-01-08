<?php
require_once __DIR__ . '/../config/connect.php';

$pdo = db();

echo "<h2>Vets Table Structure:</h2>";
$result = $pdo->query('DESCRIBE vets')->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($result);
echo "</pre>";

echo "<h2>Sample Vet Data:</h2>";
$result = $pdo->query('SELECT * FROM vets LIMIT 3')->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($result);
echo "</pre>";

echo "<h2>Appointments Table Structure (vet_id column):</h2>";
$result = $pdo->query('DESCRIBE appointments')->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($result);
echo "</pre>";
