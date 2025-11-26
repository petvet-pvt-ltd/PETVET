<?php
header('Content-Type: application/json');
session_start();

// Authentication check
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized. Please login.']);
    exit;
}

require_once __DIR__ . '/../../config/connect.php';

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate required fields
$required = ['pet_id', 'clinic_id', 'appointment_type', 'symptoms', 'appointment_date', 'appointment_time'];
foreach ($required as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => "Missing required field: $field"
        ]);
        exit;
    }
}

$petId = intval($data['pet_id']);
$clinicId = intval($data['clinic_id']);
$vetId = isset($data['vet_id']) && $data['vet_id'] != '0' ? intval($data['vet_id']) : null;
$appointmentType = $data['appointment_type'];
$symptoms = $data['symptoms'];
$appointmentDate = $data['appointment_date'];
$appointmentTime = $data['appointment_time'];
$petOwnerId = $_SESSION['user_id'];

try {
    $db = db();
    
    // Verify the pet belongs to the current user
    $stmt = $db->prepare("SELECT id FROM pets WHERE id = ? AND user_id = ?");
    $stmt->execute([$petId, $petOwnerId]);
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'error' => 'You do not have permission to book appointments for this pet.'
        ]);
        exit;
    }
    
    // Check if the time slot is already booked
    $checkStmt = $db->prepare("
        SELECT id FROM appointments 
        WHERE clinic_id = ? 
        AND appointment_date = ? 
        AND appointment_time = ?
        AND status NOT IN ('declined', 'cancelled')
        " . ($vetId ? "AND (vet_id = ? OR vet_id IS NULL)" : "")
    );
    
    if ($vetId) {
        $checkStmt->execute([$clinicId, $appointmentDate, $appointmentTime, $vetId]);
    } else {
        $checkStmt->execute([$clinicId, $appointmentDate, $appointmentTime]);
    }
    
    if ($checkStmt->fetch()) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'error' => 'This time slot is already booked. Please choose another time.'
        ]);
        exit;
    }
    
    // Insert the appointment
    $insertStmt = $db->prepare("
        INSERT INTO appointments (
            pet_id, pet_owner_id, clinic_id, vet_id, 
            appointment_type, symptoms, appointment_date, appointment_time, 
            status, duration_minutes
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', 20)
    ");
    
    $result = $insertStmt->execute([
        $petId, $petOwnerId, $clinicId, $vetId,
        $appointmentType, $symptoms, $appointmentDate, $appointmentTime
    ]);
    
    if ($result) {
        $appointmentId = $db->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'message' => 'Appointment request submitted successfully!',
            'appointment_id' => $appointmentId,
            'status' => 'pending'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to create appointment. Please try again.'
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Appointment booking error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error. Please try again later.'
    ]);
} catch (Exception $e) {
    error_log("Appointment booking error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
