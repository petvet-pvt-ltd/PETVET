<?php
/**
 * Add 'paid' status to appointments table
 */

require_once __DIR__ . '/../config/connect.php';

try {
    $pdo = db();
    
    echo "Checking appointments table status column...\n\n";
    
    // Get current ENUM values
    $stmt = $pdo->query("SHOW COLUMNS FROM appointments LIKE 'status'");
    $column = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Current status ENUM values:\n";
    echo $column['Type'] . "\n\n";
    
    // Check if 'paid' already exists
    if (strpos($column['Type'], "'paid'") !== false) {
        echo "✅ 'paid' status already exists in the ENUM!\n";
        exit;
    }
    
    echo "Adding 'paid' status to appointments table...\n";
    
    // Add 'paid' to the ENUM
    $pdo->exec("
        ALTER TABLE appointments 
        MODIFY COLUMN status ENUM(
            'pending',
            'approved',
            'declined',
            'ongoing',
            'completed',
            'cancelled',
            'no_show',
            'paid'
        ) DEFAULT 'pending'
    ");
    
    echo "✅ Successfully added 'paid' status to appointments table!\n\n";
    
    // Verify the change
    $stmt = $pdo->query("SHOW COLUMNS FROM appointments LIKE 'status'");
    $column = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Updated status ENUM values:\n";
    echo $column['Type'] . "\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
