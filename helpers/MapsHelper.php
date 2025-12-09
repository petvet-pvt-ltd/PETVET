<?php
/**
 * Maps Helper Class
 * Handles all map-related operations using OpenRouteService API
 * Future-proof design for distance, routing, geocoding, and more
 */

require_once __DIR__ . '/../config/maps_config.php';

class MapsHelper {
    
    private $apiKey;
    private $baseUrl;
    
    public function __construct() {
        $this->apiKey = OPENROUTE_API_KEY;
        $this->baseUrl = OPENROUTE_BASE_URL;
    }
    
    /**
     * Calculate road distance between two coordinates
     * Uses OSRM (Open Source Routing Machine) - FREE, no API key needed
     * @param float $lat1 Starting latitude
     * @param float $lon1 Starting longitude
     * @param float $lat2 Destination latitude
     * @param float $lon2 Destination longitude
     * @return array ['distance' => float (km), 'duration' => float (minutes), 'success' => bool]
     */
    public function getDistance($lat1, $lon1, $lat2, $lon2) {
        try {
            // OSRM Public API - FREE, no API key needed
            // Format: /route/v1/driving/{lon1},{lat1};{lon2},{lat2}
            $url = "https://router.project-osrm.org/route/v1/driving/{$lon1},{$lat1};{$lon2},{$lat2}?overview=false";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_USERAGENT, 'PETVET Application');
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                
                if ($data && $data['code'] === 'Ok' && isset($data['routes'][0])) {
                    $route = $data['routes'][0];
                    $distanceMeters = $route['distance'];
                    $durationSeconds = $route['duration'];
                    
                    $distanceKm = round($distanceMeters / 1000, 1);
                    $durationMin = round($durationSeconds / 60, 0);
                    
                    return [
                        'success' => true,
                        'distance' => $distanceKm,
                        'duration' => $durationMin,
                        'unit' => 'km',
                        'method' => 'osrm'
                    ];
                }
            }
            
            // Fallback to Haversine if OSRM fails
            return $this->getDistanceHaversine($lat1, $lon1, $lat2, $lon2);
            
        } catch (Exception $e) {
            error_log("MapsHelper getDistance error: " . $e->getMessage());
            return $this->getDistanceHaversine($lat1, $lon1, $lat2, $lon2);
        }
    }
    
    /**
     * Haversine formula for straight-line distance (fallback)
     * @param float $lat1 Starting latitude
     * @param float $lon1 Starting longitude
     * @param float $lat2 Destination latitude
     * @param float $lon2 Destination longitude
     * @return array ['distance' => float (km), 'success' => bool]
     */
    private function getDistanceHaversine($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; // Earth's radius in kilometers
        
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);
        
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        $distance = round($earthRadius * $c, 1);
        
        return [
            'success' => true,
            'distance' => $distance,
            'duration' => null,
            'unit' => 'km',
            'method' => 'haversine'
        ];
    }
    
    /**
     * Get driving directions between two points
     * @param float $lat1 Starting latitude
     * @param float $lon1 Starting longitude
     * @param float $lat2 Destination latitude
     * @param float $lon2 Destination longitude
     * @return array Route information with steps
     */
    public function getDirections($lat1, $lon1, $lat2, $lon2) {
        try {
            $url = OPENROUTE_DIRECTIONS_URL;
            
            $data = [
                'coordinates' => [
                    [$lon1, $lat1],
                    [$lon2, $lat2]
                ],
                'format' => 'json',
                'instructions' => true
            ];
            
            $response = $this->makeApiRequest($url, 'POST', $data);
            
            if ($response && isset($response['routes'][0])) {
                $route = $response['routes'][0];
                return [
                    'success' => true,
                    'distance' => round($route['summary']['distance'] / 1000, 1), // km
                    'duration' => round($route['summary']['duration'] / 60, 0), // minutes
                    'steps' => $route['segments'][0]['steps'] ?? [],
                    'geometry' => $route['geometry'] ?? null
                ];
            }
            
            return ['success' => false, 'error' => 'No route found'];
            
        } catch (Exception $e) {
            error_log("MapsHelper getDirections error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Geocode address or Plus Code to coordinates
     * Note: For Plus Codes, clinic managers should manually convert to lat,lng
     * Use https://plus.codes to convert Plus Codes
     * @param string $address Full address string
     * @return array ['latitude' => float, 'longitude' => float, 'success' => bool]
     */
    public function geocode($address) {
        // For now, return error - clinic managers should use lat,lng format
        return [
            'success' => false,
            'error' => 'Please use latitude,longitude format (e.g., "7.0011, 79.9553")',
            'note' => 'Convert Plus Codes at https://plus.codes'
        ];
    }
    

    /**
     * Reverse geocode coordinates to address
     * @param float $lat Latitude
     * @param float $lon Longitude
     * @return array ['address' => string, 'success' => bool]
     */
    public function reverseGeocode($lat, $lon) {
        try {
            $url = OPENROUTE_REVERSE_GEOCODE_URL . '?api_key=' . $this->apiKey . '&point.lon=' . $lon . '&point.lat=' . $lat;
            
            $response = $this->makeApiRequest($url, 'GET');
            
            if ($response && isset($response['features'][0])) {
                return [
                    'success' => true,
                    'address' => $response['features'][0]['properties']['label'] ?? 'Unknown location',
                    'city' => $response['features'][0]['properties']['locality'] ?? null,
                    'country' => $response['features'][0]['properties']['country'] ?? null
                ];
            }
            
            return ['success' => false, 'error' => 'Location not found'];
            
        } catch (Exception $e) {
            error_log("MapsHelper reverseGeocode error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Make API request to OpenRouteService
     * @param string $url API endpoint
     * @param string $method HTTP method (GET or POST)
     * @param array $data Request data for POST
     * @return array|null Response data
     */
    private function makeApiRequest($url, $method = 'GET', $data = null) {
        $ch = curl_init();
        
        $headers = [
            'Authorization: ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        if ($method === 'POST' && $data) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($httpCode === 200 && $response) {
            return json_decode($response, true);
        }
        
        error_log("MapsHelper API Error: HTTP $httpCode - $response");
        return null;
    }
    
    /**
     * Format distance for display
     * @param float $distanceKm Distance in kilometers
     * @return string Formatted distance string
     */
    public static function formatDistance($distanceKm) {
        if ($distanceKm < 1) {
            return round($distanceKm * 1000) . ' m';
        }
        return round($distanceKm, 1) . ' km';
    }
    
    /**
     * Format duration for display
     * @param int $minutes Duration in minutes
     * @return string Formatted duration string
     */
    public static function formatDuration($minutes) {
        if ($minutes < 60) {
            return $minutes . ' min';
        }
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return $hours . 'h ' . $mins . 'min';
    }
}
?>
