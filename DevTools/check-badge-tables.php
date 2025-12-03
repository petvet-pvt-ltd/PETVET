<?php
require_once __DIR__ . '/../config/connect.php';
$db = db();

echo "All tables in database:\n";
$stmt = $db->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
print_r($tables);

echo "\n\nChecking for badge-related tables:\n";
foreach ($tables as $table) {
    if (stripos($table, 'badge') !== false || stripos($table, 'vaccin') !== false || stripos($table, 'microchip') !== false) {
        echo "Found: $table\n";
        $stmt = $db->query("DESCRIBE $table");
        print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}
