<?php
session_start();
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../models/ProductModel.php';

// Prevent any output before JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

// Check if user is logged in and is clinic manager
$userRole = $_SESSION['current_role'] ?? $_SESSION['role'] ?? null;
if (!isset($_SESSION['user_id']) || $userRole !== 'clinic_manager') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $productId = intval($_POST['id'] ?? 0);
    
    if ($productId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
        exit;
    }
    
    $productModel = new ProductModel();
    
    // Get product to check for image
    $product = $productModel->getProductById($productId);
    
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }
    
    // Permanently delete product from database
    $success = $productModel->permanentlyDeleteProduct($productId);
    
    if ($success) {
        // Delete associated image file if it's stored locally
        if ($product['image_url'] && 
            strpos($product['image_url'], '/PETVET/public/images/products/') === 0) {
            $imagePath = __DIR__ . '/../..' . str_replace('/PETVET/', '/', $product['image_url']);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Product permanently deleted'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to delete product'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Permanent delete product error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred while deleting the product'
    ]);
}
?>
