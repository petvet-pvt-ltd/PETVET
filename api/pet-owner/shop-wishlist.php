<?php
/**
 * Shop Wishlist API
 * Manage product wishlist for pet owners
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    $pdo = db();
    
    switch ($action) {
        case 'get':
            // Get all wishlisted products for this user
            $clinicId = $_GET['clinic_id'] ?? null;
            
            $sql = "SELECT 
                        w.id as wishlist_id,
                        w.product_id,
                        w.clinic_id,
                        w.created_at as wishlisted_at,
                        p.name,
                        p.description,
                        p.price,
                        p.stock,
                        p.image,
                        p.images,
                        c.clinic_name
                    FROM shop_wishlist w
                    JOIN products p ON w.product_id = p.id
                    JOIN clinics c ON w.clinic_id = c.id
                    WHERE w.user_id = ?";
            
            $params = [$userId];
            
            if ($clinicId) {
                $sql .= " AND w.clinic_id = ?";
                $params[] = $clinicId;
            }
            
            $sql .= " ORDER BY w.created_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $wishlist = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Process images for each product
            foreach ($wishlist as &$item) {
                if (!empty($item['images'])) {
                    $item['images'] = json_decode($item['images'], true);
                } else {
                    $item['images'] = [$item['image']];
                }
            }
            
            echo json_encode([
                'success' => true,
                'wishlist' => $wishlist,
                'total' => count($wishlist)
            ]);
            break;
            
        case 'check':
            // Check if a product is in wishlist
            $productId = $_GET['product_id'] ?? null;
            
            if (!$productId) {
                echo json_encode(['success' => false, 'error' => 'Product ID required']);
                exit;
            }
            
            $stmt = $pdo->prepare("SELECT id FROM shop_wishlist WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$userId, $productId]);
            $exists = $stmt->fetch() !== false;
            
            echo json_encode([
                'success' => true,
                'in_wishlist' => $exists
            ]);
            break;
            
        case 'get_ids':
            // Get all wishlisted product IDs for quick lookup
            $clinicId = $_GET['clinic_id'] ?? null;
            
            $sql = "SELECT product_id FROM shop_wishlist WHERE user_id = ?";
            $params = [$userId];
            
            if ($clinicId) {
                $sql .= " AND clinic_id = ?";
                $params[] = $clinicId;
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            echo json_encode([
                'success' => true,
                'product_ids' => $ids
            ]);
            break;
            
        case 'add':
            // Add product to wishlist
            $productId = $_POST['product_id'] ?? null;
            $clinicId = $_POST['clinic_id'] ?? null;
            
            if (!$productId || !$clinicId) {
                echo json_encode(['success' => false, 'error' => 'Product ID and Clinic ID required']);
                exit;
            }
            
            // Check if product exists
            $stmt = $pdo->prepare("SELECT id, clinic_id FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch();
            
            if (!$product) {
                echo json_encode(['success' => false, 'error' => 'Product not found']);
                exit;
            }
            
            // Verify clinic_id matches
            if ($product['clinic_id'] != $clinicId) {
                $clinicId = $product['clinic_id'];
            }
            
            // Add to wishlist (ignore if already exists due to UNIQUE constraint)
            $sql = "INSERT INTO shop_wishlist (user_id, product_id, clinic_id) 
                    VALUES (?, ?, ?) 
                    ON DUPLICATE KEY UPDATE created_at = CURRENT_TIMESTAMP";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userId, $productId, $clinicId]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Product added to wishlist'
            ]);
            break;
            
        case 'remove':
            // Remove product from wishlist
            $productId = $_POST['product_id'] ?? null;
            
            if (!$productId) {
                echo json_encode(['success' => false, 'error' => 'Product ID required']);
                exit;
            }
            
            $sql = "DELETE FROM shop_wishlist WHERE user_id = ? AND product_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userId, $productId]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Product removed from wishlist'
            ]);
            break;
            
        case 'toggle':
            // Toggle product in wishlist (add if not exists, remove if exists)
            $productId = $_POST['product_id'] ?? null;
            $clinicId = $_POST['clinic_id'] ?? null;
            
            if (!$productId || !$clinicId) {
                echo json_encode(['success' => false, 'error' => 'Product ID and Clinic ID required']);
                exit;
            }
            
            // Check if already in wishlist
            $stmt = $pdo->prepare("SELECT id FROM shop_wishlist WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$userId, $productId]);
            $exists = $stmt->fetch();
            
            if ($exists) {
                // Remove from wishlist
                $stmt = $pdo->prepare("DELETE FROM shop_wishlist WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$userId, $productId]);
                
                echo json_encode([
                    'success' => true,
                    'action' => 'removed',
                    'in_wishlist' => false,
                    'message' => 'Removed from wishlist'
                ]);
            } else {
                // Add to wishlist
                $stmt = $pdo->prepare("INSERT INTO shop_wishlist (user_id, product_id, clinic_id) VALUES (?, ?, ?)");
                $stmt->execute([$userId, $productId, $clinicId]);
                
                echo json_encode([
                    'success' => true,
                    'action' => 'added',
                    'in_wishlist' => true,
                    'message' => 'Added to wishlist'
                ]);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
            break;
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
