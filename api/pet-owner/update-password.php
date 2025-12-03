<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../models/PetOwner/SettingsModel.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$userId = $_SESSION['user_id'];
$settingsModel = new SettingsModel();

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    // Try to get form data instead
    $data = $_POST;
}

// Validate required fields
if (empty($data['current_password']) || empty($data['new_password']) || empty($data['confirm_password'])) {
    echo json_encode(['success' => false, 'message' => 'All password fields are required']);
    exit;
}

// Update password
$result = $settingsModel->changePassword(
    $userId,
    $data['current_password'],
    $data['new_password'],
    $data['confirm_password']
);

echo json_encode($result);
