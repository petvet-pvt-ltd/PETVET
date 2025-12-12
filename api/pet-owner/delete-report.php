<?php
/**
 * Delete Lost & Found Report API
 * Allows users to delete their own reports
 */

session_start();
header('Content-Type: application/json');

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in.']);
    exit;
}

// Accept POST or DELETE requests
if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'DELETE'])) {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $db = db();
    $userId = $_SESSION['user_id'];
    
    // Get report ID from POST or query string
    $reportId = $_POST['report_id'] ?? $_GET['report_id'] ?? null;
    
    if (!$reportId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Report ID is required']);
        exit;
    }
    
    // Verify ownership - fetch existing report
    $stmt = $db->prepare("SELECT * FROM LostFoundReport WHERE report_id = :report_id");
    $stmt->execute([':report_id' => $reportId]);
    $existingReport = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$existingReport) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Report not found']);
        exit;
    }
    
    // Check ownership
    $description = json_decode($existingReport['description'], true);
    if (($description['user_id'] ?? null) != $userId) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'You do not have permission to delete this report']);
        exit;
    }
    
    // Delete associated photos from filesystem
    $photos = $description['photos'] ?? [];
    foreach ($photos as $photoPath) {
        // Convert URL path to filesystem path
        $filePath = __DIR__ . '/../../' . str_replace('/PETVET/', '', $photoPath);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    
    // Delete from database
    $stmt = $db->prepare("DELETE FROM LostFoundReport WHERE report_id = :report_id");
    $stmt->execute([':report_id' => $reportId]);
    
    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Report deleted successfully',
        'report_id' => $reportId
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in delete-report.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Error in delete-report.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred',
        'error' => $e->getMessage()
    ]);
}
