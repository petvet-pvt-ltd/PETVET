<?php
require_once __DIR__ . '/../config/connect.php';

echo "========== Appointments Table ==========\n";
$result = $conn->query("DESCRIBE appointments");
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
