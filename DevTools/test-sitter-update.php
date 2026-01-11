<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/auth_helper.php';

// Simulate logged in user
$_SESSION['user_id'] = 5; // multirole user

$user_id = currentUserId();
echo "User ID: $user_id\n\n";

// Simulate POST data
$data = [
    'sitter' => [
        'service_area' => 'Colombo',
        'experience_years' => '3',
        'bio' => 'Test bio',
        'pet_types' => ['dogs', 'cats'],
        'home_type' => 'house_with_yard',
        'phone_primary' => '0771234567',
        'phone_secondary' => ''
    ]
];

echo "Input data:\n";
print_r($data);

$s = $data['sitter'];

// Convert empty strings to null
$s['experience_years'] = empty($s['experience_years']) ? null : $s['experience_years'];

$pet_types_json = !empty($s['pet_types']) ? json_encode($s['pet_types']) : null;

echo "\nProcessed data:\n";
echo "service_area: " . var_export($s['service_area'], true) . "\n";
echo "experience_years: " . var_export($s['experience_years'], true) . "\n";
echo "bio: " . var_export($s['bio'], true) . "\n";
echo "pet_types_json: " . var_export($pet_types_json, true) . "\n";
echo "home_type: " . var_export($s['home_type'], true) . "\n";
echo "phone_primary: " . var_export($s['phone_primary'], true) . "\n";
echo "phone_secondary: " . var_export($s['phone_secondary'], true) . "\n";

$pdo = db();

try {
    $stmt = $pdo->prepare("
        UPDATE service_provider_profiles 
        SET service_area = ?, experience_years = ?, bio = ?,
            pet_types = ?, home_type = ?,
            phone_primary = ?, phone_secondary = ?,
            updated_at = CURRENT_TIMESTAMP
        WHERE user_id = ? AND role_type = 'sitter'
    ");
    
    echo "\nExecuting UPDATE...\n";
    $result = $stmt->execute([
        $s['service_area'], $s['experience_years'], $s['bio'],
        $pet_types_json, $s['home_type'],
        $s['phone_primary'], $s['phone_secondary'],
        $user_id
    ]);
    
    echo "Success! Rows affected: " . $stmt->rowCount() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
