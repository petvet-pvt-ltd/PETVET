<?php
/**
 * Check Email Availability API
 * Checks if an email already exists in the system
 */

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/auth_helper.php';
require_once __DIR__ . '/../../config/connect.php';

// Check authentication
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Check if user is a clinic manager
$userRole = getUserRole();
if ($userRole !== 'clinic_manager') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

// Get email from query parameter
$email = isset($_GET['email']) ? trim($_GET['email']) : '';

if (empty($email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

try {
    $pdo = db();
    
    // Check if email exists in users table
    $stmt = $pdo->prepare("SELECT id, first_name, last_name FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo json_encode([
            'success' => true,
            'exists' => true,
            'message' => 'Email already exists in the system',
            'user' => [
                'name' => $user['first_name'] . ' ' . $user['last_name']
            ]
        ]);
    } else {
        // Also check in clinic_staff for non-system users
        $stmt = $pdo->prepare("SELECT name FROM clinic_staff WHERE email = ?");
        $stmt->execute([$email]);
        $staff = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($staff) {
            echo json_encode([
                'success' => true,
                'exists' => true,
                'message' => 'Email already exists in staff records',
                'user' => [
                    'name' => $staff['name']
                ]
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'exists' => false,
                'message' => 'Email is available'
            ]);
        }
    }
    
} catch (Exception $e) {
    error_log("Check email error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal server error']);
}
