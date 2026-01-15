<?php
require_once '../../config/connect.php';
require_once '../../config/auth_helper.php';
require_once '../../models/SharedAppointmentsModel.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is authenticated
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Check if user is receptionist or clinic manager
$userRole = currentRole();
$allowedRoles = ['receptionist', 'clinic_manager'];
if (!in_array($userRole, $allowedRoles)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}

try {
    $sharedModel = new SharedAppointmentsModel();
    $today = date('Y-m-d');
    $userId = currentUserId();
    
    // Get pending appointments count
    $pendingAppointments = $sharedModel->getPendingAppointments();
    $pendingCount = count($pendingAppointments);
    
    // Get receptionist's clinic ID
    $pdo = db();
    $clinicFilter = "";
    $params = [$today];
    
    $checkClinic = $pdo->prepare("SELECT clinic_id FROM clinic_staff WHERE user_id = ?");
    $checkClinic->execute([$userId]);
    $clinicId = $checkClinic->fetchColumn();
    
    if ($clinicId) {
        $clinicFilter = " AND a.clinic_id = ?";
        $params[] = $clinicId;
    }
    
    // Get ongoing appointments (based on status='ongoing', not time)
    $ongoingQuery = "
        SELECT 
            a.id,
            a.appointment_time,
            a.duration_minutes,
            a.appointment_type,
            p.name as pet,
            p.species as animal,
            CONCAT(u.first_name, ' ', u.last_name) as client,
            COALESCE(CONCAT(v.first_name, ' ', v.last_name), 'Any Available Vet') as vet
        FROM appointments a
        JOIN pets p ON a.pet_id = p.id
        JOIN users u ON a.pet_owner_id = u.id
        LEFT JOIN users v ON a.vet_id = v.id
        WHERE a.appointment_date = ? 
        AND a.status = 'ongoing' $clinicFilter
        ORDER BY a.appointment_time
    ";
    
    $stmt = $pdo->prepare($ongoingQuery);
    $stmt->execute($params);
    $ongoingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $ongoingAppointments = [];
    foreach ($ongoingData as $appt) {
        $apptTime = strtotime($appt['appointment_time']);
        $apptHour = (int)date('H', $apptTime);
        $apptMinute = (int)date('i', $apptTime);
        $duration = $appt['duration_minutes'] ?? 20;
        
        $apptStart = $apptHour * 60 + $apptMinute;
        $apptEnd = $apptStart + $duration;
        $endHour = floor($apptEnd / 60);
        $endMinute = $apptEnd % 60;
        
        $ongoingAppointments[] = [
            'vet' => $appt['vet'],
            'client' => $appt['client'],
            'type' => $appt['appointment_type'],
            'time_range' => sprintf('%02d:%02d - %02d:%02d', $apptHour, $apptMinute, $endHour, $endMinute),
            'pet' => $appt['pet']
        ];
    }
    
    // Get upcoming appointments (approved status)
    $upcomingQuery = "
        SELECT 
            a.id,
            a.appointment_time,
            a.appointment_type,
            p.name as pet,
            p.species as animal,
            CONCAT(u.first_name, ' ', u.last_name) as client,
            COALESCE(CONCAT(v.first_name, ' ', v.last_name), 'Any Available Vet') as vet
        FROM appointments a
        JOIN pets p ON a.pet_id = p.id
        JOIN users u ON a.pet_owner_id = u.id
        LEFT JOIN users v ON a.vet_id = v.id
        WHERE a.appointment_date = ? 
        AND a.status = 'approved' $clinicFilter
        ORDER BY a.appointment_time
    ";
    
    $stmt = $pdo->prepare($upcomingQuery);
    $stmt->execute($params);
    $upcomingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $upcomingAppointments = [];
    foreach ($upcomingData as $appt) {
        $upcomingAppointments[] = [
            'date' => $today,
            'time' => $appt['appointment_time'],
            'pet' => $appt['pet'],
            'client' => $appt['client'],
            'vet' => $appt['vet'],
            'type' => $appt['appointment_type']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'pendingCount' => $pendingCount,
        'ongoingCount' => count($ongoingAppointments),
        'ongoingAppointments' => $ongoingAppointments,
        'upcomingAppointments' => $upcomingAppointments,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
