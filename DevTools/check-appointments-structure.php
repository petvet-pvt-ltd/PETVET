<?php
require_once __DIR__ . '/../config/connect.php';

echo "<h2>Analyzing Appointments Table Structure</h2>\n\n";

try {
    $pdo = db();
    
    // Check appointments table structure
    echo "<h3>1. appointments Table Structure:</h3>\n";
    $stmt = $pdo->query("DESCRIBE appointments");
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " (" . $row['Type'] . ") " . ($row['Key'] ? "[" . $row['Key'] . "]" : "") . "\n";
    }
    echo "</pre>\n\n";
    
    // Check sample appointments with status='completed'
    echo "<h3>2. Sample Completed Appointments:</h3>\n";
    $stmt = $pdo->query("
        SELECT * FROM appointments 
        WHERE status = 'completed' 
        LIMIT 2
    ");
    echo "<pre>";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    echo "</pre>\n\n";
    
    // Check pets table structure
    echo "<h3>3. pets Table Structure:</h3>\n";
    $stmt = $pdo->query("DESCRIBE pets");
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " (" . $row['Type'] . ") " . ($row['Key'] ? "[" . $row['Key'] . "]" : "") . "\n";
    }
    echo "</pre>\n\n";
    
} catch (Exception $e) {
    echo "<pre>Error: " . $e->getMessage() . "</pre>";
}
