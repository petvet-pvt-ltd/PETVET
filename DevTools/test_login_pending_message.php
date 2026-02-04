<?php
// DevTools: Test which pending message would be shown for an email (does not verify password)
// Usage: php DevTools/test_login_pending_message.php mo@vet.com

require_once __DIR__ . '/../config/connect.php';

$email = trim($argv[1] ?? '');
if ($email === '') {
    echo "Usage: php DevTools/test_login_pending_message.php <email>\n";
    exit(1);
}

try {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $userId = (int)($stmt->fetchColumn() ?: 0);
    if (!$userId) {
        echo "User not found\n";
        exit(0);
    }

    $stmt = $pdo->prepare(
        "SELECT r.role_name\n" .
        "FROM user_roles ur\n" .
        "JOIN roles r ON r.id = ur.role_id\n" .
        "WHERE ur.user_id = ? AND ur.is_active = 1 AND ur.verification_status = 'pending'"
    );
    $stmt->execute([$userId]);
    $pending = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $isVetPending = in_array('vet', $pending ?? [], true);
    echo $isVetPending
        ? "Your account is pending verification. Please wait for the clinic manager's approval.\n"
        : "Your account is pending verification. Please wait for admin approval.\n";

} catch (Throwable $e) {
    echo "ERROR: {$e->getMessage()}\n";
    exit(1);
}
