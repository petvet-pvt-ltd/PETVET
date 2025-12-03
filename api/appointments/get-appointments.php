<?php
/**
 * API Endpoint: Get Appointments for Real-time Calendar Updates
 * Used by both Clinic Manager and Receptionist
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../config/auth_helper.php';

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$userId = currentUserId();
$userRole = $_SESSION['current_role'] ?? '';

// Only allow clinic manager and receptionist
if (!in_array($userRole, ['clinic_manager', 'receptionist'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}

try {
    $pdo = db();
    
    // Get parameters
    $view = $_GET['view'] ?? 'week';
    $selectedVet = $_GET['vet'] ?? 'all';
    
    // Determine date range based on view
    $today = new DateTime();
    $startDate = clone $today;
    $endDate = clone $today;
    
    switch ($view) {
        case 'today':
            // Just today
            break;
        case 'week':
            // Today + next 6 days
            $endDate->modify('+6 days');
            break;
        case 'month':
            // Today + next 27 days (4 weeks)
            $endDate->modify('+27 days');
            break;
    }
    
    // Build query
    $query = "
        SELECT 
            a.id,
            a.appointment_date,
            a.appointment_time as time,
            a.appointment_type as type,
            p.name as pet,
            p.species as animal,
            CONCAT(u.first_name, ' ', u.last_name) as client,
            u.phone as client_phone,
            COALESCE(CONCAT(v.first_name, ' ', v.last_name), 'Any Available Vet') as vet,
            a.vet_id
        FROM appointments a
        JOIN pets p ON a.pet_id = p.id
        JOIN users u ON a.pet_owner_id = u.id
        LEFT JOIN users v ON a.vet_id = v.id
        WHERE a.appointment_date BETWEEN ? AND ?
        AND a.status = 'approved'
    ";
    
    $params = [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')];
    
    // Filter by clinic for receptionist
    if ($userRole === 'receptionist') {
        $clinicStmt = $pdo->prepare("SELECT clinic_id FROM clinic_staff WHERE user_id = ?");
        $clinicStmt->execute([$userId]);
        $clinicId = $clinicStmt->fetchColumn();
        
        if ($clinicId) {
            $query .= " AND a.clinic_id = ?";
            $params[] = $clinicId;
        }
    }
    
    // Filter by vet if selected
    if ($selectedVet !== 'all') {
        $query .= " AND CONCAT(v.first_name, ' ', v.last_name) = ?";
        $params[] = $selectedVet;
    }
    
    $query .= " ORDER BY a.appointment_date, a.appointment_time";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group by date
    $groupedAppointments = [];
    foreach ($appointments as $appt) {
        $date = $appt['appointment_date'];
        if (!isset($groupedAppointments[$date])) {
            $groupedAppointments[$date] = [];
        }
        $groupedAppointments[$date][] = $appt;
    }
    
    echo json_encode([
        'success' => true,
        'appointments' => $groupedAppointments,
        'view' => $view,
        'timestamp' => time()
    ]);
    
} catch (Exception $e) {
    error_log("Get appointments error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch appointments'
    ]);
}
