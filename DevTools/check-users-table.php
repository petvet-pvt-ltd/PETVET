<?php
require_once __DIR__ . '/../config/connect.php';
$db = db();
$stmt = $db->query('DESCRIBE users');
echo "Users table structure:\n";
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
