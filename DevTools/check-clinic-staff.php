<?php
require_once '../config/connect.php';

try {
    $db = db();
    
    // Check clinic_staff table
    echo "=== Clinic Staff Records ===\n\n";
    $stmt = $db->query("SELECT cs.*, u.email, u.first_name, u.last_name, c.clinic_name 
                        FROM clinic_staff cs 
                        JOIN users u ON cs.user_id = u.id 
                        JOIN clinics c ON cs.clinic_id = c.id 
                        ORDER BY c.clinic_name, u.first_name");
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($records as $rec) {
        echo "Clinic: " . $rec['clinic_name'] . "\n";
        echo "User: " . $rec['first_name'] . " " . $rec['last_name'] . " (" . $rec['email'] . ")\n";
        echo "---\n";
    }
    
    // Check clinic_manager_profiles
    echo "\n=== Clinic Manager Profiles ===\n\n";
    $stmt = $db->query("DESCRIBE clinic_manager_profiles");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo $col['Field'] . " - " . $col['Type'] . "\n";
    }
    
    echo "\n=== Clinic Manager Profile Data ===\n";
    $stmt = $db->query("SELECT * FROM clinic_manager_profiles");
    $profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($profiles);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
