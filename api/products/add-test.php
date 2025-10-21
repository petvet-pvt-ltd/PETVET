<?php
// TEST VERSION - NO AUTHENTICATION CHECK
// Use this for testing only!

session_start();

require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../models/ProductModel.php';

header('Content-Type: application/json');

// DEBUG: Log session info
error_log("TEST API - Session: " . json_encode([
    'user_id' => $_SESSION['user_id'] ?? 'not set',
    'role' => $_SESSION['role'] ?? 'not set',
    'session_id' => session_id()
]));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Handle image upload
    $imagePath = null;
    
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        
        $fileType = $_FILES['product_image']['type'];
        $fileSize = $_FILES['product_image']['size'];
        $tmpName = $_FILES['product_image']['tmp_name'];
        
        // Validate file type
        if (!in_array($fileType, $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Invalid image type. Only JPG, PNG, GIF, and WebP are allowed']);
            exit;
        }
        
        // Validate file size
        if ($fileSize > $maxFileSize) {
            echo json_encode(['success' => false, 'message' => 'Image size must be less than 5MB']);
            exit;
        }
        
        // Generate unique filename
        $extension = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
        $filename = 'product_' . time() . '_' . uniqid() . '.' . $extension;
        $uploadDir = __DIR__ . '/../../public/images/products/';
        $uploadPath = $uploadDir . $filename;
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Move uploaded file
        if (move_uploaded_file($tmpName, $uploadPath)) {
            $imagePath = '/PETVET/public/images/products/' . $filename;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
            exit;
        }
    } elseif (!empty($_POST['image_url'])) {
        // Allow external URL if no file uploaded
        $imagePath = trim($_POST['image_url']);
    }
    
    // Get POST data
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'price' => floatval($_POST['price'] ?? 0),
        'category' => trim($_POST['category'] ?? ''),
        'image_url' => $imagePath,
        'stock' => intval($_POST['stock'] ?? 0),
        'seller' => trim($_POST['seller'] ?? 'PetVet Store'),
        'is_active' => isset($_POST['is_active']) ? (bool)$_POST['is_active'] : true
    ];
    
    // Validate required fields
    if (empty($data['name'])) {
        echo json_encode(['success' => false, 'message' => 'Product name is required']);
        exit;
    }
    
    if ($data['price'] <= 0) {
        echo json_encode(['success' => false, 'message' => 'Price must be greater than zero']);
        exit;
    }
    
    if (empty($data['category'])) {
        echo json_encode(['success' => false, 'message' => 'Category is required']);
        exit;
    }
    
    // Create product
    $productModel = new ProductModel();
    $success = $productModel->createProduct($data);
    
    if ($success) {
        echo json_encode([
            'success' => true, 
            'message' => 'Product added successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to add product'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Add product error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred while adding the product: ' . $e->getMessage()
    ]);
}
?>
