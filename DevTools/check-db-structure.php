<?php
require_once __DIR__ . '/config/connect.php';

try {
    $db = db();
    
    // Get all tables
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h2>Database Tables:</h2>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li><strong>$table</strong></li>";
    }
    echo "</ul>";
    
    // Check if appointments table exists
    if (in_array('appointments', $tables)) {
        echo "<h3>Appointments Table Structure:</h3>";
        $columns = $db->query("DESCRIBE appointments")->fetchAll(PDO::FETCH_ASSOC);
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>Appointments table does not exist!</p>";
    }
    
    // Check for pets table
    if (in_array('pets', $tables)) {
        echo "<h3>Pets Table Structure:</h3>";
        $columns = $db->query("DESCRIBE pets")->fetchAll(PDO::FETCH_ASSOC);
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check for users table
    if (in_array('users', $tables)) {
        echo "<h3>Users Table Structure:</h3>";
        $columns = $db->query("DESCRIBE users")->fetchAll(PDO::FETCH_ASSOC);
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<h3>Sample Users:</h3>";
        $users = $db->query("SELECT id, email, first_name, last_name FROM users LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        echo "<pre>";
        print_r($users);
        echo "</pre>";
        
        // Check user_roles
        if (in_array('user_roles', $tables)) {
            echo "<h3>Vets (from user_roles):</h3>";
            $vets = $db->query("SELECT u.id, u.email, u.first_name, u.last_name, ur.role_id, r.role_name 
                FROM users u 
                JOIN user_roles ur ON u.id = ur.user_id 
                JOIN roles r ON ur.role_id = r.id 
                WHERE r.role_name = 'vet' LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            echo "<pre>";
            print_r($vets);
            echo "</pre>";
        }
    }
    
    // Check for clinics
    if (in_array('clinics', $tables)) {
        echo "<h3>Clinics Table:</h3>";
        $clinics = $db->query("SELECT * FROM clinics LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        echo "<pre>";
        print_r($clinics);
        echo "</pre>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
