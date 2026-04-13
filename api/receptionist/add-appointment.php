<?php
/**
 * Add Appointment API for Receptionist
 * Creates a new appointment (walk-in or registered user)
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
    
    // Check the appointment type
    $isWalkIn = $data['is_walk_in'] ?? false;
    $isRegisteredUser = !$isWalkIn && isset($data['pet_owner_id']);
    
    // Initialize variables
    $petId = null;
    $clientId = null;
    $guestPhone = null;
    $guestClientName = null;
    $guestPetName = null;
    $guestPetType = null;
    $guestEmail = null;
    
    if ($isWalkIn) {
        // WALK-IN APPOINTMENT FLOW
        $requiredFields = ['guest_phone', 'guest_client_name', 'guest_pet_name', 'guest_pet_type', 'vet_id', 'appointment_date', 'appointment_time', 'appointment_type'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Missing required field for walk-in: $field");
            }
        }
        
        $guestPhone = $data['guest_phone'];
        $guestClientName = $data['guest_client_name'];
        $guestPetName = $data['guest_pet_name'];
        $guestPetType = $data['guest_pet_type'];
        $guestEmail = $data['guest_email'] ?? null;
        
    } else if ($isRegisteredUser) {
        // REGISTERED USER APPOINTMENT FLOW
        $clientId = $data['pet_owner_id'];
        
        // Verify client exists and is a pet owner
        $stmt = $pdo->prepare("
            SELECT id FROM users 
            WHERE id = ? AND role = 'pet-owner'
            LIMIT 1
        ");
        $stmt->execute([$clientId]);
        if (!$stmt->fetchColumn()) {
            throw new Exception('Invalid pet owner');
        }
        
        if (isset($data['pet_id']) && !empty($data['pet_id'])) {
            // Using existing pet
            $petId = $data['pet_id'];
            
            // Verify pet exists and belongs to this client
            $stmt = $pdo->prepare("
                SELECT id FROM pets 
                WHERE id = ? AND owner_id = ?
                LIMIT 1
            ");
            $stmt->execute([$petId, $clientId]);
            if (!$stmt->fetchColumn()) {
                throw new Exception('Pet not found for this client');
            }
        } else if (isset($data['new_pet_name']) && !empty($data['new_pet_name'])) {
            // Creating a new pet
            $requiredFields = ['new_pet_name', 'new_pet_type'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Missing required field for new pet: $field");
                }
            }
            
            // Create new pet
            $stmt = $pdo->prepare("
                INSERT INTO pets (owner_id, name, species, created_at)
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$clientId, $data['new_pet_name'], $data['new_pet_type']]);
            $petId = $pdo->lastInsertId();
            
            if (!$petId) {
                throw new Exception('Failed to create new pet');
            }
        } else {
            throw new Exception('Please select an existing pet or add a new pet');
        }
    } else {
        // LEGACY REGISTRATION FLOW (for backward compatibility)
        $requiredFields = ['pet_name', 'client_name', 'vet_id', 'appointment_date', 'appointment_time', 'appointment_type'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }
        
        // Find client by name
        $stmt = $pdo->prepare("
            SELECT id FROM users 
            WHERE CONCAT(first_name, ' ', last_name) LIKE ? 
            AND role = 'pet-owner'
            LIMIT 1
        ");
        $stmt->execute(['%' . $data['client_name'] . '%']);
        $clientId = $stmt->fetchColumn();
        
        if (!$clientId) {
            throw new Exception('Client not found');
        }
        
        // Find pet
        $stmt = $pdo->prepare("
            SELECT id FROM pets 
            WHERE name = ? AND owner_id = ?
            LIMIT 1
        ");
        $stmt->execute([$data['pet_name'], $clientId]);
        $petId = $stmt->fetchColumn();
        
        if (!$petId) {
            throw new Exception('Pet not found');
        }
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
            guest_phone,
            guest_client_name,
            guest_pet_name,
            guest_pet_type,
            guest_email,
            is_walk_in,
            vet_id, 
            clinic_id,
            appointment_date, 
            appointment_time, 
            appointment_type,
            symptoms,
            status,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '', 'approved', NOW())
    ");
    
    $stmt->execute([
        $petId,
        $clientId,
        $guestPhone,
        $guestClientName,
        $guestPetName,
        $guestPetType,
        $guestEmail,
        $isWalkIn ? 1 : 0,
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
