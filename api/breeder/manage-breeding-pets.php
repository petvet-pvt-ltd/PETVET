<?php
header('Content-Type: application/json');
require_once dirname(__DIR__, 2) . '/config/connect.php';
require_once dirname(__DIR__, 2) . '/config/ImageUploader.php';

session_start();

// Check if user is logged in and is a breeder
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_all':
            getAllBreedingPets($conn, $userId);
            break;
        
        case 'add':
            addBreedingPet($conn, $userId);
            break;
        
        case 'update':
            updateBreedingPet($conn, $userId);
            break;
        
        case 'delete':
            deleteBreedingPet($conn, $userId);
            break;
        
        case 'toggle_status':
            togglePetStatus($conn, $userId);
            break;
        
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

function getAllBreedingPets($conn, $userId) {
    $stmt = $conn->prepare("
        SELECT id, name, breed, gender, date_of_birth as dob, 
               photo, description, is_active,
               TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) as age
        FROM breeder_pets
        WHERE breeder_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $pets = [];
    while ($row = $result->fetch_assoc()) {
        $row['is_active'] = (bool)$row['is_active'];
        $row['age'] = $row['age'] . ' ' . ($row['age'] == 1 ? 'year' : 'years');
        $pets[] = $row;
    }
    
    echo json_encode(['success' => true, 'pets' => $pets]);
}

function addBreedingPet($conn, $userId) {
    $name = $_POST['name'] ?? '';
    $breed = $_POST['breed'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $description = $_POST['description'] ?? '';
    // Handle checkbox - it's checked if the field exists in POST
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    
    // Validate required fields
    if (empty($name) || empty($breed) || empty($gender) || empty($dob)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        return;
    }
    
    // Handle photo upload
    $photoPath = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = dirname(__DIR__, 2) . '/uploads/breeder_pets/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Create custom uploader for breeder pets directory
        $uploader = new ImageUploader($uploadDir);
        $uploadResult = $uploader->upload($_FILES['photo'], 'breeder_pet_');
        
        if ($uploadResult['success']) {
            // Construct the full web path
            $photoPath = '/PETVET/uploads/breeder_pets/' . $uploadResult['path'];
        }
    }
    
    $stmt = $conn->prepare("
        INSERT INTO breeder_pets (breeder_id, name, breed, gender, date_of_birth, photo, description, is_active)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("issssssi", $userId, $name, $breed, $gender, $dob, $photoPath, $description, $isActive);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Pet added successfully',
            'pet_id' => $conn->insert_id
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to add pet: ' . $stmt->error]);
    }
}

function updateBreedingPet($conn, $userId) {
    $petId = $_POST['pet_id'] ?? 0;
    $name = $_POST['name'] ?? '';
    $breed = $_POST['breed'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $description = $_POST['description'] ?? '';
    // Handle checkbox - it's checked if the field exists in POST
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    
    // Validate required fields
    if (empty($petId) || empty($name) || empty($breed) || empty($gender) || empty($dob)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        return;
    }
    
    // Check if pet belongs to this breeder
    $checkStmt = $conn->prepare("SELECT id FROM breeder_pets WHERE id = ? AND breeder_id = ?");
    $checkStmt->bind_param("ii", $petId, $userId);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows === 0) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
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
        
        // Create custom uploader for breeder pets directory
        $uploader = new ImageUploader($uploadDir);
        $uploadResult = $uploader->upload($_FILES['photo'], 'breeder_pet_');
        
        if ($uploadResult['success']) {
            // Construct the full web path
            $photoPath = '/PETVET/uploads/breeder_pets/' . $uploadResult['path'];
            $updatePhoto = true;
        }
    }
    
    // Build update query
    if ($updatePhoto) {
        $stmt = $conn->prepare("
            UPDATE breeder_pets 
            SET name = ?, breed = ?, gender = ?, date_of_birth = ?, photo = ?, description = ?, is_active = ?
            WHERE id = ? AND breeder_id = ?
        ");
        $stmt->bind_param("sssssssii", $name, $breed, $gender, $dob, $photoPath, $description, $isActive, $petId, $userId);
    } else {
        $stmt = $conn->prepare("
            UPDATE breeder_pets 
            SET name = ?, breed = ?, gender = ?, date_of_birth = ?, description = ?, is_active = ?
            WHERE id = ? AND breeder_id = ?
        ");
        $stmt->bind_param("sssssiii", $name, $breed, $gender, $dob, $description, $isActive, $petId, $userId);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Pet updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update pet: ' . $stmt->error]);
    }
}

function deleteBreedingPet($conn, $userId) {
    $petId = $_POST['pet_id'] ?? $_GET['pet_id'] ?? 0;
    
    if (empty($petId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Pet ID is required']);
        return;
    }
    
    // Check if pet belongs to this breeder
    $checkStmt = $conn->prepare("SELECT id FROM breeder_pets WHERE id = ? AND breeder_id = ?");
    $checkStmt->bind_param("ii", $petId, $userId);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows === 0) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }
    
    $stmt = $conn->prepare("DELETE FROM breeder_pets WHERE id = ? AND breeder_id = ?");
    $stmt->bind_param("ii", $petId, $userId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Pet deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete pet']);
    }
}

function togglePetStatus($conn, $userId) {
    $petId = $_POST['pet_id'] ?? 0;
    $isActive = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 0;
    
    if (empty($petId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Pet ID is required']);
        return;
    }
    
    // Check if pet belongs to this breeder
    $checkStmt = $conn->prepare("SELECT id FROM breeder_pets WHERE id = ? AND breeder_id = ?");
    $checkStmt->bind_param("ii", $petId, $userId);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows === 0) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }
    
    $stmt = $conn->prepare("UPDATE breeder_pets SET is_active = ? WHERE id = ? AND breeder_id = ?");
    $stmt->bind_param("iii", $isActive, $petId, $userId);
    
    if ($stmt->execute()) {
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
