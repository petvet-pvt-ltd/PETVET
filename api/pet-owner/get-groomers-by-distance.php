<?php
/**
 * Get Groomers Distance
 * Returns distance (road distance via OSRM; fallback Haversine) from pet owner's location
 * for a set of groomer user IDs.
 *
 * Query params:
 * - latitude, longitude (required)
 * - ids (optional, comma-separated groomer user IDs). If omitted/empty, returns empty list.
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../helpers/MapsHelper.php';

$petOwnerLat = isset($_GET['latitude']) ? floatval($_GET['latitude']) : null;
$petOwnerLon = isset($_GET['longitude']) ? floatval($_GET['longitude']) : null;

if (!$petOwnerLat || !$petOwnerLon) {
    echo json_encode([
        'success' => true,
        'groomers' => [],
        'message' => 'Missing user location'
    ]);
    exit;
}

$idsParam = isset($_GET['ids']) ? (string)$_GET['ids'] : '';
$ids = [];
if ($idsParam !== '') {
    foreach (explode(',', $idsParam) as $raw) {
        $id = (int)trim($raw);
        if ($id > 0) $ids[] = $id;
    }
    $ids = array_values(array_unique($ids));
}

// To avoid expensive OSRM calls for the entire database by accident,
// only calculate for requested IDs.
if (empty($ids)) {
    echo json_encode([
        'success' => true,
        'groomers' => [],
        'message' => 'No groomer IDs provided'
    ]);
    exit;
}

try {
    $pdo = db();

    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $sql = "SELECT
                u.id,
                spp.location_latitude AS latitude,
                spp.location_longitude AS longitude
            FROM users u
            INNER JOIN service_provider_profiles spp ON u.id = spp.user_id AND spp.role_type = 'groomer'
            INNER JOIN user_roles ur ON u.id = ur.user_id
            INNER JOIN roles r ON ur.role_id = r.id
            WHERE r.role_name = 'groomer'
              AND ur.verification_status = 'approved'
              AND ur.is_active = 1
              AND spp.location_latitude IS NOT NULL
              AND spp.location_longitude IS NOT NULL
              AND EXISTS (
                  SELECT 1 FROM groomer_services gs WHERE gs.user_id = u.id
              )
              AND u.id IN ($placeholders)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($ids);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $mapsHelper = new MapsHelper();
    $groomers = [];

    foreach ($rows as $row) {
        $lat = isset($row['latitude']) ? floatval($row['latitude']) : null;
        $lon = isset($row['longitude']) ? floatval($row['longitude']) : null;
        if (!$lat || !$lon) continue;

        $distanceResult = $mapsHelper->getDistance($petOwnerLat, $petOwnerLon, $lat, $lon);
        if (!isset($distanceResult['success']) || $distanceResult['success'] !== true) continue;

        $distanceKm = isset($distanceResult['distance']) ? floatval($distanceResult['distance']) : null;
        if ($distanceKm === null) continue;

        $groomers[] = [
            'id' => (int)$row['id'],
            'distance_km' => $distanceKm,
            'distance_formatted' => MapsHelper::formatDistance($distanceKm),
            'duration_min' => $distanceResult['duration'] ?? null,
            'duration_formatted' => isset($distanceResult['duration']) && $distanceResult['duration'] !== null
                ? MapsHelper::formatDuration((int)$distanceResult['duration'])
                : null,
            'method' => $distanceResult['method'] ?? null
        ];
    }

    echo json_encode([
        'success' => true,
        'groomers' => $groomers,
        'pet_owner_location' => [
            'latitude' => $petOwnerLat,
            'longitude' => $petOwnerLon
        ]
    ]);

} catch (Exception $e) {
    error_log('Get groomers by distance error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to calculate distances'
    ]);
}
