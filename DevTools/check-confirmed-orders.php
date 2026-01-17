<?php
require_once '../config/connect.php';
$pdo = db();

echo "=== ORDER STATUSES ===\n\n";
$stmt = $pdo->query("SELECT DISTINCT status FROM orders");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "- " . $row['status'] . "\n";
}

echo "\n=== CONFIRMED ORDERS (PAID & WAITING DELIVERY) ===\n\n";
$stmt = $pdo->query("
    SELECT 
        o.id,
        o.order_number,
        o.user_id,
        o.clinic_id,
        o.total_amount,
        o.delivery_charge,
        o.status,
        o.created_at
    FROM orders o
    WHERE o.status = 'confirmed'
    ORDER BY o.created_at DESC
");

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Total confirmed orders: " . count($orders) . "\n\n";

foreach ($orders as $order) {
    echo "Order #{$order['order_number']}\n";
    echo "  Order ID: {$order['id']}\n";
    echo "  Clinic ID: {$order['clinic_id']}\n";
    echo "  Total: LKR {$order['total_amount']}\n";
    echo "  Delivery: LKR {$order['delivery_charge']}\n";
    echo "  Date: {$order['created_at']}\n";
    
    // Get customer details
    $userStmt = $pdo->prepare("SELECT first_name, last_name, email, phone FROM users WHERE id = ?");
    $userStmt->execute([$order['user_id']]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "  Customer: {$user['first_name']} {$user['last_name']}\n";
        echo "  Phone: {$user['phone']}\n";
        echo "  Email: {$user['email']}\n";
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
    $subtotal = 0;
    foreach ($items as $item) {
        $itemTotal = $item['quantity'] * $item['price'];
        $subtotal += $itemTotal;
        echo "    - {$item['product_name']} x{$item['quantity']} @ LKR {$item['price']} = LKR " . number_format($itemTotal, 2) . "\n";
    }
    echo "  Subtotal: LKR " . number_format($subtotal, 2) . "\n";
    echo "\n";
}

echo "\n=== CONFIRMED ORDERS BY CLINIC ===\n\n";
$stmt = $pdo->query("
    SELECT clinic_id, COUNT(*) as count 
    FROM orders 
    WHERE status = 'confirmed' 
    GROUP BY clinic_id
");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "Clinic #{$row['clinic_id']}: {$row['count']} orders pending delivery\n";
}
