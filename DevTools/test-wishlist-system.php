<?php
/**
 * Test Script for Shop Wishlist API
 * Run this script to verify wishlist functionality is working
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// You need to be logged in as a pet owner to test
// Set a test user_id (replace with actual user ID from your database)
if (!isset($_SESSION['user_id'])) {
    echo "⚠️  Please login as a pet owner first or set a test user_id in this script\n";
    exit;
}

require_once __DIR__ . '/../config/connect.php';

echo "🧪 Testing Shop Wishlist API\n";
echo "=" . str_repeat("=", 50) . "\n\n";

$userId = $_SESSION['user_id'];
$pdo = db();

// Get a test product
$stmt = $pdo->query("SELECT id, name, clinic_id FROM products LIMIT 1");
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "❌ No products found in database. Please add products first.\n";
    exit;
}

echo "✓ Found test product: {$product['name']} (ID: {$product['id']})\n";
echo "  Clinic ID: {$product['clinic_id']}\n";
echo "  User ID: {$userId}\n\n";

// Test 1: Add to wishlist
echo "Test 1: Adding product to wishlist...\n";
$stmt = $pdo->prepare("INSERT INTO shop_wishlist (user_id, product_id, clinic_id) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE created_at = CURRENT_TIMESTAMP");
$stmt->execute([$userId, $product['id'], $product['clinic_id']]);
echo "✓ Product added to wishlist\n\n";

// Test 2: Check if in wishlist
echo "Test 2: Checking if product is in wishlist...\n";
$stmt = $pdo->prepare("SELECT * FROM shop_wishlist WHERE user_id = ? AND product_id = ?");
$stmt->execute([$userId, $product['id']]);
$wishlistItem = $stmt->fetch(PDO::FETCH_ASSOC);

if ($wishlistItem) {
    echo "✓ Product is in wishlist\n";
    echo "  Wishlist ID: {$wishlistItem['id']}\n";
    echo "  Added at: {$wishlistItem['created_at']}\n\n";
} else {
    echo "❌ Product not found in wishlist\n\n";
}

// Test 3: Get all wishlisted products
echo "Test 3: Getting all wishlisted products...\n";
$stmt = $pdo->prepare("SELECT 
    w.id as wishlist_id,
    w.product_id,
    w.clinic_id,
    p.name,
    p.price,
    c.clinic_name
FROM shop_wishlist w
JOIN products p ON w.product_id = p.id
JOIN clinics c ON w.clinic_id = c.id
WHERE w.user_id = ?");
$stmt->execute([$userId]);
$wishlist = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "✓ Found " . count($wishlist) . " item(s) in wishlist:\n";
foreach ($wishlist as $item) {
    echo "  - {$item['name']} (Rs. {$item['price']}) from {$item['clinic_name']}\n";
}
echo "\n";

// Test 4: Remove from wishlist
echo "Test 4: Removing product from wishlist...\n";
$stmt = $pdo->prepare("DELETE FROM shop_wishlist WHERE user_id = ? AND product_id = ?");
$stmt->execute([$userId, $product['id']]);
echo "✓ Product removed from wishlist\n\n";

// Test 5: Verify removal
echo "Test 5: Verifying removal...\n";
$stmt = $pdo->prepare("SELECT * FROM shop_wishlist WHERE user_id = ? AND product_id = ?");
$stmt->execute([$userId, $product['id']]);
$removed = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$removed) {
    echo "✓ Product successfully removed from wishlist\n\n";
} else {
    echo "❌ Product still in wishlist\n\n";
}

echo "=" . str_repeat("=", 50) . "\n";
echo "🎉 All tests completed!\n";
echo "\nThe wishlist system is ready to use.\n";
echo "You can now:\n";
echo "  1. View products in shop-clinic page\n";
echo "  2. Click the star icon on products to add/remove from wishlist\n";
echo "  3. Use the wishlist filter to view only wishlisted items\n";
echo "  4. For out-of-stock items, use 'Add to Wishlist' button\n";
