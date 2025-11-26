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
    
    // Get existing product
    $productModel = new ProductModel();
    $existingProduct = $productModel->getProductById($productId);
    
    if (!$existingProduct) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }
    
    // Get current images
    $currentImages = $productModel->getProductImages($productId);
    
    // Handle deleted images
    $deletedImages = json_decode($_POST['deleted_images'] ?? '[]', true);
    if (!empty($deletedImages)) {
        foreach ($currentImages as $img) {
            if (in_array($img['image_url'], $deletedImages)) {
                // Delete from database (returns image URL)
                $imageUrl = $productModel->deleteProductImage($img['id']);
                if ($imageUrl && strpos($imageUrl, '/PETVET/public/images/products/') === 0) {
                    // Delete file from server
                    $filePath = __DIR__ . '/../..' . str_replace('/PETVET/', '/', $imageUrl);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }
        }
    }
    
    // Get remaining images count
    $remainingImages = $productModel->getProductImages($productId);
    $remainingCount = count($remainingImages);
    
    // Handle new image uploads
    $newImagePaths = [];
    if (isset($_FILES['product_images']) && is_array($_FILES['product_images']['tmp_name'])) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        $uploadDir = __DIR__ . '/../../public/images/products/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Filter out empty uploads (count only actual files)
        $actualFiles = array_filter($_FILES['product_images']['tmp_name'], function($tmpName) {
            return !empty($tmpName) && is_uploaded_file($tmpName);
        });
        
        $fileCount = count($actualFiles);
        
        // Only check limit if there are actual files to upload
        if ($fileCount > 0 && $remainingCount + $fileCount > 5) {
            echo json_encode(['success' => false, 'message' => "Cannot add $fileCount image(s). Maximum 5 images total. You have $remainingCount image(s) currently."]);
            exit;
        }
        
        for ($i = 0; $i < $fileCount; $i++) {
            if ($_FILES['product_images']['error'][$i] === UPLOAD_ERR_OK) {
                $fileType = $_FILES['product_images']['type'][$i];
                $fileSize = $_FILES['product_images']['size'][$i];
                $tmpName = $_FILES['product_images']['tmp_name'][$i];
                $originalName = $_FILES['product_images']['name'][$i];
                
                // Validate
                if (!in_array($fileType, $allowedTypes)) {
                    echo json_encode(['success' => false, 'message' => 'Invalid image type for file ' . ($i+1)]);
                    exit;
                }
                
                if ($fileSize > $maxFileSize) {
                    echo json_encode(['success' => false, 'message' => 'Image ' . ($i+1) . ' size must be less than 5MB']);
                    exit;
                }
                
                // Upload
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                $filename = 'product_' . time() . '_' . uniqid() . '_' . $i . '.' . $extension;
                $uploadPath = $uploadDir . $filename;
                
                if (move_uploaded_file($tmpName, $uploadPath)) {
                    $imagePath = '/PETVET/public/images/products/' . $filename;
                    $newImagePaths[] = $imagePath;
                    
                    // Add to database
                    $displayOrder = $remainingCount + count($newImagePaths);
                    $productModel->addProductImage($productId, $imagePath, $displayOrder);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to upload image ' . ($i+1)]);
                    exit;
                }
            }
        }
    }
    
    // Update product basic info
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'price' => floatval($_POST['price'] ?? 0),
        'category' => trim($_POST['category'] ?? ''),
        'image_url' => $existingProduct['image_url'], // Keep current primary image
        'stock' => intval($_POST['stock'] ?? 0),
        'seller' => trim($_POST['seller'] ?? 'PetVet Store'),
        'is_active' => isset($_POST['is_active']) ? (bool)$_POST['is_active'] : true
    ];
    
    // Update primary image to first available image
    $allImages = $productModel->getProductImages($productId);
    if (!empty($allImages)) {
        $data['image_url'] = $allImages[0]['image_url'];
    }
    
    // Validate
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
    
    // Update product
    $success = $productModel->updateProduct($productId, $data);
    
    if ($success) {
        $finalImages = $productModel->getProductImages($productId);
        echo json_encode([
            'success' => true, 
            'message' => 'Product updated successfully',
            'images_deleted' => count($deletedImages),
            'images_added' => count($newImagePaths),
            'total_images' => count($finalImages)
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to update product'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Update product error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
