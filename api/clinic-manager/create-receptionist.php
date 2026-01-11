<?php
/**
 * Create Receptionist Account API
 * Creates a new receptionist user with system login access
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

// Get clinic manager's clinic ID
$userId = currentUserId();
$pdo = db();
$stmt = $pdo->prepare("SELECT clinic_id FROM clinic_manager_profiles WHERE user_id = ?");
$stmt->execute([$userId]);
$clinicId = $stmt->fetchColumn();

if (!$clinicId) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No clinic associated with this manager']);
    exit;
}

// Get request data
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$requiredFields = ['first_name', 'last_name', 'email', 'phone', 'password'];
$missing = [];

foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        $missing[] = $field;
    }
}

if (!empty($missing)) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Missing required fields: ' . implode(', ', $missing)
    ]);
    exit;
}

// Validate email format
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

// Validate password length
if (strlen($data['password']) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
    exit;
}

try {
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    if ($stmt->fetchColumn()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        exit;
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Hash password
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    
    // Create user account
    $stmt = $pdo->prepare("
        INSERT INTO users (email, password, first_name, last_name, phone, is_active, email_verified) 
        VALUES (?, ?, ?, ?, ?, 1, 1)
    ");
    $stmt->execute([
        $data['email'],
        $hashedPassword,
        $data['first_name'],
        $data['last_name'],
        $data['phone']
    ]);
    
    $newUserId = $pdo->lastInsertId();
    
    // Get receptionist role ID
    $stmt = $pdo->query("SELECT id FROM roles WHERE role_name = 'receptionist'");
    $receptionistRoleId = $stmt->fetchColumn();
    
    if (!$receptionistRoleId) {
        throw new Exception('Receptionist role not found');
    }
    
    // Assign receptionist role
    $stmt = $pdo->prepare("
        INSERT INTO user_roles (user_id, role_id, is_primary, is_active, verification_status) 
        VALUES (?, ?, 1, 1, 'approved')
    ");
    $stmt->execute([$newUserId, $receptionistRoleId]);
    
    // Add to clinic_staff table (only store link - clinic_id and user_id)
    // Don't duplicate name, email, phone - those come from users table
    $stmt = $pdo->prepare("
        INSERT INTO clinic_staff (user_id, clinic_id, name, role, email, phone, status) 
        VALUES (?, ?, '', 'Receptionist', '', '', 'Active')
    ");
    $stmt->execute([
        $newUserId,
        $clinicId
    ]);
    
    // Commit transaction
    $pdo->commit();
    
    // Return success with receptionist data
    $fullName = $data['first_name'] . ' ' . $data['last_name'];
    echo json_encode([
        'success' => true,
        'message' => 'Receptionist account created successfully',
        'receptionist' => [
            'id' => $newUserId,
            'name' => $fullName,
            'email' => $data['email'],
            'phone' => $data['phone']
        ]
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("Create receptionist error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to create receptionist account'
    ]);
}
