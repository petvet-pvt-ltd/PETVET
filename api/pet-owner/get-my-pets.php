<?php
/**
 * API Endpoint: Get current user's pets
 * Returns list of active pets for the logged-in user
 */

session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../models/PetOwner/PetProfileModel.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get user info
$userId = $_SESSION['user_id'];
$userName = $_SESSION['name'] ?? '';

if (!$userId) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

try {
    $petModel = new PetProfileModel();
    $pets = $petModel->getUserPets($userId, false); // Only active pets
    
    echo json_encode([
        'success' => true,
        'owner_name' => $userName,
        'pets' => $pets
    ]);
} catch (Exception $e) {
    error_log("Error fetching pets: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch pets'
    ]);
}
