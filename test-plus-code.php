<?php
/**
 * Test Plus Code Geocoding
 */

require_once __DIR__ . '/helpers/MapsHelper.php';

$mapsHelper = new MapsHelper();

echo "Testing Plus Code: 2XGH+X6 Kadawatha\n\n";

$result = $mapsHelper->geocode('2XGH+X6 Kadawatha');

echo "Result:\n";
print_r($result);
echo "\n";

// Also test the API endpoint
echo "\n--- Testing API Endpoint ---\n";
echo "URL: /api/pet-owner/get-clinics-by-distance.php?latitude=6.9271&longitude=79.8612\n";
echo "Visit this URL in your browser to see the full response.\n";
?>
