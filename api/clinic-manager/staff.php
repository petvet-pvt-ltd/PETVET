<?php
/**
 * Staff Management API
 * Handles CRUD operations for clinic staff
 */

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/auth_helper.php';
require_once __DIR__ . '/../../models/ClinicManager/StaffModel.php';

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

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Initialize model
$staffModel = new StaffModel();

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

try {
    switch ($method) {
        case 'GET':
            handleGet($staffModel, $clinicId);
            break;
            
        case 'POST':
            handlePost($staffModel, $clinicId);
            break;
            
        case 'PUT':
            handlePut($staffModel, $clinicId);
            break;
            
        case 'DELETE':
            handleDelete($staffModel, $clinicId);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    error_log("Staff API error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal server error']);
}

/**
 * Handle GET requests - List all staff or get single staff member
 */
function handleGet($staffModel, $clinicId) {
    if (isset($_GET['id'])) {
        // Get single staff member
        $id = (int)$_GET['id'];
        $staff = $staffModel->findById($id, $clinicId);
        
        if ($staff) {
            echo json_encode(['success' => true, 'staff' => $staff]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Staff member not found']);
        }
    } else {
        // Get all staff
        $staff = $staffModel->all($clinicId);
        echo json_encode(['success' => true, 'staff' => $staff]);
    }
}

/**
 * Handle POST requests - Create new staff member
 */
function handlePost($staffModel, $clinicId) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields (email is optional now)
    $requiredFields = ['name', 'role', 'phone'];
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
        return;
    }
    
    // Validate email format if provided
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        return;
    }
    
    // Prevent adding receptionists through this form (they need system accounts)
    if (strtolower($data['role']) === 'receptionist') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Receptionists must be added through "Add Receptionist" with system account']);
        return;
    }
    
    // Check if email already exists (only if email is provided)
    if (!empty($data['email']) && $staffModel->emailExists($data['email'], null, $clinicId)) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        return;
    }
    
    // Add staff member
    $newId = $staffModel->add($data, $clinicId);
    
    if ($newId) {
        $newStaff = $staffModel->findById($newId, $clinicId);
        echo json_encode([
            'success' => true, 
            'message' => 'Staff member added successfully',
            'staff' => $newStaff
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to add staff member']);
    }
}

/**
 * Handle PUT requests - Update existing staff member
 */
function handlePut($staffModel, $clinicId) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate ID
    if (empty($data['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Staff ID is required']);
        return;
    }
    
    $id = (int)$data['id'];
    
    // Check if staff exists
    $existing = $staffModel->findById($id, $clinicId);
    if (!$existing) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Staff member not found']);
        return;
    }
    
    // Validate required fields
    $requiredFields = ['name', 'role', 'email', 'phone', 'status'];
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
        return;
    }
    
    // Validate email format
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        return;
    }
    
    // Check if email already exists for another staff member
    if ($staffModel->emailExists($data['email'], $id, $clinicId)) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        return;
    }
    
    // Update staff member
    $success = $staffModel->update($id, $data, $clinicId);
    
    if ($success) {
        $updatedStaff = $staffModel->findById($id, $clinicId);
        echo json_encode([
            'success' => true, 
            'message' => 'Staff member updated successfully',
            'staff' => $updatedStaff
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update staff member']);
    }
}

/**
 * Handle DELETE requests - Delete staff member
 */
function handleDelete($staffModel, $clinicId) {
    // Parse DELETE request - ID can come from query string or request body
    $data = json_decode(file_get_contents('php://input'), true);
    
    $id = isset($data['id']) ? (int)$data['id'] : null;
    $source = $data['source'] ?? 'clinic_staff'; // 'clinic_staff' or 'user_roles'
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Staff ID is required']);
        return;
    }
    
    $pdo = db();
    
    try {
        if ($source === 'user_roles') {
            // This is a receptionist from user_roles - delete from users table completely
            // First, get the user_id from clinic_staff using the clinic_staff id
            $stmt = $pdo->prepare("SELECT user_id FROM clinic_staff WHERE id = ? AND clinic_id = ?");
            $stmt->execute([$id, $clinicId]);
            $userId = $stmt->fetchColumn();
            
            if (!$userId) {
                throw new Exception('Receptionist not found in clinic staff');
            }
            
            $pdo->beginTransaction();
            
            // 1. Delete from clinic_staff (the link record)
            $stmt = $pdo->prepare("DELETE FROM clinic_staff WHERE id = ? AND clinic_id = ?");
            $stmt->execute([$id, $clinicId]);
            
            // 2. Delete from favorite_clinics (if exists)
            $stmt = $pdo->prepare("DELETE FROM favorite_clinics WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            // 3. Delete any sessions
            $stmt = $pdo->prepare("DELETE FROM sessions WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            // 4. Delete from user_roles
            $stmt = $pdo->prepare("DELETE FROM user_roles WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            // 5. Finally delete the user account (prevents login)
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $result = $stmt->execute([$userId]);
            
            if (!$result) {
                throw new Exception('Failed to delete user account');
            }
            
            $pdo->commit();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Receptionist account permanently deleted'
            ]);
        } else {
            // This is regular staff from clinic_staff - just delete the record
            $success = $staffModel->delete($id, $clinicId);
            
            if ($success) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Staff member deleted successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to delete staff member']);
            }
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Delete staff error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete: ' . $e->getMessage()]);
    }
}
