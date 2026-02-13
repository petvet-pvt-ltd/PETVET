<?php
/**
 * Get Pending Registration Requests API
 * Fetches all pending role registration requests with verification documents
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

try {
    $db = db();
    
    // Fetch all pending role registrations with user info and documents
    $stmt = $db->prepare("
        SELECT 
            ur.id as request_id,
            ur.user_id,
            ur.applied_at,
            ur.verification_status,
            u.first_name,
            u.last_name,
            u.email,
            u.phone,
            u.avatar,
            u.address,
            r.role_name,
            r.role_display_name,
            vd.id as document_id,
            vd.document_type,
            vd.document_name,
            vd.file_path,
            vd.file_size,
            vd.uploaded_at
        FROM user_roles ur
        JOIN users u ON ur.user_id = u.id
        JOIN roles r ON ur.role_id = r.id
        LEFT JOIN role_verification_documents vd ON ur.id = vd.user_role_id
        WHERE ur.verification_status = 'pending'
        ORDER BY ur.applied_at DESC
    ");
    
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group results by request_id to handle multiple documents per request
    $requests = [];
    foreach ($results as $row) {
        $requestId = $row['request_id'];
        
        if (!isset($requests[$requestId])) {
            $requests[$requestId] = [
                'request_id' => $row['request_id'],
                'user_id' => $row['user_id'],
                'name' => $row['first_name'] . ' ' . $row['last_name'],
                'email' => $row['email'],
                'phone' => $row['phone'],
                'address' => $row['address'],
                'avatar' => $row['avatar'],
                'role' => $row['role_display_name'],
                'role_name' => $row['role_name'],
                'applied_at' => $row['applied_at'],
                'verification_status' => $row['verification_status'],
                'documents' => []
            ];
        }
        
        // Add document if exists
        if ($row['document_id']) {
            $requests[$requestId]['documents'][] = [
                'id' => $row['document_id'],
                'type' => $row['document_type'],
                'name' => $row['document_name'],
                'path' => $row['file_path'],
                'size' => $row['file_size'],
                'uploaded_at' => $row['uploaded_at']
            ];
        }
    }
    
    // Convert associative array to indexed array
    $requests = array_values($requests);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'count' => count($requests),
        'requests' => $requests
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in get-pending-requests.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error occurred while fetching pending requests'
    ]);
}
?>
