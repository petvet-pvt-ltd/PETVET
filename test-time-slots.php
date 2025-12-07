<?php
// Test the API
$url = "http://localhost/PETVET/api/appointments/get-available-times.php?clinic_id=1&vet_id=19&date=2025-12-17";

$response = file_get_contents($url);
$data = json_decode($response, true);

echo "Testing time slots for Emily Rodriguez (ID: 19) on Dec 17, 2025:\n\n";
echo "Success: " . ($data['success'] ? 'Yes' : 'No') . "\n";
echo "Available Slots: " . count($data['available_slots'] ?? []) . "\n\n";

if (isset($data['available_slots'])) {
    echo "Slots:\n";
    foreach ($data['available_slots'] as $slot) {
        echo "- $slot\n";
    }
    
    // Check if 11:00 is in the list
    if (in_array('11:00', $data['available_slots'])) {
        echo "\n❌ ERROR: 11:00 AM is showing as available but there's a pending appointment!\n";
    } else {
        echo "\n✅ CORRECT: 11:00 AM is blocked (has pending appointment)\n";
    }
}
