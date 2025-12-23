<?php
require_once '../config/connect.php';

try {
    $db = db();
    
    // Check Peter Parker's current status
    echo "=== Checking Peter Parker Account ===\n";
    $stmt = $db->prepare("SELECT id, email, first_name, last_name, is_active, email_verified FROM users WHERE email = 'pokerpeter474@gmail.com'");
    $stmt->execute();
    $peter = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($peter);
    
    // Update to verified and active
    $stmt = $db->prepare("UPDATE users SET is_active = 1, email_verified = 1 WHERE email = 'pokerpeter474@gmail.com'");
    $stmt->execute();
    echo "\n✓ Peter Parker account activated and verified\n\n";
    
    // Check Akila Receptionist status
    echo "=== Checking Akila Receptionist Account ===\n";
    $stmt = $db->prepare("SELECT id, email, first_name, last_name, is_active, email_verified FROM users WHERE email = 'akilareceptionist@gmail.com'");
    $stmt->execute();
    $recep = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($recep);
    
    // Update to verified and active
    $stmt = $db->prepare("UPDATE users SET is_active = 1, email_verified = 1 WHERE email = 'akilareceptionist@gmail.com'");
    $stmt->execute();
    echo "\n✓ Akila Receptionist account activated and verified\n\n";
    
    echo "=== Both accounts are now active and verified ===\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
