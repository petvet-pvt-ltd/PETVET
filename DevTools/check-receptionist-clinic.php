<?php
require_once __DIR__ . '/../config/connect.php';

echo "<h2>Analyzing Receptionist and Clinic Staff Structure</h2>\n\n";

try {
    $pdo = db();
    
    // Check clinic_staff table structure
    echo "<h3>1. clinic_staff Table Structure:</h3>\n";
    $stmt = $pdo->query("DESCRIBE clinic_staff");
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " (" . $row['Type'] . ") " . ($row['Key'] ? "[" . $row['Key'] . "]" : "") . "\n";
    }
    echo "</pre>\n\n";
    
    // Check sample receptionist in clinic_staff
    echo "<h3>2. Sample Receptionist Records in clinic_staff:</h3>\n";
    $stmt = $pdo->query("
        SELECT cs.*, u.first_name, u.last_name, u.email, r.role_name 
        FROM clinic_staff cs 
        JOIN users u ON cs.user_id = u.id 
        JOIN user_roles ur ON u.id = ur.user_id 
        JOIN roles r ON ur.role_id = r.id 
        WHERE r.role_name = 'receptionist' 
        LIMIT 3
    ");
    echo "<pre>";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    echo "</pre>\n\n";
    
    // Check if there are receptionists WITHOUT clinic_id in clinic_staff
    echo "<h3>3. Receptionists NOT in clinic_staff:</h3>\n";
    $stmt = $pdo->query("
        SELECT u.id, u.email, u.first_name, u.last_name 
        FROM users u
        JOIN user_roles ur ON u.id = ur.user_id
        JOIN roles r ON ur.role_id = r.id
        WHERE r.role_name = 'receptionist'
        AND u.id NOT IN (SELECT user_id FROM clinic_staff)
        LIMIT 5
    ");
    $missing = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($missing)) {
        echo "<pre>All receptionists are in clinic_staff table ✓</pre>\n\n";
    } else {
        echo "<pre>";
        print_r($missing);
        echo "</pre>\n\n";
    }
    
    // Check clinics table structure
    echo "<h3>4. clinics Table Structure:</h3>\n";
    $stmt = $pdo->query("DESCRIBE clinics");
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    echo "</pre>\n\n";
    
    // Sample clinic data
    echo "<h3>5. Sample Clinic Data:</h3>\n";
    $stmt = $pdo->query("SELECT id, clinic_name, clinic_phone, clinic_address FROM clinics LIMIT 2");
    echo "<pre>";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    echo "</pre>\n\n";
    
} catch (Exception $e) {
    echo "<pre>Error: " . $e->getMessage() . "</pre>";
}
