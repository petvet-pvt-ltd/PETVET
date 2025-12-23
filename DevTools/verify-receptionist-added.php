<?php
require_once '../config/connect.php';

try {
    $db = db();
    
    echo "=== Checking Akila Receptionist in clinic_staff ===\n";
    $stmt = $db->query("SELECT * FROM clinic_staff WHERE user_id = 30027");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($result);
    
    if (empty($result)) {
        echo "\nNOT FOUND - Adding now...\n";
        
        $stmt = $db->prepare("
            INSERT INTO clinic_staff (user_id, clinic_id, name, role, email, phone, status) 
            VALUES (30027, 3, 'Akila Receptionist', 'Receptionist', 'akilareceptionist@gmail.com', '0775983002', 'Active')
        ");
        $stmt->execute();
        echo "✓ Added successfully\n";
        
        // Verify again
        $stmt = $db->query("SELECT * FROM clinic_staff WHERE user_id = 30027");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        print_r($result);
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
