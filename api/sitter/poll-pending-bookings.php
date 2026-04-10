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

if (!hasRole('sitter')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit;
}

$sitterId = (int)currentUserId();

try {
    $pdo = db();
    $stmt = $pdo->prepare("SELECT id FROM sitter_service_requests WHERE sitter_id = ? AND status = 'pending' ORDER BY id ASC");
    $stmt->execute([$sitterId]);
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

    $ids = array_values(array_map('intval', $ids ?: []));

    echo json_encode([
        'success' => true,
        'booking_ids' => $ids,
    ]);
} catch (PDOException $e) {
    // Table missing (migration not run yet)
    if (($e->errorInfo[0] ?? null) === '42S02') {
        echo json_encode(['success' => true, 'booking_ids' => []]);
        exit;
    }

    error_log('sitter poll-pending-bookings PDO error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
} catch (Throwable $e) {
    error_log('sitter poll-pending-bookings error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
