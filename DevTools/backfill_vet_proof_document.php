<?php
// DevTools: Backfill a vet proof document row into role_verification_documents
// Useful when the PDF exists on disk but the DB insert failed due to document_type mismatch.
//
// Usage:
//   php DevTools/backfill_vet_proof_document.php <email> <relative_file_path> [document_name]
// Example:
//   php DevTools/backfill_vet_proof_document.php mo@vet.com uploads/verification_documents/69831aa91ff55_1770199721.pdf "Proof Document.pdf"

require_once __DIR__ . '/../config/connect.php';

$email = trim($argv[1] ?? '');
$relativePath = trim($argv[2] ?? '');
$documentName = trim($argv[3] ?? '');

if ($email === '' || $relativePath === '') {
    echo "Usage: php DevTools/backfill_vet_proof_document.php <email> <relative_file_path> [document_name]\n";
    exit(1);
}

try {
    $pdo = db();
    echo "DB OK\n";

    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $userId = (int)($stmt->fetchColumn() ?: 0);
    if (!$userId) {
        echo "User not found for email: {$email}\n";
        exit(0);
    }

    // Find vet user_role_id
    $stmt = $pdo->prepare(
        "SELECT ur.id\n" .
        "FROM user_roles ur\n" .
        "JOIN roles r ON r.id = ur.role_id\n" .
        "WHERE ur.user_id = ? AND r.role_name = 'vet' AND ur.is_active = 1\n" .
        "ORDER BY ur.id DESC\n" .
        "LIMIT 1"
    );
    $stmt->execute([$userId]);
    $userRoleId = (int)($stmt->fetchColumn() ?: 0);
    if (!$userRoleId) {
        echo "No active vet role found for user_id={$userId}\n";
        exit(0);
    }

    $relativePath = str_replace('\\', '/', $relativePath);
    $relativePath = ltrim($relativePath, '/');

    $projectRoot = realpath(__DIR__ . '/..');
    $diskPath = $projectRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

    if (!is_file($diskPath)) {
        echo "File not found on disk: {$diskPath}\n";
        exit(1);
    }

    $size = filesize($diskPath);
    $name = $documentName !== '' ? $documentName : basename($relativePath);

    // Insert as document_type='other' (matches current enum)
    $insert = $pdo->prepare(
        "INSERT INTO role_verification_documents\n" .
        "(user_role_id, document_type, document_name, file_path, file_size, mime_type, uploaded_at)\n" .
        "VALUES (?, 'other', ?, ?, ?, 'application/pdf', NOW())"
    );

    $ok = $insert->execute([$userRoleId, $name, $relativePath, (int)$size]);
    if (!$ok) {
        echo "Insert failed.\n";
        exit(1);
    }

    $docId = (int)$pdo->lastInsertId();
    echo "Inserted doc_id={$docId} for user_role_id={$userRoleId}\n";
    echo "file_path={$relativePath}\n";

} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
