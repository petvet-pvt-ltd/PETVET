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
    echo json_encode([
        'success' => false,
        'error' => 'Unauthorized',
        'debug' => 'User not logged in'
    ]);
    exit;
}

// Check if user is receptionist or clinic manager
$userRole = currentRole();
$allowedRoles = ['receptionist', 'clinic_manager'];
if (!in_array($userRole, $allowedRoles)) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error' => 'Access denied',
        'debug' => 'User role: ' . ($userRole ?? 'none') . ' (requires: receptionist or clinic_manager)'
    ]);
    exit;
}

try {
    $model = new SharedAppointmentsModel();
    $pending = $model->getPendingAppointments();
    
    echo json_encode([
        'success' => true,
        'appointments' => $pending,
        'count' => count($pending),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
