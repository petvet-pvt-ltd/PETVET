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

try {
	$pdo = db();

	$today = (new DateTime('today'))->format('Y-m-d');

	$data = [
		'trainers' => [],
		'sitters' => [],
		'breeders' => []
	];
	$activeProviderIds = [
		'trainers' => [],
		'sitters' => [],
		'breeders' => []
	];

	// Trainers
	try {
		$stmt = $pdo->prepare("SELECT
				r.id,
				r.trainer_id AS provider_id,
				COALESCE(NULLIF(TRIM(u.avatar), ''), '/PETVET/public/images/default-avatar.png') AS provider_avatar,
				COALESCE(NULLIF(TRIM(spp.business_name), ''), CONCAT(u.first_name, ' ', u.last_name)) AS provider_display,
				CONCAT(u.first_name, ' ', u.last_name) AS provider_name,
				r.pet_name,
				r.pet_breed,
				r.training_type AS service_label,
				r.preferred_date AS start_date,
				TIME_FORMAT(r.preferred_time, '%H:%i') AS start_time,
				r.status
			FROM trainer_training_requests r
			JOIN users u ON u.id = r.trainer_id
			LEFT JOIN service_provider_profiles spp
				ON spp.user_id = r.trainer_id AND spp.role_type = 'trainer'
			WHERE r.pet_owner_id = ?
			ORDER BY r.created_at DESC");
		$stmt->execute([$userId]);
		$data['trainers'] = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

		$stmt2 = $pdo->prepare("SELECT DISTINCT trainer_id
			FROM trainer_training_requests
			WHERE pet_owner_id = ?
			  AND status IN ('pending','accepted')
			  AND preferred_date >= ?");
		$stmt2->execute([$userId, $today]);
		$activeProviderIds['trainers'] = array_map('intval', $stmt2->fetchAll(PDO::FETCH_COLUMN) ?: []);
	} catch (Throwable $e) {
		// If table missing or query fails, keep empty and avoid fatal.
		error_log('get-my-bookings trainers: ' . $e->getMessage());
	}

	// Sitters
	try {
		$stmt = $pdo->prepare("SELECT
				r.id,
				r.sitter_id AS provider_id,
				COALESCE(NULLIF(TRIM(u.avatar), ''), '/PETVET/public/images/default-avatar.png') AS provider_avatar,
				COALESCE(NULLIF(TRIM(spp.business_name), ''), CONCAT(u.first_name, ' ', u.last_name)) AS provider_display,
				CONCAT(u.first_name, ' ', u.last_name) AS provider_name,
				r.pet_name,
				r.pet_type,
				r.pet_breed,
				r.service_type AS service_label,
				r.start_date AS start_date,
				TIME_FORMAT(r.start_time, '%H:%i') AS start_time,
				r.status
			FROM sitter_service_requests r
			JOIN users u ON u.id = r.sitter_id
			LEFT JOIN service_provider_profiles spp
				ON spp.user_id = r.sitter_id AND spp.role_type = 'sitter'
			WHERE r.pet_owner_id = ?
			ORDER BY r.created_at DESC");
		$stmt->execute([$userId]);
		$data['sitters'] = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

		$stmt2 = $pdo->prepare("SELECT DISTINCT sitter_id
			FROM sitter_service_requests
			WHERE pet_owner_id = ?
			  AND status IN ('pending','accepted')
			  AND end_date >= ?");
		$stmt2->execute([$userId, $today]);
		$activeProviderIds['sitters'] = array_map('intval', $stmt2->fetchAll(PDO::FETCH_COLUMN) ?: []);
	} catch (Throwable $e) {
		error_log('get-my-bookings sitters: ' . $e->getMessage());
	}

	// Breeders
	try {
		$stmt = $pdo->prepare("SELECT
				r.id,
				r.breeder_id AS provider_id,
				COALESCE(NULLIF(TRIM(u.avatar), ''), '/PETVET/public/images/default-avatar.png') AS provider_avatar,
				CONCAT(u.first_name, ' ', u.last_name) AS provider_display,
				CONCAT(u.first_name, ' ', u.last_name) AS provider_name,
				r.owner_pet_name AS pet_name,
				r.owner_pet_breed AS pet_breed,
				r.owner_pet_gender AS pet_gender,
				r.preferred_date AS start_date,
				NULL AS start_time,
				r.status
			FROM breeding_requests r
			JOIN users u ON u.id = r.breeder_id
			WHERE r.owner_id = ?
			ORDER BY r.created_at DESC");
		$stmt->execute([$userId]);
		$data['breeders'] = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

		$stmt2 = $pdo->prepare("SELECT DISTINCT breeder_id
			FROM breeding_requests
			WHERE owner_id = ?
			  AND status IN ('pending','approved')
			  AND preferred_date >= ?");
		$stmt2->execute([$userId, $today]);
		$activeProviderIds['breeders'] = array_map('intval', $stmt2->fetchAll(PDO::FETCH_COLUMN) ?: []);
	} catch (Throwable $e) {
		error_log('get-my-bookings breeders: ' . $e->getMessage());
	}

	json_out([
		'success' => true,
		'data' => $data,
		'active_provider_ids' => $activeProviderIds
	]);
} catch (Throwable $e) {
	error_log('get-my-bookings fatal: ' . $e->getMessage());
	json_out(['success' => false, 'message' => 'Server error'], 500);
}

