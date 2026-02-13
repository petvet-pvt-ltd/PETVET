<?php
/**
 * Download Verification Document
 * Allows admin to download/view verification documents for pending registrations
 */

session_start();
require_once __DIR__ . '/../../config/connect.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    die('Unauthorized access');
}

// Get document ID from query parameter
$documentId = $_GET['id'] ?? null;

if (!$documentId) {
    http_response_code(400);
    die('Document ID is required');
}

try {
    $db = db();
    
    // Fetch document details
    $stmt = $db->prepare("
        SELECT 
            vd.file_path,
            vd.document_name,
            vd.mime_type
        FROM role_verification_documents vd
        WHERE vd.id = ?
    ");
    
    $stmt->execute([$documentId]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$document) {
        http_response_code(404);
        die('Document not found');
    }
    
    // Build full file path
    $filePath = __DIR__ . '/../../' . $document['file_path'];
    
    if (!file_exists($filePath)) {
        http_response_code(404);
        die('File not found on server');
    }
    
    // Set appropriate headers for file download/display
    header('Content-Type: ' . ($document['mime_type'] ?? 'application/pdf'));
    header('Content-Disposition: inline; filename="' . $document['document_name'] . '"');
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    
    // Read and output file
    readfile($filePath);
    exit;
    
} catch (PDOException $e) {
    error_log("Database error in download-verification-document.php: " . $e->getMessage());
    http_response_code(500);
    die('Server error occurred');
}
?>
