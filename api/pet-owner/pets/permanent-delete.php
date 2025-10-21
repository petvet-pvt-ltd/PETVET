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
    
    // Verify ownership and get pet data
    $existingPet = $petModel->getPetById($petId, $_SESSION['user_id']);
    if (!$existingPet) {
        echo json_encode(['success' => false, 'message' => 'Pet not found or access denied']);
        exit;
    }
    
    // Delete pet photo if it exists and is a local file
    if ($existingPet['photo_url'] && strpos($existingPet['photo_url'], '/PETVET/public/images/pets/') === 0) {
        $photoPath = __DIR__ . '/../../../' . ltrim($existingPet['photo_url'], '/PETVET/');
        if (file_exists($photoPath)) {
            unlink($photoPath);
        }
    }
    
    // Permanently delete the pet from database
    $success = $petModel->permanentlyDeletePet($petId, $_SESSION['user_id']);
    
    if ($success) {
        echo json_encode([
            'success' => true, 
            'message' => 'Pet profile permanently deleted'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to permanently delete pet profile'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Permanent delete pet error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred while permanently deleting the pet: ' . $e->getMessage()
    ]);
}
?>
