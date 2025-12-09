<?php
/**
 * Test clinic distance API
 */

// Simulate a pet owner at a different location (Colombo city center)
$petOwnerLat = 6.9271;
$petOwnerLon = 79.8612;

echo "Testing API: get-clinics-by-distance.php\n";
echo "Pet Owner Location: $petOwnerLat, $petOwnerLon (Colombo)\n\n";

$url = "http://localhost/PETVET/api/pet-owner/get-clinics-by-distance.php?latitude=$petOwnerLat&longitude=$petOwnerLon";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$response = curl_exec($ch);
$error = curl_error($ch);

if ($error) {
    echo "cURL Error: $error\n";
} else {
    echo "Response:\n";
    $data = json_decode($response, true);
    print_r($data);
}
?>
