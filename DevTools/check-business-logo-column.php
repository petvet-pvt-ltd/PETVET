<?php
require_once __DIR__ . '/../config/connect.php';

$pdo = db();

echo "=== Checking for business_logo column ===\n";
$stmt = $pdo->query("DESCRIBE service_provider_profiles");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

$hasBusinessLogo = false;
foreach ($columns as $col) {
    if ($col['Field'] === 'business_logo') {
        $hasBusinessLogo = true;
        echo "✓ business_logo column exists: " . $col['Type'] . "\n";
        break;
    }
}

if (!$hasBusinessLogo) {
    echo "✗ business_logo column does NOT exist\n";
    echo "\nAdding business_logo column...\n";
    
    try {
        $pdo->exec("ALTER TABLE service_provider_profiles ADD COLUMN business_logo VARCHAR(255) NULL AFTER business_name");
        echo "✓ business_logo column added successfully\n";
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}
