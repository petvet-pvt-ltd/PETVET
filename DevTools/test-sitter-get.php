<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/auth_helper.php';

// Simulate logged in user
$_SESSION['user_id'] = 5; // multirole user

$user_id = currentUserId();
echo "User ID: $user_id\n\n";

$pdo = db();

// Get user profile
$stmt = $pdo->prepare("SELECT first_name, last_name, email, phone, address, avatar FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

echo "Profile data:\n";
print_r($profile);

// Get sitter profile
$stmt = $pdo->prepare("
    SELECT service_area, experience_years, bio, pet_types, home_type,
           phone_primary, phone_secondary
    FROM service_provider_profiles
    WHERE user_id = ? AND role_type = 'sitter'
");
$stmt->execute([$user_id]);
$sitter = $stmt->fetch(PDO::FETCH_ASSOC);

echo "\nSitter data:\n";
print_r($sitter);
