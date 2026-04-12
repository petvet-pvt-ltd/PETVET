<?php
error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/auth_helper.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!hasRole('trainer')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit;
}

$trainerId = (int)currentUserId();

try {
    $pdo = db();
    $stmt = $pdo->prepare("SELECT id FROM trainer_training_requests WHERE trainer_id = ? AND status = 'pending' ORDER BY id ASC");
    $stmt->execute([$trainerId]);
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

    $ids = array_values(array_map('intval', $ids ?: []));

    echo json_encode([
        'success' => true,
        'request_ids' => $ids,
    ]);
} catch (Throwable $e) {
    error_log('trainer poll-pending-requests error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
