<?php
session_start();
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../models/ProductModel.php';

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
    // Handle multiple image uploads
    $imagePaths = [];
    
    if (isset($_FILES['product_images']) && is_array($_FILES['product_images']['tmp_name'])) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        $uploadDir = __DIR__ . '/../../public/images/products/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileCount = count($_FILES['product_images']['tmp_name']);
        if ($fileCount > 5) {
            echo json_encode(['success' => false, 'message' => 'Maximum 5 images allowed']);
            exit;
        }
        
        for ($i = 0; $i < $fileCount; $i++) {
            if ($_FILES['product_images']['error'][$i] === UPLOAD_ERR_OK) {
                $fileType = $_FILES['product_images']['type'][$i];
                $fileSize = $_FILES['product_images']['size'][$i];
                $tmpName = $_FILES['product_images']['tmp_name'][$i];
                $originalName = $_FILES['product_images']['name'][$i];
                
                // Validate file type
                if (!in_array($fileType, $allowedTypes)) {
                    echo json_encode(['success' => false, 'message' => 'Invalid image type for file ' . ($i+1)]);
                    exit;
                }
                
                // Validate file size
                if ($fileSize > $maxFileSize) {
                    echo json_encode(['success' => false, 'message' => 'Image ' . ($i+1) . ' size must be less than 5MB']);
                    exit;
                }
                
                // Generate unique filename
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                $filename = 'product_' . time() . '_' . uniqid() . '_' . $i . '.' . $extension;
                $uploadPath = $uploadDir . $filename;
                
                // Move uploaded file
                if (move_uploaded_file($tmpName, $uploadPath)) {
                    $imagePaths[] = '/PETVET/public/images/products/' . $filename;
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to upload image ' . ($i+1)]);
                    exit;
                }
            }
        }
    }
    
    // Get POST data
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'price' => floatval($_POST['price'] ?? 0),
        'category' => trim($_POST['category'] ?? ''),
        'image_url' => !empty($imagePaths) ? $imagePaths[0] : null, // First image as primary
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
        // Get the last inserted product ID
        $productId = $productModel->getLastInsertId();
        
        // Add all images to product_images table
        foreach ($imagePaths as $index => $imagePath) {
            $productModel->addProductImage($productId, $imagePath, $index);
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Product added successfully with ' . count($imagePaths) . ' image(s)',
            'product_id' => $productId
        ]);
    } else {
        // Clean up uploaded files if product creation failed
        foreach ($imagePaths as $imagePath) {
            $fullPath = __DIR__ . '/../../' . ltrim($imagePath, '/PETVET/');
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
        
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
