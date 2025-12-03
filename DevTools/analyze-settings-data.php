<?php
/**
 * Analyze existing data for settings page
 */

require_once __DIR__ . '/../config/connect.php';

try {
    $pdo = db();
    
    echo "=== ANALYZING DATABASE FOR CLINIC MANAGER SETTINGS ===\n\n";
    
    // Check users table
    echo "1. USERS TABLE:\n";
    $users = $pdo->query("DESCRIBE users")->fetchAll();
    foreach ($users as $col) {
        echo "  - {$col['Field']} ({$col['Type']})\n";
    }
    
    // Check clinic_manager_profiles
    echo "\n2. CLINIC_MANAGER_PROFILES TABLE:\n";
    $cmProfiles = $pdo->query("DESCRIBE clinic_manager_profiles")->fetchAll();
    foreach ($cmProfiles as $col) {
        echo "  - {$col['Field']} ({$col['Type']})\n";
    }
    
    // Check clinics table
    echo "\n3. CLINICS TABLE:\n";
    $clinics = $pdo->query("DESCRIBE clinics")->fetchAll();
    foreach ($clinics as $col) {
        echo "  - {$col['Field']} ({$col['Type']})\n";
    }
    
    // Sample data from clinic_manager_profiles
    echo "\n4. SAMPLE CLINIC MANAGER DATA:\n";
    $sampleCM = $pdo->query("SELECT * FROM clinic_manager_profiles LIMIT 1")->fetch();
    if ($sampleCM) {
        print_r($sampleCM);
    }
    
    // Sample data from clinics
    echo "\n5. SAMPLE CLINIC DATA:\n";
    $sampleClinic = $pdo->query("SELECT * FROM clinics LIMIT 1")->fetch();
    if ($sampleClinic) {
        print_r($sampleClinic);
    }
    
    // Check user_roles for clinic managers
    echo "\n6. CLINIC MANAGERS IN SYSTEM:\n";
    $managers = $pdo->query("
        SELECT u.id, u.email, u.first_name, u.last_name, u.phone, 
               cm.clinic_id, c.clinic_name
        FROM users u
        JOIN user_roles ur ON u.id = ur.user_id
        JOIN roles r ON ur.role_id = r.id
        LEFT JOIN clinic_manager_profiles cm ON u.id = cm.user_id
        LEFT JOIN clinics c ON cm.clinic_id = c.id
        WHERE r.role_name = 'clinic_manager'
    ")->fetchAll();
    
    foreach ($managers as $mgr) {
        echo "  - {$mgr['email']} ({$mgr['first_name']} {$mgr['last_name']}) - Clinic: {$mgr['clinic_name']}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
