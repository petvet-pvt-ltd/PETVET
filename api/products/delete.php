<?php
session_start();
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../models/ProductModel.php';

header('Content-Type: application/json');

// Check if user is logged in and is clinic manager
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'clinic_manager') {
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
    
    // Soft delete product (set is_active = false)
    $productModel = new ProductModel();
    $success = $productModel->deleteProduct($productId);
    
    if ($success) {
        echo json_encode([
            'success' => true, 
            'message' => 'Product deleted successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to delete product'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Delete product error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred while deleting the product'
    ]);
}
?>
