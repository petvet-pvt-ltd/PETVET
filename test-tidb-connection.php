<?php
/**
 * TiDB Connection Test Script
 * Tests both PDO and MySQLi connections to TiDB
 */

echo "<h2>TiDB Connection Test</h2>";
echo "<hr>";

// Load the connection configuration
require_once __DIR__ . '/config/connect.php';

echo "<h3>Configuration Details:</h3>";
echo "<ul>";
echo "<li><strong>Host:</strong> " . DB_HOST . "</li>";
echo "<li><strong>Port:</strong> " . DB_PORT . "</li>";
echo "<li><strong>Database:</strong> " . DB_NAME . "</li>";
echo "<li><strong>Username:</strong> " . DB_USER . "</li>";
echo "<li><strong>CA Path:</strong> " . DB_CA_PATH . "</li>";
echo "<li><strong>CA File Exists:</strong> " . (file_exists(DB_CA_PATH) ? "✓ Yes" : "✗ No") . "</li>";
echo "</ul>";
echo "<hr>";

// Test PDO Connection
echo "<h3>Testing PDO Connection...</h3>";
try {
    $pdo = db();
    echo "<p style='color: green;'>✓ PDO Connection successful!</p>";
    
    // Test query
    $stmt = $pdo->query("SELECT VERSION() as version, DATABASE() as db_name");
    $result = $stmt->fetch();
    echo "<p><strong>MySQL Version:</strong> " . $result['version'] . "</p>";
    echo "<p><strong>Current Database:</strong> " . $result['db_name'] . "</p>";
    
    // List tables
    echo "<h4>Tables in database:</h4>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (count($tables) > 0) {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . htmlspecialchars($table) . "</li>";
        }
        echo "</ul>";
        echo "<p><strong>Total tables:</strong> " . count($tables) . "</p>";
    } else {
        echo "<p><em>No tables found in database (ready for import)</em></p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ PDO Connection failed!</p>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";

// Test MySQLi Connection
echo "<h3>Testing MySQLi Connection...</h3>";
try {
    if (mysqli_ping($conn)) {
        echo "<p style='color: green;'>✓ MySQLi Connection successful!</p>";
        
        // Test query
        $result = mysqli_query($conn, "SELECT VERSION() as version, DATABASE() as db_name");
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            echo "<p><strong>MySQL Version:</strong> " . $row['version'] . "</p>";
            echo "<p><strong>Current Database:</strong> " . $row['db_name'] . "</p>";
        }
        
        // Check connection status
        $ssl_cipher = mysqli_get_server_info($conn);
        echo "<p><strong>Server Info:</strong> " . $ssl_cipher . "</p>";
        
    } else {
        echo "<p style='color: red;'>✗ MySQLi Connection ping failed!</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ MySQLi Connection failed!</p>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<h3>✓ Connection test complete!</h3>";
echo "<p>If both connections are successful, you're ready to import your data.</p>";
?>
