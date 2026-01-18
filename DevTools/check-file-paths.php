<?php
require_once __DIR__ . '/../config/connect.php';

echo "=== CHECKING FILE PATHS IN DATABASE ===\n\n";

// Check medical records
$result = mysqli_query($conn, "SELECT reports FROM medical_records WHERE reports IS NOT NULL LIMIT 3");
echo "Medical Records:\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['reports'] . "\n";
}

echo "\n";

// Check vaccinations
$result = mysqli_query($conn, "SELECT reports FROM vaccinations WHERE reports IS NOT NULL LIMIT 3");
echo "Vaccinations:\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['reports'] . "\n";
}

echo "\n";

// Check prescriptions
$result = mysqli_query($conn, "SELECT reports FROM prescriptions WHERE reports IS NOT NULL LIMIT 3");
echo "Prescriptions:\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['reports'] . "\n";
}
