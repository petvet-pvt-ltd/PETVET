<?php
require_once '../config/connect.php';

try {
    $db = db();
    
    $email = 'ho@gmail.com';
    
    // Get user ID
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $userId = $stmt->fetchColumn();
    
    if (!$userId) {
        echo "Account not found\n";
        exit;
    }
    
    echo "Deleting user ID: $userId\n\n";
    
    $db->beginTransaction();
    
    // Delete from user_roles
    $stmt = $db->prepare("DELETE FROM user_roles WHERE user_id = ?");
    $stmt->execute([$userId]);
    echo "✓ Deleted from user_roles\n";
    
    // Delete from favorite_clinics
    $stmt = $db->prepare("DELETE FROM favorite_clinics WHERE user_id = ?");
    $stmt->execute([$userId]);
    echo "✓ Deleted from favorite_clinics\n";
    
    // Delete from clinic_staff
    $stmt = $db->prepare("DELETE FROM clinic_staff WHERE user_id = ?");
    $stmt->execute([$userId]);
    echo "✓ Deleted from clinic_staff\n";
    
    // Delete from users
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    echo "✓ Deleted from users\n";
    
    $db->commit();
    
    echo "\n✓✓✓ Account completely deleted!\n";
    
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo "Error: " . $e->getMessage() . "\n";
}
