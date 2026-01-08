<?php
require_once '../../config/connect.php';
require_once '../../config/auth_helper.php';

header('Content-Type: application/json');

// Only for Pet Owners
if (!isLoggedIn() || getUserRole() !== 'pet_owner') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$userId = currentUserId();
$method = $_SERVER['REQUEST_METHOD'];
$db = db();

if ($method === 'POST') {
    // Save order after payment
    $input = json_decode(file_get_contents('php://input'), true);
    
    error_log("Orders API - Received data: " . json_encode($input));
    
    $clinicId = $input['clinic_id'] ?? null;
    $sessionId = $input['session_id'] ?? null;
    $items = $input['items'] ?? [];
    $total = $input['total'] ?? 0;
    $deliveryCharge = $input['delivery_charge'] ?? 0;
    
    error_log("Orders API - Parsed: clinic_id=$clinicId, items_count=" . count($items) . ", total=$total, delivery=$deliveryCharge");
    
    if (!$clinicId || empty($items)) {
        error_log("Orders API - Validation failed: Missing clinic_id or items");
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit;
    }
    
    try {
        $db->beginTransaction();
        
        // Generate order number
        $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
        
        error_log("Orders API - Inserting order: $orderNumber");
        
        // Insert order
        $stmt = $db->prepare("
            INSERT INTO orders (order_number, user_id, clinic_id, total_amount, delivery_charge, payment_session_id, status)
            VALUES (?, ?, ?, ?, ?, ?, 'Confirmed')
        ");
        $stmt->execute([$orderNumber, $userId, $clinicId, $total, $deliveryCharge, $sessionId]);
        $orderId = $db->lastInsertId();
        
        error_log("Orders API - Order created with ID: $orderId");
        
        // Insert order items
        $itemStmt = $db->prepare("
            INSERT INTO order_items (order_id, product_id, product_name, quantity, price)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        foreach ($items as $item) {
            error_log("Orders API - Inserting item: " . json_encode($item));
            $itemStmt->execute([
                $orderId,
                $item['product_id'] ?? null,
                $item['name'],
                $item['quantity'],
                $item['price']
            ]);
        }
        
        $db->commit();
        
        error_log("Orders API - Order saved successfully!");
        
        echo json_encode([
            'success' => true,
            'order_id' => $orderId,
            'order_number' => $orderNumber
        ]);
        
    } catch (Exception $e) {
        $db->rollBack();
        error_log("Orders API - Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
} elseif ($method === 'GET') {
    // Get user's orders
    try {
        $stmt = $db->prepare("
            SELECT 
                o.id,
                o.order_number,
                o.total_amount,
                o.delivery_charge,
                o.status,
                o.created_at,
                c.clinic_name,
                c.clinic_logo
            FROM orders o
            JOIN clinics c ON o.clinic_id = c.id
            WHERE o.user_id = ?
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$userId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get items for each order
        foreach ($orders as &$order) {
            $itemStmt = $db->prepare("
                SELECT product_name as name, quantity, price
                FROM order_items
                WHERE order_id = ?
            ");
            $itemStmt->execute([$order['id']]);
            $order['items'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        echo json_encode([
            'success' => true,
            'orders' => $orders
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
