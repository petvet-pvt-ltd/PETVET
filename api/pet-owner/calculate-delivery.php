<?php
/**
 * Calculate Delivery Charges
 * Calculate delivery charges based on distance and clinic delivery rules
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../config/auth_helper.php';
require_once __DIR__ . '/../../helpers/MapsHelper.php';

// Check if user is logged in
if (!isLoggedIn() || getUserRole() !== 'pet_owner') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$userId = currentUserId();
$pdo = db();

// Get clinic ID and user location from request
$clinicId = $_GET['clinic_id'] ?? null;
$userLat = isset($_GET['latitude']) ? floatval($_GET['latitude']) : null;
$userLon = isset($_GET['longitude']) ? floatval($_GET['longitude']) : null;

if (!$clinicId || !$userLat || !$userLon) {
    echo json_encode([
        'success' => false, 
        'error' => 'Missing required parameters: clinic_id, latitude, longitude'
    ]);
    exit;
}

try {
    // Get clinic location
    $stmt = $pdo->prepare("SELECT map_location FROM clinics WHERE id = ?");
    $stmt->execute([$clinicId]);
    $clinic = $stmt->fetch();
    
    if (!$clinic || empty($clinic['map_location'])) {
        echo json_encode([
            'success' => false, 
            'error' => 'Clinic location not found'
        ]);
        exit;
    }
    
    // Parse clinic coordinates
    $coords = explode(',', $clinic['map_location']);
    if (count($coords) !== 2 || !is_numeric(trim($coords[0])) || !is_numeric(trim($coords[1]))) {
        echo json_encode([
            'success' => false, 
            'error' => 'Invalid clinic location format'
        ]);
        exit;
    }
    
    $clinicLat = floatval(trim($coords[0]));
    $clinicLon = floatval(trim($coords[1]));
    
    // Get delivery settings
    $settingsStmt = $pdo->prepare("
        SELECT base_delivery_charge, max_delivery_distance, delivery_rules 
        FROM clinic_shop_settings 
        WHERE clinic_id = ?
    ");
    $settingsStmt->execute([$clinicId]);
    $settings = $settingsStmt->fetch();
    
    if (!$settings) {
        // Default settings
        $settings = [
            'base_delivery_charge' => 0,
            'max_delivery_distance' => 0,
            'delivery_rules' => '[]'
        ];
    }
    
    $baseCharge = floatval($settings['base_delivery_charge']);
    $maxDistance = floatval($settings['max_delivery_distance']);
    $deliveryRules = json_decode($settings['delivery_rules'], true) ?? [];
    
    // Calculate distance using MapsHelper (OSRM)
    $mapsHelper = new MapsHelper();
    $distanceResult = $mapsHelper->getDistance($userLat, $userLon, $clinicLat, $clinicLon);
    
    if (!$distanceResult['success']) {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to calculate distance'
        ]);
        exit;
    }
    
    $distance = $distanceResult['distance']; // in km
    $duration = $distanceResult['duration']; // in minutes
    
    // Check if distance exceeds maximum delivery distance
    $exceedsMaxDistance = false;
    if ($maxDistance > 0 && $distance > $maxDistance) {
        $exceedsMaxDistance = true;
    }
    
    // Calculate delivery charge
    $deliveryCharge = $baseCharge;
    
    // Apply distance-based rules (sorted by distance ascending)
    if (!empty($deliveryRules)) {
        // Sort rules by distance ascending
        usort($deliveryRules, function($a, $b) {
            return floatval($a['distance']) - floatval($b['distance']);
        });
        
        // Get the first rule's distance threshold
        $firstRuleDistance = floatval($deliveryRules[0]['distance']);
        
        // Calculate remaining distance after the first threshold
        $remainingDistance = $distance - $firstRuleDistance;
        
        // Only apply rules if distance exceeds the first threshold
        if ($remainingDistance > 0) {
            // Find which rule applies to the REMAINING distance
            $applicableRule = null;
            foreach ($deliveryRules as $rule) {
                $ruleThreshold = floatval($rule['distance']);
                if ($remainingDistance >= $ruleThreshold) {
                    $applicableRule = $rule; // Keep updating to get highest matching
                }
            }
            
            // Apply the rule's rate to the remaining distance
            if ($applicableRule) {
                $chargePerKm = floatval($applicableRule['charge_per_km']);
                $deliveryCharge = $baseCharge + ($remainingDistance * $chargePerKm);
            }
        }
    }
    
    // Round to 2 decimal places
    $deliveryCharge = round($deliveryCharge, 2);
    
    echo json_encode([
        'success' => true,
        'distance' => round($distance, 1),
        'distance_formatted' => round($distance, 1) . ' km',
        'duration' => $duration,
        'duration_formatted' => $duration ? round($duration) . ' min' : null,
        'delivery_charge' => $deliveryCharge,
        'max_delivery_distance' => $maxDistance,
        'exceeds_max_distance' => $exceedsMaxDistance,
        'can_deliver' => !$exceedsMaxDistance
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
