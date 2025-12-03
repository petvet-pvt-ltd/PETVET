<?php
require_once '../../config/connect.php';
require_once '../../config/auth_helper.php';

header('Content-Type: application/json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is authenticated and is receptionist or clinic manager
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$userRole = currentRole();
if (!in_array($userRole, ['receptionist', 'clinic_manager'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['appointment_id']) || !isset($input['date']) || !isset($input['time'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

$appointmentId = intval($input['appointment_id']);
$newDate = $input['date'];
$newTime = $input['time'];
$newVet = isset($input['vet']) ? $input['vet'] : null;

try {
    $pdo = db();
    
    // Build update query
    $updates = ["appointment_date = ?", "appointment_time = ?", "updated_at = NOW()"];
    $params = [$newDate, $newTime];
    
    // Handle vet update if provided
    if ($newVet !== null) {
        if ($newVet === 'Any Available Vet' || $newVet === '0') {
            $updates[] = "vet_id = NULL";
        } else {
            // Get vet user ID by name
            $vetStmt = $pdo->prepare("SELECT u.id FROM users u JOIN user_roles ur ON u.id = ur.user_id JOIN roles r ON ur.role_id = r.id WHERE CONCAT(u.first_name, ' ', u.last_name) = ? AND r.role_name = 'vet' LIMIT 1");
            $vetStmt->execute([$newVet]);
            $vetId = $vetStmt->fetchColumn();
            
            if ($vetId) {
                $updates[] = "vet_id = ?";
                $params[] = $vetId;
            }
        }
    }
    
    $params[] = $appointmentId;
    
    $query = "UPDATE appointments SET " . implode(", ", $updates) . " WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $success = $stmt->execute($params);
    
    if ($success && $stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Appointment rescheduled successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Appointment not found or no changes made'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
