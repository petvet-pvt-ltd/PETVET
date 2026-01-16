<?php
/**
 * Delete All Appointments
 * 
 * This script safely deletes all appointments from the database
 * Note: Child records (medical_records, prescriptions, vaccinations) must be deleted first
 * to avoid foreign key constraint violations
 * 
 * WARNING: This operation is irreversible. Ensure you have a backup!
 */

require_once __DIR__ . '/../config/connect.php';

// Set up error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/delete-appointments-errors.log');

$pdo = db();

// Start output
echo "<!DOCTYPE html><html><head><title>Delete Appointments</title>";
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

echo "<h1>🗑️ Delete All Appointments</h1>";
echo "<p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p>";

try {
    // Check if there are dependent records
    echo "<div class='warning'>";
    echo "<h2>⚠️ Pre-Deletion Checks</h2>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM medical_records");
    $medicalCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM prescriptions");
    $prescriptionCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM vaccinations");
    $vaccinationCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM appointments");
    $appointmentCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "<table>";
    echo "<tr><th>Table</th><th>Record Count</th><th>Status</th></tr>";
    echo "<tr><td>Medical Records</td><td>{$medicalCount}</td><td>" . ($medicalCount > 0 ? "❌ Must be deleted first" : "✅ Empty") . "</td></tr>";
    echo "<tr><td>Prescriptions</td><td>{$prescriptionCount}</td><td>" . ($prescriptionCount > 0 ? "❌ Must be deleted first" : "✅ Empty") . "</td></tr>";
    echo "<tr><td>Vaccinations</td><td>{$vaccinationCount}</td><td>" . ($vaccinationCount > 0 ? "❌ Must be deleted first" : "✅ Empty") . "</td></tr>";
    echo "<tr><td><strong>Appointments</strong></td><td><strong>{$appointmentCount}</strong></td><td>" . ($appointmentCount > 0 ? "📋 Ready to delete" : "✅ Already empty") . "</td></tr>";
    echo "</table>";
    echo "</div>";
    
    if ($medicalCount > 0 || $prescriptionCount > 0 || $vaccinationCount > 0) {
        echo "<div class='danger'>";
        echo "<h2>❌ Cannot Delete Appointments</h2>";
        echo "<p><strong>Reason:</strong> There are dependent records that must be deleted first:</p>";
        echo "<ul>";
        if ($medicalCount > 0) echo "<li>Medical Records: {$medicalCount} records</li>";
        if ($prescriptionCount > 0) echo "<li>Prescriptions: {$prescriptionCount} records (including child items)</li>";
        if ($vaccinationCount > 0) echo "<li>Vaccinations: {$vaccinationCount} records (including child items)</li>";
        echo "</ul>";
        echo "<p><strong>Action Required:</strong> Please delete these records first, or use the master deletion script (delete-all-medical-data.php).</p>";
        echo "</div>";
        echo "</body></html>";
        exit;
    }
    
    if ($appointmentCount == 0) {
        echo "<div class='info'>";
        echo "<h2>ℹ️ No Appointments to Delete</h2>";
        echo "<p>The appointments table is already empty.</p>";
        echo "</div>";
        echo "</body></html>";
        exit;
    }
    
    // Begin transaction
    $pdo->beginTransaction();
    
    echo "<div class='info'>";
    echo "<h2>🔄 Starting Deletion Process...</h2>";
    echo "<p>Transaction started. Changes will be rolled back if any error occurs.</p>";
    echo "</div>";
    
    // Get sample data before deletion (for logging)
    $stmt = $pdo->query("SELECT id, appointment_date, appointment_type, status FROM appointments LIMIT 5");
    $sampleData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div class='info'>";
    echo "<h3>Sample Appointments to be Deleted:</h3>";
    echo "<pre>";
    print_r($sampleData);
    echo "</pre>";
    echo "</div>";
    
    // Delete appointments
    $deleteStmt = $pdo->prepare("DELETE FROM appointments");
    $deleteStmt->execute();
    $deletedCount = $deleteStmt->rowCount();
    
    // Commit transaction
    $pdo->commit();
    
    // Verify deletion
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM appointments");
    $remainingCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "<div class='success'>";
    echo "<h2>✅ Deletion Completed Successfully</h2>";
    echo "<table>";
    echo "<tr><th>Metric</th><th>Value</th></tr>";
    echo "<tr><td>Total Appointments Deleted</td><td><strong>{$deletedCount}</strong></td></tr>";
    echo "<tr><td>Remaining Appointments</td><td><strong>{$remainingCount}</strong></td></tr>";
    echo "<tr><td>Transaction Status</td><td><span style='color: #28a745;'>✅ Committed</span></td></tr>";
    echo "<tr><td>Completion Time</td><td>" . date('Y-m-d H:i:s') . "</td></tr>";
    echo "</table>";
    echo "</div>";
    
    // Log the operation
    $logMessage = sprintf(
        "[%s] Successfully deleted %d appointments. Remaining: %d\n",
        date('Y-m-d H:i:s'),
        $deletedCount,
        $remainingCount
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
    echo "<p><strong>Transaction Status:</strong> <span style='color: #dc3545;'>❌ Rolled Back</span></p>";
    echo "<p>No data was deleted. The database remains unchanged.</p>";
    echo "</div>";
    
    // Log the error
    $errorMessage = sprintf(
        "[%s] ERROR deleting appointments: %s\n",
        date('Y-m-d H:i:s'),
        $e->getMessage()
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
