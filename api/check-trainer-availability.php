<?php
/**
 * Check Trainer Availability for a specific date and time
 */

header('Content-Type: application/json');
require_once dirname(__DIR__) . '/config/connect.php';

$trainer_id = $_GET['trainer_id'] ?? null;
$date = $_GET['date'] ?? null;
$time = $_GET['time'] ?? null;

if (!$trainer_id || !$date) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

try {
    $db = db();
    
    // Check if date is in the past
    $today = date('Y-m-d');
    if ($date < $today) {
        echo json_encode([
            'success' => true,
            'available' => false,
            'message' => 'Cannot select past dates. Please choose a future date.'
        ]);
        exit;
    }
    
    // If date is today, check if time is in the past
    if ($time && $date === $today) {
        $currentTime = date('H:i:s');
        $selectedTime = date('H:i:s', strtotime($time));
        if ($selectedTime < $currentTime) {
            echo json_encode([
                'success' => true,
                'available' => false,
                'message' => 'Cannot select past times. Please choose a future time.'
            ]);
            exit;
        }
    }
    
    // Get day of week from date
    $dayOfWeek = date('l', strtotime($date)); // e.g., "Monday"
    
    // Check 1: Weekly schedule - is the trainer available on this day and time?
    $stmt = $db->prepare("
        SELECT is_available, start_time, end_time
        FROM service_provider_weekly_schedule
        WHERE user_id = ? AND role_type = 'trainer' AND day_of_week = ?
    ");
    $stmt->execute([$trainer_id, $dayOfWeek]);
    $weeklySchedule = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // If no schedule found, assume default business hours (9 AM - 6 PM)
    $isAvailableDay = true;
    $startTime = '09:00:00';
    $endTime = '18:00:00';
    
    if ($weeklySchedule) {
        $isAvailableDay = (bool)$weeklySchedule['is_available'];
        $startTime = $weeklySchedule['start_time'];
        $endTime = $weeklySchedule['end_time'];
    }
    
    if (!$isAvailableDay) {
        echo json_encode([
            'success' => true,
            'available' => false,
            'message' => 'Trainer is not available on ' . $dayOfWeek . 's'
        ]);
        exit;
    }
    
    // Check if time is within working hours (only if time is provided)
    $timeFormatted = null;
    if ($time) {
        $timeFormatted = date('H:i:s', strtotime($time));
        if ($timeFormatted < $startTime || $timeFormatted > $endTime) {
            $start = date('g:i A', strtotime($startTime));
            $end = date('g:i A', strtotime($endTime));
            echo json_encode([
                'success' => true,
                'available' => false,
                'message' => 'Trainer is available from ' . $start . ' to ' . $end . ' on ' . $dayOfWeek . 's'
            ]);
            exit;
        }
    }
    
    // Check 2: Blocked dates - is this specific date blocked?
    $stmt = $db->prepare("
        SELECT block_type, block_time
        FROM service_provider_blocked_dates
        WHERE user_id = ? AND role_type = 'trainer' AND blocked_date = ?
    ");
    $stmt->execute([$trainer_id, $date]);
    $blockedDate = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Debug logging
    error_log("Checking blocked dates for trainer_id: $trainer_id, date: $date");
    error_log("Blocked date result: " . json_encode($blockedDate));
    
    if ($blockedDate) {
        if ($blockedDate['block_type'] === 'full-day') {
            echo json_encode([
                'success' => true,
                'available' => false,
                'message' => 'Trainer is unavailable on this date'
            ]);
            exit;
        } elseif ($blockedDate['block_type'] === 'before' || $blockedDate['block_type'] === 'after') {
            // Half-day blocks are stored as availability windows:
            // - 'before' => available before the time (unavailable at/after)
            // - 'after'  => available after the time  (unavailable at/before)
            // Only enforce when time is provided.
            if ($time) {
                $blockedTime = $blockedDate['block_time'];
                if ($blockedTime) {
                    $blockedTimeFormatted = substr($blockedTime, 0, 5);
                    $selectedTime = substr($timeFormatted, 0, 5);
                    
                    if ($blockedDate['block_type'] === 'before' && $selectedTime >= $blockedTimeFormatted) {
                        echo json_encode([
                            'success' => true,
                            'available' => false,
                            'message' => 'Trainer is unavailable after ' . date('g:i A', strtotime($blockedTime)) . ' on this date.'
                        ]);
                        exit;
                    } elseif ($blockedDate['block_type'] === 'after' && $selectedTime <= $blockedTimeFormatted) {
                        echo json_encode([
                            'success' => true,
                            'available' => false,
                            'message' => 'Trainer is unavailable before ' . date('g:i A', strtotime($blockedTime)) . ' on this date.'
                        ]);
                        exit;
                    }
                }
            }
        }
    }
    
    // Check 3: Existing appointments at this time (skip if table doesn't exist yet)
    // TODO: Uncomment when trainer_appointments table is created
    /*
    $stmt = $db->prepare("
        SELECT COUNT(*) as count
        FROM trainer_appointments
        WHERE trainer_id = ? 
        AND appointment_date = ? 
        AND appointment_time = ?
        AND status IN ('pending', 'accepted')
    ");
    $stmt->execute([$trainer_id, $date, $timeFormatted]);
    $existingAppointment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingAppointment && $existingAppointment['count'] > 0) {
        echo json_encode([
            'success' => true,
            'available' => false,
            'message' => 'This time slot is already booked. Please select another time.'
        ]);
        exit;
    }
    */
    
    // All checks passed - available!
    echo json_encode([
        'success' => true,
        'available' => true,
        'message' => 'This time slot is available'
    ]);
    
} catch (Exception $e) {
    error_log("Availability check error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error checking availability'
    ]);
}
