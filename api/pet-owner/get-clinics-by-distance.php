<?php
/**
 * Get Clinics Sorted by Distance
 * Returns all active clinics sorted by distance from pet owner's location
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../helpers/MapsHelper.php';

// Get pet owner's current location from request (optional)
$petOwnerLat = isset($_GET['latitude']) ? floatval($_GET['latitude']) : null;
$petOwnerLon = isset($_GET['longitude']) ? floatval($_GET['longitude']) : null;
$hasLocation = $petOwnerLat && $petOwnerLon;

try {
    $pdo = db();
    
    // Fetch all active clinics
    $sql = "SELECT 
                id,
                clinic_name,
                clinic_description,
                clinic_logo,
                clinic_address,
                map_location,
                city,
                district,
                clinic_phone,
                clinic_email
            FROM clinics 
            WHERE is_active = 1 
            AND verification_status = 'approved'
            ORDER BY clinic_name";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $clinics = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($clinics)) {
        echo json_encode([
            'success' => true,
            'clinics' => [],
            'message' => 'No clinics available'
        ]);
        exit;
    }
    
    // Initialize MapsHelper
    $mapsHelper = new MapsHelper();
    
    // Calculate distance for each clinic
    $clinicsWithDistance = [];
    
    foreach ($clinics as $clinic) {
        // Parse map_location (format: "6.9271, 79.8612" or Plus Code "2XGH+X6 Kadawatha")
        if (!empty($clinic['map_location'])) {
            $clinicLat = null;
            $clinicLon = null;
            
            // Check if it's lat,lng coordinates
            $coords = explode(',', $clinic['map_location']);
            if (count($coords) === 2 && is_numeric(trim($coords[0])) && is_numeric(trim($coords[1]))) {
                $clinicLat = floatval(trim($coords[0]));
                $clinicLon = floatval(trim($coords[1]));
            } else {
                // Try to geocode as Plus Code or address
                $geocodeResult = $mapsHelper->geocode($clinic['map_location']);
                if ($geocodeResult['success']) {
                    $clinicLat = $geocodeResult['latitude'];
                    $clinicLon = $geocodeResult['longitude'];
                }
            }
            
            // If we have valid coordinates
            if ($clinicLat && $clinicLon) {
                $clinic['latitude'] = $clinicLat;
                $clinic['longitude'] = $clinicLon;
                
                // Calculate distance only if user location is available
                if ($hasLocation) {
                    $distanceResult = $mapsHelper->getDistance(
                        $petOwnerLat, 
                        $petOwnerLon, 
                        $clinicLat, 
                        $clinicLon
                    );
                    
                    if ($distanceResult['success']) {
                        $clinic['distance_km'] = $distanceResult['distance'];
                        $clinic['distance_formatted'] = MapsHelper::formatDistance($distanceResult['distance']);
                        $clinic['duration_min'] = $distanceResult['duration'] ?? null;
                        $clinic['duration_formatted'] = $distanceResult['duration'] 
                            ? MapsHelper::formatDuration($distanceResult['duration']) 
                            : null;
                    }
                }
                
                $clinicsWithDistance[] = $clinic;
            }
        } else {
            // No map_location, still include clinic
            $clinicsWithDistance[] = $clinic;
        }
    }
    
    // Sort clinics by distance if available, otherwise by name
    if ($hasLocation) {
        usort($clinicsWithDistance, function($a, $b) {
            // Clinics with distance first, sorted by distance
            $aHasDistance = isset($a['distance_km']);
            $bHasDistance = isset($b['distance_km']);
            
            if ($aHasDistance && $bHasDistance) {
                return $a['distance_km'] <=> $b['distance_km'];
            } elseif ($aHasDistance) {
                return -1;
            } elseif ($bHasDistance) {
                return 1;
            }
            
            return strcasecmp($a['clinic_name'], $b['clinic_name']);
        });
    } else {
        // No location, sort alphabetically
        usort($clinicsWithDistance, function($a, $b) {
            return strcasecmp($a['clinic_name'], $b['clinic_name']);
        });
    }
    
    $response = [
        'success' => true,
        'clinics' => $clinicsWithDistance,
        'total_clinics' => count($clinicsWithDistance)
    ];
    
    if ($hasLocation) {
        $response['pet_owner_location'] = [
            'latitude' => $petOwnerLat,
            'longitude' => $petOwnerLon
        ];
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("Get clinics by distance error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch clinics: ' . $e->getMessage()
    ]);
}
?>
