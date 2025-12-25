<?php
session_start();
require_once '../../config/connect.php';

// Check if admin
if (!isset($_SESSION['current_role']) || $_SESSION['current_role'] !== 'admin') {
    die('Unauthorized');
}

$type = $_GET['type'] ?? 'users';
$format = $_GET['format'] ?? 'csv';

$pdo = db();

// Set headers for download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $type . '_report_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

switch ($type) {
    case 'users':
        // CSV Header
        fputcsv($output, ['ID', 'Name', 'Email', 'Role', 'Verification Status', 'Created At', 'Last Login']);
        
        // Get users
        $stmt = $pdo->query("
            SELECT u.id, CONCAT(u.first_name, ' ', u.last_name) as full_name, u.email, 
                   GROUP_CONCAT(r.role_display_name SEPARATOR ', ') as roles,
                   GROUP_CONCAT(DISTINCT ur.verification_status SEPARATOR ', ') as verification,
                   u.created_at, u.last_login
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            GROUP BY u.id
            ORDER BY u.created_at DESC
        ");
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, [
                $row['id'],
                $row['full_name'],
                $row['email'],
                $row['roles'] ?? 'No role',
                $row['verification'] ?? 'N/A',
                $row['created_at'],
                $row['last_login'] ?? 'Never'
            ]);
        }
        break;

    case 'clinics':
        fputcsv($output, ['ID', 'Clinic Name', 'Email', 'Phone', 'District', 'City', 'Status', 'Active', 'Created At']);
        
        $stmt = $pdo->query("
            SELECT id, clinic_name, clinic_email, clinic_phone, district, city, 
                   verification_status, is_active, created_at
            FROM clinics
            ORDER BY created_at DESC
        ");
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, [
                $row['id'],
                $row['clinic_name'],
                $row['clinic_email'],
                $row['clinic_phone'],
                $row['district'],
                $row['city'],
                $row['verification_status'],
                $row['is_active'] ? 'Yes' : 'No',
                $row['created_at']
            ]);
        }
        break;

    case 'appointments':
        fputcsv($output, ['ID', 'Pet Owner', 'Pet Name', 'Clinic', 'Date', 'Time', 'Status', 'Created At']);
        
        $stmt = $pdo->query("
            SELECT a.id, CONCAT(u.first_name, ' ', u.last_name) as owner, p.pet_name, c.clinic_name,
                   a.appointment_date, a.appointment_time, a.status, a.created_at
            FROM appointments a
            LEFT JOIN pets p ON a.pet_id = p.id
            LEFT JOIN users u ON p.user_id = u.id
            LEFT JOIN clinics c ON a.clinic_id = c.id
            ORDER BY a.created_at DESC
            LIMIT 1000
        ");
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, [
                $row['id'],
                $row['owner'] ?? 'N/A',
                $row['pet_name'] ?? 'N/A',
                $row['clinic_name'] ?? 'N/A',
                $row['appointment_date'],
                $row['appointment_time'],
                $row['status'],
                $row['created_at']
            ]);
        }
        break;

    case 'activity':
        fputcsv($output, ['Metric', 'Value']);
        
        // Get various stats
        $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $totalClinics = $pdo->query("SELECT COUNT(*) FROM clinics")->fetchColumn();
        $totalAppointments = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
        $activeToday = $pdo->query("SELECT COUNT(*) FROM users WHERE DATE(last_login) = CURDATE()")->fetchColumn();
        $newUsersThisMonth = $pdo->query("SELECT COUNT(*) FROM users WHERE MONTH(created_at) = MONTH(CURDATE())")->fetchColumn();
        
        fputcsv($output, ['Total Users', $totalUsers]);
        fputcsv($output, ['Total Clinics', $totalClinics]);
        fputcsv($output, ['Total Appointments', $totalAppointments]);
        fputcsv($output, ['Active Users Today', $activeToday]);
        fputcsv($output, ['New Users This Month', $newUsersThisMonth]);
        fputcsv($output, ['Report Generated', date('Y-m-d H:i:s')]);
        break;
}

fclose($output);
exit;
?>
