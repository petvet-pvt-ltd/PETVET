<?php
/**
 * PHP-Based Database Backup Script
 * Creates a backup without relying on mysqldump
 */

require_once __DIR__ . '/../../config/connect.php';

$backupDir = __DIR__;
$backupFile = 'backup_' . date('Y-m-d_His') . '.sql';
$backupPath = $backupDir . '/' . $backupFile;

echo "Creating database backup using PHP...\n";
echo "Backup file: $backupFile\n\n";

try {
    $pdo = db();
    
    // Start building backup
    $backup = "-- PETVET Database Backup\n";
    $backup .= "-- Date: " . date('Y-m-d H:i:s') . "\n";
    $backup .= "-- Database: btfrleeonbksuwewbmxg\n";
    $backup .= "-- Generated using PHP\n\n";
    $backup .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
    
    // Get all tables
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    $totalTables = count($tables);
    
    echo "Found $totalTables tables to backup\n";
    
    $tableNum = 0;
    foreach ($tables as $table) {
        $tableNum++;
        echo "[$tableNum/$totalTables] Backing up table: $table...";
        
        // Get CREATE TABLE statement
        $createStmt = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
        $backup .= "-- Table: $table\n";
        $backup .= "DROP TABLE IF EXISTS `$table`;\n";
        $backup .= $createStmt['Create Table'] . ";\n\n";
        
        // Get table data
        $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
        $rowCount = count($rows);
        
        if ($rowCount > 0) {
            $columns = array_keys($rows[0]);
            $columnList = '`' . implode('`, `', $columns) . '`';
            
            $backup .= "INSERT INTO `$table` ($columnList) VALUES\n";
            
            $values = [];
            foreach ($rows as $row) {
                $escapedValues = [];
                foreach ($row as $value) {
                    if ($value === null) {
                        $escapedValues[] = 'NULL';
                    } else {
                        $escapedValues[] = $pdo->quote($value);
                    }
                }
                $values[] = '(' . implode(', ', $escapedValues) . ')';
            }
            
            $backup .= implode(",\n", $values) . ";\n\n";
        }
        
        echo " $rowCount rows\n";
    }
    
    $backup .= "SET FOREIGN_KEY_CHECKS=1;\n";
    
    // Write backup file
    file_put_contents($backupPath, $backup);
    
    $fileSize = filesize($backupPath);
    $fileSizeMB = round($fileSize / 1024 / 1024, 2);
    
    echo "\n✅ Backup created successfully!\n";
    echo "File: $backupPath\n";
    echo "Size: $fileSizeMB MB\n";
    
    // Create compressed version
    if (function_exists('gzencode')) {
        $gzipPath = $backupPath . '.gz';
        file_put_contents($gzipPath, gzencode($backup, 9));
        $gzipSize = filesize($gzipPath);
        $gzipSizeMB = round($gzipSize / 1024 / 1024, 2);
        echo "✅ Compressed backup: $gzipSizeMB MB (saved " . round(($fileSize - $gzipSize) / $fileSize * 100, 1) . "%)\n";
    }
    
    echo "\n✅ Backup process completed successfully!\n";
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
