<?php
// TiDB database connection
$dsn = "mysql:host=gateway01.ap-southeast-1.prod.aws.tidbcloud.com;port=4000;dbname=petvetDB;charset=utf8mb4";
$pdo = new PDO($dsn, '2iYmekB7i4tHWm7.root', 'Po3TdFdOuAqvbtCn', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_SSL_CA => __DIR__ . '/database/CA/isrgrootx1.pem',
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true,
]);

// Count records before deletion
$stmt = $pdo->query('SELECT * FROM medical_records');
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query('SELECT * FROM prescriptions');
$prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query('SELECT * FROM vaccinations');
$vaccinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "================================\n";
echo "RECORD DELETION SUMMARY\n";
echo "================================\n";
echo "Medical Records: " . count($records) . "\n";
echo "Prescriptions: " . count($prescriptions) . "\n";
echo "Vaccinations: " . count($vaccinations) . "\n";

// Count cancelled appointments
$stmt = $pdo->query("SELECT * FROM appointments WHERE status = 'cancelled'");
$cancelled = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Cancelled Appointments: " . count($cancelled) . "\n";
echo "TOTAL: " . (count($records) + count($prescriptions) + count($vaccinations) + count($cancelled)) . "\n\n";

// Delete all records
try {
    $pdo->exec("DELETE FROM appointments WHERE status = 'cancelled'");
    echo "✓ Deleted all cancelled appointments\n";
    
    $pdo->exec('DELETE FROM prescriptions');
    echo "✓ Deleted all prescriptions\n";
    
    $pdo->exec('DELETE FROM medical_records');
    echo "✓ Deleted all medical records\n";
    
    $pdo->exec('DELETE FROM vaccinations');
    echo "✓ Deleted all vaccinations\n";
    
    echo "\n✓ All records deleted successfully!\n";
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>
