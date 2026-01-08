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

// Helper to get or create cart
function getOrCreateCart($db, $userId, $clinicId) {
    $stmt = $db->prepare("SELECT id FROM carts WHERE user_id = ? AND clinic_id = ?");
    $stmt->execute([$userId, $clinicId]);
    $cartId = $stmt->fetchColumn();

    if (!$cartId) {
        $stmt = $db->prepare("INSERT INTO carts (user_id, clinic_id) VALUES (?, ?)");
        $stmt->execute([$userId, $clinicId]);
        $cartId = $db->lastInsertId();
    }
    return $cartId;
}

if ($method === 'GET') {
    $clinicId = $_GET['clinic_id'] ?? null;
    if (!$clinicId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Clinic ID required']);
        exit;
    }

    try {
        // Get clinic name and settings
        $clinicStmt = $db->prepare("SELECT clinic_name FROM clinics WHERE id = ?");
        $clinicStmt->execute([$clinicId]);
        $clinic = $clinicStmt->fetch();
        $clinicName = $clinic ? $clinic['clinic_name'] : 'PetVet Shop';
        
        // Get clinic's max items per order setting
        $settingsStmt = $db->prepare("SELECT max_items_per_order FROM clinic_shop_settings WHERE clinic_id = ?");
        $settingsStmt->execute([$clinicId]);
        $settings = $settingsStmt->fetch();
        $maxItemsPerOrder = $settings ? (int)$settings['max_items_per_order'] : 10;
        
        // Get cart ID
        $stmt = $db->prepare("SELECT id FROM carts WHERE user_id = ? AND clinic_id = ?");
        $stmt->execute([$userId, $clinicId]);
        $cartId = $stmt->fetchColumn();

        if (!$cartId) {
            echo json_encode([
                'success' => true, 
                'items' => [], 
                'total' => 0, 
                'totalQuantity' => 0,
                'maxItemsPerOrder' => $maxItemsPerOrder,
                'clinic_name' => $clinicName
            ]);
            exit;
        }

        // Fetch items and validate stock
        $stmt = $db->prepare("
            SELECT ci.id, ci.product_id, ci.quantity, p.name, p.price, p.stock, p.image_url as image, p.is_active
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.cart_id = ?
        ");
        $stmt->execute([$cartId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $validItems = [];
        $itemsToRemove = [];
        $itemsToUpdate = [];

        foreach ($items as $item) {
            // Check if product is active and has stock
            if (!$item['is_active'] || $item['stock'] <= 0) {
                $itemsToRemove[] = $item['id'];
                continue;
            }

            // Check quantity limit
            $newQty = $item['quantity'];
            if ($newQty > $item['stock']) {
                $newQty = $item['stock'];
                $itemsToUpdate[$item['id']] = $newQty;
            }
            if ($newQty > 5) {
                $newQty = 5;
                $itemsToUpdate[$item['id']] = $newQty;
            }

            $item['quantity'] = $newQty;
            $validItems[] = $item;
        }

        // Perform DB updates for invalid items
        if (!empty($itemsToRemove)) {
            $ids = implode(',', $itemsToRemove);
            $db->exec("DELETE FROM cart_items WHERE id IN ($ids)");
        }
        foreach ($itemsToUpdate as $id => $qty) {
            $upd = $db->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
            $upd->execute([$qty, $id]);
        }

        // Calculate total
        $total = 0;
        $totalQuantity = 0;
        foreach ($validItems as $item) {
            $total += $item['price'] * $item['quantity'];
            $totalQuantity += $item['quantity'];
        }

        echo json_encode([
            'success' => true, 
            'items' => $validItems, 
            'total' => $total,
            'totalQuantity' => $totalQuantity,
            'maxItemsPerOrder' => $maxItemsPerOrder,
            'clinic_name' => $clinicName
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }

} elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    $clinicId = $input['clinic_id'] ?? null;
    $productId = $input['product_id'] ?? null;
    $quantity = intval($input['quantity'] ?? 1);

    if (!$clinicId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Clinic ID required']);
        exit;
    }

    try {
        $cartId = getOrCreateCart($db, $userId, $clinicId);

        if ($action === 'add' || $action === 'update') {
            if (!$productId) throw new Exception("Product ID required");

            // Get clinic's max items per order setting
            $settingsStmt = $db->prepare("SELECT max_items_per_order FROM clinic_shop_settings WHERE clinic_id = ?");
            $settingsStmt->execute([$clinicId]);
            $settings = $settingsStmt->fetch();
            $maxItemsPerOrder = $settings ? (int)$settings['max_items_per_order'] : 10;

            // Check stock
            $stmt = $db->prepare("SELECT stock, is_active FROM products WHERE id = ? AND clinic_id = ?");
            $stmt->execute([$productId, $clinicId]);
            $product = $stmt->fetch();

            if (!$product || !$product['is_active']) {
                throw new Exception("Product not available");
            }

            if ($quantity > $product['stock']) {
                throw new Exception("Requested quantity exceeds stock ({$product['stock']})");
            }
            if ($quantity > 5) {
                throw new Exception("Maximum 5 items per product allowed");
            }
            if ($quantity < 1) {
                throw new Exception("Quantity must be at least 1");
            }

            // Check total cart quantity including this addition
            $cartTotalStmt = $db->prepare("SELECT COALESCE(SUM(quantity), 0) as total FROM cart_items WHERE cart_id = ? AND product_id != ?");
            $cartTotalStmt->execute([$cartId, $productId]);
            $currentCartTotal = (int)$cartTotalStmt->fetchColumn();

            if ($action === 'add') {
                // Check if exists, if so update
                $check = $db->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?");
                $check->execute([$cartId, $productId]);
                $existing = $check->fetch();

                if ($existing) {
                    $newQty = $existing['quantity'] + $quantity;
                    
                    // Check against max items per order
                    if (($currentCartTotal + $newQty) > $maxItemsPerOrder) {
                        throw new Exception("Cannot add more items. This shop allows maximum {$maxItemsPerOrder} items per order.");
                    }
                    
                    if ($newQty > 5) $newQty = 5;
                    if ($newQty > $product['stock']) $newQty = $product['stock'];
                    
                    $upd = $db->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
                    $upd->execute([$newQty, $existing['id']]);
                } else {
                    // Check against max items per order
                    if (($currentCartTotal + $quantity) > $maxItemsPerOrder) {
                        throw new Exception("Cannot add more items. This shop allows maximum {$maxItemsPerOrder} items per order.");
                    }
                    
                    $ins = $db->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)");
                    $ins->execute([$cartId, $productId, $quantity]);
                }
            } elseif ($action === 'update') {
                // Check against max items per order
                if (($currentCartTotal + $quantity) > $maxItemsPerOrder) {
                    throw new Exception("Cannot update quantity. This shop allows maximum {$maxItemsPerOrder} items per order.");
                }
                
                // Direct set quantity (from cart edit)
                $upd = $db->prepare("UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND product_id = ?");
                $upd->execute([$quantity, $cartId, $productId]);
            }

        } elseif ($action === 'remove') {
            if (!$productId) throw new Exception("Product ID required");
            $del = $db->prepare("DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?");
            $del->execute([$cartId, $productId]);
        } elseif ($action === 'clear') {
            $del = $db->prepare("DELETE FROM cart_items WHERE cart_id = ?");
            $del->execute([$cartId]);
        }

        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
