<?php
/**
 * Get Appointments API for Clinic Manager
 * Returns appointments data for calendar views (today, week, month)
 */

session_start();
header('Content-Type: application/json');

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
    
    // Get filter parameters
    $vetFilter = $_GET['vet'] ?? 'all';
    
    // Base query
    $query = "
        SELECT 
            a.id,
            a.appointment_date,
            a.appointment_time as time,
            a.appointment_type as type,
            a.status,
            p.name as pet,
            p.species as animal,
            CONCAT(u.first_name, ' ', u.last_name) as client,
            u.phone as client_phone,
            a.vet_id,
            CONCAT(vet.first_name, ' ', vet.last_name) as vet
        FROM appointments a
        JOIN pets p ON a.pet_id = p.id
        JOIN users u ON a.pet_owner_id = u.id
        LEFT JOIN users vet ON a.vet_id = vet.id
        WHERE a.clinic_id = ?
        AND a.status IN ('approved', 'completed')
    ";
    
    $params = [$clinicId];
    
    // Add vet filter if specified
    if ($vetFilter !== 'all') {
        $query .= " AND CONCAT(vet.first_name, ' ', vet.last_name) = ?";
        $params[] = $vetFilter;
    }
    
    $query .= " ORDER BY a.appointment_date ASC, a.appointment_time ASC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Organize by date
    $organized = [];
    foreach ($appointments as $apt) {
        $date = $apt['appointment_date'];
        if (!isset($organized[$date])) {
            $organized[$date] = [];
        }
        $organized[$date][] = $apt;
    }
    
    echo json_encode([
        'success' => true,
        'appointments' => $organized,
        'timestamp' => time()
    ]);
    
} catch (Exception $e) {
    error_log("Get appointments error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
