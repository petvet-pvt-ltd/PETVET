<?php
require_once '../config/connect.php';

try {
    $db = db();
    
    // Hash password123
    $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
    echo "Hashed password: " . $hashedPassword . "\n\n";
    
    // Update Peter Parker's password
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = 'pokerpeter474@gmail.com'");
    $stmt->execute([$hashedPassword]);
    echo "✓ Updated Peter Parker's password\n\n";
    
    // Create receptionist for Akila clinic
    $stmt = $db->prepare("
        INSERT INTO users (email, password, first_name, last_name, phone, is_active) 
        VALUES (?, ?, ?, ?, ?, 1)
    ");
    $stmt->execute([
        'akilareceptionist@gmail.com',
        $hashedPassword,
        'Akila',
        'Receptionist',
        '0775983002'
    ]);
    $newUserId = $db->lastInsertId();
    echo "✓ Created receptionist user (ID: $newUserId)\n";
    
    // Get receptionist role ID
    $stmt = $db->query("SELECT id FROM roles WHERE role_name = 'receptionist'");
    $receptionistRoleId = $stmt->fetchColumn();
    
    // Assign receptionist role
    $stmt = $db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
    $stmt->execute([$newUserId, $receptionistRoleId]);
    echo "✓ Assigned receptionist role\n\n";
    
    echo "=== Akila Clinic Receptionist Created ===\n";
    echo "Email: akilareceptionist@gmail.com\n";
    echo "Password: password123\n";
    echo "Phone: 0775983002\n";
    echo "User ID: $newUserId\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
