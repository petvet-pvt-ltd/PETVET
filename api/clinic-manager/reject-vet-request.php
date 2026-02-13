<?php
/**
 * API Endpoint: Reject Vet Request
 * Clinic manager rejects a pending vet role request for their clinic.
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['current_role']) || $_SESSION['current_role'] !== 'clinic_manager') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../../config/connect.php';

try {
    $pdo = db();
    $data = json_decode(file_get_contents('php://input'), true);
    $userRoleId = (int)($data['user_role_id'] ?? 0);

    if ($userRoleId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing user_role_id']);
        exit;
    }

    // Manager clinic
    $stmt = $pdo->prepare('SELECT clinic_id FROM clinic_manager_profiles WHERE user_id = ? LIMIT 1');
    $stmt->execute([$_SESSION['user_id']]);
    $clinicId = (int)($stmt->fetchColumn() ?: 0);

    if ($clinicId <= 0) {
        throw new Exception('Clinic manager profile not found');
    }

    // Validate pending vet request belongs to this clinic
    $stmt = $pdo->prepare(
        "SELECT ur.user_id, v.clinic_id\n" .
        "FROM user_roles ur\n" .
        "JOIN roles r ON r.id = ur.role_id\n" .
        "JOIN vets v ON v.user_id = ur.user_id\n" .
        "WHERE ur.id = ?\n" .
        "  AND r.role_name = 'vet'\n" .
        "  AND ur.is_active = 1\n" .
        "  AND ur.verification_status = 'pending'\n" .
        "LIMIT 1"
    );
    $stmt->execute([$userRoleId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Pending vet request not found']);
        exit;
    }

    if ((int)$row['clinic_id'] !== $clinicId) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'No permission to reject this request']);
        exit;
    }

    $vetUserId = (int)$row['user_id'];

    $pdo->beginTransaction();

    // Reject role (enum uses 'rejected')
    $stmt = $pdo->prepare(
        "UPDATE user_roles\n" .
        "SET verification_status = 'rejected',\n" .
        "    verified_by = ?,\n" .
        "    verified_at = NOW(),\n" .
        "    is_active = 0\n" .
        "WHERE id = ?"
    );
    $stmt->execute([$_SESSION['user_id'], $userRoleId]);

    // Ensure vet not available (keep flags reset)
    $stmt = $pdo->prepare('UPDATE vets SET available = 0, is_suspended = 0, suspended_at = NULL, is_on_leave = 0, updated_at = CURRENT_TIMESTAMP WHERE user_id = ? AND clinic_id = ?');
    $stmt->execute([$vetUserId, $clinicId]);

    $pdo->commit();

    echo json_encode(['success' => true]);

} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
