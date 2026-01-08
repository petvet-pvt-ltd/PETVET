<?php
require_once '../../config/connect.php';
require_once '../../config/auth_helper.php';

// Only for Pet Owners
if (!isLoggedIn() || getUserRole() !== 'pet_owner') {
    http_response_code(401);
    die('Unauthorized');
}

$userId = currentUserId();
$orderId = $_GET['order_id'] ?? null;

if (!$orderId) {
    http_response_code(400);
    die('Order ID required');
}

$db = db();

try {
    // Get order details
    $stmt = $db->prepare("
        SELECT 
            o.id,
            o.order_number,
            o.total_amount,
            o.delivery_charge,
            o.status,
            o.created_at,
            c.clinic_name,
            c.clinic_address,
            c.clinic_phone,
            CONCAT(u.first_name, ' ', u.last_name) as customer_name,
            u.email as customer_email,
            u.address as customer_address,
            u.phone as customer_phone
        FROM orders o
        JOIN clinics c ON o.clinic_id = c.id
        JOIN users u ON o.user_id = u.id
        WHERE o.id = ? AND o.user_id = ?
    ");
    $stmt->execute([$orderId, $userId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        http_response_code(404);
        die('Order not found');
    }
    
    // Get order items
    $itemStmt = $db->prepare("
        SELECT product_name as name, quantity, price
        FROM order_items
        WHERE order_id = ?
    ");
    $itemStmt->execute([$orderId]);
    $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate subtotal
    $subtotal = $order['total_amount'] - $order['delivery_charge'];
    
    // Generate HTML invoice
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Invoice - ' . htmlspecialchars($order['order_number']) . '</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: Arial, sans-serif; padding: 40px; background: #f5f5f5; }
            .invoice { max-width: 800px; margin: 0 auto; background: white; padding: 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .header { border-bottom: 3px solid #2563eb; padding-bottom: 20px; margin-bottom: 30px; }
            .header h1 { color: #2563eb; font-size: 32px; }
            .header .invoice-number { color: #64748b; margin-top: 5px; }
            .info-section { display: flex; justify-content: space-between; margin-bottom: 30px; }
            .info-block h3 { color: #1e293b; font-size: 14px; margin-bottom: 10px; }
            .info-block p { color: #64748b; font-size: 13px; line-height: 1.6; }
            table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            thead { background: #f8fafc; }
            th { text-align: left; padding: 12px; font-size: 13px; color: #475569; border-bottom: 2px solid #e2e8f0; }
            td { padding: 12px; border-bottom: 1px solid #f1f5f9; color: #1e293b; font-size: 14px; }
            .text-right { text-align: right; }
            .summary { margin-top: 30px; }
            .summary-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; }
            .summary-row.total { border-top: 2px solid #e2e8f0; padding-top: 12px; margin-top: 8px; font-size: 18px; font-weight: bold; color: #10b981; }
            .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #e2e8f0; text-align: center; color: #94a3b8; font-size: 12px; }
            @media print {
                body { padding: 0; background: white; }
                .invoice { box-shadow: none; }
            }
        </style>
    </head>
    <body>
        <div class="invoice">
            <div class="header">
                <h1>🐾 PetVet Invoice</h1>
                <div class="invoice-number">Order #' . htmlspecialchars($order['order_number']) . '</div>
                <div style="color: #64748b; font-size: 13px; margin-top: 5px;">Date: ' . date('F d, Y', strtotime($order['created_at'])) . '</div>
            </div>
            
            <div class="info-section">
                <div class="info-block">
                    <h3>FROM:</h3>
                    <p>
                        <strong>' . htmlspecialchars($order['clinic_name']) . '</strong><br>
                        ' . htmlspecialchars($order['clinic_address'] ?? 'N/A') . '<br>
                        Phone: ' . htmlspecialchars($order['clinic_phone'] ?? 'N/A') . '
                    </p>
                </div>
                <div class="info-block">
                    <h3>TO:</h3>
                    <p>
                        <strong>' . htmlspecialchars($order['customer_name']) . '</strong><br>
                        ' . htmlspecialchars($order['customer_email']) . '<br>' .
                        ($order['customer_address'] ? htmlspecialchars($order['customer_address']) . '<br>' : '') .
                        ($order['customer_phone'] ? 'Phone: ' . htmlspecialchars($order['customer_phone']) : '') . '
                    </p>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-right">Quantity</th>
                        <th class="text-right">Price</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>';
    
    foreach ($items as $item) {
        $itemTotal = $item['price'] * $item['quantity'];
        $html .= '
                    <tr>
                        <td>' . htmlspecialchars($item['name']) . '</td>
                        <td class="text-right">' . $item['quantity'] . '</td>
                        <td class="text-right">Rs. ' . number_format($item['price'], 2) . '</td>
                        <td class="text-right">Rs. ' . number_format($itemTotal, 2) . '</td>
                    </tr>';
    }
    
    $html .= '
                </tbody>
            </table>
            
            <div class="summary">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>Rs. ' . number_format($subtotal, 2) . '</span>
                </div>';
    
    if ($order['delivery_charge'] > 0) {
        $html .= '
                <div class="summary-row">
                    <span>Delivery Charge:</span>
                    <span>Rs. ' . number_format($order['delivery_charge'], 2) . '</span>
                </div>';
    }
    
    $html .= '
                <div class="summary-row total">
                    <span>Total Amount:</span>
                    <span>Rs. ' . number_format($order['total_amount'], 2) . '</span>
                </div>
            </div>
            
            <div class="footer">
                <p>Thank you for your purchase!</p>
                <p>Status: ' . htmlspecialchars($order['status']) . '</p>
            </div>
        </div>
    </body>
    </html>';
    
    // Output as PDF would require a library, for now output as HTML with print-friendly styling
    header('Content-Type: text/html; charset=utf-8');
    header('Content-Disposition: inline; filename="invoice-' . $order['order_number'] . '.html"');
    echo $html;
    
} catch (Exception $e) {
    http_response_code(500);
    die('Error generating invoice: ' . $e->getMessage());
}
