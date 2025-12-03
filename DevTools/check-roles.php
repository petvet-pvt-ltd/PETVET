<?php
require_once __DIR__ . '/../config/connect.php';
$db = db();

echo "User_roles table structure:\n";
$stmt = $db->query('DESCRIBE user_roles');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "\n\nSample user_roles data:\n";
$stmt = $db->query('SELECT * FROM user_roles WHERE user_id IN (18, 19, 20) LIMIT 5');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "\n\nRoles table:\n";
$stmt = $db->query('SELECT * FROM roles');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
