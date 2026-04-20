<?php
header('Content-Type: application/json');
require_once dirname(__DIR__, 2) . '/config/connect.php';
require_once dirname(__DIR__, 2) . '/config/ImageUploader.php';
require_once dirname(__DIR__, 2) . '/models/Breeder/PetsModel.php';

session_start();

// Check if user is logged in and is a breeder
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$model = new BreederPetsModel();

try {
    switch ($action) {
        case 'get_all':
            getAllBreedingPets($model, $userId);
            break;
        
        case 'add':
            addBreedingPet($model, $userId);
            break;
        
        case 'update':
            updateBreedingPet($model, $userId);
            break;
        
        case 'delete':
            deleteBreedingPet($model, $userId);
            break;
        
        case 'toggle_status':
            togglePetStatus($model, $userId);
            break;
        
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

function getAllBreedingPets($model, $userId) {
    $pets = $model->getBreedingPets($userId);
    echo json_encode(['success' => true, 'pets' => $pets]);
}

function addBreedingPet($model, $userId) {
    $name = $_POST['name'] ?? '';
    $breed = $_POST['breed'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $species = $_POST['species'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $reward = $_POST['reward'] ?? 0;
    $description = $_POST['description'] ?? '';
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    
    // Validate required fields
    if (empty($name) || empty($breed) || empty($gender) || empty($dob) || $reward === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        return;
    }
    
    // Calculate age from date of birth and validate
    $age = 0;
    try {
        $dobDate = new DateTime($dob);
        $today = new DateTime();
        $age = $today->diff($dobDate)->y;
        
        if ($age < 1) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Breeding pet must be at least 1 year old']);
            return;
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid date format: ' . $e->getMessage()]);
        return;
    }
    
    // Handle photo upload
    $photoPath = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = dirname(__DIR__, 2) . '/uploads/breeder_pets/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $uploader = new ImageUploader($uploadDir);
        $uploadResult = $uploader->upload($_FILES['photo'], 'breeder_pet_');
        
        if ($uploadResult['success']) {
            $photoPath = '/PETVET/uploads/breeder_pets/' . $uploadResult['path'];
        }
    }
    
    // Prepare data for model
    $data = [
        'name' => $name,
        'breed' => $breed,
        'gender' => $gender,
        'dob' => $dob,
        'age' => $age,
        'species' => $species,
        'photo' => $photoPath,
        'description' => $description,
        'reward' => $reward,
        'is_active' => $isActive
    ];
    
    $result = $model->addBreedingPet($userId, $data);
    
    if ($result['success']) {
        echo json_encode([
            'success' => true, 
            'message' => 'Pet added successfully',
            'pet_id' => $result['pet_id']
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to add pet: ' . $result['error']]);
    }
}

function updateBreedingPet($model, $userId) {
    $petId = $_POST['pet_id'] ?? 0;
    $name = $_POST['name'] ?? '';
    $breed = $_POST['breed'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $species = $_POST['species'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $reward = $_POST['reward'] ?? 0;
    $description = $_POST['description'] ?? '';
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    
    // Validate required fields
    if (empty($petId) || empty($name) || empty($breed) || empty($gender) || empty($dob) || $reward === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        return;
    }
    
    // Verify ownership
    if (!$model->verifyPetOwnership($petId, $userId)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }
    
    // Calculate age and validate
    $age = 0;
    try {
        $dobDate = new DateTime($dob);
        $today = new DateTime();
        $age = $today->diff($dobDate)->y;
        
        if ($age < 1) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Breeding pet must be at least 1 year old']);
            return;
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid date format: ' . $e->getMessage()]);
        return;
    }
    
    // Handle photo upload
    $photoPath = null;
    $updatePhoto = false;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = dirname(__DIR__, 2) . '/uploads/breeder_pets/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $uploader = new ImageUploader($uploadDir);
        $uploadResult = $uploader->upload($_FILES['photo'], 'breeder_pet_');
        
        if ($uploadResult['success']) {
            $photoPath = '/PETVET/uploads/breeder_pets/' . $uploadResult['path'];
            $updatePhoto = true;
        }
    }
    
    // Prepare data for model
    $data = [
        'name' => $name,
        'breed' => $breed,
        'gender' => $gender,
        'dob' => $dob,
        'age' => $age,
        'species' => $species,
        'photo' => $photoPath,
        'description' => $description,
        'reward' => $reward,
        'is_active' => $isActive
    ];
    
    // Call appropriate model method
    if ($updatePhoto) {
        $result = $model->updateBreedingPetWithPhoto($petId, $userId, $data);
    } else {
        $result = $model->updateBreedingPet($petId, $userId, $data);
    }
    
    if ($result['success']) {
        echo json_encode(['success' => true, 'message' => 'Pet updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update pet: ' . $result['error']]);
    }
}

function deleteBreedingPet($model, $userId) {
    $petId = $_POST['pet_id'] ?? $_GET['pet_id'] ?? 0;
    
    if (empty($petId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Pet ID is required']);
        return;
    }
    
    // Verify ownership
    if (!$model->verifyPetOwnership($petId, $userId)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }
    
    $result = $model->deleteBreedingPet($petId, $userId);
    
    if ($result['success']) {
        echo json_encode(['success' => true, 'message' => 'Pet deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete pet']);
    }
}

function togglePetStatus($model, $userId) {
    $petId = $_POST['pet_id'] ?? 0;
    $isActive = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 0;
    
    if (empty($petId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Pet ID is required']);
        return;
    }
    
    // Verify ownership
    if (!$model->verifyPetOwnership($petId, $userId)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }
    
    $result = $model->togglePetStatus($petId, $userId, $isActive);
    
    if ($result['success']) {
        echo json_encode([
            'success' => true, 
            'message' => 'Pet status updated successfully',
            'is_active' => (bool)$isActive
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }
}

