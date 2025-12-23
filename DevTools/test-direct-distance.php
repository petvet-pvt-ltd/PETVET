<?php
/**
 * Direct test of MapsHelper distance calculation
 */

require_once __DIR__ . '/helpers/MapsHelper.php';

$mapsHelper = new MapsHelper();

// Test coordinates
$petOwnerLat = 6.9271;  // Colombo city
$petOwnerLon = 79.8612;

$clinicLat = 7.0011;    // Kadawatha
$clinicLon = 79.9553;

echo "Testing Distance Calculation\n";
echo "From: $petOwnerLat, $petOwnerLon (Colombo)\n";
echo "To: $clinicLat, $clinicLon (Kadawatha)\n\n";

$result = $mapsHelper->getDistance($petOwnerLat, $petOwnerLon, $clinicLat, $clinicLon);

echo "Result:\n";
print_r($result);

echo "\n\nExpected: ~10-15 km (Colombo to Kadawatha)\n";
?>
