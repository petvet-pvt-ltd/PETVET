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

// TODO: Get clinic ID from user session/profile
// For now, default to 1
$clinicId = 1;

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
    
    // Validate required fields
    $requiredFields = ['name', 'role', 'email', 'phone'];
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
    
    // Check if email already exists
    if ($staffModel->emailExists($data['email'], null, $clinicId)) {
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
    $id = null;
    
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
    } else {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = isset($data['id']) ? (int)$data['id'] : null;
    }
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Staff ID is required']);
        return;
    }
    
    // Check if staff exists
    $existing = $staffModel->findById($id, $clinicId);
    if (!$existing) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Staff member not found']);
        return;
    }
    
    // Delete staff member
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
