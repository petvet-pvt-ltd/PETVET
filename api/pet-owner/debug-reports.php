<?php
/**
 * Debug endpoint to check user_id storage in reports
 * Temporary file for troubleshooting
 */

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in', 'session' => $_SESSION]);
    exit;
}

try {
    $db = db();
    $userId = $_SESSION['user_id'];
    
    // Get all reports and show user_id extraction
    $stmt = $db->prepare("
        SELECT 
            report_id,
            type,
            location,
            JSON_EXTRACT(description, '$.user_id') as user_id_raw,
            JSON_UNQUOTE(JSON_EXTRACT(description, '$.user_id')) as user_id_clean,
            description
        FROM LostFoundReport 
        ORDER BY date_reported DESC
        LIMIT 10
    ");
    $stmt->execute();
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'current_user_id' => $userId,
        'current_user_type' => gettype($userId),
        'total_reports' => count($reports),
        'reports' => $reports
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
