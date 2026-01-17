<?php
require_once '../config/connect.php';
$pdo = db();

echo "Checking clinic manager profiles...\n\n";
$stmt = $pdo->query("SELECT user_id, clinic_id FROM clinic_manager_profiles LIMIT 5");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "User ID: {$row['user_id']}, Clinic ID: {$row['clinic_id']}\n";
}

echo "\n\nOrders by clinic:\n";
$stmt = $pdo->query("SELECT clinic_id, COUNT(*) as count FROM orders WHERE status = 'Confirmed' GROUP BY clinic_id");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "Clinic {$row['clinic_id']}: {$row['count']} orders\n";
}
