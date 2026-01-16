<?php
require_once __DIR__ . '/../config/connect.php';

$pdo = db();

echo "=== Cleaning Duplicate Payment Records ===\n\n";

try {
    // Find duplicates (same appointment_id)
    $stmt = $pdo->query("
        SELECT appointment_id, COUNT(*) as count, GROUP_CONCAT(id) as ids
        FROM payments
        GROUP BY appointment_id
        HAVING count > 1
    ");
    $duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($duplicates)) {
        echo "✅ No duplicate payment records found!\n";
        exit;
    }
    
    echo "Found duplicates for " . count($duplicates) . " appointment(s):\n\n";
    
    foreach ($duplicates as $dup) {
        $ids = explode(',', $dup['ids']);
        echo "Appointment ID {$dup['appointment_id']} has {$dup['count']} payment records (IDs: {$dup['ids']})\n";
        
        // Keep the first record, delete the rest
        $keepId = $ids[0];
        $deleteIds = array_slice($ids, 1);
        
        if (!empty($deleteIds)) {
            $placeholders = implode(',', array_fill(0, count($deleteIds), '?'));
            $stmt = $pdo->prepare("DELETE FROM payments WHERE id IN ($placeholders)");
            $stmt->execute($deleteIds);
            
            echo "  ✅ Kept payment ID {$keepId}, deleted " . count($deleteIds) . " duplicate(s)\n";
        }
    }
    
    echo "\n✅ Cleanup complete!\n";
    
    // Show final count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM payments");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "\nTotal payment records remaining: {$count}\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
