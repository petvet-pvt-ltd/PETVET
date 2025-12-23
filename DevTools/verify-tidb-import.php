<?php
/**
 * Verify TiDB Import - Check table row counts
 */

require_once __DIR__ . '/config/connect.php';

echo "<h2>TiDB Import Verification</h2>";
echo "<hr>";

try {
    $pdo = db();
    
    // Get all tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Table Row Counts:</h3>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>Table Name</th><th>Row Count</th></tr>";
    
    $totalRows = 0;
    foreach ($tables as $table) {
        $countStmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
        $count = $countStmt->fetch()['count'];
        $totalRows += $count;
        
        $color = $count > 0 ? 'green' : 'gray';
        echo "<tr>";
        echo "<td><strong>$table</strong></td>";
        echo "<td style='color: $color; text-align: center;'>$count</td>";
        echo "</tr>";
    }
    
    echo "<tr style='background-color: #f0f0f0; font-weight: bold;'>";
    echo "<td>TOTAL</td>";
    echo "<td style='text-align: center;'>$totalRows</td>";
    echo "</tr>";
    echo "</table>";
    
    echo "<hr>";
    echo "<h3 style='color: green;'>✓ Database imported successfully!</h3>";
    echo "<p><strong>Total Tables:</strong> " . count($tables) . "</p>";
    echo "<p><strong>Total Rows:</strong> $totalRows</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
