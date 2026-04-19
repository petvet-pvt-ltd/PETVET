<?php
/**
 * Get My Lost & Found Reports API
 * Fetches reports submitted by the currently logged-in user
 */

session_start();
header('Content-Type: application/json');

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../models/PetOwner/LostFoundModel.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in.']);
    exit;
}

// Only accept GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $model = new LostFoundModel();
    $userId = $_SESSION['user_id'];
    
    // Fetch reports for current user using MODEL
    $reports = $model->getUserReports($userId);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'count' => count($reports),
        'user_id' => (int)$userId,
        'reports' => $reports
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in get-my-reports.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Error in get-my-reports.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred',
        'error' => $e->getMessage()
    ]);
}
