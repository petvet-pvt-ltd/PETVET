<?php
session_start();
$_SESSION['user_id'] = 2;
$_SESSION['user_name'] = 'John Doe';

require_once __DIR__ . '/../models/PetOwner/SettingsModel.php';

$settingsModel = new SettingsModel();
$userId = 2;

echo "========== Testing Settings Data Loading ==========\n\n";

echo "1. User Profile:\n";
$profile = $settingsModel->getUserProfile($userId);
if ($profile) {
    print_r($profile);
} else {
    echo "Failed to load profile\n";
}

echo "\n2. User Preferences:\n";
$prefs = $settingsModel->getUserPreferences($userId);
print_r($prefs);

echo "\n3. Account Stats:\n";
$stats = $settingsModel->getAccountStats($userId);
print_r($stats);

echo "\n========== Test Complete ==========\n";
