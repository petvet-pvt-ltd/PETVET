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

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
	json_out(['success' => false, 'message' => 'Invalid request method'], 405);
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

$type = strtolower(trim((string)($payload['type'] ?? '')));
$bookingId = (int)($payload['bookingId'] ?? 0);
if (!in_array($type, ['trainer', 'sitter', 'breeder'], true)) {
	json_out(['success' => false, 'message' => 'Invalid type'], 400);
}
if ($bookingId <= 0) {
	json_out(['success' => false, 'message' => 'Invalid bookingId'], 400);
}

try {
	$pdo = db();

	if ($type === 'trainer') {
		$stmt = $pdo->prepare("DELETE FROM trainer_training_requests
			WHERE id = ? AND pet_owner_id = ? AND status = 'pending'");
		$stmt->execute([$bookingId, $userId]);
	} elseif ($type === 'sitter') {
		$stmt = $pdo->prepare("DELETE FROM sitter_service_requests
			WHERE id = ? AND pet_owner_id = ? AND status = 'pending'");
		$stmt->execute([$bookingId, $userId]);
	} else { // breeder
		$stmt = $pdo->prepare("DELETE FROM breeding_requests
			WHERE id = ? AND owner_id = ? AND status = 'pending'");
		$stmt->execute([$bookingId, $userId]);
	}

	if (($stmt->rowCount() ?? 0) === 0) {
		json_out(['success' => false, 'message' => 'Booking not found or cannot be cancelled'], 404);
	}

	json_out(['success' => true, 'message' => 'Booking cancelled']);
} catch (Throwable $e) {
	error_log('cancel-booking error: ' . $e->getMessage());
	json_out(['success' => false, 'message' => 'Server error'], 500);
}

