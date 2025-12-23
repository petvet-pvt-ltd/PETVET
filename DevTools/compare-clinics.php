<?php
require_once '../config/connect.php';

try {
    $db = db();
    
    // Check Happy Paws clinic structure
    echo "=== Happy Paws Vet Clinic ===\n";
    $stmt = $db->query("SELECT * FROM clinics WHERE clinic_name LIKE '%Happy Paws%'");
    $happyPaws = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($happyPaws);
    
    echo "\n=== Akila Veterinary Clinic ===\n";
    $stmt = $db->query("SELECT * FROM clinics WHERE clinic_name LIKE '%Akila%'");
    $akila = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($akila);
    
    // Check if there's a clinic_manager or receptionist table linking users to clinics
    echo "\n=== Checking for clinic relationship tables ===\n";
    $tables = $db->query("SHOW TABLES LIKE '%clinic%'")->fetchAll(PDO::FETCH_COLUMN);
    print_r($tables);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
