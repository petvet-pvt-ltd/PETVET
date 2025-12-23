<?php
require_once '../config/connect.php';

try {
    $db = db();
    
    echo "=== clinic_staff Table Structure ===\n";
    $stmt = $db->query("DESCRIBE clinic_staff");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
