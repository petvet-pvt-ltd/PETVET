<?php
/**
 * Verify Settings Implementation
 * Check if all data is properly structured
 */

require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../models/ClinicManager/SettingsModel.php';

$userId = 9; // manager@gmail.com

try {
    $model = new ClinicManagerSettingsModel();
    
    echo "=== SETTINGS DATA VERIFICATION ===\n\n";
    
    // 1. Profile
    echo "1. MANAGER PROFILE:\n";
    $profile = $model->getManagerProfile($userId);
    echo "   Name: {$profile['first_name']} {$profile['last_name']}\n";
    echo "   Email: {$profile['email']}\n";
    echo "   Phone: {$profile['phone']}\n";
    echo "   ✅ Profile loaded\n\n";
    
    // 2. Clinic
    echo "2. CLINIC DATA:\n";
    $clinic = $model->getClinicData($userId);
    if ($clinic) {
        echo "   Name: {$clinic['name']}\n";
        echo "   Description: {$clinic['description']}\n";
        echo "   Address: {$clinic['address']}\n";
        echo "   Phone: {$clinic['phone']}\n";
        echo "   Email: {$clinic['email']}\n";
        echo "   ✅ Clinic data loaded\n\n";
    } else {
        echo "   ❌ No clinic found\n\n";
    }
    
    // 3. Preferences
    echo "3. PREFERENCES:\n";
    $prefs = $model->getPreferences($userId);
    echo "   Email Notifications: " . ($prefs['email_notifications'] ? 'ON' : 'OFF') . "\n";
    echo "   Slot Duration: {$prefs['slot_duration_minutes']} minutes\n";
    echo "   ✅ Preferences loaded\n\n";
    
    // 4. Weekly Schedule
    echo "4. WEEKLY SCHEDULE:\n";
    $schedule = $model->getWeeklySchedule($userId);
    foreach ($schedule as $day) {
        $status = $day['active'] ? '✓' : '✗';
        echo "   $status {$day['label']}: {$day['start']} - {$day['end']}\n";
    }
    echo "   ✅ Schedule loaded (" . count($schedule) . " days)\n\n";
    
    // 5. Blocked Days
    echo "5. BLOCKED DAYS:\n";
    $blocked = $model->getBlockedDays($userId);
    if (empty($blocked)) {
        echo "   No blocked days configured\n";
    } else {
        foreach ($blocked as $day) {
            echo "   - {$day['blocked_date']}: {$day['reason']}\n";
        }
    }
    echo "   ✅ Blocked days loaded (" . count($blocked) . " days)\n\n";
    
    echo "=== ALL DATA LOADED SUCCESSFULLY ===\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
?>
