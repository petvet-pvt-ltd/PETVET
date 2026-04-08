<?php
header('Content-Type: application/json');

session_start();

require_once __DIR__ . '/../../config/connect.php';

function json_out($arr, $code = 200) {
    http_response_code($code);
    echo json_encode($arr);
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    json_out(['success' => false, 'message' => 'Unauthorized'], 401);
}

$raw = file_get_contents('php://input');
$payload = null;
if ($raw) {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) $payload = $decoded;
}
if (!is_array($payload)) {
    $payload = $_POST;
}

$trainerId = isset($payload['trainerId']) ? (int)$payload['trainerId'] : 0;
$trainingType = trim((string)($payload['trainingType'] ?? ''));
$petName = trim((string)($payload['petName'] ?? ''));
$petBreed = trim((string)($payload['dogBreed'] ?? $payload['petBreed'] ?? ''));
$date = trim((string)($payload['appointmentDate'] ?? ''));
$time = trim((string)($payload['appointmentTime'] ?? ''));
$trainingLocation = trim((string)($payload['trainingLocation'] ?? ''));
$additionalNotes = trim((string)($payload['additionalNotes'] ?? ''));

$mapLocation = trim((string)($payload['mapLocation'] ?? ''));
$mapLat = $payload['mapLat'] ?? null;
$mapLng = $payload['mapLng'] ?? null;
$district = trim((string)($payload['locationDistrict'] ?? $payload['district'] ?? ''));

if ($trainerId <= 0) json_out(['success' => false, 'message' => 'trainerId is required'], 400);
if ($trainingType === '') json_out(['success' => false, 'message' => 'trainingType is required'], 400);
if ($petName === '') json_out(['success' => false, 'message' => 'petName is required'], 400);
if ($petBreed === '') json_out(['success' => false, 'message' => 'petBreed is required'], 400);
if ($date === '') json_out(['success' => false, 'message' => 'appointmentDate is required'], 400);
if ($time === '') json_out(['success' => false, 'message' => 'appointmentTime is required'], 400);
if ($trainingLocation === '') json_out(['success' => false, 'message' => 'trainingLocation is required'], 400);

$locationTypeMap = [
    'trainer' => 'trainer',
    'home' => 'home',
    'park' => 'park',
    'other' => 'other'
];
if (!isset($locationTypeMap[$trainingLocation])) {
    json_out(['success' => false, 'message' => 'Invalid trainingLocation'], 400);
}
$locationType = $locationTypeMap[$trainingLocation];

// For map-selected locations we require lat/lng and a readable address text
if (in_array($locationType, ['home','park','other'], true)) {
    if ($mapLocation === '') {
        json_out(['success' => false, 'message' => 'mapLocation is required'], 400);
    }
    if ($mapLat === null || $mapLng === null || $mapLat === '' || $mapLng === '') {
        json_out(['success' => false, 'message' => 'mapLat/mapLng are required'], 400);
    }
}

try {
    $pdo = db();

    // Prevent duplicate active booking with same trainer
    $today = (new DateTime('today'))->format('Y-m-d');
    $dup = $pdo->prepare("SELECT 1
        FROM trainer_training_requests
        WHERE pet_owner_id = ? AND trainer_id = ?
          AND status IN ('pending','accepted')
          AND preferred_date >= ?
        LIMIT 1");
    $dup->execute([$userId, $trainerId, $today]);
    if ($dup->fetchColumn()) {
        json_out(['success' => false, 'message' => 'You already have an active booking with this trainer'], 409);
    }

    // Require membership as pet_owner (multi-role safe)
    $roleStmt = $pdo->prepare("SELECT 1
        FROM user_roles ur
        JOIN roles r ON r.id = ur.role_id
        WHERE ur.user_id = ? AND r.role_name = 'pet_owner'
        LIMIT 1");
    $roleStmt->execute([$userId]);
    if (!$roleStmt->fetchColumn()) {
        json_out(['success' => false, 'message' => 'Pet owner role required'], 403);
    }

    // Validate trainer exists and is approved trainer
    $trainerStmt = $pdo->prepare("SELECT 1
        FROM user_roles ur
        JOIN roles r ON r.id = ur.role_id
        WHERE ur.user_id = ?
          AND r.role_name = 'trainer'
          AND ur.verification_status = 'approved'
          AND ur.is_active = 1
        LIMIT 1");
    $trainerStmt->execute([$trainerId]);
    if (!$trainerStmt->fetchColumn()) {
        json_out(['success' => false, 'message' => 'Invalid trainer'], 400);
    }

    $insert = $pdo->prepare("INSERT INTO trainer_training_requests (
            trainer_id, pet_owner_id,
            training_type, pet_name, pet_breed,
            preferred_date, preferred_time,
            location_type, location_address, location_lat, location_lng, location_district,
            additional_notes
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");

    $locationAddress = null;
    $lat = null;
    $lng = null;
    $districtVal = null;

    if (in_array($locationType, ['home','park','other'], true)) {
        $locationAddress = $mapLocation;
        $lat = is_numeric($mapLat) ? (float)$mapLat : null;
        $lng = is_numeric($mapLng) ? (float)$mapLng : null;
        $districtVal = $district !== '' ? $district : null;
    }

    $insert->execute([
        $trainerId,
        $userId,
        $trainingType,
        $petName,
        $petBreed,
        $date,
        $time,
        $locationType,
        $locationAddress,
        $lat,
        $lng,
        $districtVal,
        $additionalNotes !== '' ? $additionalNotes : null
    ]);

    $requestId = (int)$pdo->lastInsertId();

    json_out([
        'success' => true,
        'message' => 'Training request submitted',
        'request_id' => $requestId
    ]);

} catch (Throwable $e) {
    error_log('create-training-request error: ' . $e->getMessage());
    json_out(['success' => false, 'message' => 'Server error'], 500);
}
