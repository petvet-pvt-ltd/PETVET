<?php
/**
 * Get Available Time Slots API
 * Returns available X:00 and X:30 time slots for a specific date, clinic, and vet
 * 
 * Input: clinic_id, vet_id (or 'any'), date
 * Output: Array of available time slots
 */

header('Content-Type: application/json');

// Get parameters from request
$clinic_id = isset($_GET['clinic_id']) ? intval($_GET['clinic_id']) : 0;
$vet_id = isset($_GET['vet_id']) ? $_GET['vet_id'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';

if (!$clinic_id || !$date) {
    echo json_encode([
        'success' => false,
        'error' => 'Clinic ID and date are required'
    ]);
    exit;
}

// Validate date format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid date format. Use YYYY-MM-DD'
    ]);
    exit;
}

try {
    // Get database connection
    require_once '../../config/connect.php';
    global $conn;
    
    // Get clinic operating hours for this day
    $day_of_week = strtolower(date('l', strtotime($date)));
    
    $stmt = $conn->prepare("
        SELECT start_time, end_time, is_enabled 
        FROM clinic_weekly_schedule 
        WHERE clinic_id = ? 
        AND day_of_week = ?
    ");
    $stmt->bind_param("is", $clinic_id, $day_of_week);
    $stmt->execute();
    $result = $stmt->get_result();
    $schedule = $result->fetch_assoc();
    $stmt->close();
    
    // If clinic is closed on this day
    if (!$schedule || !$schedule['is_enabled']) {
        echo json_encode([
            'success' => true,
            'available_slots' => [],
            'message' => 'Clinic is closed on this day'
        ]);
        exit;
    }
    
    // Get clinic slot duration
    $stmt = $conn->prepare("
        SELECT slot_duration_minutes 
        FROM clinic_preferences 
        WHERE clinic_id = ?
    ");
    $stmt->bind_param("i", $clinic_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $prefs = $result->fetch_assoc();
    $slot_duration = $prefs ? $prefs['slot_duration_minutes'] : 20;
    $stmt->close();
    
    // Generate all possible time slots (X:00 and X:30 only)
    $start_time = strtotime($date . ' ' . $schedule['start_time']);
    $end_time = strtotime($date . ' ' . $schedule['end_time']);
    
    $all_slots = [];
    $current_time = $start_time;
    
    while ($current_time < $end_time) {
        $time_string = date('H:i', $current_time);
        $minutes = intval(date('i', $current_time));
        
        // Only include :00 and :30 times
        if ($minutes == 0 || $minutes == 30) {
            // Check if slot + duration fits within operating hours
            $slot_end_time = $current_time + ($slot_duration * 60);
            if ($slot_end_time <= $end_time) {
                $all_slots[] = $time_string;
            }
        }
        
        $current_time += 1800; // Add 30 minutes
    }
    
    // Now check availability based on vet selection
    $available_slots = [];
    
    if ($vet_id === 'any' || $vet_id === '') {
        // "Any Vet" - show slot if ANY vet at clinic is available
        
        // Get all vets at this clinic
        $stmt = $conn->prepare("
            SELECT user_id 
            FROM clinic_staff 
            WHERE clinic_id = ? 
            AND role = 'vet'
        ");
        $stmt->bind_param("i", $clinic_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $vet_ids = [];
        while ($row = $result->fetch_assoc()) {
            $vet_ids[] = $row['user_id'];
        }
        $stmt->close();
        
        // For each slot, check if at least one vet is available
        foreach ($all_slots as $slot_time) {
            $at_least_one_vet_available = false;
            
            foreach ($vet_ids as $vid) {
                if (isVetAvailable($conn, $vid, $date, $slot_time, $slot_duration)) {
                    $at_least_one_vet_available = true;
                    break;
                }
            }
            
            if ($at_least_one_vet_available) {
                $available_slots[] = $slot_time;
            }
        }
        
    } else {
        // Specific vet - check their availability
        $vet_id = intval($vet_id);
        
        foreach ($all_slots as $slot_time) {
            if (isVetAvailable($conn, $vet_id, $date, $slot_time, $slot_duration)) {
                $available_slots[] = $slot_time;
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'available_slots' => $available_slots,
        'clinic_hours' => [
            'start' => $schedule['start_time'],
            'end' => $schedule['end_time']
        ],
        'slot_duration' => $slot_duration
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

/**
 * Check if a vet is available at a specific date and time
 */
function isVetAvailable($conn, $vet_id, $date, $time, $duration) {
    // Check for overlapping appointments (include pending, approved, and completed appointments)
    // An appointment overlaps if it starts at the same time OR if it would overlap with our slot
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count
        FROM appointments
        WHERE vet_id = ?
        AND appointment_date = ?
        AND status IN ('pending', 'approved', 'completed')
        AND (
            appointment_time = ?
            OR (
                appointment_time < ADDTIME(?, SEC_TO_TIME(? * 60))
                AND ADDTIME(appointment_time, SEC_TO_TIME(duration_minutes * 60)) > ?
            )
        )
    ");
    
    $stmt->bind_param("isssss", 
        $vet_id, 
        $date, 
        $time,
        $time,
        $duration,
        $time
    );
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    // Available if no overlapping appointments
    return $row['count'] == 0;
}
