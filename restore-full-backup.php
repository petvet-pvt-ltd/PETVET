<?php
// Full Database Restore Script - Complete Rebuild
$host = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
$port = 4000;
$username = '2iYmekB7i4tHWm7.root';
$password = 'Po3TdFdOuAqvbtCn';
$database = 'petvetDB';
$caPath = __DIR__ . '/database/CA/isrgrootx1.pem';
$backupFile = __DIR__ . '';

echo "🔄 Starting FULL database restore...\n";
echo "Host: $host:$port\n";
echo "Database: $database\n";
echo "Backup File: $backupFile\n\n";

if (!file_exists($backupFile)) {
    die("❌ Error: Backup file not found at: $backupFile\n");
}

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $options = [
        PDO::MYSQL_ATTR_SSL_CA => $caPath,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    echo "✅ Connected to database successfully\n\n";
    
    // Disable foreign key checks
    echo "📋 Disabling foreign key checks...\n";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // Read the entire backup file
    echo "📖 Reading backup file...\n";
    $sqlContent = file_get_contents($backupFile);
    
    // Parse all SQL statements (DROP TABLE, CREATE TABLE, INSERT)
    // Split by semicolon but be careful with multi-line statements
    $statements = [];
    $currentStatement = '';
    
    foreach (explode("\n", $sqlContent) as $line) {
        $line = trim($line);
        
        // Skip comments and empty lines
        if (empty($line) || strpos($line, '--') === 0) {
            continue;
        }
        
        $currentStatement .= ' ' . $line;
        
        // Check if statement ends with semicolon
        if (substr($line, -1) === ';') {
            $stmt = trim($currentStatement);
            if (!empty($stmt)) {
                $statements[] = $stmt;
            }
            $currentStatement = '';
        }
    }
    
    // Add any remaining statement
    if (!empty($currentStatement)) {
        $statements[] = trim($currentStatement);
    }
    
    echo "Found " . count($statements) . " SQL statements\n\n";
    
    // Execute all statements
    echo "⚙️  Executing SQL statements...\n";
    $successCount = 0;
    $errorCount = 0;
    $errors = [];
    
    foreach ($statements as $index => $statement) {
        try {
            if (!empty($statement)) {
                $pdo->exec($statement);
                $successCount++;
                
                if (($index + 1) % 20 === 0) {
                    echo "  ✓ Executed " . ($index + 1) . " statements...\n";
                }
            }
        } catch (Exception $e) {
            $errorCount++;
            if (count($errors) < 10) {
                $errors[] = [
                    'statement' => ($index + 1),
                    'error' => $e->getMessage(),
                    'code' => substr($statement, 0, 60)
                ];
            }
        }
    }
    
    // Re-enable foreign key checks
    echo "\n🔗 Re-enabling foreign key checks...\n";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    // Summary
    echo "\n";
    echo "✅ Restore completed!\n";
    echo "   Total statements: " . count($statements) . "\n";
    echo "   Successful: $successCount\n";
    echo "   Failed: $errorCount\n";
    
    if (count($errors) > 0) {
        echo "\n⚠️  Errors encountered:\n";
        foreach ($errors as $error) {
            echo "   Statement #" . $error['statement'] . "\n";
            echo "   Error: " . $error['error'] . "\n";
            echo "   Code: " . $error['code'] . "...\n\n";
        }
    }
    
    // Verify structure
    echo "📊 Database verification:\n";
    $result = $pdo->query("SHOW TABLES");
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    echo "   Total tables: " . count($tables) . "\n";
    
    // Check if favourite_food exists in pets table
    $result = $pdo->query("DESCRIBE pets");
    $columns = $result->fetchAll(PDO::FETCH_COLUMN);
    $has_favourite_food = in_array('favourite_food', $columns);
    echo "   favourite_food column in pets: " . ($has_favourite_food ? "✓ YES" : "✗ NO") . "\n";
    
    // Data counts
    echo "\n📈 Data in tables:\n";
    $tables_to_check = ['users', 'pets', 'clinics', 'appointments', 'products'];
    foreach ($tables_to_check as $table) {
        try {
            $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
            echo "   $table: " . number_format($count) . " records\n";
        } catch (Exception $e) {
            echo "   $table: ERROR\n";
        }
    }
    
} catch (PDOException $e) {
    die("❌ Connection Error: " . $e->getMessage() . "\n");
}

echo "\n✨ Database restore finished!\n";
?>
