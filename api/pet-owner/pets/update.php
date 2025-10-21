<?php
session_start();
require_once __DIR__ . '/../../../config/connect.php';
require_once __DIR__ . '/../../../models/PetOwner/PetProfileModel.php';

// Prevent any output before JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

// Check if user is logged in as pet owner
$userRole = $_SESSION['current_role'] ?? $_SESSION['role'] ?? null;
if (!isset($_SESSION['user_id']) || $userRole !== 'pet_owner') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Get pet ID
    $petId = intval($_POST['id'] ?? 0);
    
    if ($petId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid pet ID']);
        exit;
    }
    
    $petModel = new PetProfileModel();
    
    // Verify ownership
    $existingPet = $petModel->getPetById($petId, $_SESSION['user_id']);
    if (!$existingPet) {
        echo json_encode(['success' => false, 'message' => 'Pet not found or access denied']);
        exit;
    }
    
    // Handle image upload
    $photoUrl = $existingPet['photo_url']; // Keep existing photo by default
    
    if (isset($_FILES['pet_photo']) && $_FILES['pet_photo']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        $uploadDir = __DIR__ . '/../../../public/images/pets/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileType = $_FILES['pet_photo']['type'];
        $fileSize = $_FILES['pet_photo']['size'];
        $tmpName = $_FILES['pet_photo']['tmp_name'];
        $originalName = $_FILES['pet_photo']['name'];
        
        // Validate file type
        if (!in_array($fileType, $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Invalid image type. Please use JPG, PNG, GIF, or WebP']);
            exit;
        }
        
        // Validate file size
        if ($fileSize > $maxFileSize) {
            echo json_encode(['success' => false, 'message' => 'Image size must be less than 5MB']);
            exit;
        }
        
        // Delete old photo if it exists and is a local file
        if ($existingPet['photo_url'] && strpos($existingPet['photo_url'], '/PETVET/public/images/pets/') === 0) {
            $oldPhotoPath = __DIR__ . '/../../../' . ltrim($existingPet['photo_url'], '/PETVET/');
            if (file_exists($oldPhotoPath)) {
                unlink($oldPhotoPath);
            }
        }
        
        // Generate unique filename
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $filename = 'pet_' . $_SESSION['user_id'] . '_' . time() . '_' . uniqid() . '.' . $extension;
        $uploadPath = $uploadDir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($tmpName, $uploadPath)) {
            $photoUrl = '/PETVET/public/images/pets/' . $filename;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload photo']);
            exit;
        }
    }
    
    // Get POST data
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'species' => trim($_POST['species'] ?? ''),
        'breed' => trim($_POST['breed'] ?? null),
        'sex' => trim($_POST['sex'] ?? null),
        'date_of_birth' => !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null,
        'weight' => !empty($_POST['weight']) ? floatval($_POST['weight']) : null,
        'color' => trim($_POST['color'] ?? null),
        'allergies' => trim($_POST['allergies'] ?? null),
        'notes' => trim($_POST['notes'] ?? null),
        'photo_url' => $photoUrl,
        'is_active' => isset($_POST['is_active']) ? (bool)$_POST['is_active'] : true
    ];
    
    // Validate required fields
    if (empty($data['name'])) {
        echo json_encode(['success' => false, 'message' => 'Pet name is required']);
        exit;
    }
    
    if (empty($data['species'])) {
        echo json_encode(['success' => false, 'message' => 'Species is required']);
        exit;
    }
    
    // Update pet profile
    $success = $petModel->updatePet($petId, $data, $_SESSION['user_id']);
    
    if ($success) {
        echo json_encode([
            'success' => true, 
            'message' => 'Pet profile updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to update pet profile'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Update pet error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred while updating the pet: ' . $e->getMessage()
    ]);
}
?>
