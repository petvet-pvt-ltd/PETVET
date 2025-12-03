<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../models/PetOwner/SettingsModel.php';
require_once __DIR__ . '/../../config/ImageUploader.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$userId = $_SESSION['user_id'];
$settingsModel = new SettingsModel();

// Get POST data
$data = $_POST;

// Validate required fields
if (empty($data['first_name']) || empty($data['last_name'])) {
    echo json_encode(['success' => false, 'message' => 'First name and last name are required']);
    exit;
}

// Handle avatar upload if provided
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    try {
        // Create avatars directory if it doesn't exist
        $avatarDir = __DIR__ . '/../../uploads/avatars/';
        if (!is_dir($avatarDir)) {
            mkdir($avatarDir, 0755, true);
        }
        
        $uploader = new ImageUploader($avatarDir);
        $uploadResult = $uploader->upload($_FILES['avatar'], 'avatar_');
        
        if ($uploadResult['success']) {
            // Update the path to point to the correct location
            $filename = basename($uploadResult['path']);
            $data['avatar'] = '/PETVET/uploads/avatars/' . $filename;
        } else {
            echo json_encode(['success' => false, 'message' => $uploadResult['message']]);
            exit;
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to upload avatar: ' . $e->getMessage()]);
        exit;
    }
}

// Update profile
$result = $settingsModel->updateUserProfile($userId, $data);

if ($result['success']) {
    // Update session data
    $_SESSION['user_name'] = $data['first_name'] . ' ' . $data['last_name'];
}

echo json_encode($result);
