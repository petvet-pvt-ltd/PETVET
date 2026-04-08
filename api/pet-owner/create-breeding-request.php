<?php
header('Content-Type: application/json');

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

require_once __DIR__ . '/../../config/connect.php';

function json_out(array $payload, int $code = 200): void {
	http_response_code($code);
	echo json_encode($payload);
	exit;
}

$userId = (int)($_SESSION['user_id'] ?? 0);
if ($userId <= 0) {
	json_out(['success' => false, 'message' => 'Unauthorized'], 401);
}

$raw = file_get_contents('php://input');
$payload = null;
if ($raw) {
	$decoded = json_decode($raw, true);
	if (is_array($decoded)) {
		$payload = $decoded;
	}
}
if (!is_array($payload)) {
	$payload = $_POST;
}

$breederId = (int)($payload['breederId'] ?? 0);
$petName = trim((string)($payload['petName'] ?? ''));
$petBreed = trim((string)($payload['petBreed'] ?? ''));
$petGender = trim((string)($payload['petGender'] ?? ''));
$preferredDate = trim((string)($payload['breedingDate'] ?? $payload['preferredDate'] ?? ''));
$appointmentTime = trim((string)($payload['breedingTime'] ?? $payload['appointmentTime'] ?? $payload['preferredTime'] ?? ''));
$message = trim((string)($payload['notes'] ?? $payload['message'] ?? ''));

// Optional location/map details (trainer-like UX)
$locationType = trim((string)($payload['breedingLocation'] ?? $payload['locationType'] ?? $payload['trainingLocation'] ?? ''));
$locationDistrict = trim((string)($payload['locationDistrict'] ?? $payload['district'] ?? ''));
$mapLocation = trim((string)($payload['mapLocation'] ?? $payload['locationAddress'] ?? ''));
$mapLat = trim((string)($payload['mapLat'] ?? $payload['locationLat'] ?? ''));
$mapLng = trim((string)($payload['mapLng'] ?? $payload['locationLng'] ?? ''));

if ($locationType !== '' || $locationDistrict !== '' || $mapLocation !== '' || ($mapLat !== '' && $mapLng !== '')) {
	$locationMap = [
		'home' => 'At My Home',
		'breeder' => "At Breeder's Location",
		'park' => 'At Nearby Park',
		'other' => 'Other Location'
	];

	$parts = [];
	if ($message !== '') $parts[] = $message;
	$parts[] = '';
	$parts[] = 'Location Details:';
	if ($locationType !== '') {
		$parts[] = '- Location: ' . ($locationMap[$locationType] ?? $locationType);
	}
	if ($locationDistrict !== '') {
		$parts[] = '- District: ' . $locationDistrict;
	}
	if ($mapLocation !== '') {
		$parts[] = '- Map Location: ' . $mapLocation;
	} elseif ($mapLat !== '' && $mapLng !== '') {
		$parts[] = '- Coordinates: ' . $mapLat . ', ' . $mapLng;
	}

	$message = trim(implode("\n", $parts));
}

if ($breederId <= 0) json_out(['success' => false, 'message' => 'breederId is required'], 400);
if ($petName === '') json_out(['success' => false, 'message' => 'petName is required'], 400);
if ($petBreed === '') json_out(['success' => false, 'message' => 'petBreed is required'], 400);
if (!in_array($petGender, ['Male', 'Female'], true)) json_out(['success' => false, 'message' => 'petGender is required'], 400);
if ($preferredDate === '') json_out(['success' => false, 'message' => 'breedingDate is required'], 400);
if ($appointmentTime === '') json_out(['success' => false, 'message' => 'appointmentTime is required'], 400);
if (!preg_match('/^\d{2}:\d{2}$/', $appointmentTime)) json_out(['success' => false, 'message' => 'appointmentTime is invalid'], 400);

// Append appointment time into message in a structured, parseable way (no schema change)
if ($appointmentTime !== '') {
	$parts = [];
	if ($message !== '') $parts[] = $message;
	$parts[] = '';
	$parts[] = 'Appointment Details:';
	$parts[] = '- Appointment Time: ' . $appointmentTime;
	$message = trim(implode("\n", $parts));
}

try {
	$pdo = db();

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

	// Validate breeder exists and is approved breeder
	$breederStmt = $pdo->prepare("SELECT 1
		FROM user_roles ur
		JOIN roles r ON r.id = ur.role_id
		WHERE ur.user_id = ?
		  AND r.role_name = 'breeder'
		  AND ur.verification_status = 'approved'
		  AND ur.is_active = 1
		LIMIT 1");
	$breederStmt->execute([$breederId]);
	if (!$breederStmt->fetchColumn()) {
		json_out(['success' => false, 'message' => 'Invalid breeder'], 400);
	}

	// Enforce breeder availability (weekly schedule + blocked dates)
	$today = date('Y-m-d');
	if ($preferredDate < $today) {
		json_out(['success' => false, 'message' => 'Cannot select past dates. Please choose a future date.'], 409);
	}
	if ($preferredDate === $today) {
		$currentTime = date('H:i:s');
		$selectedTime = date('H:i:s', strtotime($appointmentTime));
		if ($selectedTime < $currentTime) {
			json_out(['success' => false, 'message' => 'Cannot select past times. Please choose a future time.'], 409);
		}
	}

	$dayOfWeek = date('l', strtotime($preferredDate));
	$scheduleStmt = $pdo->prepare("SELECT is_available, start_time, end_time
		FROM service_provider_weekly_schedule
		WHERE user_id = ? AND role_type = 'breeder' AND day_of_week = ?");
	$scheduleStmt->execute([$breederId, $dayOfWeek]);
	$weeklySchedule = $scheduleStmt->fetch(PDO::FETCH_ASSOC);

	// Defaults if not set (match service-provider-availability UI defaults)
	// - Sunday: unavailable
	// - Mon–Fri: 09:00–18:00
	// - Saturday: 10:00–16:00
	$isAvailableDay = in_array($dayOfWeek, ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'], true);
	$startTime = $dayOfWeek === 'Saturday' ? '10:00:00' : '09:00:00';
	$endTime = $dayOfWeek === 'Saturday' ? '16:00:00' : '18:00:00';
	if ($weeklySchedule) {
		$isAvailableDay = (bool)$weeklySchedule['is_available'];
		$startTime = $weeklySchedule['start_time'];
		$endTime = $weeklySchedule['end_time'];
	}
	if (!$isAvailableDay) {
		json_out(['success' => false, 'message' => 'Breeder is not available on ' . $dayOfWeek . 's'], 409);
	}

	$timeFormatted = date('H:i:s', strtotime($appointmentTime));
	if ($timeFormatted < $startTime || $timeFormatted > $endTime) {
		$startHuman = date('g:i A', strtotime($startTime));
		$endHuman = date('g:i A', strtotime($endTime));
		json_out(['success' => false, 'message' => 'Breeder is available from ' . $startHuman . ' to ' . $endHuman . ' on ' . $dayOfWeek . 's'], 409);
	}

	$blockedStmt = $pdo->prepare("SELECT block_type, block_time
		FROM service_provider_blocked_dates
		WHERE user_id = ? AND role_type = 'breeder' AND blocked_date = ?");
	$blockedStmt->execute([$breederId, $preferredDate]);
	$blockedDate = $blockedStmt->fetch(PDO::FETCH_ASSOC);
	if ($blockedDate) {
		if (($blockedDate['block_type'] ?? '') === 'full-day') {
			json_out(['success' => false, 'message' => 'Breeder is unavailable on this date'], 409);
		}

		$blockType = (string)($blockedDate['block_type'] ?? '');
		if (($blockType === 'before' || $blockType === 'after') && !empty($blockedDate['block_time'])) {
			$blockedTimeFormatted = substr((string)$blockedDate['block_time'], 0, 5);
			$selectedTimeShort = substr($timeFormatted, 0, 5);
			if ($blockType === 'before' && $selectedTimeShort >= $blockedTimeFormatted) {
				json_out(['success' => false, 'message' => 'Breeder is unavailable after ' . date('g:i A', strtotime((string)$blockedDate['block_time'])) . ' on this date.'], 409);
			}
			if ($blockType === 'after' && $selectedTimeShort <= $blockedTimeFormatted) {
				json_out(['success' => false, 'message' => 'Breeder is unavailable before ' . date('g:i A', strtotime((string)$blockedDate['block_time'])) . ' on this date.'], 409);
			}
		}
	}

	// Prevent duplicate active booking with same breeder
	$today = (new DateTime('today'))->format('Y-m-d');
	$dup = $pdo->prepare("SELECT 1
		FROM breeding_requests
		WHERE owner_id = ? AND breeder_id = ?
		  AND status IN ('pending','approved')
		  AND preferred_date >= ?
		LIMIT 1");
	$dup->execute([$userId, $breederId, $today]);
	if ($dup->fetchColumn()) {
		json_out(['success' => false, 'message' => 'You already have an active booking with this breeder'], 409);
	}

	$ins = $pdo->prepare("INSERT INTO breeding_requests (
			breeder_id, owner_id,
			owner_pet_name, owner_pet_breed, owner_pet_gender,
			preferred_date, message,
			status
		) VALUES (?,?,?,?,?,?,?, 'pending')");
	$ins->execute([
		$breederId,
		$userId,
		$petName,
		$petBreed,
		$petGender,
		$preferredDate,
		$message !== '' ? $message : null
	]);

	$requestId = (int)$pdo->lastInsertId();

	json_out([
		'success' => true,
		'message' => 'Breeding request submitted',
		'request_id' => $requestId
	]);
} catch (Throwable $e) {
	error_log('create-breeding-request error: ' . $e->getMessage());
	json_out(['success' => false, 'message' => 'Server error'], 500);
}

