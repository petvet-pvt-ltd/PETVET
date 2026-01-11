<?php
/**
 * Test script to verify receptionists are showing from user_roles table
 */

require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../models/ClinicManager/StaffModel.php';

try {
    $staffModel = new StaffModel();
    
    echo "=== Testing StaffModel->all() for Clinic ID 1 ===\n\n";
    $allStaff = $staffModel->all(1);
    
    echo "Total staff members: " . count($allStaff) . "\n\n";
    
    foreach ($allStaff as $staff) {
        echo "ID: " . $staff['id'] . "\n";
        echo "Name: " . $staff['name'] . "\n";
        echo "Role: " . $staff['role'] . "\n";
        echo "Email: " . $staff['email'] . "\n";
        echo "Phone: " . $staff['phone'] . "\n";
        echo "Status: " . $staff['status'] . "\n";
        echo "Source: " . $staff['source'] . "\n";
        echo "---\n\n";
    }
    
    echo "\n=== Filtering Receptionists Only ===\n\n";
    $receptionists = array_filter($allStaff, function($s) {
        return $s['role'] === 'Receptionist';
    });
    
    echo "Total receptionists: " . count($receptionists) . "\n\n";
    
    foreach ($receptionists as $r) {
        echo "Name: " . $r['name'] . " (" . $r['email'] . ") - Source: " . $r['source'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
