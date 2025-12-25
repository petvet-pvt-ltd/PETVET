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
    $db = db();
    $userId = $_SESSION['user_id'];
    
    // Fetch reports for current user
    // Note: JSON_UNQUOTE removes quotes from JSON_EXTRACT result for proper comparison
    $stmt = $db->prepare("
        SELECT * FROM LostFoundReport 
        WHERE JSON_UNQUOTE(JSON_EXTRACT(description, '$.user_id')) = :user_id
        ORDER BY date_reported DESC
    ");
    $stmt->execute([':user_id' => $userId]);
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Parse JSON description field for each report
    $formattedReports = [];
    foreach ($reports as $report) {
        $description = json_decode($report['description'], true);
        
        $formattedReports[] = [
            'id' => $report['report_id'],
            'type' => $report['type'],
            'location' => $report['location'],
            'date' => $report['date_reported'],
            'species' => $description['species'] ?? '',
            'name' => $description['name'] ?? null,
            'color' => $description['color'] ?? '',
            'notes' => $description['notes'] ?? '',
            'photos' => $description['photos'] ?? [],
            'latitude' => $description['latitude'] ?? null,
            'longitude' => $description['longitude'] ?? null,
            'time' => $description['time'] ?? null,
            'contact' => $description['contact'] ?? [
                'phone' => '',
                'phone2' => '',
                'email' => ''
            ],
            'user_id' => $description['user_id'] ?? null,
            'submitted_at' => $description['submitted_at'] ?? null
        ];
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'count' => count($formattedReports),
        'user_id' => $userId,
        'reports' => $formattedReports
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in get-my-reports.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred',
        'error' => $e->getMessage(),
        'query_user_id' => $userId ?? null
    ]);
} catch (Exception $e) {
    error_log("Error in get-my-reports.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred',
        'error' => $e->getMessage(),
        'query_user_id' => $userId ?? null
    ]);
}
