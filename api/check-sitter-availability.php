<?php
/**
 * Check Sitter Availability for a specific date and time
 * Uses service_provider_weekly_schedule + service_provider_blocked_dates (role_type = 'sitter')
 */

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
require_once dirname(__DIR__) . '/config/connect.php';

$sitter_id = $_GET['sitter_id'] ?? null;
$date = $_GET['date'] ?? null;
$time = $_GET['time'] ?? null;

if (!$sitter_id || !$date) {
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
    $dayOfWeek = date('l', strtotime($date));

    // Check 1: Weekly schedule
    $stmt = $db->prepare("
        SELECT is_available, start_time, end_time
        FROM service_provider_weekly_schedule
        WHERE user_id = ? AND role_type = 'sitter' AND day_of_week = ?
    ");
    $stmt->execute([$sitter_id, $dayOfWeek]);
    $weeklySchedule = $stmt->fetch(PDO::FETCH_ASSOC);

    // Defaults if not set (match service-provider-availability UI defaults)
    // - Sunday: unavailable
    // - Mon–Fri: 09:00–18:00
    // - Saturday: 10:00–16:00
    $isAvailableDay = in_array($dayOfWeek, ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'], true);
    $startTime = $dayOfWeek === 'Saturday' ? '10:00:00' : '09:00:00';
    $endTime = $dayOfWeek === 'Saturday' ? '16:00:00' : '18:00:00';

    if ($weeklySchedule) {
        $isAvailableDay = (bool)$weeklySchedule['is_available'];
        $startTime = $weeklySchedule['start_time'];
        $endTime = $weeklySchedule['end_time'];
    }

    if (!$isAvailableDay) {
        echo json_encode([
            'success' => true,
            'available' => false,
            'message' => 'Sitter is not available on ' . $dayOfWeek . 's'
        ]);
        exit;
    }

    // Check time within working hours (only if time provided)
    $timeFormatted = null;
    if ($time) {
        $timeFormatted = date('H:i:s', strtotime($time));
        if ($timeFormatted < $startTime || $timeFormatted > $endTime) {
            $start = date('g:i A', strtotime($startTime));
            $end = date('g:i A', strtotime($endTime));
            echo json_encode([
                'success' => true,
                'available' => false,
                'message' => 'Sitter is available from ' . $start . ' to ' . $end . ' on ' . $dayOfWeek . 's'
            ]);
            exit;
        }
    }

    // Check 2: Blocked dates
    $stmt = $db->prepare("
        SELECT block_type, block_time
        FROM service_provider_blocked_dates
        WHERE user_id = ? AND role_type = 'sitter' AND blocked_date = ?
    ");
    $stmt->execute([$sitter_id, $date]);
    $blockedDate = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($blockedDate) {
        if ($blockedDate['block_type'] === 'full-day') {
            echo json_encode([
                'success' => true,
                'available' => false,
                'message' => 'Sitter is unavailable on this date'
            ]);
            exit;
        } elseif ($blockedDate['block_type'] === 'before' || $blockedDate['block_type'] === 'after') {
            // Half-day blocks are stored as availability windows:
            // - 'before' => available before the time (unavailable at/after)
            // - 'after'  => available after the time  (unavailable at/before)
            if ($time) {
                $blockedTime = $blockedDate['block_time'];
                if ($blockedTime) {
                    $blockedTimeFormatted = substr($blockedTime, 0, 5);
                    $selectedTime = substr($timeFormatted, 0, 5);

                    if ($blockedDate['block_type'] === 'before' && $selectedTime >= $blockedTimeFormatted) {
                        echo json_encode([
                            'success' => true,
                            'available' => false,
                            'message' => 'Sitter is unavailable after ' . date('g:i A', strtotime($blockedTime)) . ' on this date.'
                        ]);
                        exit;
                    } elseif ($blockedDate['block_type'] === 'after' && $selectedTime <= $blockedTimeFormatted) {
                        echo json_encode([
                            'success' => true,
                            'available' => false,
                            'message' => 'Sitter is unavailable before ' . date('g:i A', strtotime($blockedTime)) . ' on this date.'
                        ]);
                        exit;
                    }
                }
            }
        }
    }

    echo json_encode([
        'success' => true,
        'available' => true,
        'message' => 'This time slot is available'
    ]);
} catch (Exception $e) {
    error_log('Sitter availability check error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error checking availability'
    ]);
}
