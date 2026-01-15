<?php
/**
 * Reverse Geocoding Proxy
 * Converts latitude/longitude to readable address using Nominatim API
 * Avoids CORS issues by making server-side request
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Get coordinates from query parameters
$lat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
$lng = isset($_GET['lng']) ? floatval($_GET['lng']) : null;

if (!$lat || !$lng) {
    echo json_encode([
        'success' => false,
        'error' => 'Latitude and longitude are required'
    ]);
    exit;
}

try {
    // Call Nominatim API
    $url = "https://nominatim.openstreetmap.org/reverse?lat={$lat}&lon={$lng}&format=json&addressdetails=1";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'PETVET Application');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept-Language: en'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $response) {
        $data = json_decode($response, true);
        
        if ($data && isset($data['address'])) {
            $address = $data['address'];

            // Attempt to extract Sri Lanka district (for Groomer map auto-detect)
            $district = null;
            $slDistricts = [
                'Ampara','Anuradhapura','Badulla','Batticaloa','Colombo','Galle','Gampaha','Hambantota','Jaffna',
                'Kalutara','Kandy','Kegalle','Kilinochchi','Kurunegala','Mannar','Matale','Matara','Monaragala',
                'Mullaitivu','Nuwara Eliya','Polonnaruwa','Puttalam','Ratnapura','Trincomalee','Vavuniya'
            ];

            $candidates = [];
            foreach (['state_district','county','district','region','state','city','town','village','municipality'] as $k) {
                if (!empty($address[$k])) $candidates[] = (string)$address[$k];
            }

            $normalize = function($s) {
                $s = trim((string)$s);
                $s = preg_replace('/\s+District\s*$/i', '', $s);
                $s = preg_replace('/\s+Distric\s*$/i', '', $s); // common typo
                $s = preg_replace('/\s+/', ' ', $s);
                return trim($s);
            };

            foreach ($candidates as $cand) {
                $candNorm = $normalize($cand);
                foreach ($slDistricts as $d) {
                    if (strcasecmp($candNorm, $d) === 0) {
                        $district = $d;
                        break 2;
                    }
                }
                // If Nominatim returns e.g. "Colombo District", still accept after normalization
                foreach ($slDistricts as $d) {
                    if (stripos($candNorm, $d) !== false) {
                        $district = $d;
                        break 2;
                    }
                }
            }
            $locationParts = [];
            
            // Priority order for location components
            // Add area/suburb/neighborhood
            if (!empty($address['suburb'])) $locationParts[] = $address['suburb'];
            else if (!empty($address['neighbourhood'])) $locationParts[] = $address['neighbourhood'];
            else if (!empty($address['quarter'])) $locationParts[] = $address['quarter'];
            else if (!empty($address['hamlet'])) $locationParts[] = $address['hamlet'];
            
            // Add city/town/village
            if (!empty($address['city'])) $locationParts[] = $address['city'];
            else if (!empty($address['town'])) $locationParts[] = $address['town'];
            else if (!empty($address['village'])) $locationParts[] = $address['village'];
            else if (!empty($address['municipality'])) $locationParts[] = $address['municipality'];
            
            // If we don't have enough info, add more details
            if (count($locationParts) === 0) {
                if (!empty($address['county'])) $locationParts[] = $address['county'];
                if (!empty($address['state'])) $locationParts[] = $address['state'];
                if (!empty($address['region'])) $locationParts[] = $address['region'];
            }
            
            // Always add country for context
            if (!empty($address['country'])) $locationParts[] = $address['country'];
            
            $conciseLocation = count($locationParts) > 0 
                ? implode(', ', array_slice($locationParts, 0, 3))  // Max 3 components
                : ($data['display_name'] ?? '');
            
            echo json_encode([
                'success' => true,
                'location' => $conciseLocation,
                'district' => $district,
                'full_address' => $data['display_name'] ?? '',
                'address_components' => $address
            ]);
        } else if ($data && isset($data['display_name'])) {
            echo json_encode([
                'success' => true,
                'location' => $data['display_name'],
                'full_address' => $data['display_name']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'No address found for these coordinates',
                'fallback' => number_format($lat, 4) . ', ' . number_format($lng, 4)
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to fetch address from geocoding service',
            'fallback' => number_format($lat, 4) . ', ' . number_format($lng, 4)
        ]);
    }
    
} catch (Exception $e) {
    error_log("Reverse geocoding error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred while geocoding',
        'fallback' => number_format($lat, 4) . ', ' . number_format($lng, 4)
    ]);
}
?>
