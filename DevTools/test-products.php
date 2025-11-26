<?php
require_once __DIR__ . '/models/ProductModel.php';
require_once __DIR__ . '/models/Guest/GuestShopModel.php';

echo "<h2>Testing Product Database Integration</h2>";

// Test ProductModel
echo "<h3>1. ProductModel - Get All Products:</h3>";
$productModel = new ProductModel();
$products = $productModel->getAllProducts();

echo "<pre>";
echo "Found " . count($products) . " products\n\n";
foreach ($products as $product) {
    echo "ID: {$product['id']}\n";
    echo "Name: {$product['name']}\n";
    echo "Price: {$product['price']}\n";
    echo "Category: {$product['category']}\n";
    echo "Stock: {$product['stock']}\n";
    echo "Active: " . ($product['is_active'] ? 'Yes' : 'No') . "\n";
    echo "---\n";
}
echo "</pre>";

// Test GuestShopModel
echo "<h3>2. GuestShopModel - Get All Products:</h3>";
$guestShopModel = new GuestShopModel();
$guestProducts = $guestShopModel->getAllProducts();

echo "<pre>";
echo "Found " . count($guestProducts) . " products\n\n";
foreach ($guestProducts as $product) {
    echo "ID: {$product['id']}\n";
    echo "Name: {$product['name']}\n";
    echo "Price: {$product['price']}\n";
    echo "Category: {$product['category']}\n";
    echo "Stock: {$product['stock']}\n";
    echo "---\n";
}
echo "</pre>";

// Test categories
echo "<h3>3. Categories:</h3>";
$categories = $guestShopModel->getCategories();
echo "<pre>";
print_r($categories);
echo "</pre>";

echo "<p><strong>✅ If you see 8 products above, the database integration is working!</strong></p>";
?>
