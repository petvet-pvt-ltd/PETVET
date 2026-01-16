<?php
/**
 * Delete All Prescription Records
 * 
 * This script safely deletes all prescription records and their child items
 * Must be run before deleting appointments
 * 
 * Tables affected:
 * - prescription_items (child table)
 * - prescriptions (parent table)
 * 
 * WARNING: This operation is irreversible. Ensure you have a backup!
 */

require_once __DIR__ . '/../config/connect.php';

// Set up error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/delete-prescriptions-errors.log');

$pdo = db();

// Start output
echo "<!DOCTYPE html><html><head><title>Delete Prescription Records</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    h1 { color: #333; border-bottom: 3px solid #dc3545; padding-bottom: 10px; }
    .info { background: #e7f3ff; border-left: 4px solid #007bff; padding: 15px; margin: 15px 0; }
    .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 15px 0; }
    .danger { background: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 15px 0; }
    .success { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 15px 0; }
    pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
    table { border-collapse: collapse; width: 100%; margin: 15px 0; background: white; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    th { background-color: #dc3545; color: white; }
</style></head><body>";

echo "<h1>🗑️ Delete All Prescription Records</h1>";
echo "<p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p>";

try {
    // Get counts before deletion
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM prescriptions");
    $prescriptionCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Check if prescription_items table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'prescription_items'");
    $itemsTableExists = $stmt->rowCount() > 0;
    
    $prescriptionItemsCount = 0;
    if ($itemsTableExists) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM prescription_items");
        $prescriptionItemsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }
    
    echo "<div class='info'>";
    echo "<h2>📊 Pre-Deletion Status</h2>";
    echo "<table>";
    echo "<tr><th>Table</th><th>Record Count</th></tr>";
    echo "<tr><td>Prescriptions</td><td><strong>{$prescriptionCount}</strong></td></tr>";
    if ($itemsTableExists) {
        echo "<tr><td>Prescription Items (child records)</td><td><strong>{$prescriptionItemsCount}</strong></td></tr>";
    }
    echo "</table>";
    echo "</div>";
    
    if ($prescriptionCount == 0) {
        echo "<div class='info'>";
        echo "<h2>ℹ️ No Prescription Records to Delete</h2>";
        echo "<p>The prescriptions table is already empty.</p>";
        echo "</div>";
        echo "</body></html>";
        exit;
    }
    
    // Get sample data before deletion
    $stmt = $pdo->query("SELECT * FROM prescriptions LIMIT 3");
    $sampleData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div class='info'>";
    echo "<h3>Sample Prescription Records to be Deleted:</h3>";
    echo "<pre>";
    print_r($sampleData);
    echo "</pre>";
    echo "</div>";
    
    // Begin transaction
    $pdo->beginTransaction();
    
    echo "<div class='info'>";
    echo "<h2>🔄 Starting Deletion Process...</h2>";
    echo "<p>Transaction started. Changes will be rolled back if any error occurs.</p>";
    echo "</div>";
    
    $deletedItemsCount = 0;
    
    // Delete prescription_items first (if table exists)
    if ($itemsTableExists && $prescriptionItemsCount > 0) {
        $deleteItemsStmt = $pdo->prepare("DELETE FROM prescription_items");
        $deleteItemsStmt->execute();
        $deletedItemsCount = $deleteItemsStmt->rowCount();
        
        echo "<div class='info'>";
        echo "<p>✅ Deleted {$deletedItemsCount} prescription item records</p>";
        echo "</div>";
    }
    
    // Delete prescriptions
    $deleteStmt = $pdo->prepare("DELETE FROM prescriptions");
    $deleteStmt->execute();
    $deletedCount = $deleteStmt->rowCount();
    
    // Commit transaction
    $pdo->commit();
    
    // Verify deletion
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM prescriptions");
    $remainingCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $remainingItemsCount = 0;
    if ($itemsTableExists) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM prescription_items");
        $remainingItemsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }
    
    echo "<div class='success'>";
    echo "<h2>✅ Deletion Completed Successfully</h2>";
    echo "<table>";
    echo "<tr><th>Metric</th><th>Value</th></tr>";
    echo "<tr><td>Prescription Records Deleted</td><td><strong>{$deletedCount}</strong></td></tr>";
    if ($itemsTableExists) {
        echo "<tr><td>Prescription Items Deleted</td><td><strong>{$deletedItemsCount}</strong></td></tr>";
    }
    echo "<tr><td>Remaining Prescriptions</td><td><strong>{$remainingCount}</strong></td></tr>";
    if ($itemsTableExists) {
        echo "<tr><td>Remaining Prescription Items</td><td><strong>{$remainingItemsCount}</strong></td></tr>";
    }
    echo "<tr><td>Transaction Status</td><td><span style='color: #28a745;'>✅ Committed</span></td></tr>";
    echo "<tr><td>Completion Time</td><td>" . date('Y-m-d H:i:s') . "</td></tr>";
    echo "</table>";
    echo "</div>";
    
    // Log the operation
    $logMessage = sprintf(
        "[%s] Successfully deleted %d prescription records and %d prescription items. Remaining: %d prescriptions, %d items\n",
        date('Y-m-d H:i:s'),
        $deletedCount,
        $deletedItemsCount,
        $remainingCount,
        $remainingItemsCount
    );
    error_log($logMessage);
    
    echo "<div class='info'>";
    echo "<h3>📋 Operation Log:</h3>";
    echo "<pre>{$logMessage}</pre>";
    echo "</div>";
    
} catch (PDOException $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    echo "<div class='danger'>";
    echo "<h2>❌ Error During Deletion</h2>";
    echo "<p><strong>Error Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Error Code:</strong> " . $e->getCode() . "</p>";
    echo "<p><strong>Transaction Status:</strong> <span style='color: #dc3545;'>❌ Rolled Back</span></p>";
    echo "<p>No data was deleted. The database remains unchanged.</p>";
    echo "</div>";
    
    // Log the error
    $errorMessage = sprintf(
        "[%s] ERROR deleting prescriptions: %s (Code: %s)\n",
        date('Y-m-d H:i:s'),
        $e->getMessage(),
        $e->getCode()
    );
    error_log($errorMessage);
    
} catch (Exception $e) {
    // Handle other exceptions
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    echo "<div class='danger'>";
    echo "<h2>❌ Unexpected Error</h2>";
    echo "<p><strong>Error Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Transaction Status:</strong> <span style='color: #dc3545;'>❌ Rolled Back</span></p>";
    echo "</div>";
    
    error_log("[" . date('Y-m-d H:i:s') . "] UNEXPECTED ERROR: " . $e->getMessage() . "\n");
}

echo "</body></html>";
