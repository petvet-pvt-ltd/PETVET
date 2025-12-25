<?php
/**
 * Get Lost & Found Reports Sorted by Distance
 * Returns all reports sorted by distance from user's location
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../helpers/MapsHelper.php';

// Get user's current location from request
$userLat = isset($_GET['latitude']) ? floatval($_GET['latitude']) : null;
$userLon = isset($_GET['longitude']) ? floatval($_GET['longitude']) : null;
$type = isset($_GET['type']) ? $_GET['type'] : null; // 'lost', 'found', or null for all

if (!$userLat || !$userLon) {
    echo json_encode([
        'success' => false,
        'error' => 'User location required (latitude and longitude)'
    ]);
    exit;
}

try {
    $pdo = db();
    
    // Build query based on type
    $sql = "SELECT * FROM LostFoundReport";
    if ($type && in_array($type, ['lost', 'found'])) {
        $sql .= " WHERE type = :type";
    }
    $sql .= " ORDER BY date_reported DESC";
    
    $stmt = $pdo->prepare($sql);
    if ($type && in_array($type, ['lost', 'found'])) {
        $stmt->bindParam(':type', $type);
    }
    $stmt->execute();
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($reports)) {
        echo json_encode([
            'success' => true,
            'reports' => [],
            'message' => 'No reports found'
        ]);
        exit;
    }
    
    // Initialize MapsHelper
    $mapsHelper = new MapsHelper();
    
    // Calculate distance for each report
    $reportsWithDistance = [];
    
    foreach ($reports as $report) {
        // Parse JSON description to get latitude/longitude
        $description = json_decode($report['description'], true);
        
        $reportLat = $description['latitude'] ?? null;
        $reportLon = $description['longitude'] ?? null;
        
        // If report has valid coordinates, calculate distance
        if ($reportLat && $reportLon) {
            $distanceResult = $mapsHelper->getDistance(
                $userLat, 
                $userLon, 
                floatval($reportLat), 
                floatval($reportLon)
            );
            
            if ($distanceResult['success']) {
                $report['distance_km'] = $distanceResult['distance'];
                $report['distance_formatted'] = MapsHelper::formatDistance($distanceResult['distance']);
                $report['duration_min'] = $distanceResult['duration'] ?? null;
                $report['duration_formatted'] = $distanceResult['duration'] 
                    ? MapsHelper::formatDuration($distanceResult['duration']) 
                    : null;
            } else {
                // Fallback: very high distance if calculation fails
                $report['distance_km'] = 999999;
                $report['distance_formatted'] = 'Unknown';
            }
        } else {
            // No coordinates: assign very high distance
            $report['distance_km'] = 999999;
            $report['distance_formatted'] = 'Unknown';
        }
        
        $reportsWithDistance[] = $report;
    }
    
    // Sort by distance (nearest first)
    usort($reportsWithDistance, function($a, $b) {
        return $a['distance_km'] <=> $b['distance_km'];
    });
    
    // Format reports for frontend
    $formattedReports = [];
    foreach ($reportsWithDistance as $report) {
        $description = json_decode($report['description'], true);
        
        // Build photo array
        $photos = $description['photos'] ?? [];
        if (empty($photos)) {
            $photos = ['/PETVET/public/img/default-pet.jpg'];
        }
        
        $formattedReports[] = [
            'id' => $report['report_id'],
            'type' => $report['type'],
            'name' => $description['name'] ?? null,
            'species' => $description['species'] ?? 'Unknown',
            'breed' => $description['breed'] ?? 'Unknown',
            'age' => $description['age'] ?? 'Unknown',
            'color' => $description['color'] ?? '',
            'photo' => $photos,
            'last_seen' => $report['location'],
            'date' => $report['date_reported'],
            'time' => $description['time'] ?? null,
            'notes' => $description['notes'] ?? '',
            'contact' => $description['contact'] ?? [
                'name' => 'Anonymous',
                'email' => '',
                'phone' => '',
                'phone2' => ''
            ],
            'latitude' => $description['latitude'] ?? null,
            'longitude' => $description['longitude'] ?? null,
            'distance_km' => $report['distance_km'],
            'distance_formatted' => $report['distance_formatted'],
            'duration_min' => $report['duration_min'] ?? null,
            'duration_formatted' => $report['duration_formatted'] ?? null
        ];
    }
    
    echo json_encode([
        'success' => true,
        'reports' => $formattedReports,
        'count' => count($formattedReports),
        'user_location' => [
            'latitude' => $userLat,
            'longitude' => $userLon
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in get-reports-by-distance.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
} catch (Exception $e) {
    error_log("Error in get-reports-by-distance.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred'
    ]);
}
?>
