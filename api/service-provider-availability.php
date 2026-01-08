<?php
/**
 * Service Provider Availability API
 * Handles weekly schedule and blocked dates for trainers, sitters, breeders, and groomers
 */

header('Content-Type: application/json');
require_once dirname(__DIR__) . '/config/connect.php';
require_once dirname(__DIR__) . '/config/auth_helper.php';

// Ensure user is authenticated
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';
$role_type = $_POST['role_type'] ?? $_GET['role_type'] ?? '';

// Validate role type
$valid_roles = ['trainer', 'sitter', 'breeder', 'groomer'];
if (!in_array($role_type, $valid_roles)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid role type']);
    exit;
}

// Verify user has this role
$role_check = $conn->prepare("
    SELECT ur.id 
    FROM user_roles ur
    JOIN roles r ON ur.role_id = r.id
    WHERE ur.user_id = ? AND r.role_name = ? AND ur.is_active = 1
");
$role_check->bind_param('is', $user_id, $role_type);
$role_check->execute();
if ($role_check->get_result()->num_rows === 0) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'You do not have access to this role']);
    exit;
}

// Handle different actions
switch ($action) {
    case 'get_schedule':
        getWeeklySchedule($conn, $user_id, $role_type);
        break;
    
    case 'save_schedule':
        saveWeeklySchedule($conn, $user_id, $role_type);
        break;
    
    case 'get_blocked_dates':
        getBlockedDates($conn, $user_id, $role_type);
        break;
    
    case 'add_blocked_date':
        addBlockedDate($conn, $user_id, $role_type);
        break;
    
    case 'remove_blocked_date':
        removeBlockedDate($conn, $user_id, $role_type);
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

/**
 * Get weekly schedule for a service provider
 */
function getWeeklySchedule($conn, $user_id, $role_type) {
    $stmt = $conn->prepare("
        SELECT day_of_week, is_available, start_time, end_time
        FROM service_provider_weekly_schedule
        WHERE user_id = ? AND role_type = ?
        ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')
    ");
    $stmt->bind_param('is', $user_id, $role_type);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $schedule = [];
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    
    // Create a map of existing data
    $existing = [];
    while ($row = $result->fetch_assoc()) {
        $existing[$row['day_of_week']] = [
            'day' => $row['day_of_week'],
            'enabled' => (bool)$row['is_available'],
            'start' => substr($row['start_time'], 0, 5), // Format as HH:MM
            'end' => substr($row['end_time'], 0, 5)
        ];
    }
    
    // Return all days with defaults for missing days
    foreach ($days as $day) {
        if (isset($existing[$day])) {
            $schedule[] = $existing[$day];
        } else {
            // Default schedule
            $schedule[] = [
                'day' => $day,
                'enabled' => in_array($day, ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']),
                'start' => $day === 'Saturday' ? '10:00' : '09:00',
                'end' => $day === 'Saturday' ? '16:00' : '18:00'
            ];
        }
    }
    
    echo json_encode(['success' => true, 'schedule' => $schedule]);
}

/**
 * Save weekly schedule for a service provider
 */
function saveWeeklySchedule($conn, $user_id, $role_type) {
    $schedule = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($schedule['schedule']) || !is_array($schedule['schedule'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid schedule data']);
        return;
    }
    
    $conn->begin_transaction();
    
    try {
        // Delete existing schedule for this user and role
        $delete_stmt = $conn->prepare("
            DELETE FROM service_provider_weekly_schedule
            WHERE user_id = ? AND role_type = ?
        ");
        $delete_stmt->bind_param('is', $user_id, $role_type);
        $delete_stmt->execute();
        
        // Insert new schedule
        $insert_stmt = $conn->prepare("
            INSERT INTO service_provider_weekly_schedule 
            (user_id, role_type, day_of_week, is_available, start_time, end_time)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($schedule['schedule'] as $day) {
            $is_available = $day['enabled'] ? 1 : 0;
            $insert_stmt->bind_param(
                'ississ',
                $user_id,
                $role_type,
                $day['day'],
                $is_available,
                $day['start'],
                $day['end']
            );
            $insert_stmt->execute();
        }
        
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Schedule saved successfully']);
        
    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save schedule: ' . $e->getMessage()]);
    }
}

/**
 * Get blocked dates for a service provider
 */
function getBlockedDates($conn, $user_id, $role_type) {
    $stmt = $conn->prepare("
        SELECT id, blocked_date, block_type, block_time, reason
        FROM service_provider_blocked_dates
        WHERE user_id = ? AND role_type = ?
        ORDER BY blocked_date ASC
    ");
    $stmt->bind_param('is', $user_id, $role_type);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $blocked_dates = [];
    while ($row = $result->fetch_assoc()) {
        $blocked_dates[] = [
            'id' => $row['id'],
            'date' => $row['blocked_date'],
            'type' => $row['block_type'],
            'time' => $row['block_time'] ? substr($row['block_time'], 0, 5) : null,
            'reason' => $row['reason']
        ];
    }
    
    echo json_encode(['success' => true, 'blocked_dates' => $blocked_dates]);
}

/**
 * Add a blocked date for a service provider
 */
function addBlockedDate($conn, $user_id, $role_type) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $date = $data['date'] ?? '';
    $type = $data['type'] ?? 'full-day';
    $time = $data['time'] ?? null;
    $reason = $data['reason'] ?? null;
    
    if (empty($date)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Date is required']);
        return;
    }
    
    // Validate block type
    if (!in_array($type, ['full-day', 'before', 'after'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid block type']);
        return;
    }
    
    try {
        // Check if date is already blocked
        $check_stmt = $conn->prepare("
            SELECT id FROM service_provider_blocked_dates
            WHERE user_id = ? AND role_type = ? AND blocked_date = ?
        ");
        $check_stmt->bind_param('iss', $user_id, $role_type, $date);
        $check_stmt->execute();
        $existing = $check_stmt->get_result();
        
        if ($existing->num_rows > 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'This date is already blocked']);
            return;
        }
        
        // Insert new blocked date
        $stmt = $conn->prepare("
            INSERT INTO service_provider_blocked_dates 
            (user_id, role_type, blocked_date, block_type, block_time, reason)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('isssss', $user_id, $role_type, $date, $type, $time, $reason);
        $stmt->execute();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Blocked date added successfully',
            'id' => $conn->insert_id
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to add blocked date: ' . $e->getMessage()]);
    }
}

/**
 * Remove a blocked date for a service provider
 */
function removeBlockedDate($conn, $user_id, $role_type) {
    $data = json_decode(file_get_contents('php://input'), true);
    $date = $data['date'] ?? '';
    
    if (empty($date)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Date is required']);
        return;
    }
    
    try {
        $stmt = $conn->prepare("
            DELETE FROM service_provider_blocked_dates
            WHERE user_id = ? AND role_type = ? AND blocked_date = ?
        ");
        $stmt->bind_param('iss', $user_id, $role_type, $date);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Blocked date removed successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Blocked date not found']);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to remove blocked date: ' . $e->getMessage()]);
    }
}
