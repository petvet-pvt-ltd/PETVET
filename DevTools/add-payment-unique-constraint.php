<?php
/**
 * Add unique constraint to payments table to prevent duplicate payment records
 */

require_once __DIR__ . '/../config/connect.php';

try {
    $pdo = db();
    
    echo "=== Adding Unique Constraint to Payments Table ===\n\n";
    
    // Check if constraint already exists
    $stmt = $pdo->query("
        SELECT CONSTRAINT_NAME
        FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'payments'
        AND CONSTRAINT_TYPE = 'UNIQUE'
        AND CONSTRAINT_NAME LIKE '%appointment%'
    ");
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        echo "✅ Unique constraint already exists: {$existing['CONSTRAINT_NAME']}\n";
        echo "This prevents duplicate payment records for the same appointment.\n";
    } else {
        echo "Adding unique constraint on appointment_id...\n";
        $pdo->exec("
            ALTER TABLE payments
            ADD UNIQUE KEY unique_appointment_payment (appointment_id)
        ");
        echo "✅ Successfully added unique constraint!\n";
        echo "Future attempts to insert duplicate payments will be blocked.\n";
    }
    
    echo "\n=== Done ===\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    
    if (strpos($e->getMessage(), 'Duplicate key') !== false) {
        echo "\n⚠️ Note: Cannot add constraint because duplicate records exist.\n";
        echo "Run cleanup-duplicate-payments.php first to remove duplicates.\n";
    }
}
