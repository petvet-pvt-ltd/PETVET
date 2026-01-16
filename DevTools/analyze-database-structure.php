<?php
/**
 * Database Structure Analysis for Medical Data Deletion
 * This script analyzes all tables related to appointments, medical_records, prescriptions, and vaccinations
 * to understand the relationships and foreign keys before deletion
 */

require_once __DIR__ . '/../config/connect.php';

$pdo = db();

echo "<!DOCTYPE html><html><head><title>Database Structure Analysis</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
    h2 { color: #007bff; margin-top: 30px; }
    h3 { color: #28a745; margin-top: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 15px 0; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    th { background-color: #007bff; color: white; }
    tr:nth-child(even) { background-color: #f8f9fa; }
    .info-box { background: #e7f3ff; border-left: 4px solid #007bff; padding: 15px; margin: 15px 0; }
    .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 15px 0; }
    .danger { background: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 15px 0; }
    .success { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 15px 0; }
    pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
    .count { font-weight: bold; color: #dc3545; font-size: 1.2em; }
</style></head><body>";

echo "<h1>🔍 Database Structure Analysis for Medical Data Deletion</h1>";
echo "<p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p>";

// Define tables to analyze
$tables = ['appointments', 'medical_records', 'prescriptions', 'vaccinations'];

try {
    // Get all tables in database
    echo "<div class='info-box'>";
    echo "<h2>📊 Database Overview</h2>";
    $stmt = $pdo->query("SHOW TABLES");
    $allTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p><strong>Total tables in database:</strong> " . count($allTables) . "</p>";
    echo "</div>";

    // Analyze each table structure
    foreach ($tables as $table) {
        echo "<div style='background: white; padding: 20px; margin: 20px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>";
        echo "<h2>📋 Table: {$table}</h2>";
        
        // Check if table exists
        if (!in_array($table, $allTables)) {
            echo "<div class='warning'><strong>⚠️ WARNING:</strong> Table '{$table}' does not exist in the database!</div>";
            echo "</div>";
            continue;
        }
        
        // Get table structure
        echo "<h3>Table Structure:</h3>";
        $stmt = $pdo->query("DESCRIBE {$table}");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td><strong>{$col['Field']}</strong></td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>" . ($col['Key'] ? "<span style='color: #dc3545;'>{$col['Key']}</span>" : '') . "</td>";
            echo "<td>{$col['Default']}</td>";
            echo "<td>{$col['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Get row count
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM {$table}");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "<div class='info-box'>";
        echo "<p><strong>📊 Total Records:</strong> <span class='count'>{$count}</span></p>";
        echo "</div>";
        
        // Get foreign key information
        echo "<h3>Foreign Key Constraints:</h3>";
        $stmt = $pdo->query("
            SELECT 
                CONSTRAINT_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = '" . DB_NAME . "'
            AND TABLE_NAME = '{$table}'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        $foreignKeys = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($foreignKeys) > 0) {
            echo "<table>";
            echo "<tr><th>Constraint Name</th><th>Column</th><th>References Table</th><th>References Column</th></tr>";
            foreach ($foreignKeys as $fk) {
                echo "<tr>";
                echo "<td>{$fk['CONSTRAINT_NAME']}</td>";
                echo "<td><strong>{$fk['COLUMN_NAME']}</strong></td>";
                echo "<td>{$fk['REFERENCED_TABLE_NAME']}</td>";
                echo "<td>{$fk['REFERENCED_COLUMN_NAME']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p><em>No foreign key constraints found</em></p>";
        }
        
        // Find tables that reference this table
        echo "<h3>Tables Referencing This Table:</h3>";
        $stmt = $pdo->query("
            SELECT 
                TABLE_NAME,
                CONSTRAINT_NAME,
                COLUMN_NAME,
                REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = '" . DB_NAME . "'
            AND REFERENCED_TABLE_NAME = '{$table}'
        ");
        $referencingTables = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($referencingTables) > 0) {
            echo "<div class='warning'>";
            echo "<p><strong>⚠️ WARNING:</strong> The following tables have foreign keys referencing this table:</p>";
            echo "<table>";
            echo "<tr><th>Table Name</th><th>Constraint Name</th><th>Column</th><th>References Column</th></tr>";
            foreach ($referencingTables as $ref) {
                echo "<tr>";
                echo "<td><strong>{$ref['TABLE_NAME']}</strong></td>";
                echo "<td>{$ref['CONSTRAINT_NAME']}</td>";
                echo "<td>{$ref['COLUMN_NAME']}</td>";
                echo "<td>{$ref['REFERENCED_COLUMN_NAME']}</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "</div>";
        } else {
            echo "<p><em>No tables reference this table</em></p>";
        }
        
        // Sample data (first 3 rows)
        echo "<h3>Sample Data (first 3 rows):</h3>";
        $stmt = $pdo->query("SELECT * FROM {$table} LIMIT 3");
        $sampleData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($sampleData) > 0) {
            echo "<pre>";
            foreach ($sampleData as $row) {
                print_r($row);
                echo "\n";
            }
            echo "</pre>";
        } else {
            echo "<p><em>No data in table</em></p>";
        }
        
        echo "</div>";
    }
    
    // Deletion Order Recommendation
    echo "<div style='background: white; padding: 20px; margin: 20px 0; border-radius: 5px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);'>";
    echo "<h2>🎯 Recommended Deletion Order</h2>";
    
    echo "<div class='success'>";
    echo "<h3>Safe Deletion Sequence:</h3>";
    echo "<p>Based on the foreign key relationships, here's the recommended deletion order:</p>";
    echo "<ol>";
    echo "<li><strong>medical_records</strong> - References appointments table</li>";
    echo "<li><strong>prescriptions</strong> - References appointments table</li>";
    echo "<li><strong>vaccinations</strong> - References appointments table</li>";
    echo "<li><strong>appointments</strong> - Parent table (delete last to avoid FK constraint violations)</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<div class='danger'>";
    echo "<h3>⚠️ Important Notes:</h3>";
    echo "<ul>";
    echo "<li>Always create a backup before deletion</li>";
    echo "<li>Use transactions to ensure data integrity</li>";
    echo "<li>Disable foreign key checks if needed (with caution)</li>";
    echo "<li>Delete child records before parent records</li>";
    echo "<li>Log all deletion operations for audit purposes</li>";
    echo "</ul>";
    echo "</div>";
    
    // Summary
    echo "<h3>📈 Summary:</h3>";
    echo "<table>";
    echo "<tr><th>Table</th><th>Record Count</th><th>Action</th></tr>";
    foreach ($tables as $table) {
        if (in_array($table, $allTables)) {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM {$table}");
            $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            echo "<tr>";
            echo "<td><strong>{$table}</strong></td>";
            echo "<td class='count'>{$count}</td>";
            echo "<td>" . ($count > 0 ? "❌ Will be deleted" : "✅ Already empty") . "</td>";
            echo "</tr>";
        }
    }
    echo "</table>";
    
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div class='danger'>";
    echo "<h2>❌ Error</h2>";
    echo "<p><strong>Error Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "</body></html>";
