<?php
require_once '../config/connect.php';

try {
    $db = db();
    
    // Check if ho@gmail.com still exists
    echo "=== Checking ho@gmail.com account ===\n\n";
    
    $stmt = $db->prepare("SELECT id, email, first_name, last_name, is_active FROM users WHERE email = 'ho@gmail.com'");
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "❌ Account still exists:\n";
        print_r($user);
        
        echo "\n=== Checking user_roles ===\n";
        $stmt = $db->prepare("SELECT * FROM user_roles WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        print_r($roles);
        
        echo "\n=== Checking clinic_staff ===\n";
        $stmt = $db->prepare("SELECT * FROM clinic_staff WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $staff = $stmt->fetchAll(PDO::FETCH_ASSOC);
        print_r($staff);
        
    } else {
        echo "✓ Account has been deleted successfully\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
