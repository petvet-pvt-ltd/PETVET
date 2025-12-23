<?php
require_once '../config/connect.php';

try {
    $db = db();
    
    // Get Akila clinic ID
    $akilaClinicId = 3;
    
    // Get Akila Receptionist user ID
    $stmt = $db->prepare("SELECT id FROM users WHERE email = 'akilareceptionist@gmail.com'");
    $stmt->execute();
    $receptionistId = $stmt->fetchColumn();
    
    // Get receptionist details
    $stmt = $db->prepare("SELECT first_name, last_name, email, phone FROM users WHERE id = ?");
    $stmt->execute([$receptionistId]);
    $receptionistData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Add to clinic_staff table
    $stmt = $db->prepare("
        INSERT INTO clinic_staff (user_id, clinic_id, name, role, email, phone, status) 
        VALUES (?, ?, ?, 'Receptionist', ?, ?, 'Active')
        ON DUPLICATE KEY UPDATE role = 'Receptionist', status = 'Active'
    ");
    $stmt->execute([
        $receptionistId, 
        $akilaClinicId, 
        $receptionistData['first_name'] . ' ' . $receptionistData['last_name'],
        $receptionistData['email'],
        $receptionistData['phone']
    ]);
    
    echo "✓ Added Akila Receptionist to clinic_staff table\n\n";
    
    // Verify
    echo "=== Akila Clinic Staff ===\n";
    $stmt = $db->prepare("
        SELECT name, role, email 
        FROM clinic_staff
        WHERE clinic_id = ?
    ");
    $stmt->execute([$akilaClinicId]);
    $staff = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($staff as $s) {
        echo $s['name'] . " - " . $s['role'] . " (" . $s['email'] . ")\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
