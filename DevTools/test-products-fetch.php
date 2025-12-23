<?php
require_once 'config/connect.php';

$clinicId = 1;

echo "Testing product fetch for clinic ID: $clinicId\n\n";

$pdo = db();

// Fetch clinic details
$stmt = $pdo->prepare("SELECT * FROM clinics WHERE id = ? AND is_active = 1 AND verification_status = 'approved'");
$stmt->execute([$clinicId]);
$clinic = $stmt->fetch(PDO::FETCH_ASSOC);

echo "Clinic found: " . ($clinic ? "YES" : "NO") . "\n";
if ($clinic) {
    echo "Clinic name: " . $clinic['clinic_name'] . "\n";
    echo "Clinic logo: " . $clinic['clinic_logo'] . "\n\n";
}

// Fetch products for this clinic
$stmt = $pdo->prepare("SELECT * FROM products WHERE clinic_id = ? AND is_active = 1 ORDER BY created_at DESC");
$stmt->execute([$clinicId]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Products found: " . count($products) . "\n\n";

foreach ($products as $product) {
    echo "Product ID: " . $product['id'] . "\n";
    echo "Name: " . $product['name'] . "\n";
    echo "Price: " . $product['price'] . "\n";
    echo "Image URL: " . $product['image_url'] . "\n";
    
    // Fetch images
    $imgStmt = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ? ORDER BY display_order");
    $imgStmt->execute([$product['id']]);
    $images = $imgStmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Images from product_images table: " . count($images) . "\n";
    foreach ($images as $img) {
        echo "  - " . $img . "\n";
    }
    echo "\n";
}
