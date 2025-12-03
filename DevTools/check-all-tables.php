<?php
require_once __DIR__ . '/../config/connect.php';

$tables = ['users', 'user_roles', 'roles', 'pets'];

foreach ($tables as $table) {
    echo "\n========== $table ==========\n";
    $result = $conn->query("DESCRIBE $table");
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo sprintf(
                "%-25s %-20s %-10s %-10s %-20s\n",
                $row['Field'],
                $row['Type'],
                $row['Null'],
                $row['Key'],
                $row['Extra']
            );
        }
    }
}

echo "\n========== Sample User Data ==========\n";
$result = $conn->query("SELECT id, email, first_name, last_name, phone, address, avatar, email_verified, is_active, created_at FROM users LIMIT 3");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
}
