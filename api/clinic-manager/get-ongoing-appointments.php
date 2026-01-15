<?php
/**
 * Get Ongoing Appointments API for Clinic Manager
 * Returns real-time ongoing appointments data
 */

session_start();
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../config/auth_helper.php';

// Check authentication
if (!isLoggedIn() || getUserRole() !== 'clinic_manager') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $pdo = db();
    $userId = currentUserId();
    
    // Get clinic manager's clinic ID
    $stmt = $pdo->prepare("SELECT clinic_id FROM clinic_manager_profiles WHERE user_id = ?");
    $stmt->execute([$userId]);
    $clinicId = $stmt->fetchColumn();
    
    if (!$clinicId) {
        throw new Exception('No clinic associated with this manager');
    }
    
    $today = date('Y-m-d');
    
    // Get ongoing appointments (based on status='ongoing', not time calculation)
    $stmt = $pdo->prepare("
        SELECT 
            a.id,
            a.appointment_time,
            a.appointment_type,
            a.duration_minutes,
            p.name as pet_name,
            p.species,
            CONCAT(u_owner.first_name, ' ', u_owner.last_name) as owner_name,
            CONCAT(u_vet.first_name, ' ', u_vet.last_name) as vet_name,
            a.vet_id
        FROM appointments a
        JOIN pets p ON a.pet_id = p.id
        JOIN users u_owner ON a.pet_owner_id = u_owner.id
        JOIN users u_vet ON a.vet_id = u_vet.id
        WHERE a.clinic_id = ? 
        AND a.appointment_date = ?
        AND a.status = 'ongoing'
        ORDER BY a.appointment_time ASC
    ");
    $stmt->execute([$clinicId, $today]);
    $appointmentsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Build ongoing appointments list
    $ongoing = [];
    $slotMinutes = 60; // Default duration
    
    foreach ($appointmentsData as $appt) {
        $startTime = $appt['appointment_time'];
        $duration = $appt['duration_minutes'] ?? $slotMinutes;
        $endTime = date('H:i:s', strtotime($startTime) + ($duration * 60));
        
        $ongoing[] = [
            'vet' => $appt['vet_name'] ?? 'Unknown Vet',
            'hasAppointment' => true,
            'animal' => $appt['species'] ?? 'Pet',
            'client' => $appt['owner_name'] ?? 'Unknown',
            'type' => $appt['appointment_type'] ?? 'Checkup',
            'time_range' => date('H:i', strtotime($startTime)) . ' – ' . date('H:i', strtotime($endTime))
        ];
    }
    
    // If no ongoing appointments, show vets with no current appointments
    if (empty($ongoing)) {
        $stmt = $pdo->prepare("
            SELECT CONCAT(u.first_name, ' ', u.last_name) as vet_name
            FROM vets v
            JOIN users u ON v.user_id = u.id
            WHERE v.clinic_id = ? AND v.available = 1 AND u.is_active = 1 AND u.is_blocked = 0
            LIMIT 3
        ");
        $stmt->execute([$clinicId]);
        $availableVets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($availableVets as $vet) {
            $ongoing[] = [
                'vet' => $vet['vet_name'],
                'hasAppointment' => false
            ];
        }
    }
    
    echo json_encode([
        'success' => true,
        'ongoingAppointments' => $ongoing,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    error_log("Get ongoing appointments error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
