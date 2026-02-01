<?php
/**
 * Migration: Add reschedule request columns to appointments table
 * Allows pet owners to request reschedules that need receptionist approval
 */

require_once __DIR__ . '/../../config/connect.php';

try {
    $pdo = db();
    
    echo "Adding reschedule request columns to appointments table...\n\n";
    
    // Check if columns already exist
    $checkSql = "SHOW COLUMNS FROM appointments LIKE 'reschedule_requested_date'";
    $result = $pdo->query($checkSql);
    
    if ($result->rowCount() > 0) {
        echo "✓ Columns already exist. No migration needed.\n";
        exit;
    }
    
    // Add the columns
    $sql = "ALTER TABLE appointments 
            ADD COLUMN reschedule_requested_date DATE NULL AFTER decline_reason,
            ADD COLUMN reschedule_requested_time TIME NULL AFTER reschedule_requested_date";
    
    $pdo->exec($sql);
    
    echo "✓ Successfully added reschedule_requested_date and reschedule_requested_time columns!\n\n";
    echo "These columns will store pet owner reschedule requests until approved by receptionist.\n";
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
