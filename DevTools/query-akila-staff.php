<?php
require_once '../config/connect.php';

try {
    $db = db();
    
    // First get Akila clinic ID
    $stmt = $db->prepare("SELECT id, clinic_name, clinic_email FROM clinics WHERE clinic_name LIKE '%Akila%'");
    $stmt->execute();
    $clinic = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "=== Akila Veterinary Clinic ===\n";
    echo "Clinic ID: " . $clinic['id'] . "\n";
    echo "Clinic Name: " . $clinic['clinic_name'] . "\n";
    echo "Clinic Email: " . $clinic['clinic_email'] . "\n\n";
    
    // Get ALL users with their roles to find clinic staff
    $stmt = $db->prepare("
        SELECT u.id, CONCAT(u.first_name, ' ', u.last_name) as full_name, 
               u.email, u.phone, r.role_name
        FROM users u 
        JOIN user_roles ur ON u.id = ur.user_id 
        JOIN roles r ON ur.role_id = r.id 
        WHERE r.role_name IN ('clinic_manager', 'receptionist') 
        ORDER BY r.role_name, u.first_name
    ");
    $stmt->execute();
    $allStaff = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "=== All Clinic Managers and Receptionists ===\n\n";
    foreach ($allStaff as $person) {
        // Highlight if email matches or similar to clinic email
        $highlight = (strpos($person['email'], 'gklnkler') !== false) ? " ← AKILA CLINIC" : "";
        
        echo "Role: " . strtoupper(str_replace('_', ' ', $person['role_name'])) . $highlight . "\n";
        echo "ID: " . $person['id'] . "\n";
        echo "Name: " . $person['full_name'] . "\n";
        echo "Email: " . $person['email'] . "\n";
        echo "Phone: " . ($person['phone'] ?: 'N/A') . "\n";
        echo "---\n\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
