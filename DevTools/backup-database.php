<?php
/**
 * Database Backup Script
 * Creates a complete backup of the database
 */

require_once __DIR__ . '/../config/connect.php';

$backupDir = __DIR__ . '/../database/backups/';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

$filename = 'backup_' . date('Y-m-d_His') . '.sql';
$filepath = $backupDir . $filename;

try {
    $pdo = db();
    
    // Get all tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    $sql = "-- Database Backup\n";
    $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
    $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
    
    foreach ($tables as $table) {
        // Drop table statement
        $sql .= "DROP TABLE IF EXISTS `$table`;\n\n";
        
        // Create table statement
        $create = $pdo->query("SHOW CREATE TABLE `$table`")->fetch();
        $sql .= $create['Create Table'] . ";\n\n";
        
        // Insert data
        $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll();
        if (!empty($rows)) {
            $columns = array_keys($rows[0]);
            $sql .= "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES\n";
            
            $values = [];
            foreach ($rows as $row) {
                $escapedValues = array_map(function($val) use ($pdo) {
                    return $val === null ? 'NULL' : $pdo->quote($val);
                }, array_values($row));
                $values[] = '(' . implode(', ', $escapedValues) . ')';
            }
            $sql .= implode(",\n", $values) . ";\n\n";
        }
    }
    
    $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
    
    file_put_contents($filepath, $sql);
    
    echo "✅ Backup created successfully!\n";
    echo "📁 File: $filename\n";
    echo "📊 Size: " . round(filesize($filepath) / 1024, 2) . " KB\n";
    echo "📋 Tables backed up: " . count($tables) . "\n\n";
    echo "Tables included:\n";
    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        echo "  - $table ($count rows)\n";
    }
    
} catch (Exception $e) {
    echo "❌ Backup failed: " . $e->getMessage() . "\n";
}
?>
