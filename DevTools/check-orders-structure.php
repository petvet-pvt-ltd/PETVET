<?php
require_once '../config/connect.php';
$pdo = db();

echo "=== ORDERS TABLE STRUCTURE ===\n\n";
try {
    $stmt = $pdo->query('DESCRIBE orders');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("%-30s %-20s %s\n", 
            $row['Field'], 
            $row['Type'], 
            $row['Null'] === 'YES' ? 'NULL' : 'NOT NULL'
        );
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== ORDER_ITEMS TABLE STRUCTURE ===\n\n";
try {
    $stmt = $pdo->query('DESCRIBE order_items');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("%-30s %-20s %s\n", 
            $row['Field'], 
            $row['Type'], 
            $row['Null'] === 'YES' ? 'NULL' : 'NOT NULL'
        );
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== SAMPLE ORDERS WITH ITEMS ===\n\n";
try {
    $stmt = $pdo->query("
        SELECT 
            o.id as order_id,
            o.user_id,
            o.clinic_id,
            o.total_amount,
            o.status,
            o.delivery_address,
            o.phone,
            o.created_at,
            u.first_name,
            u.last_name,
            COUNT(oi.id) as item_count
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE o.status = 'paid'
        GROUP BY o.id
        ORDER BY o.created_at DESC
        LIMIT 5
    ");
    
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($orders) > 0) {
        foreach ($orders as $order) {
            echo "Order #{$order['order_id']}\n";
            echo "  Customer: {$order['first_name']} {$order['last_name']}\n";
            echo "  Phone: {$order['phone']}\n";
            echo "  Address: {$order['delivery_address']}\n";
            echo "  Status: {$order['status']}\n";
            echo "  Total: LKR {$order['total_amount']}\n";
            echo "  Items: {$order['item_count']}\n";
            echo "  Date: {$order['created_at']}\n";
            
            // Get items for this order
            $itemStmt = $pdo->prepare("
                SELECT 
                    oi.quantity,
                    oi.price,
                    p.title as product_name
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
            ");
            $itemStmt->execute([$order['order_id']]);
            $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "  Products:\n";
            foreach ($items as $item) {
                echo "    - {$item['product_name']} x{$item['quantity']} @ LKR {$item['price']}\n";
            }
            echo "\n";
        }
    } else {
        echo "No paid orders found.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== TOTAL PAID ORDERS BY CLINIC ===\n\n";
try {
    $stmt = $pdo->query("
        SELECT 
            clinic_id,
            COUNT(*) as total_orders,
            SUM(total_amount) as total_revenue
        FROM orders
        WHERE status = 'paid'
        GROUP BY clinic_id
    ");
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "Clinic #{$row['clinic_id']}: {$row['total_orders']} orders, LKR {$row['total_revenue']}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
