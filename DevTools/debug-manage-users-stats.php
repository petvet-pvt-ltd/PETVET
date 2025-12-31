<?php
/**
 * Debug Manage Users Statistics
 * Check what data is being returned from ManageUsersModel
 */

session_start();
require_once __DIR__ . '/../models/Admin/ManageUsersModel.php';

echo "<h1>Debug: Manage Users Stats</h1>";
echo "<style>body{font-family:Arial;padding:20px}pre{background:#f4f4f4;padding:15px;border-radius:5px;overflow:auto}table{border-collapse:collapse;margin:20px 0}th,td{border:1px solid #ddd;padding:8px}th{background:#333;color:white}</style>";

echo "<h2>1. Direct Database Queries</h2>";

require_once __DIR__ . '/../config/connect.php';
$db = db();

echo "<h3>Total Users Query:</h3>";
$stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE is_active = 1");
$totalUsers = $stmt->fetch()['total'];
echo "<p><strong>Result:</strong> $totalUsers</p>";

echo "<h3>Pending Requests Query:</h3>";
$stmt = $db->query("
    SELECT COUNT(DISTINCT ur.user_id) as pending 
    FROM user_roles ur 
    WHERE ur.verification_status = 'pending'
");
$pendingRequests = $stmt->fetch()['pending'];
echo "<p><strong>Result:</strong> $pendingRequests</p>";

echo "<h3>Verified Professionals Query:</h3>";
$query = "
    SELECT COUNT(DISTINCT ur.user_id) as professionals
    FROM user_roles ur
    JOIN roles r ON ur.role_id = r.id
    WHERE ur.verification_status = 'approved'
    AND r.role_name IN ('vet', 'groomer', 'breeder', 'sitter')
";
echo "<pre>$query</pre>";
$stmt = $db->query($query);
$verifiedProfessionals = $stmt->fetch()['professionals'];
echo "<p><strong>Result:</strong> $verifiedProfessionals</p>";

echo "<h3>Active Clinics Query:</h3>";
$query = "
    SELECT COUNT(*) as clinics 
    FROM clinics 
    WHERE verification_status = 'approved' AND is_active = 1
";
echo "<pre>$query</pre>";
$stmt = $db->query($query);
$activeClinics = $stmt->fetch()['clinics'];
echo "<p><strong>Result:</strong> $activeClinics</p>";

echo "<h2>2. Individual Professional Counts:</h2>";
$query = "
    SELECT r.role_name, COUNT(DISTINCT ur.user_id) as count
    FROM user_roles ur
    JOIN roles r ON ur.role_id = r.id
    WHERE ur.verification_status = 'approved'
    AND r.role_name IN ('vet', 'groomer', 'breeder', 'sitter')
    GROUP BY r.role_name
";
echo "<pre>$query</pre>";
$stmt = $db->query($query);
$professionalCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<table><tr><th>Role</th><th>Count</th></tr>";
if (!empty($professionalCounts)) {
    foreach ($professionalCounts as $row) {
        echo "<tr><td>{$row['role_name']}</td><td>{$row['count']}</td></tr>";
    }
} else {
    echo "<tr><td colspan='2'>No professionals found</td></tr>";
}
echo "</table>";

echo "<h2>3. ManageUsersModel Output:</h2>";
$model = new ManageUsersModel();
$data = $model->fetchUsersData();

echo "<h3>Stats Array:</h3>";
echo "<pre>";
print_r($data['stats']);
echo "</pre>";

echo "<h3>Pending Requests Array:</h3>";
echo "<pre>";
print_r($data['pendingRequests']);
echo "</pre>";

echo "<h2>4. All User Roles:</h2>";
$stmt = $db->query("
    SELECT 
        ur.id,
        ur.user_id,
        u.email,
        u.first_name,
        u.last_name,
        r.role_name,
        r.role_display_name,
        ur.verification_status,
        ur.is_active,
        ur.applied_at
    FROM user_roles ur
    JOIN users u ON ur.user_id = u.id
    JOIN roles r ON ur.role_id = r.id
    ORDER BY ur.applied_at DESC
    LIMIT 20
");
$allRoles = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<table>";
echo "<tr><th>ID</th><th>User</th><th>Email</th><th>Role</th><th>Status</th><th>Active</th><th>Applied At</th></tr>";
foreach ($allRoles as $role) {
    $statusColor = $role['verification_status'] === 'pending' ? 'red' : ($role['verification_status'] === 'approved' ? 'green' : 'orange');
    echo "<tr>";
    echo "<td>{$role['id']}</td>";
    echo "<td>{$role['first_name']} {$role['last_name']}</td>";
    echo "<td>{$role['email']}</td>";
    echo "<td>{$role['role_display_name']}</td>";
    echo "<td style='color:{$statusColor}'><strong>{$role['verification_status']}</strong></td>";
    echo "<td>{$role['is_active']}</td>";
    echo "<td>{$role['applied_at']}</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>5. All Clinics:</h2>";
$stmt = $db->query("
    SELECT 
        id,
        clinic_name,
        district,
        verification_status,
        is_active,
        created_at
    FROM clinics
    ORDER BY created_at DESC
");
$allClinics = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!empty($allClinics)) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Clinic Name</th><th>District</th><th>Verification Status</th><th>Active</th><th>Created At</th></tr>";
    foreach ($allClinics as $clinic) {
        $statusColor = $clinic['verification_status'] === 'pending' ? 'red' : ($clinic['verification_status'] === 'approved' ? 'green' : 'orange');
        echo "<tr>";
        echo "<td>{$clinic['id']}</td>";
        echo "<td>{$clinic['clinic_name']}</td>";
        echo "<td>{$clinic['district']}</td>";
        echo "<td style='color:{$statusColor}'><strong>{$clinic['verification_status']}</strong></td>";
        echo "<td>{$clinic['is_active']}</td>";
        echo "<td>{$clinic['created_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No clinics found in database.</p>";
}

echo "<h2>6. Roles Table:</h2>";
$stmt = $db->query("SELECT * FROM roles");
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<table>";
echo "<tr><th>ID</th><th>Role Name</th><th>Role Display Name</th></tr>";
foreach ($roles as $role) {
    echo "<tr><td>{$role['id']}</td><td>{$role['role_name']}</td><td>{$role['role_display_name']}</td></tr>";
}
echo "</table>";

?>
