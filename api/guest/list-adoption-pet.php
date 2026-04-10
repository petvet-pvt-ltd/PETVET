<?php
session_start();

// Set header first before any output
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../models/SellPetListingModel.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
    }

    $model = new SellPetListingModel();
    
    // Validate required fields
    $required = ['name', 'species', 'breed', 'age', 'gender', 'location', 'phone'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
            exit;
        }
    }
    
    // Prepare data
    $latitude = null;
    $longitude = null;
    
    if (!empty($_POST['latitude']) && !empty($_POST['longitude'])) {
        $latitude = floatval($_POST['latitude']);
        $longitude = floatval($_POST['longitude']);
    }
    
    // Use current user ID if logged in, otherwise NULL for guests
    $user_id = $_SESSION['user_id'] ?? null;
    
    // For guest submissions (no user_id), email is required for contact
    if (is_null($user_id) && empty($_POST['email'])) {
        echo json_encode(['success' => false, 'message' => 'Please provide an email address so people can contact you about this pet']);
        exit;
    }
    
    $data = [
        'user_id' => $user_id,
        'name' => trim($_POST['name']),
        'species' => trim($_POST['species']),
        'breed' => trim($_POST['breed']),
        'age' => trim($_POST['age']),
        'gender' => $_POST['gender'],
        'price' => 0, // Free adoption
        'listing_type' => 'adoption',
        'location' => trim($_POST['location']),
        'description' => trim($_POST['desc'] ?? ''),
        'phone' => trim($_POST['phone']),
        'phone2' => trim($_POST['phone2'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'latitude' => $latitude,
        'longitude' => $longitude
    ];
    
    // Create listing
    $listingId = $model->createListing($data);
    
    if (!$listingId) {
        $error = mysqli_error($GLOBALS['conn'] ?? null);
        error_log("Failed to create adoption listing in database: " . $error);
        echo json_encode(['success' => false, 'message' => 'Failed to create listing in database', 'error' => $error]);
        exit;
    }
    
    // Handle image uploads
    if (isset($_FILES['images']) && is_array($_FILES['images']['tmp_name'])) {
        $uploadDir = __DIR__ . '/../../public/images/uploads/pet-listings/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $maxFiles = 3;
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        $uploadedCount = 0;
        foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {
            if ($uploadedCount >= $maxFiles) break;
            
            if (!empty($tmpName) && $_FILES['images']['error'][$index] === UPLOAD_ERR_OK) {
                $fileSize = $_FILES['images']['size'][$index];
                $fileType = $_FILES['images']['type'][$index];
                
                if (!in_array($fileType, $allowedTypes)) {
                    error_log("Skipping invalid file type: $fileType");
                    continue;
                }
                
                if ($fileSize > $maxSize) {
                    error_log("Skipping file too large: $fileSize bytes");
                    continue;
                }
                
                $extension = pathinfo($_FILES['images']['name'][$index], PATHINFO_EXTENSION);
                $filename = 'pet_' . $listingId . '_' . time() . '_' . $index . '.' . $extension;
                $filepath = $uploadDir . $filename;
                
                if (move_uploaded_file($tmpName, $filepath)) {
                    $imageUrl = '/PETVET/public/images/uploads/pet-listings/' . $filename;
                    $model->addImage($listingId, $imageUrl, $uploadedCount);
                    $uploadedCount++;
                } else {
                    error_log("Failed to move uploaded file to: $filepath");
                }
            }
        }
        
        if ($uploadedCount === 0) {
            error_log("No images were uploaded successfully for listing $listingId");
        }
    }
    
    // Handle badges
    if (isset($_POST['badges']) && is_array($_POST['badges'])) {
        foreach ($_POST['badges'] as $badge) {
            $model->addBadge($listingId, trim($badge));
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Pet listed successfully! It will be visible after admin approval.',
        'listing_id' => $listingId
    ]);
    
} catch (Exception $e) {
    error_log("Error creating adoption listing: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred while creating the listing',
        'error' => $e->getMessage()
    ]);
}
?>
