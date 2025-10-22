<?php
session_start();
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../models/SellPetListingModel.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $model = new SellPetListingModel();
    
    $listingId = intval($_POST['id'] ?? 0);
    if ($listingId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid listing ID']);
        exit;
    }
    
    // Verify ownership
    $listing = $model->getListingById($listingId);
    if (!$listing || $listing['user_id'] != $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'You do not have permission to edit this listing']);
        exit;
    }
    
    // Validate required fields
    $required = ['name', 'species', 'breed', 'age', 'gender', 'price', 'location', 'phone'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
            exit;
        }
    }
    
    // Prepare data
    $data = [
        'name' => trim($_POST['name']),
        'species' => trim($_POST['species']),
        'breed' => trim($_POST['breed']),
        'age' => trim($_POST['age']),
        'gender' => $_POST['gender'],
        'price' => floatval($_POST['price']),
        'location' => trim($_POST['location']),
        'description' => trim($_POST['desc'] ?? ''),
        'phone' => trim($_POST['phone']),
        'phone2' => trim($_POST['phone2'] ?? ''),
        'email' => trim($_POST['email'] ?? '')
    ];
    
    // Update listing
    if (!$model->updateListing($listingId, $data)) {
        echo json_encode(['success' => false, 'message' => 'Failed to update listing']);
        exit;
    }
    
    // Handle new image uploads if provided
    if (isset($_FILES['editImages']) && $_FILES['editImages']['error'][0] !== UPLOAD_ERR_NO_FILE) {
        $uploadDir = __DIR__ . '/../../public/images/uploads/pet-listings/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Get current image count
        $currentImages = $model->getImages($listingId);
        $currentCount = count($currentImages);
        $maxFiles = 3;
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        foreach ($_FILES['editImages']['tmp_name'] as $index => $tmpName) {
            if ($currentCount >= $maxFiles) break;
            
            if ($_FILES['editImages']['error'][$index] === UPLOAD_ERR_OK) {
                $fileSize = $_FILES['editImages']['size'][$index];
                $fileType = $_FILES['editImages']['type'][$index];
                
                if (!in_array($fileType, $allowedTypes) || $fileSize > $maxSize) {
                    continue;
                }
                
                $extension = pathinfo($_FILES['editImages']['name'][$index], PATHINFO_EXTENSION);
                $filename = 'pet_' . $listingId . '_' . time() . '_' . $index . '.' . $extension;
                $filepath = $uploadDir . $filename;
                
                if (move_uploaded_file($tmpName, $filepath)) {
                    $imageUrl = '/PETVET/public/images/uploads/pet-listings/' . $filename;
                    $model->addImage($listingId, $imageUrl, $currentCount);
                    $currentCount++;
                }
            }
        }
    }
    
    // Handle image deletion if provided
    if (isset($_POST['deletedImages'])) {
        $deletedImages = json_decode($_POST['deletedImages'], true);
        if (is_array($deletedImages)) {
            foreach ($deletedImages as $imageUrl) {
                // Delete file from server
                $filepath = __DIR__ . '/../../public' . str_replace('/PETVET/public', '', $imageUrl);
                if (file_exists($filepath)) {
                    unlink($filepath);
                }
            }
            // Refresh images in database
            $model->deleteImages($listingId);
            $existingImages = isset($_POST['existingImages']) ? json_decode($_POST['existingImages'], true) : [];
            if (is_array($existingImages)) {
                foreach ($existingImages as $idx => $imageUrl) {
                    $model->addImage($listingId, $imageUrl, $idx);
                }
            }
        }
    }
    
    // Update badges
    $model->deleteBadges($listingId);
    if (isset($_POST['badges']) && is_array($_POST['badges'])) {
        foreach ($_POST['badges'] as $badge) {
            $model->addBadge($listingId, trim($badge));
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Listing updated successfully'
    ]);
    
} catch (Exception $e) {
    error_log("Error updating pet listing: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while updating the listing']);
}
?>
