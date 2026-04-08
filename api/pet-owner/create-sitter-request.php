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

$sitterId = isset($payload['sitterId']) ? (int)$payload['sitterId'] : 0;
$petName = trim((string)($payload['petName'] ?? ''));
$petTypeRaw = trim((string)($payload['petType'] ?? ''));
$petBreed = trim((string)($payload['petBreed'] ?? $payload['dogBreed'] ?? ''));

$serviceTypeRaw = trim((string)($payload['serviceType'] ?? ''));
$durationType = trim((string)($payload['durationType'] ?? 'single'));

// For single duration the UI uses `date` field; for multiple it uses startDate/endDate
$date = trim((string)($payload['date'] ?? ''));
$startDate = trim((string)($payload['startDate'] ?? ''));
$endDate = trim((string)($payload['endDate'] ?? ''));

$startTime = trim((string)($payload['startTime'] ?? $payload['appointmentTime'] ?? ''));
$endTime = trim((string)($payload['endTime'] ?? $payload['appointmentTime'] ?? ''));

$locationRaw = trim((string)($payload['location'] ?? ''));
$notes = trim((string)($payload['notes'] ?? $payload['special_notes'] ?? ''));

$mapLocation = trim((string)($payload['mapLocation'] ?? ''));
$mapLat = $payload['mapLat'] ?? null;
$mapLng = $payload['mapLng'] ?? null;
$district = trim((string)($payload['district'] ?? $payload['locationDistrict'] ?? ''));

if ($sitterId <= 0) json_out(['success' => false, 'message' => 'sitterId is required'], 400);
if ($petName === '') json_out(['success' => false, 'message' => 'petName is required'], 400);
if ($petTypeRaw === '') json_out(['success' => false, 'message' => 'petType is required'], 400);
if ($petBreed === '') json_out(['success' => false, 'message' => 'petBreed is required'], 400);
if ($serviceTypeRaw === '') json_out(['success' => false, 'message' => 'serviceType is required'], 400);
if ($durationType !== 'single' && $durationType !== 'multiple') json_out(['success' => false, 'message' => 'Invalid durationType'], 400);
if ($startTime === '') json_out(['success' => false, 'message' => 'startTime is required'], 400);
if ($endTime === '') json_out(['success' => false, 'message' => 'endTime is required'], 400);
if ($locationRaw === '') json_out(['success' => false, 'message' => 'location is required'], 400);

if ($durationType === 'single') {
    if ($date === '') json_out(['success' => false, 'message' => 'date is required'], 400);
    $startDate = $date;
    $endDate = $date;
} else {
    if ($startDate === '') json_out(['success' => false, 'message' => 'startDate is required'], 400);
    if ($endDate === '') json_out(['success' => false, 'message' => 'endDate is required'], 400);
}

// Normalize pet type for sitter UI display (Dog/Cat/Bird/Other)
$petTypeMap = [
    'dog' => 'Dog',
    'cat' => 'Cat',
    'bird' => 'Bird',
    'other' => 'Other',
    'Dog' => 'Dog',
    'Cat' => 'Cat',
    'Bird' => 'Bird',
    'Other' => 'Other'
];
$petType = $petTypeMap[$petTypeRaw] ?? ucfirst(strtolower($petTypeRaw));

// Store service type as the display label used by sitter bookings UI
$serviceTypeMap = [
    'dog-walking' => 'Dog Walking',
    'pet-sitting' => 'Pet Sitting (Daily Visits)',
    'overnight-care' => 'Overnight Care',
    'house-sitting' => 'House Sitting with Pets',
    'daycare' => 'Pet Daycare'
];
$serviceType = $serviceTypeMap[$serviceTypeRaw] ?? $serviceTypeRaw;

// Map UI location values to DB enum
$locationTypeMap = [
    'sitter-home' => 'sitter',
    'my-home' => 'home',
    'park' => 'park',
    'other' => 'other'
];
if (!isset($locationTypeMap[$locationRaw])) {
    json_out(['success' => false, 'message' => 'Invalid location'], 400);
}
$locationType = $locationTypeMap[$locationRaw];

// For map-selected locations, require lat/lng and readable address
if (in_array($locationType, ['home', 'park', 'other'], true)) {
    if ($mapLocation === '') {
        json_out(['success' => false, 'message' => 'mapLocation is required'], 400);
    }
    if ($mapLat === null || $mapLng === null || $mapLat === '' || $mapLng === '') {
        json_out(['success' => false, 'message' => 'mapLat/mapLng are required'], 400);
    }
}

$numberOfDays = null;
if ($durationType === 'multiple') {
    $numberOfDays = isset($payload['numberOfDays']) ? (int)$payload['numberOfDays'] : null;
    if ($numberOfDays !== null && $numberOfDays <= 0) {
        $numberOfDays = null;
    }
}

try {
    $pdo = db();

    // Prevent duplicate active booking with same sitter
    $today = (new DateTime('today'))->format('Y-m-d');
    $dup = $pdo->prepare("SELECT 1
        FROM sitter_service_requests
        WHERE pet_owner_id = ? AND sitter_id = ?
          AND status IN ('pending','accepted')
          AND end_date >= ?
        LIMIT 1");
    $dup->execute([$userId, $sitterId, $today]);
    if ($dup->fetchColumn()) {
        json_out(['success' => false, 'message' => 'You already have an active booking with this sitter'], 409);
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

    // Validate sitter exists and is approved sitter
    $sitterStmt = $pdo->prepare("SELECT 1
        FROM user_roles ur
        JOIN roles r ON r.id = ur.role_id
        WHERE ur.user_id = ?
          AND r.role_name = 'sitter'
          AND ur.verification_status = 'approved'
          AND ur.is_active = 1
        LIMIT 1");
    $sitterStmt->execute([$sitterId]);
    if (!$sitterStmt->fetchColumn()) {
        json_out(['success' => false, 'message' => 'Invalid sitter'], 400);
    }

    $insert = $pdo->prepare("INSERT INTO sitter_service_requests (
            sitter_id, pet_owner_id,
            service_type,
            pet_name, pet_type, pet_breed,
            duration_type, number_of_days,
            start_date, end_date, start_time, end_time,
            location_type, location_address, location_lat, location_lng, location_district,
            special_notes
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

    $locationAddress = null;
    $lat = null;
    $lng = null;
    $districtVal = null;

    if (in_array($locationType, ['home', 'park', 'other'], true)) {
        $locationAddress = $mapLocation;
        $lat = is_numeric($mapLat) ? (float)$mapLat : null;
        $lng = is_numeric($mapLng) ? (float)$mapLng : null;
        $districtVal = $district !== '' ? $district : null;
    }

    $insert->execute([
        $sitterId,
        $userId,
        $serviceType,
        $petName,
        $petType,
        $petBreed,
        $durationType,
        $numberOfDays,
        $startDate,
        $endDate,
        $startTime,
        $endTime,
        $locationType,
        $locationAddress,
        $lat,
        $lng,
        $districtVal,
        $notes !== '' ? $notes : null
    ]);

    $requestId = (int)$pdo->lastInsertId();

    json_out([
        'success' => true,
        'message' => 'Sitter booking request submitted',
        'request_id' => $requestId
    ]);
} catch (Throwable $e) {
    error_log('create-sitter-request error: ' . $e->getMessage());
    json_out(['success' => false, 'message' => 'Server error'], 500);
}
