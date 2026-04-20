<?php

// Load database configuration
require_once __DIR__ . '/config/connect.php';

// Set timezone to Asia/Colombo (Sri Lanka Standard Time - UTC+5:30)
// Change this to your local timezone if different
date_default_timezone_set('Asia/Colombo');

// Get optional description parameter
$description = isset($argv[1]) ? trim($argv[1]) : '';

// Generate timestamp
$timestamp = date('Y.m.d-H.i.s');

// Generate filename
$filename = 'petvetBackup_' . $timestamp;
if (!empty($description)) {
    // Sanitize description for filename
    $safeDescription = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $description);
    $safeDescription = preg_replace('/\s+/', ' ', $safeDescription);
    $filename .= '_[' . $safeDescription . ']';
}
$filename .= '.sql';

// Create backups directory if it doesn't exist
$backupDir = __DIR__ . '/database/backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$filepath = $backupDir . '/' . $filename;

echo "===========================================\n";
echo "  PETVET Database Backup Script\n";
echo "===========================================\n";
echo "Database: " . DB_NAME . "\n";
echo "Host: " . DB_HOST . "\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n";
if (!empty($description)) {
    echo "Description: " . $description . "\n";
}
echo "Output file: " . $filename . "\n";
echo "===========================================\n\n";

try {
    // Connect to database
    $pdo = db();
    
    // Start building SQL content
    $sqlContent = "-- =============================================\n";
    $sqlContent .= "-- PETVET Database Backup\n";
    $sqlContent .= "-- =============================================\n";
    $sqlContent .= "-- Database: " . DB_NAME . "\n";
    $sqlContent .= "-- Backup Date: " . date('Y-m-d H:i:s') . "\n";
    $sqlContent .= "-- Timestamp: " . $timestamp . "\n";
    if (!empty($description)) {
        $sqlContent .= "-- Description: " . $description . "\n";
    }
    $sqlContent .= "-- =============================================\n\n";
    
    $sqlContent .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
    $sqlContent .= "SET time_zone = \"+00:00\";\n\n";
    $sqlContent .= "-- Database: `" . DB_NAME . "`\n";
    $sqlContent .= "-- =============================================\n\n";
    
    // Get all tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Found " . count($tables) . " tables to backup...\n\n";
    
    foreach ($tables as $table) {
        echo "Processing table: $table... ";
        
        // Add table separator
        $sqlContent .= "\n-- =============================================\n";
        $sqlContent .= "-- Table structure for table `$table`\n";
        $sqlContent .= "-- =============================================\n\n";
        
        // Drop table if exists
        $sqlContent .= "DROP TABLE IF EXISTS `$table`;\n";
        
        // Get CREATE TABLE statement
        $createTableStmt = $pdo->query("SHOW CREATE TABLE `$table`")->fetch();
        $sqlContent .= $createTableStmt['Create Table'] . ";\n\n";
        
        // Get table data
        $sqlContent .= "-- Dumping data for table `$table`\n\n";
        
        // Count rows
        $rowCount = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        
        if ($rowCount > 0) {
            // Get all data
            $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
            
            // Get column names
            $columns = array_keys($rows[0]);
            $columnList = '`' . implode('`, `', $columns) . '`';
            
            // Insert data in batches of 100 rows
            $batchSize = 100;
            $totalBatches = ceil($rowCount / $batchSize);
            
            for ($i = 0; $i < $totalBatches; $i++) {
                $batchRows = array_slice($rows, $i * $batchSize, $batchSize);
                
                $sqlContent .= "INSERT INTO `$table` ($columnList) VALUES\n";
                
                $values = [];
                foreach ($batchRows as $row) {
                    $rowValues = [];
                    foreach ($row as $value) {
                        if ($value === null) {
                            $rowValues[] = 'NULL';
                        } else {
                            // Escape and quote the value
                            $escaped = $pdo->quote($value);
                            $rowValues[] = $escaped;
                        }
                    }
                    $values[] = '(' . implode(', ', $rowValues) . ')';
                }
                
                $sqlContent .= implode(",\n", $values) . ";\n\n";
            }
            
            echo "$rowCount rows\n";
        } else {
            echo "0 rows (empty table)\n";
        }
    }
    
    // Write to file
    file_put_contents($filepath, $sqlContent);
    
    $fileSize = filesize($filepath);
    $fileSizeMB = round($fileSize / 1024 / 1024, 2);
    
    echo "\n===========================================\n";
    echo "  Backup completed successfully!\n";
    echo "===========================================\n";
    echo "File: $filename\n";
    echo "Size: " . ($fileSizeMB > 0 ? $fileSizeMB . " MB" : round($fileSize / 1024, 2) . " KB") . "\n";
    echo "Location: $filepath\n";
    echo "===========================================\n";
    
} catch (PDOException $e) {
    echo "\n❌ ERROR: Database error occurred!\n";
    echo "Message: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

// groomer
ALTER TABLE groomer_packages 
ADD COLUMN pet_size ENUM('Small', 'Medium', 'Large') 
DEFAULT 'Medium';

packages.php 
167, 82
<div class="form-group">
    <label for="pet_size">Pet Size *</label>
    <select id="petSize" name="pet_size" required>
        <option value="">Select Pet Size</option>
        <option value="Small">Small</option>
        <option value="Medium">Medium</option>
        <option value="Large">Large</option>
    </select>
</div>
<div class="meta-item">
<span class="meta-icon">📏</span>
<span class="meta-value"><?php echo htmlspecialchars($package['pet_size'] ?? 'Medium'); ?> </span>
</div>

packages.js - 61,
292 - let packagePetSize = 'Medium';
296 - if (icon.includes('📏')) packagePetSize = value;
315, 477 -   
const petSizeSelect = document.getElementById('petSize');
        if (petSizeSelect) {
            formData.append('pet_size', petSizeSelect.value || 'Medium');
        }

Api/packages.php - 74,116
packagesModel.php - insert , 35, 127, 128,138,179, 191



//vet dashboard
ALTER TABLE medical_records 
ADD COLUMN treatment_status ENUM('Pending', 'In Progress', 'Completed', 'Urgent') DEFAULT 'Pending';
//ALTER TABLE medical_records ADD COLUMN medication_name VARCHAR(255);

medical-records.php - 83
<div class="form-row">
    <label>
        Treatment Status *
        <select name="treatment_status" required>
            <option value="">Select Status</option>
            <option value="Pending">Pending</option>
            <option value="In Progress">In Progress</option>
            <option value="Completed">Completed</option>
            <option value="Urgent">Urgent</option>
        </select>
    </label>
</div>

//text field
<div class="form-row">
  <label>
    Medication Name
    <input type="text" name="medication_name" placeholder="e.g., Amoxicillin, Ibuprofen...">
  </label>
</div>

medical-records.js - 50,82
        <td>${r.treatment_status || 'Pending'}</td>
        //<td>${r.medication_name || '-'}</td>
241

api/add.php - 23
$treatmentStatus = trim($_POST['treatment_status'] ?? 'Pending');
85,86,93



//


