<?php
require_once __DIR__ . '/../config/connect.php';
$db = db();

echo "Pets table structure:\n";
$stmt = $db->query('DESCRIBE pets');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
