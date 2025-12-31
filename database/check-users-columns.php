<?php
require_once __DIR__ . '/../config/connect.php';
$columns = db()->query('DESCRIBE users')->fetchAll(PDO::FETCH_COLUMN);
echo "Users table columns:\n";
foreach ($columns as $col) {
    echo "- $col\n";
}
