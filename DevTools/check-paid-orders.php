<?php
require_once '../config/connect.php';
$pdo = db();

echo "=== CHECKING ORDERS TABLE COLUMNS ===\n\n";
$stmt = $pdo->query("SELECT * FROM orders LIMIT 1");
$sample = $stmt->fetch(PDO::FETCH_ASSOC);
if ($sample) {
    echo "Available columns:\n";
    foreach ($sample as $key => $value) {
        echo "  - $key\n";
    }
}

echo "\n=== PAID ORDERS WAITING FOR DELIVERY ===\n\n";
$stmt = $pdo->query("
    SELECT 
        o.id,
        o.order_number,
        o.user_id,
        o.clinic_id,
        o.total_amount,
        o.status,
        o.created_at
    FROM orders o
    WHERE o.status = 'paid'
    ORDER BY o.created_at DESC
    LIMIT 10
");

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Total paid orders: " . count($orders) . "\n\n";

foreach ($orders as $order) {
    echo "Order #{$order['order_number']}\n";
    echo "  Order ID: {$order['id']}\n";
    echo "  User ID: {$order['user_id']}\n";
    echo "  Clinic ID: {$order['clinic_id']}\n";
    echo "  Total: LKR {$order['total_amount']}\n";
    echo "  Status: {$order['status']}\n";
    echo "  Date: {$order['created_at']}\n";
    
    // Get customer details
    $userStmt = $pdo->prepare("SELECT first_name, last_name, email, phone FROM users WHERE id = ?");
    $userStmt->execute([$order['user_id']]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "  Customer: {$user['first_name']} {$user['last_name']}\n";
        echo "  Email: {$user['email']}\n";
        echo "  Phone: {$user['phone']}\n";
    }
    
    // Get order items
    $itemsStmt = $pdo->prepare("
        SELECT product_name, quantity, price 
        FROM order_items 
        WHERE order_id = ?
    ");
    $itemsStmt->execute([$order['id']]);
    $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "  Items (" . count($items) . "):\n";
    foreach ($items as $item) {
        echo "    - {$item['product_name']} x{$item['quantity']} @ LKR {$item['price']}\n";
    }
    echo "\n";
}

echo "\n=== PAID ORDERS COUNT BY CLINIC ===\n\n";
$stmt = $pdo->query("
    SELECT clinic_id, COUNT(*) as count 
    FROM orders 
    WHERE status = 'paid' 
    GROUP BY clinic_id
");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "Clinic #{$row['clinic_id']}: {$row['count']} pending orders\n";
}
