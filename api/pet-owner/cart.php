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
        // Get cart ID
        $stmt = $db->prepare("SELECT id FROM carts WHERE user_id = ? AND clinic_id = ?");
        $stmt->execute([$userId, $clinicId]);
        $cartId = $stmt->fetchColumn();

        if (!$cartId) {
            echo json_encode(['success' => true, 'items' => [], 'total' => 0]);
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
        foreach ($validItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        echo json_encode(['success' => true, 'items' => $validItems, 'total' => $total]);

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

            if ($action === 'add') {
                // Check if exists, if so update
                $check = $db->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?");
                $check->execute([$cartId, $productId]);
                $existing = $check->fetch();

                if ($existing) {
                    // For 'add' from shop page, we usually just increment or set? 
                    // Requirement: "if from shop-clinic page, quantity = 1" (implies add 1 or set to 1?)
                    // Usually "Add to Cart" means "Add this quantity to existing".
                    // But let's follow: "if from shop-product page, quantity = whatever".
                    // Let's assume the frontend sends the *desired final quantity* or we handle logic there.
                    // Actually, safer to handle "set quantity" for update, and "add" logic here.
                    
                    // Let's treat 'add' as "Insert or Update to specific quantity" or "Increment"?
                    // To keep it simple: The frontend will send the *target* quantity for 'update'.
                    // For 'add', let's assume it means "Add this amount to current".
                    
                    // Wait, requirement: "if from shop-clinic page, quantity = 1".
                    // If I click add to cart twice, should it be 2? Yes.
                    
                    $newQty = $existing['quantity'] + $quantity;
                    if ($newQty > 5) $newQty = 5;
                    if ($newQty > $product['stock']) $newQty = $product['stock'];
                    
                    $upd = $db->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
                    $upd->execute([$newQty, $existing['id']]);
                } else {
                    $ins = $db->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)");
                    $ins->execute([$cartId, $productId, $quantity]);
                }
            } elseif ($action === 'update') {
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
