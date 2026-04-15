<?php
/**
 * Lost & Found Pet Reports Retrieval API
 * Fetches reports from database with optional filtering
 */

header('Content-Type: application/json');

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/connect.php';

// Only accept GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $db = db();
    
    // Get optional filter parameters
    $type = $_GET['type'] ?? null; // 'lost', 'found', or null for all
    $species = $_GET['species'] ?? null; // species filter
    
    // Build query
    $sql = "SELECT * FROM LostFoundReport";
    $params = [];
    $where = [];
    
    if ($type && in_array($type, ['lost', 'found'])) {
        $where[] = "type = :type";
        $params[':type'] = $type;
    }
    
    if ($species) {
        $where[] = "description LIKE :species";
        $params[':species'] = '%"species":"' . $species . '"%';
    }
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    
    // Get sort parameter
    $sort = $_GET['sort'] ?? 'new';
    
    // Add sorting based on preference
    if ($sort === 'old') {
        $sql .= " ORDER BY date_reported ASC";
    } elseif ($sort === 'days_missing') {
        $sql .= " ORDER BY date_reported ASC";
    } else {
        // 'new' (default) or 'nearby'
        $sql .= " ORDER BY date_reported DESC";
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Parse JSON description field for each report and add days missing
    $formattedReports = [];
    foreach ($reports as $report) {
        $description = json_decode($report['description'], true);
        
        // Calculate days missing
        try {
            $today = new DateTime();
            $reported = new DateTime($report['date_reported']);
            $interval = $today->diff($reported);
            $daysMissing = $interval->days;
        } catch (Exception $e) {
            $daysMissing = 0;
        }
        
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
            'days_missing' => $daysMissing,
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
        'reports' => $formattedReports
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in get-reports.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Error in get-reports.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred',
        'error' => $e->getMessage()
    ]);
}
