<?php
require_once '../config/connect.php';
$pdo = db();

echo "=== APPOINTMENTS TABLE STRUCTURE ===\n\n";
$stmt = $pdo->query('DESCRIBE appointments');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo sprintf("%-30s %-20s %s\n", 
        $row['Field'], 
        $row['Type'], 
        $row['Null'] === 'YES' ? 'NULL' : 'NOT NULL'
    );
}

echo "\n=== SAMPLE APPOINTMENT RECORD ===\n\n";
$stmt = $pdo->query('SELECT * FROM appointments LIMIT 1');
$sample = $stmt->fetch(PDO::FETCH_ASSOC);
if ($sample) {
    foreach ($sample as $key => $value) {
        echo sprintf("%-30s %s\n", $key . ':', $value);
    }
}

echo "\n=== CHECKING PAYMENTS TABLE ===\n\n";
$stmt = $pdo->query("SHOW TABLES LIKE 'payments'");
if ($stmt->rowCount() > 0) {
    echo "Payments table exists.\n\n";
    $stmt = $pdo->query('DESCRIBE payments');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("%-30s %-20s %s\n", 
            $row['Field'], 
            $row['Type'], 
            $row['Null'] === 'YES' ? 'NULL' : 'NOT NULL'
        );
    }
} else {
    echo "Payments table does NOT exist.\n";
}
