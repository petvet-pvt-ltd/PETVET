<?php
require_once __DIR__ . '/../config/connect.php';
$columns = db()->query('DESCRIBE clinics')->fetchAll(PDO::FETCH_COLUMN);
echo "Clinics table columns:\n";
foreach ($columns as $col) {
    echo "- $col\n";
}
