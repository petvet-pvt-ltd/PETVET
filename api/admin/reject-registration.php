<?php
/**
 * Reject User Registration Request API
 * Admin rejects pending user role registration
 */

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/connect.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Admin access required.']);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $db = db();
    $requestId = $_POST['request_id'] ?? null;
    $reason = $_POST['reason'] ?? 'No reason provided';
    
    if (!$requestId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Request ID is required']);
        exit;
    }
    
    // Update user_roles verification status
    $stmt = $db->prepare("
        UPDATE user_roles 
        SET verification_status = 'rejected',
            verification_notes = :reason,
            verified_by = :admin_id,
            verified_at = NOW(),
            is_active = 0
        WHERE id = :request_id
    ");
    
    $stmt->execute([
        ':reason' => $reason,
        ':admin_id' => $_SESSION['user_id'],
        ':request_id' => $requestId
    ]);
    
    if ($stmt->rowCount() > 0) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Registration rejected successfully'
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Request not found']);
    }
    
} catch (PDOException $e) {
    error_log("Database error in reject-registration.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Error in reject-registration.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred',
        'error' => $e->getMessage()
    ]);
}
