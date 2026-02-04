<?php
// DevTools: Check vet proof/CV document row for a vet email
// Usage: php DevTools/check_vet_proof_document.php mo@vet.com

require_once __DIR__ . '/../config/connect.php';

$email = $argv[1] ?? '';
$email = trim($email);

if ($email === '') {
    echo "Usage: php DevTools/check_vet_proof_document.php <email>\n";
    exit(1);
}

try {
    $pdo = db();
    echo "DB OK\n\n";

    $stmt = $pdo->prepare('SELECT id, email, avatar FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "User not found for email: {$email}\n";
        exit(0);
    }

    $userId = (int)$user['id'];
    echo "User ID: {$userId}\n";
    echo "Avatar: " . (!empty($user['avatar']) ? $user['avatar'] : '(none)') . "\n\n";

    $roleStmt = $pdo->prepare(
        "SELECT ur.id as user_role_id, r.role_name, ur.verification_status, ur.is_active, ur.applied_at\n" .
        "FROM user_roles ur JOIN roles r ON r.id = ur.role_id\n" .
        "WHERE ur.user_id = ?\n" .
        "ORDER BY ur.id DESC"
    );
    $roleStmt->execute([$userId]);
    $roles = $roleStmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$roles) {
        echo "No user_roles rows found for user_id={$userId}\n";
        exit(0);
    }

    echo "Roles:\n";
    foreach ($roles as $r) {
        echo "- user_role_id={$r['user_role_id']} role={$r['role_name']} status={$r['verification_status']} active={$r['is_active']} applied_at={$r['applied_at']}\n";
    }
    echo "\n";

    $docStmt = $pdo->prepare(
        "SELECT id, user_role_id, document_type, document_name, file_path, file_size, mime_type, uploaded_at\n" .
        "FROM role_verification_documents\n" .
        "WHERE user_role_id = ?\n" .
        "ORDER BY uploaded_at DESC"
    );

    $projectRoot = realpath(__DIR__ . '/..');

    foreach ($roles as $r) {
        $userRoleId = (int)$r['user_role_id'];
        $docStmt->execute([$userRoleId]);
        $docs = $docStmt->fetchAll(PDO::FETCH_ASSOC);

        echo "Documents for user_role_id={$userRoleId}:\n";
        if (!$docs) {
            echo "  (none)\n\n";
            continue;
        }

        foreach ($docs as $d) {
            $filePath = (string)($d['file_path'] ?? '');
            $diskPath = $filePath !== '' && $projectRoot ? ($projectRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath)) : '';
            $exists = ($diskPath !== '' && is_file($diskPath)) ? 'YES' : 'NO';

            echo "  - doc_id={$d['id']} type={$d['document_type']} name={$d['document_name']}\n";
            echo "    file_path={$filePath}\n";
            echo "    mime={$d['mime_type']} size={$d['file_size']} uploaded_at={$d['uploaded_at']}\n";
            echo "    exists_on_disk={$exists}\n";
        }
        echo "\n";
    }

} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
