<?php
/**
 * File Viewer/Download API
 * Securely serves medical report files to authorized users
 */

require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../config/auth_helper.php';

requireLogin('/PETVET/index.php?module=guest&page=login');

// Get file path from query parameter
$filepath = $_GET['file'] ?? '';

if (empty($filepath)) {
    http_response_code(400);
    die('File path required');
}

// Security: Prevent directory traversal attacks
$filepath = str_replace(['../', '..\\'], '', $filepath);

// Construct full path
$fullPath = __DIR__ . '/../../' . $filepath;

// Check if file exists
if (!file_exists($fullPath) || !is_file($fullPath)) {
    http_response_code(404);
    die('File not found');
}

// Verify user has permission (vet, receptionist, pet owner can view their own records)
// For now, we'll allow any authenticated user to view
// You can add more specific permission checks based on the record ownership

// Get file info
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $fullPath);
finfo_close($finfo);

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
