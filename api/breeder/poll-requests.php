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

if (!hasRole('breeder')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit;
}

$breederId = (int)currentUserId();

try {
    $pdo = db();
    $stmt = $pdo->prepare("SELECT id, status FROM breeding_requests WHERE breeder_id = ? AND status IN ('pending','approved','completed')");
    $stmt->execute([$breederId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $out = [
        'pending' => [],
        'approved' => [],
        'completed' => [],
    ];

    foreach ($rows as $row) {
        $status = (string)($row['status'] ?? '');
        $id = (int)($row['id'] ?? 0);
        if ($id <= 0) continue;
        if (!isset($out[$status])) continue;
        $out[$status][] = $id;
    }

    // Stable ordering (oldest-first), especially for pending
    sort($out['pending']);
    sort($out['approved']);
    sort($out['completed']);

    echo json_encode([
        'success' => true,
        'request_ids' => $out,
    ]);
} catch (Throwable $e) {
    error_log('breeder poll-requests error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
