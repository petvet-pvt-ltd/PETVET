<?php
echo "=== TESTING GET-AVAILABLE-DATES API (FIXED) ===\n\n";

// Make request to the API
$url = 'http://localhost/petvet/api/appointments/get-available-dates.php?clinic_id=1';
$response = file_get_contents($url);
$data = json_decode($response, true);

echo "API Response:\n";
echo json_encode($data, JSON_PRETTY_PRINT);

if ($data['success']) {
    echo "\n\n=== DISABLED DATES ===\n";
    foreach ($data['disabled_dates'] as $date) {
        $timestamp = strtotime($date);
        $dayOfWeek = date('l', $timestamp);
        echo "$date ($dayOfWeek)\n";
    }
    
    echo "\n=== CHECKING FOR SATURDAYS AND DEC 25 ===\n";
    $saturdays = array_filter($data['disabled_dates'], function($date) {
        return date('l', strtotime($date)) === 'Saturday';
    });
    
    $hasDec25 = in_array('2025-12-25', $data['disabled_dates']);
    
    echo "Saturdays found: " . count($saturdays) . "\n";
    echo "December 25 blocked: " . ($hasDec25 ? 'YES ✓' : 'NO ✗') . "\n";
}
