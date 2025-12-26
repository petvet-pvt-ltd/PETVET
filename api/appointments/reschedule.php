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
$newVetId = isset($input['vet_id']) ? intval($input['vet_id']) : null;
$newType = isset($input['appointment_type']) ? trim((string)$input['appointment_type']) : null;

try {
    $pdo = db();
    
    // First, get the current appointment details to check clinic context
    $checkStmt = $pdo->prepare("SELECT clinic_id, vet_id FROM appointments WHERE id = ?");
    $checkStmt->execute([$appointmentId]);
    $currentAppointment = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$currentAppointment) {
        echo json_encode(['success' => false, 'error' => 'Appointment not found']);
        exit;
    }
    
    $clinicId = $currentAppointment['clinic_id'];
    
    // Determine the final vet_id that will be used
    $finalVetId = $newVetId !== null ? $newVetId : $currentAppointment['vet_id'];
    
    // Check for overlapping appointments (if a specific vet is assigned)
    if ($finalVetId !== null) {
        $overlapStmt = $pdo->prepare("
            SELECT id, pet_id 
            FROM appointments 
            WHERE vet_id = ? 
            AND clinic_id = ?
            AND appointment_date = ? 
            AND appointment_time = ? 
            AND id != ? 
            AND status != 'cancelled'
            LIMIT 1
        ");
        $overlapStmt->execute([$finalVetId, $clinicId, $newDate, $newTime, $appointmentId]);
        $overlap = $overlapStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($overlap) {
            // Get vet name for error message
            $vetNameStmt = $pdo->prepare("SELECT CONCAT(first_name, ' ', last_name) as name FROM users WHERE id = ?");
            $vetNameStmt->execute([$finalVetId]);
            $vetName = $vetNameStmt->fetchColumn();
            
            echo json_encode([
                'success' => false, 
                'error' => "Dr. {$vetName} already has an appointment at this time. Please select a different time or vet."
            ]);
            exit;
        }
    }
    
    // Build update query
    $updates = ["appointment_date = ?", "appointment_time = ?", "updated_at = NOW()"]; 
    $params = [$newDate, $newTime];

    // Handle vet update if provided
    if ($newVetId !== null) {
        if ($newVetId === 0) {
            $updates[] = "vet_id = NULL";
        } else {
            $updates[] = "vet_id = ?";
            $params[] = $newVetId;
        }
    }

    // Handle appointment type update if provided
    if ($newType !== null && $newType !== '') {
        $updates[] = "appointment_type = ?";
        $params[] = $newType;
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
