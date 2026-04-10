<?php
require_once 'config/connect.php';

echo "<h2>sell_pet_listings Table Structure:</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Column</th><th>Type</th></tr>";

$result = mysqli_query($conn, 'DESCRIBE sell_pet_listings');
while($row = mysqli_fetch_assoc($result)) {
    echo "<tr><td>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td></tr>";
}
echo "</table>";

echo "<h2>Sample Data (first row):</h2>";
$sampleResult = mysqli_query($conn, 'SELECT * FROM sell_pet_listings LIMIT 1');
if ($sample = mysqli_fetch_assoc($sampleResult)) {
    echo "<pre>";
    print_r(array_keys($sample));
    echo "</pre>";
    echo "<pre>";
    print_r($sample);
    echo "</pre>";
} else {
    echo "No data in table";
}
?>
