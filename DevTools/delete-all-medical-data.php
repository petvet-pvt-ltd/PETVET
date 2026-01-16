<?php
/**
 * Delete All Medical Data - Master Script
 * 
 * This script safely deletes all medical-related data in the correct order:
 * 1. Medical Records
 * 2. Prescriptions (including prescription_items)
 * 3. Vaccinations (including vaccination_items)
 * 4. Appointments
 * 
 * This ensures no foreign key constraint violations occur.
 * 
 * WARNING: This operation is IRREVERSIBLE. 
 * ALWAYS create a backup before running this script!
 */

require_once __DIR__ . '/../config/connect.php';

// Set up error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/delete-all-medical-data-errors.log');

$pdo = db();

// Start output
echo "<!DOCTYPE html><html><head><title>Delete All Medical Data</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    h1 { color: #333; border-bottom: 3px solid #dc3545; padding-bottom: 10px; }
    h2 { color: #007bff; margin-top: 30px; }
    .info { background: #e7f3ff; border-left: 4px solid #007bff; padding: 15px; margin: 15px 0; }
    .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 15px 0; }
    .danger { background: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 15px 0; }
    .success { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 15px 0; }
    pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
    table { border-collapse: collapse; width: 100%; margin: 15px 0; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    th { background-color: #dc3545; color: white; }
    .step { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .step-header { background: #007bff; color: white; padding: 10px; margin: -20px -20px 20px -20px; border-radius: 5px 5px 0 0; }
    .count { font-weight: bold; color: #dc3545; font-size: 1.2em; }
    .progress { background: #e9ecef; height: 30px; border-radius: 5px; margin: 20px 0; }
    .progress-bar { background: #28a745; height: 100%; border-radius: 5px; text-align: center; line-height: 30px; color: white; font-weight: bold; transition: width 0.5s; }
</style></head><body>";

echo "<h1>🗑️ Delete All Medical Data - Master Script</h1>";
echo "<p><strong>Start Time:</strong> " . date('Y-m-d H:i:s') . "</p>";

// Log array to track operations
$operationLog = [];
$startTime = microtime(true);

try {
    // ==================== PRE-DELETION CHECK ====================
    echo "<div class='warning'>";
    echo "<h2>⚠️ Pre-Deletion Analysis</h2>";
    echo "<p><strong>IMPORTANT:</strong> This will permanently delete ALL medical data from the database.</p>";
    
    // Check counts
    $counts = [];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM medical_records");
    $counts['medical_records'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM prescriptions");
    $counts['prescriptions'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Check prescription_items
    $stmt = $pdo->query("SHOW TABLES LIKE 'prescription_items'");
    $prescriptionItemsExists = $stmt->rowCount() > 0;
    $counts['prescription_items'] = 0;
    if ($prescriptionItemsExists) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM prescription_items");
        $counts['prescription_items'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM vaccinations");
    $counts['vaccinations'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Check vaccination_items
    $stmt = $pdo->query("SHOW TABLES LIKE 'vaccination_items'");
    $vaccinationItemsExists = $stmt->rowCount() > 0;
    $counts['vaccination_items'] = 0;
    if ($vaccinationItemsExists) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM vaccination_items");
        $counts['vaccination_items'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM appointments");
    $counts['appointments'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $totalRecords = array_sum($counts);
    
    echo "<table>";
    echo "<tr><th>Table</th><th>Record Count</th><th>Action</th></tr>";
    echo "<tr><td>Medical Records</td><td class='count'>{$counts['medical_records']}</td><td>Step 1: Delete First</td></tr>";
    echo "<tr><td>Prescriptions</td><td class='count'>{$counts['prescriptions']}</td><td>Step 2: Delete Second</td></tr>";
    if ($prescriptionItemsExists) {
        echo "<tr><td>&nbsp;&nbsp;&nbsp;└─ Prescription Items</td><td class='count'>{$counts['prescription_items']}</td><td>Step 2a: Delete with prescriptions</td></tr>";
    }
    echo "<tr><td>Vaccinations</td><td class='count'>{$counts['vaccinations']}</td><td>Step 3: Delete Third</td></tr>";
    if ($vaccinationItemsExists) {
        echo "<tr><td>&nbsp;&nbsp;&nbsp;└─ Vaccination Items</td><td class='count'>{$counts['vaccination_items']}</td><td>Step 3a: Delete with vaccinations</td></tr>";
    }
    echo "<tr><td>Appointments</td><td class='count'>{$counts['appointments']}</td><td>Step 4: Delete Last</td></tr>";
    echo "<tr style='background: #ffe6e6; font-weight: bold;'><td><strong>TOTAL RECORDS TO DELETE</strong></td><td class='count'>{$totalRecords}</td><td>4 Steps</td></tr>";
    echo "</table>";
    echo "</div>";
    
    if ($totalRecords == 0) {
        echo "<div class='info'>";
        echo "<h2>ℹ️ No Data to Delete</h2>";
        echo "<p>All medical data tables are already empty. No action needed.</p>";
        echo "</div>";
        echo "</body></html>";
        exit;
    }
    
    // Initialize progress tracking
    $totalSteps = 4;
    $currentStep = 0;
    
    // ==================== BEGIN TRANSACTION ====================
    $pdo->beginTransaction();
    
    echo "<div class='info'>";
    echo "<h2>🔄 Starting Master Transaction</h2>";
    echo "<p>All operations will be performed in a single transaction. If any error occurs, ALL changes will be rolled back.</p>";
    echo "</div>";
    
    // ==================== STEP 1: DELETE MEDICAL RECORDS ====================
    $currentStep++;
    echo "<div class='step'>";
    echo "<div class='step-header'><h3>Step {$currentStep}/{$totalSteps}: Delete Medical Records</h3></div>";
    
    if ($counts['medical_records'] > 0) {
        $deleteStmt = $pdo->prepare("DELETE FROM medical_records");
        $deleteStmt->execute();
        $deletedMedical = $deleteStmt->rowCount();
        
        $operationLog[] = [
            'step' => $currentStep,
            'table' => 'medical_records',
            'deleted' => $deletedMedical,
            'time' => date('Y-m-d H:i:s')
        ];
        
        echo "<div class='success'>";
        echo "<p>✅ Successfully deleted <strong>{$deletedMedical}</strong> medical records</p>";
        echo "</div>";
    } else {
        echo "<p>ℹ️ No medical records to delete (table already empty)</p>";
        $operationLog[] = [
            'step' => $currentStep,
            'table' => 'medical_records',
            'deleted' => 0,
            'time' => date('Y-m-d H:i:s')
        ];
    }
    
    echo "<div class='progress'>";
    $progress = ($currentStep / $totalSteps) * 100;
    echo "<div class='progress-bar' style='width: {$progress}%'>{$progress}% Complete</div>";
    echo "</div>";
    echo "</div>";
    
    // ==================== STEP 2: DELETE PRESCRIPTIONS ====================
    $currentStep++;
    echo "<div class='step'>";
    echo "<div class='step-header'><h3>Step {$currentStep}/{$totalSteps}: Delete Prescription Records</h3></div>";
    
    $deletedPrescriptionItems = 0;
    $deletedPrescriptions = 0;
    
    if ($prescriptionItemsExists && $counts['prescription_items'] > 0) {
        $deleteItemsStmt = $pdo->prepare("DELETE FROM prescription_items");
        $deleteItemsStmt->execute();
        $deletedPrescriptionItems = $deleteItemsStmt->rowCount();
        
        echo "<p>✅ Deleted <strong>{$deletedPrescriptionItems}</strong> prescription items (child records)</p>";
        
        $operationLog[] = [
            'step' => "{$currentStep}a",
            'table' => 'prescription_items',
            'deleted' => $deletedPrescriptionItems,
            'time' => date('Y-m-d H:i:s')
        ];
    }
    
    if ($counts['prescriptions'] > 0) {
        $deleteStmt = $pdo->prepare("DELETE FROM prescriptions");
        $deleteStmt->execute();
        $deletedPrescriptions = $deleteStmt->rowCount();
        
        $operationLog[] = [
            'step' => $currentStep,
            'table' => 'prescriptions',
            'deleted' => $deletedPrescriptions,
            'time' => date('Y-m-d H:i:s')
        ];
        
        echo "<div class='success'>";
        echo "<p>✅ Successfully deleted <strong>{$deletedPrescriptions}</strong> prescription records</p>";
        echo "</div>";
    } else {
        echo "<p>ℹ️ No prescription records to delete (table already empty)</p>";
        $operationLog[] = [
            'step' => $currentStep,
            'table' => 'prescriptions',
            'deleted' => 0,
            'time' => date('Y-m-d H:i:s')
        ];
    }
    
    echo "<div class='progress'>";
    $progress = ($currentStep / $totalSteps) * 100;
    echo "<div class='progress-bar' style='width: {$progress}%'>{$progress}% Complete</div>";
    echo "</div>";
    echo "</div>";
    
    // ==================== STEP 3: DELETE VACCINATIONS ====================
    $currentStep++;
    echo "<div class='step'>";
    echo "<div class='step-header'><h3>Step {$currentStep}/{$totalSteps}: Delete Vaccination Records</h3></div>";
    
    $deletedVaccinationItems = 0;
    $deletedVaccinations = 0;
    
    if ($vaccinationItemsExists && $counts['vaccination_items'] > 0) {
        $deleteItemsStmt = $pdo->prepare("DELETE FROM vaccination_items");
        $deleteItemsStmt->execute();
        $deletedVaccinationItems = $deleteItemsStmt->rowCount();
        
        echo "<p>✅ Deleted <strong>{$deletedVaccinationItems}</strong> vaccination items (child records)</p>";
        
        $operationLog[] = [
            'step' => "{$currentStep}a",
            'table' => 'vaccination_items',
            'deleted' => $deletedVaccinationItems,
            'time' => date('Y-m-d H:i:s')
        ];
    }
    
    if ($counts['vaccinations'] > 0) {
        $deleteStmt = $pdo->prepare("DELETE FROM vaccinations");
        $deleteStmt->execute();
        $deletedVaccinations = $deleteStmt->rowCount();
        
        $operationLog[] = [
            'step' => $currentStep,
            'table' => 'vaccinations',
            'deleted' => $deletedVaccinations,
            'time' => date('Y-m-d H:i:s')
        ];
        
        echo "<div class='success'>";
        echo "<p>✅ Successfully deleted <strong>{$deletedVaccinations}</strong> vaccination records</p>";
        echo "</div>";
    } else {
        echo "<p>ℹ️ No vaccination records to delete (table already empty)</p>";
        $operationLog[] = [
            'step' => $currentStep,
            'table' => 'vaccinations',
            'deleted' => 0,
            'time' => date('Y-m-d H:i:s')
        ];
    }
    
    echo "<div class='progress'>";
    $progress = ($currentStep / $totalSteps) * 100;
    echo "<div class='progress-bar' style='width: {$progress}%'>{$progress}% Complete</div>";
    echo "</div>";
    echo "</div>";
    
    // ==================== STEP 4: DELETE APPOINTMENTS ====================
    $currentStep++;
    echo "<div class='step'>";
    echo "<div class='step-header'><h3>Step {$currentStep}/{$totalSteps}: Delete Appointments</h3></div>";
    
    if ($counts['appointments'] > 0) {
        $deleteStmt = $pdo->prepare("DELETE FROM appointments");
        $deleteStmt->execute();
        $deletedAppointments = $deleteStmt->rowCount();
        
        $operationLog[] = [
            'step' => $currentStep,
            'table' => 'appointments',
            'deleted' => $deletedAppointments,
            'time' => date('Y-m-d H:i:s')
        ];
        
        echo "<div class='success'>";
        echo "<p>✅ Successfully deleted <strong>{$deletedAppointments}</strong> appointments</p>";
        echo "</div>";
    } else {
        echo "<p>ℹ️ No appointments to delete (table already empty)</p>";
        $operationLog[] = [
            'step' => $currentStep,
            'table' => 'appointments',
            'deleted' => 0,
            'time' => date('Y-m-d H:i:s')
        ];
    }
    
    echo "<div class='progress'>";
    $progress = ($currentStep / $totalSteps) * 100;
    echo "<div class='progress-bar' style='width: {$progress}%'>100% Complete</div>";
    echo "</div>";
    echo "</div>";
    
    // ==================== COMMIT TRANSACTION ====================
    $pdo->commit();
    
    $endTime = microtime(true);
    $executionTime = round($endTime - $startTime, 3);
    
    // ==================== VERIFY DELETION ====================
    echo "<div class='info'>";
    echo "<h2>🔍 Post-Deletion Verification</h2>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM medical_records");
    $remainingMedical = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM prescriptions");
    $remainingPrescriptions = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $remainingPrescriptionItems = 0;
    if ($prescriptionItemsExists) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM prescription_items");
        $remainingPrescriptionItems = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM vaccinations");
    $remainingVaccinations = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $remainingVaccinationItems = 0;
    if ($vaccinationItemsExists) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM vaccination_items");
        $remainingVaccinationItems = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM appointments");
    $remainingAppointments = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $totalRemaining = $remainingMedical + $remainingPrescriptions + $remainingPrescriptionItems + 
                      $remainingVaccinations + $remainingVaccinationItems + $remainingAppointments;
    
    echo "<table>";
    echo "<tr><th>Table</th><th>Remaining Records</th><th>Status</th></tr>";
    echo "<tr><td>Medical Records</td><td>{$remainingMedical}</td><td>" . ($remainingMedical == 0 ? "✅" : "❌") . "</td></tr>";
    echo "<tr><td>Prescriptions</td><td>{$remainingPrescriptions}</td><td>" . ($remainingPrescriptions == 0 ? "✅" : "❌") . "</td></tr>";
    if ($prescriptionItemsExists) {
        echo "<tr><td>&nbsp;&nbsp;&nbsp;└─ Prescription Items</td><td>{$remainingPrescriptionItems}</td><td>" . ($remainingPrescriptionItems == 0 ? "✅" : "❌") . "</td></tr>";
    }
    echo "<tr><td>Vaccinations</td><td>{$remainingVaccinations}</td><td>" . ($remainingVaccinations == 0 ? "✅" : "❌") . "</td></tr>";
    if ($vaccinationItemsExists) {
        echo "<tr><td>&nbsp;&nbsp;&nbsp;└─ Vaccination Items</td><td>{$remainingVaccinationItems}</td><td>" . ($remainingVaccinationItems == 0 ? "✅" : "❌") . "</td></tr>";
    }
    echo "<tr><td>Appointments</td><td>{$remainingAppointments}</td><td>" . ($remainingAppointments == 0 ? "✅" : "❌") . "</td></tr>";
    echo "<tr style='background: " . ($totalRemaining == 0 ? "#d4edda" : "#f8d7da") . "; font-weight: bold;'>";
    echo "<td><strong>TOTAL REMAINING</strong></td><td><strong>{$totalRemaining}</strong></td>";
    echo "<td><strong>" . ($totalRemaining == 0 ? "✅ SUCCESS" : "❌ FAILED") . "</strong></td></tr>";
    echo "</table>";
    echo "</div>";
    
    // ==================== FINAL SUMMARY ====================
    echo "<div class='success'>";
    echo "<h2>✅ Master Deletion Completed Successfully</h2>";
    
    echo "<h3>📊 Deletion Summary:</h3>";
    echo "<table>";
    echo "<tr><th>Step</th><th>Table</th><th>Records Deleted</th><th>Time</th></tr>";
    foreach ($operationLog as $log) {
        echo "<tr>";
        echo "<td>Step {$log['step']}</td>";
        echo "<td>{$log['table']}</td>";
        echo "<td><strong>{$log['deleted']}</strong></td>";
        echo "<td>{$log['time']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>⏱️ Performance Metrics:</h3>";
    echo "<table>";
    echo "<tr><th>Metric</th><th>Value</th></tr>";
    echo "<tr><td>Total Records Deleted</td><td class='count'>{$totalRecords}</td></tr>";
    echo "<tr><td>Total Tables Processed</td><td><strong>" . count($operationLog) . "</strong></td></tr>";
    echo "<tr><td>Execution Time</td><td><strong>{$executionTime} seconds</strong></td></tr>";
    echo "<tr><td>Transaction Status</td><td><span style='color: #28a745; font-weight: bold;'>✅ COMMITTED</span></td></tr>";
    echo "<tr><td>Completion Time</td><td>" . date('Y-m-d H:i:s') . "</td></tr>";
    echo "</table>";
    echo "</div>";
    
    // Log to file
    $logContent = "\n" . str_repeat("=", 80) . "\n";
    $logContent .= "MASTER DELETION COMPLETED - " . date('Y-m-d H:i:s') . "\n";
    $logContent .= str_repeat("=", 80) . "\n";
    $logContent .= "Total Records Deleted: {$totalRecords}\n";
    $logContent .= "Execution Time: {$executionTime} seconds\n";
    $logContent .= "\nDetailed Log:\n";
    foreach ($operationLog as $log) {
        $logContent .= sprintf("  Step %s: %s - %d records deleted at %s\n", 
            $log['step'], $log['table'], $log['deleted'], $log['time']);
    }
    $logContent .= "\nVerification: All tables are now empty (Total Remaining: {$totalRemaining})\n";
    $logContent .= str_repeat("=", 80) . "\n";
    
    error_log($logContent);
    
} catch (PDOException $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    echo "<div class='danger'>";
    echo "<h2>❌ Critical Error During Master Deletion</h2>";
    echo "<p><strong>Error Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Error Code:</strong> " . $e->getCode() . "</p>";
    echo "<p><strong>Failed at Step:</strong> {$currentStep} of {$totalSteps}</p>";
    echo "<p><strong>Transaction Status:</strong> <span style='color: #dc3545; font-weight: bold;'>❌ ROLLED BACK</span></p>";
    echo "<p><strong>Result:</strong> No data was deleted. The database remains unchanged.</p>";
    
    if (!empty($operationLog)) {
        echo "<h3>Operations Attempted Before Failure:</h3>";
        echo "<pre>";
        print_r($operationLog);
        echo "</pre>";
    }
    echo "</div>";
    
    // Log the error
    $errorLog = "\n" . str_repeat("=", 80) . "\n";
    $errorLog .= "MASTER DELETION FAILED - " . date('Y-m-d H:i:s') . "\n";
    $errorLog .= str_repeat("=", 80) . "\n";
    $errorLog .= "Error: " . $e->getMessage() . "\n";
    $errorLog .= "Error Code: " . $e->getCode() . "\n";
    $errorLog .= "Failed at Step: {$currentStep} of {$totalSteps}\n";
    $errorLog .= "Transaction: ROLLED BACK\n";
    $errorLog .= str_repeat("=", 80) . "\n";
    error_log($errorLog);
    
} catch (Exception $e) {
    // Handle other exceptions
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    echo "<div class='danger'>";
    echo "<h2>❌ Unexpected Error</h2>";
    echo "<p><strong>Error Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Transaction Status:</strong> <span style='color: #dc3545; font-weight: bold;'>❌ ROLLED BACK</span></p>";
    echo "</div>";
    
    error_log("[" . date('Y-m-d H:i:s') . "] UNEXPECTED ERROR in master deletion: " . $e->getMessage() . "\n");
}

echo "</body></html>";
