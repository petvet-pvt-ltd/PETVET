<?php
require_once __DIR__ . '/../config/connect.php';

$pdo = db();

echo "=== service_provider_profiles columns ===\n";
$stmt = $pdo->query("DESCRIBE service_provider_profiles");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . ' - ' . $row['Type'] . ' - Null: ' . $row['Null'] . ' - Default: ' . $row['Default'] . "\n";
}

echo "\n=== Current sitter data ===\n";
$stmt = $pdo->query("SELECT * FROM service_provider_profiles WHERE role_type = 'sitter' LIMIT 1");
$data = $stmt->fetch(PDO::FETCH_ASSOC);
if ($data) {
    foreach ($data as $key => $value) {
        echo "$key = " . var_export($value, true) . "\n";
    }
} else {
    echo "No sitter data found\n";
}
