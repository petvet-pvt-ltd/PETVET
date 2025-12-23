<?php
/**
 * Add Appointment API for Receptionist
 * Creates a new appointment in the system
 */

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../config/auth_helper.php';

// Check authentication
if (!isLoggedIn() || getUserRole() !== 'receptionist') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $pdo = db();
    $userId = currentUserId();
    
    // Get receptionist's clinic ID
    $stmt = $pdo->prepare("SELECT clinic_id FROM clinic_staff WHERE user_id = ?");
    $stmt->execute([$userId]);
    $clinicId = $stmt->fetchColumn();
    
    if (!$clinicId) {
        throw new Exception('No clinic associated with this receptionist');
    }
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Invalid request data');
    }
    
    // Validate required fields
    $requiredFields = ['pet_name', 'client_name', 'vet_id', 'appointment_date', 'appointment_time', 'appointment_type'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    // Check if client exists by name (simple search)
    $stmt = $pdo->prepare("
        SELECT id FROM users 
        WHERE CONCAT(first_name, ' ', last_name) LIKE ? 
        LIMIT 1
    ");
    $stmt->execute(['%' . $data['client_name'] . '%']);
    $clientId = $stmt->fetchColumn();
    
    if (!$clientId) {
        throw new Exception('Client not found. Please register the client first.');
    }
    
    // Check if pet exists for this client
    $stmt = $pdo->prepare("
        SELECT id FROM pets 
        WHERE name = ? AND owner_id = ?
        LIMIT 1
    ");
    $stmt->execute([$data['pet_name'], $clientId]);
    $petId = $stmt->fetchColumn();
    
    if (!$petId) {
        throw new Exception('Pet not found for this client. Please register the pet first.');
    }
    
    // Verify the vet exists and belongs to this clinic
    $stmt = $pdo->prepare("
        SELECT user_id FROM vets 
        WHERE user_id = ? AND clinic_id = ? AND available = 1
    ");
    $stmt->execute([$data['vet_id'], $clinicId]);
    if (!$stmt->fetchColumn()) {
        throw new Exception('Selected veterinarian is not available');
    }
    
    // Insert the appointment
    $stmt = $pdo->prepare("
        INSERT INTO appointments (
            pet_id, 
            pet_owner_id, 
            vet_id, 
            clinic_id,
            appointment_date, 
            appointment_time, 
            appointment_type,
            status,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, 'approved', NOW())
    ");
    
    $stmt->execute([
        $petId,
        $clientId,
        $data['vet_id'],
        $clinicId,
        $data['appointment_date'],
        $data['appointment_time'],
        $data['appointment_type']
    ]);
    
    $appointmentId = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Appointment created successfully',
        'appointment_id' => $appointmentId
    ]);
    
} catch (Exception $e) {
    error_log("Add appointment error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
