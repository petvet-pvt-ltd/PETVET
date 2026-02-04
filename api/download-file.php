<?php
/**
 * File Viewer/Download API
 * Securely serves stored files (including verification PDFs) to authorized users
 */

// Resolve project root from /api directory
$projectRoot = dirname(__DIR__);

require_once $projectRoot . '/config/connect.php';
require_once $projectRoot . '/config/auth_helper.php';

requireLogin('/PETVET/index.php?module=guest&page=login');

// Preferred: download by document id
$docId = isset($_GET['doc_id']) ? (int)$_GET['doc_id'] : 0;

$filepath = '';

if ($docId > 0) {
    $pdo = db();

    $stmt = $pdo->prepare("
        SELECT 
            d.file_path,
            ur.user_id,
            r.role_name,
            v.clinic_id
        FROM role_verification_documents d
        JOIN user_roles ur ON ur.id = d.user_role_id
        JOIN roles r ON r.id = ur.role_id
        LEFT JOIN vets v ON v.user_id = ur.user_id
        WHERE d.id = ?
        LIMIT 1
    ");
    $stmt->execute([$docId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || empty($row['file_path'])) {
        http_response_code(404);
        die('Document not found');
    }

    // Authorization:
    // - admin: always allowed
    // - vet (owner): can access own docs
    // - clinic_manager: can access vet docs for their clinic
    $currentId = currentUserId() ?? 0;
    $allowed = false;

    if (hasRole('admin')) {
        $allowed = true;
    } elseif ($currentId && (int)$row['user_id'] === (int)$currentId) {
        $allowed = true;
    } elseif (hasRole('clinic_manager') && !empty($row['clinic_id'])) {
        $mgrStmt = $pdo->prepare("SELECT clinic_id FROM clinic_manager_profiles WHERE user_id = ? LIMIT 1");
        $mgrStmt->execute([$currentId]);
        $mgrClinicId = (int)($mgrStmt->fetchColumn() ?: 0);
        $allowed = $mgrClinicId > 0 && (int)$row['clinic_id'] === $mgrClinicId;
    }

    if (!$allowed) {
        http_response_code(403);
        die('Access denied');
    }

    $filepath = $row['file_path'];
} else {
    // Legacy: download by file path
    $filepath = $_GET['file'] ?? '';
    if (empty($filepath)) {
        http_response_code(400);
        die('doc_id or file is required');
    }
}

// Security: Prevent directory traversal attacks
$filepath = str_replace(['../', '..\\'], '', $filepath);

// Only allow serving from known safe folders
$allowedPrefixes = ['uploads/', 'public/', 'views/'];
$isAllowedPrefix = false;
foreach ($allowedPrefixes as $prefix) {
    if (strpos($filepath, $prefix) === 0) {
        $isAllowedPrefix = true;
        break;
    }
}
if (!$isAllowedPrefix) {
    http_response_code(400);
    die('Invalid file path');
}

// Construct full path
$fullPath = $projectRoot . '/' . $filepath;

// Check if file exists
if (!file_exists($fullPath) || !is_file($fullPath)) {
    http_response_code(404);
    die('File not found');
}

// Verify user has permission (vet, receptionist, pet owner can view their own records)
// For now, we'll allow any authenticated user to view
// You can add more specific permission checks based on the record ownership

// Get file info
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($fullPath) ?: 'application/octet-stream';

$filename = basename($fullPath);

// Set headers
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($fullPath));

// Determine if inline display or download
$inlineTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
if (in_array($mimeType, $inlineTypes)) {
    header('Content-Disposition: inline; filename="' . $filename . '"');
} else {
    header('Content-Disposition: attachment; filename="' . $filename . '"');
}

// Serve file
readfile($fullPath);
exit;
