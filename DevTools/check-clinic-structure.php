<?php
require_once '../config/connect.php';

try {
    $db = db();
    
    // First check the clinics table structure
    echo "=== Clinics Table Structure ===\n";
    $stmt = $db->query("DESCRIBE clinics");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . "\n";
    }
    
    echo "\n=== Checking Akila Clinic ===\n";
    $stmt = $db->query("SELECT * FROM clinics WHERE clinic_name LIKE '%Akila%'");
    $clinic = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($clinic);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
