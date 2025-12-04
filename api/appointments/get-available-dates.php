<?php
/**
 * Get Available Dates API
 * Returns disabled dates for calendar based on clinic schedule and blocked days
 * 
 * Input: clinic_id
 * Output: Array of disabled dates
 */

header('Content-Type: application/json');

// Get clinic_id from request
$clinic_id = isset($_GET['clinic_id']) ? intval($_GET['clinic_id']) : 0;

if (!$clinic_id) {
    echo json_encode([
        'success' => false,
        'error' => 'Clinic ID is required'
    ]);
    exit;
}

try {
    // Get database connection
    require_once '../../config/connect.php';
    global $conn;
    
    // Calculate date range: today to 30 days from now
    $today = date('Y-m-d');
    $max_date = date('Y-m-d', strtotime('+30 days'));
    
    // Get clinic weekly schedule
    $stmt = $conn->prepare("
        SELECT day_of_week, is_enabled 
        FROM clinic_weekly_schedule 
        WHERE clinic_id = ?
    ");
    $stmt->bind_param("i", $clinic_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $weekly_schedule = [];
    while ($row = $result->fetch_assoc()) {
        $weekly_schedule[$row['day_of_week']] = $row['is_enabled'];
    }
    $stmt->close();
    
    // Get clinic blocked days
    $stmt = $conn->prepare("
        SELECT blocked_date 
        FROM clinic_blocked_days 
        WHERE clinic_id = ? 
        AND blocked_date >= ?
        AND blocked_date <= ?
    ");
    $stmt->bind_param("iss", $clinic_id, $today, $max_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $blocked_dates = [];
    while ($row = $result->fetch_assoc()) {
        $blocked_dates[] = $row['blocked_date'];
    }
    $stmt->close();
    
    // Build array of all disabled dates
    $disabled_dates = [];
    
    // Iterate through each day in range
    $current_date = new DateTime($today);
    $end_date = new DateTime($max_date);
    
    while ($current_date <= $end_date) {
        $date_string = $current_date->format('Y-m-d');
        $day_of_week = strtolower($current_date->format('l')); // monday, tuesday, etc.
        
        $is_disabled = false;
        
        // Check if this day is disabled in weekly schedule
        if (isset($weekly_schedule[$day_of_week]) && !$weekly_schedule[$day_of_week]) {
            $is_disabled = true;
        }
        
        // Check if this date is in blocked days
        if (in_array($date_string, $blocked_dates)) {
            $is_disabled = true;
        }
        
        if ($is_disabled) {
            $disabled_dates[] = $date_string;
        }
        
        $current_date->modify('+1 day');
    }
    
    echo json_encode([
        'success' => true,
        'disabled_dates' => $disabled_dates,
        'min_date' => $today,
        'max_date' => $max_date
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
