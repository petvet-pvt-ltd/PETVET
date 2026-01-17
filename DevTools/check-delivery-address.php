<?php
require_once '../config/connect.php';
$pdo = db();

echo "=== CHECKING ADDRESS STORAGE ===\n\n";
$stmt = $pdo->query("SELECT address, city, district FROM pet_owner_profiles WHERE user_id = 2 LIMIT 1");
$addr = $stmt->fetch(PDO::FETCH_ASSOC);
if ($addr) {
    echo "Pet Owner Profile has:\n";
    echo "  Address: {$addr['address']}\n";
    echo "  City: {$addr['city']}\n";
    echo "  District: {$addr['district']}\n";
}
