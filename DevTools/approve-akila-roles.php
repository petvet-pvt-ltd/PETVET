<?php
require_once '../config/connect.php';

try {
    $db = db();
    
    // Check Akila Receptionist's role status
    echo "=== Checking Akila Receptionist Role Status ===\n";
    $stmt = $db->prepare("
        SELECT ur.*, r.role_name 
        FROM user_roles ur 
        JOIN roles r ON ur.role_id = r.id 
        WHERE ur.user_id = (SELECT id FROM users WHERE email = 'akilareceptionist@gmail.com')
    ");
    $stmt->execute();
    $roleStatus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($roleStatus);
    
    // Update to approved
    $stmt = $db->prepare("
        UPDATE user_roles 
        SET verification_status = 'approved', is_active = 1 
        WHERE user_id = (SELECT id FROM users WHERE email = 'akilareceptionist@gmail.com')
    ");
    $stmt->execute();
    echo "\n✓ Akila Receptionist role approved and activated\n\n";
    
    // Also check Peter Parker
    echo "=== Checking Peter Parker Role Status ===\n";
    $stmt = $db->prepare("
        SELECT ur.*, r.role_name 
        FROM user_roles ur 
        JOIN roles r ON ur.role_id = r.id 
        WHERE ur.user_id = (SELECT id FROM users WHERE email = 'pokerpeter474@gmail.com')
    ");
    $stmt->execute();
    $peterRoles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($peterRoles);
    
    // Update Peter's role too
    $stmt = $db->prepare("
        UPDATE user_roles 
        SET verification_status = 'approved', is_active = 1 
        WHERE user_id = (SELECT id FROM users WHERE email = 'pokerpeter474@gmail.com')
    ");
    $stmt->execute();
    echo "\n✓ Peter Parker role approved and activated\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
