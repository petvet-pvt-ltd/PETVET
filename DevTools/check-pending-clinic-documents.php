<?php
/**
 * Check Pending Clinic Documents
 * Verify that clinic manager registrations have documents stored properly
 */

require_once __DIR__ . '/../config/connect.php';

$db = db();

echo "<h2>Checking Pending Clinic Registrations</h2>";

// Check clinics table
$stmt = $db->query("
    SELECT 
        c.id,
        c.clinic_name,
        c.clinic_email,
        c.verification_status,
        c.license_document,
        c.created_at
    FROM clinics c
    WHERE c.verification_status = 'pending'
    ORDER BY c.created_at DESC
");

$clinics = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>Clinics Table (Pending)</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Status</th><th>License Document</th><th>Created</th></tr>";

foreach ($clinics as $clinic) {
    echo "<tr>";
    echo "<td>{$clinic['id']}</td>";
    echo "<td>{$clinic['clinic_name']}</td>";
    echo "<td>{$clinic['clinic_email']}</td>";
    echo "<td>{$clinic['verification_status']}</td>";
    echo "<td>" . ($clinic['license_document'] ? 
        "<a href='/PETVET/{$clinic['license_document']}' target='_blank'>View Document</a>" : 
        "<span style='color:red'>No document</span>") . "</td>";
    echo "<td>{$clinic['created_at']}</td>";
    echo "</tr>";
}

if (empty($clinics)) {
    echo "<tr><td colspan='6'>No pending clinics found</td></tr>";
}

echo "</table>";

// Check role_verification_documents for clinic_manager role
echo "<h3>Role Verification Documents (Clinic Manager)</h3>";

$stmt = $db->query("
    SELECT 
        vd.id,
        vd.document_name,
        vd.file_path,
        vd.file_size,
        vd.uploaded_at,
        u.email,
        u.first_name,
        u.last_name,
        r.role_name
    FROM role_verification_documents vd
    JOIN user_roles ur ON vd.user_role_id = ur.id
    JOIN users u ON ur.user_id = u.id
    JOIN roles r ON ur.role_id = r.id
    WHERE r.role_name = 'clinic_manager'
    ORDER BY vd.uploaded_at DESC
");

$docs = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Doc ID</th><th>User</th><th>Document Name</th><th>File Path</th><th>Size (KB)</th><th>Uploaded</th></tr>";

foreach ($docs as $doc) {
    $sizeKB = round($doc['file_size'] / 1024, 2);
    echo "<tr>";
    echo "<td>{$doc['id']}</td>";
    echo "<td>{$doc['first_name']} {$doc['last_name']} ({$doc['email']})</td>";
    echo "<td>{$doc['document_name']}</td>";
    echo "<td><a href='/PETVET/{$doc['file_path']}' target='_blank'>{$doc['file_path']}</a></td>";
    echo "<td>{$sizeKB} KB</td>";
    echo "<td>{$doc['uploaded_at']}</td>";
    echo "</tr>";
}

if (empty($docs)) {
    echo "<tr><td colspan='6'>No documents found</td></tr>";
}

echo "</table>";

// Check if documents exist on filesystem
echo "<h3>File System Check</h3>";
$uploadDir = __DIR__ . '/../uploads/verification_documents/';

if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    $files = array_diff($files, ['.', '..']);
    
    echo "<p><strong>Files in uploads/verification_documents/:</strong></p>";
    echo "<ul>";
    foreach ($files as $file) {
        $filePath = $uploadDir . $file;
        $size = round(filesize($filePath) / 1024, 2);
        echo "<li>$file ({$size} KB) - <a href='/PETVET/uploads/verification_documents/$file' target='_blank'>View</a></li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color:red'>Directory uploads/verification_documents/ does not exist!</p>";
}

echo "<hr>";
echo "<p><a href='/PETVET/index.php?module=admin&page=manage-clinics'>Go to Manage Clinics</a></p>";
?>
