<?php
/**
 * Verification Script for Admin Approval System
 * Tests all components of the clinic manager approval workflow
 */

require_once __DIR__ . '/../config/connect.php';

echo "<h1>Admin Approval System Verification</h1>";
echo "<style>body{font-family:Arial;padding:20px}table{border-collapse:collapse;width:100%;margin:20px 0}th,td{border:1px solid #ddd;padding:8px;text-align:left}th{background:#4CAF50;color:white}.success{color:green}.error{color:red}.info{color:blue}</style>";

$db = db();

echo "<h2>1. Database Schema Check</h2>";

// Check user_roles table structure
echo "<h3>user_roles Table Columns:</h3>";
$stmt = $db->query("DESCRIBE user_roles");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<table><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
foreach ($columns as $col) {
    $highlight = ($col['Field'] === 'verification_status') ? ' style="background:#ffffcc"' : '';
    echo "<tr{$highlight}><td>{$col['Field']}</td><td>{$col['Type']}</td><td>{$col['Null']}</td><td>{$col['Key']}</td><td>{$col['Default']}</td></tr>";
}
echo "</table>";

// Check clinics table structure
echo "<h3>clinics Table Columns:</h3>";
$stmt = $db->query("DESCRIBE clinics");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<table><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
foreach ($columns as $col) {
    $highlight = ($col['Field'] === 'verification_status') ? ' style="background:#ffffcc"' : '';
    echo "<tr{$highlight}><td>{$col['Field']}</td><td>{$col['Type']}</td><td>{$col['Null']}</td><td>{$col['Key']}</td><td>{$col['Default']}</td></tr>";
}
echo "</table>";

echo "<h2>2. Current Data Statistics</h2>";

// Total Users
$stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE is_active = 1");
$totalUsers = $stmt->fetch()['total'];
echo "<p><strong>Total Active Users:</strong> <span class='info'>{$totalUsers}</span></p>";

// Pending Registration Requests
$stmt = $db->query("SELECT COUNT(DISTINCT ur.user_id) as pending FROM user_roles ur WHERE ur.verification_status = 'pending'");
$pendingRequests = $stmt->fetch()['pending'];
echo "<p><strong>Pending Registration Requests:</strong> <span class='info'>{$pendingRequests}</span></p>";

// Verified Professionals
$stmt = $db->query("
    SELECT COUNT(DISTINCT ur.user_id) as professionals
    FROM user_roles ur
    JOIN roles r ON ur.role_id = r.id
    WHERE ur.verification_status = 'approved'
    AND r.role_name IN ('vet', 'groomer', 'breeder', 'sitter')
");
$verifiedProfessionals = $stmt->fetch()['professionals'];
echo "<p><strong>Verified Professionals:</strong> <span class='info'>{$verifiedProfessionals}</span></p>";

// Individual Professional Counts
$stmt = $db->query("
    SELECT r.role_name, COUNT(DISTINCT ur.user_id) as count
    FROM user_roles ur
    JOIN roles r ON ur.role_id = r.id
    WHERE ur.verification_status = 'approved'
    AND r.role_name IN ('vet', 'groomer', 'breeder', 'sitter')
    GROUP BY r.role_name
");
echo "<ul>";
while ($row = $stmt->fetch()) {
    echo "<li>{$row['role_name']}: <span class='info'>{$row['count']}</span></li>";
}
echo "</ul>";

// Active Clinics
$stmt = $db->query("SELECT COUNT(*) as clinics FROM clinics WHERE verification_status = 'approved' AND is_active = 1");
$activeClinics = $stmt->fetch()['clinics'];
echo "<p><strong>Active Clinics (Approved):</strong> <span class='info'>{$activeClinics}</span></p>";

// Pending Clinics
$stmt = $db->query("SELECT COUNT(*) as clinics FROM clinics WHERE verification_status = 'pending'");
$pendingClinics = $stmt->fetch()['clinics'];
echo "<p><strong>Pending Clinics:</strong> <span class='info'>{$pendingClinics}</span></p>";

echo "<h2>3. Pending Registration Details</h2>";

$stmt = $db->query("
    SELECT 
        ur.id as request_id,
        u.id as user_id,
        CONCAT(u.first_name, ' ', u.last_name) as name,
        u.email,
        u.phone,
        r.role_display_name as role,
        ur.verification_status,
        ur.is_active,
        ur.applied_at,
        ur.verification_notes
    FROM user_roles ur
    JOIN users u ON ur.user_id = u.id
    JOIN roles r ON ur.role_id = r.id
    WHERE ur.verification_status = 'pending'
    ORDER BY ur.applied_at DESC
");
$pendingList = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($pendingList)) {
    echo "<table>";
    echo "<tr><th>Request ID</th><th>User ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Active</th><th>Applied At</th></tr>";
    foreach ($pendingList as $p) {
        echo "<tr>";
        echo "<td>{$p['request_id']}</td>";
        echo "<td>{$p['user_id']}</td>";
        echo "<td>{$p['name']}</td>";
        echo "<td>{$p['email']}</td>";
        echo "<td>{$p['role']}</td>";
        echo "<td><span class='error'>{$p['verification_status']}</span></td>";
        echo "<td>{$p['is_active']}</td>";
        echo "<td>{$p['applied_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='info'>No pending registration requests found.</p>";
}

echo "<h2>4. All Clinic Manager Registrations</h2>";

$stmt = $db->query("
    SELECT 
        ur.id as request_id,
        u.id as user_id,
        CONCAT(u.first_name, ' ', u.last_name) as name,
        u.email,
        r.role_display_name as role,
        ur.verification_status,
        ur.is_active,
        ur.applied_at,
        c.clinic_name,
        c.verification_status as clinic_status,
        c.is_active as clinic_active
    FROM user_roles ur
    JOIN users u ON ur.user_id = u.id
    JOIN roles r ON ur.role_id = r.id
    LEFT JOIN clinic_manager_profiles cmp ON u.id = cmp.user_id
    LEFT JOIN clinics c ON cmp.clinic_id = c.id
    WHERE r.role_name = 'clinic_manager'
    ORDER BY ur.applied_at DESC
");
$clinicManagers = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($clinicManagers)) {
    echo "<table>";
    echo "<tr><th>Request ID</th><th>Name</th><th>Email</th><th>User Status</th><th>User Active</th><th>Clinic Name</th><th>Clinic Status</th><th>Clinic Active</th><th>Applied At</th></tr>";
    foreach ($clinicManagers as $cm) {
        $statusColor = $cm['verification_status'] === 'pending' ? 'error' : ($cm['verification_status'] === 'approved' ? 'success' : 'info');
        echo "<tr>";
        echo "<td>{$cm['request_id']}</td>";
        echo "<td>{$cm['name']}</td>";
        echo "<td>{$cm['email']}</td>";
        echo "<td><span class='{$statusColor}'>{$cm['verification_status']}</span></td>";
        echo "<td>{$cm['is_active']}</td>";
        echo "<td>" . ($cm['clinic_name'] ?? 'N/A') . "</td>";
        echo "<td>" . ($cm['clinic_status'] ?? 'N/A') . "</td>";
        echo "<td>" . ($cm['clinic_active'] ?? 'N/A') . "</td>";
        echo "<td>{$cm['applied_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='info'>No clinic manager registrations found.</p>";
}

echo "<h2>5. System Checks</h2>";

// Check if ManageUsersModel exists
$modelPath = __DIR__ . '/../models/Admin/ManageUsersModel.php';
if (file_exists($modelPath)) {
    echo "<p class='success'>✓ ManageUsersModel.php exists</p>";
} else {
    echo "<p class='error'>✗ ManageUsersModel.php NOT FOUND</p>";
}

// Check if API endpoints exist
$approveAPI = __DIR__ . '/../api/admin/approve-registration.php';
if (file_exists($approveAPI)) {
    echo "<p class='success'>✓ approve-registration.php exists</p>";
} else {
    echo "<p class='error'>✗ approve-registration.php NOT FOUND</p>";
}

$rejectAPI = __DIR__ . '/../api/admin/reject-registration.php';
if (file_exists($rejectAPI)) {
    echo "<p class='success'>✓ reject-registration.php exists</p>";
} else {
    echo "<p class='error'>✗ reject-registration.php NOT FOUND</p>";
}

// Check manage-users view
$viewPath = __DIR__ . '/../views/admin/manage-users.php';
if (file_exists($viewPath)) {
    echo "<p class='success'>✓ manage-users.php view exists</p>";
    
    // Check for JavaScript functions
    $viewContent = file_get_contents($viewPath);
    if (strpos($viewContent, 'function approveRegistration') !== false) {
        echo "<p class='success'>✓ approveRegistration() JavaScript function found</p>";
    } else {
        echo "<p class='error'>✗ approveRegistration() JavaScript function NOT FOUND</p>";
    }
    
    if (strpos($viewContent, 'function rejectRegistration') !== false) {
        echo "<p class='success'>✓ rejectRegistration() JavaScript function found</p>";
    } else {
        echo "<p class='error'>✗ rejectRegistration() JavaScript function NOT FOUND</p>";
    }
} else {
    echo "<p class='error'>✗ manage-users.php view NOT FOUND</p>";
}

echo "<h2>6. Test Instructions</h2>";
echo "<ol>";
echo "<li><strong>Register a Test Clinic Manager:</strong> Go to <a href='/PETVET/index.php?module=guest&page=clinic-manager-register' target='_blank'>/PETVET/index.php?module=guest&page=clinic-manager-register</a></li>";
echo "<li><strong>Check Pending Status:</strong> After registration, the clinic manager should have verification_status='pending' and is_active=0</li>";
echo "<li><strong>Login as Admin:</strong> Access the admin panel at <a href='/PETVET/index.php?module=admin&page=manage-users' target='_blank'>/PETVET/index.php?module=admin&page=manage-users</a></li>";
echo "<li><strong>View Pending Requests:</strong> Click the 'Pending Registration Requests' card to see the new registration</li>";
echo "<li><strong>Approve Registration:</strong> Click the 'Approve' button to activate the user and clinic</li>";
echo "<li><strong>Verify Activation:</strong> Refresh this page to confirm verification_status='approved' and is_active=1</li>";
echo "</ol>";

echo "<h2>✅ Verification Complete</h2>";
echo "<p>System is ready for testing. Reload this page after making changes to verify updates.</p>";
?>
