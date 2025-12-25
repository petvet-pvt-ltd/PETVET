<?php
session_start();
require_once '../../config/connect.php';
header('Content-Type: application/json');

// Check if admin
if (!isset($_SESSION['current_role']) || $_SESSION['current_role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

$type = $_GET['type'] ?? 'users';
$pdo = db();

$html = '';

try {
    switch ($type) {
        case 'users':
            $stmt = $pdo->query("
                SELECT u.id, CONCAT(u.first_name, ' ', u.last_name) as full_name, u.email, 
                       u.created_at, u.last_login
                FROM users u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                GROUP BY u.id
                ORDER BY u.created_at DESC
                LIMIT 100
            ");
            
            $html = '<table style="width:100%;border-collapse:collapse;font-size:14px;">';
            $html .= '<thead><tr style="background:#F3F4F6;"><th style="padding:12px;text-align:left;border-bottom:2px solid #E5E7EB;">ID</th><th style="padding:12px;text-align:left;border-bottom:2px solid #E5E7EB;">Name</th><th style="padding:12px;text-align:left;border-bottom:2px solid #E5E7EB;">Email</th><th style="padding:12px;text-align:left;border-bottom:2px solid #E5E7EB;">Roles</th><th style="padding:12px;text-align:left;border-bottom:2px solid #E5E7EB;">Status</th><th style="padding:12px;text-align:left;border-bottom:2px solid #E5E7EB;">Created</th></tr></thead><tbody>';
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $html .= '<tr style="border-bottom:1px solid #E5E7EB;">';
                $html .= '<td style="padding:12px;">' . htmlspecialchars($row['id']) . '</td>';
                $html .= '<td style="padding:12px;">' . htmlspecialchars($row['full_name']) . '</td>';
                $html .= '<td style="padding:12px;">' . htmlspecialchars($row['email']) . '</td>';
                $html .= '<td style="padding:12px;">' . htmlspecialchars($row['roles'] ?? 'No role') . '</td>';
                $html .= '<td style="padding:12px;"><span style="padding:4px 8px;border-radius:4px;background:' . ($row['verification'] === 'approved' ? '#D1FAE5;color:#059669' : '#FEF3C7;color:#D97706') . ';">' . htmlspecialchars($row['verification'] ?? 'N/A') . '</span></td>';
                $html .= '<td style="padding:12px;">' . date('M d, Y', strtotime($row['created_at'])) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';
            $html .= '<p style="margin-top:16px;color:#6B7280;font-size:14px;">Showing last 100 users. Download CSV for full report.</p>';
            break;

        case 'clinics':
            $stmt = $pdo->query("
                SELECT id, clinic_name, clinic_email, clinic_phone, district, city, 
                       verification_status, is_active, created_at
                FROM clinics
                ORDER BY created_at DESC
                LIMIT 100
            ");
            
            $html = '<table style="width:100%;border-collapse:collapse;font-size:14px;">';
            $html .= '<thead><tr style="background:#F3F4F6;"><th style="padding:12px;text-align:left;border-bottom:2px solid #E5E7EB;">ID</th><th style="padding:12px;text-align:left;border-bottom:2px solid #E5E7EB;">Clinic Name</th><th style="padding:12px;text-align:left;border-bottom:2px solid #E5E7EB;">Email</th><th style="padding:12px;text-align:left;border-bottom:2px solid #E5E7EB;">Location</th><th style="padding:12px;text-align:left;border-bottom:2px solid #E5E7EB;">Status</th><th style="padding:12px;text-align:left;border-bottom:2px solid #E5E7EB;">Active</th></tr></thead><tbody>';
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $html .= '<tr style="border-bottom:1px solid #E5E7EB;">';
                $html .= '<td style="padding:12px;">' . htmlspecialchars($row['id']) . '</td>';
                $html .= '<td style="padding:12px;">' . htmlspecialchars($row['clinic_name']) . '</td>';
                $html .= '<td style="padding:12px;">' . htmlspecialchars($row['clinic_email']) . '</td>';
                $html .= '<td style="padding:12px;">' . htmlspecialchars($row['district'] . ', ' . $row['city']) . '</td>';
                $html .= '<td style="padding:12px;"><span style="padding:4px 8px;border-radius:4px;background:' . ($row['verification_status'] === 'approved' ? '#D1FAE5;color:#059669' : ($row['verification_status'] === 'pending' ? '#FEF3C7;color:#D97706' : '#FEE2E2;color:#DC2626')) . ';">' . htmlspecialchars($row['verification_status']) . '</span></td>';
                $html .= '<td style="padding:12px;">' . ($row['is_active'] ? '✅ Yes' : '❌ No') . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';
            $html .= '<p style="margin-top:16px;color:#6B7280;font-size:14px;">Showing last 100 clinics. Download CSV for full report.</p>';
            break;

        case 'appointments':
            $stmt = $pdo->query("
                SELECT a.id, CONCAT(u.first_name, ' ', u.last_name) as owner, p.pet_name, c.clinic_name,
                       a.appointment_date, a.appointment_time, a.status, a.created_at
                FROM appointments a
                LEFT JOIN pets p ON a.pet_id = p.id
                LEFT JOIN users u ON p.user_id = u.id
                LEFT JOIN clinics c ON a.clinic_id = c.id
                ORDER BY a.created_at DESC
                LIMIT 100
            ");
            
            $html = '<table style="width:100%;border-collapse:collapse;font-size:14px;">';
            $html .= '<thead><tr style="background:#F3F4F6;"><th style="padding:12px;text-align:left;border-bottom:2px solid #E5E7EB;">ID</th><th style="padding:12px;text-align:left;border-bottom:2px solid #E5E7EB;">Owner</th><th style="padding:12px;text-align:left;border-bottom:2px solid #E5E7EB;">Pet</th><th style="padding:12px;text-align:left;border-bottom:2px solid #E5E7EB;">Clinic</th><th style="padding:12px;text-align:left;border-bottom:2px solid #E5E7EB;">Date & Time</th><th style="padding:12px;text-align:left;border-bottom:2px solid #E5E7EB;">Status</th></tr></thead><tbody>';
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $html .= '<tr style="border-bottom:1px solid #E5E7EB;">';
                $html .= '<td style="padding:12px;">' . htmlspecialchars($row['id']) . '</td>';
                $html .= '<td style="padding:12px;">' . htmlspecialchars($row['owner'] ?? 'N/A') . '</td>';
                $html .= '<td style="padding:12px;">' . htmlspecialchars($row['pet_name'] ?? 'N/A') . '</td>';
                $html .= '<td style="padding:12px;">' . htmlspecialchars($row['clinic_name'] ?? 'N/A') . '</td>';
                $html .= '<td style="padding:12px;">' . date('M d, Y', strtotime($row['appointment_date'])) . ' ' . date('h:i A', strtotime($row['appointment_time'])) . '</td>';
                $html .= '<td style="padding:12px;"><span style="padding:4px 8px;border-radius:4px;background:' . ($row['status'] === 'confirmed' ? '#D1FAE5;color:#059669' : ($row['status'] === 'pending' ? '#FEF3C7;color:#D97706' : '#FEE2E2;color:#DC2626')) . ';">' . htmlspecialchars($row['status']) . '</span></td>';
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';
            $html .= '<p style="margin-top:16px;color:#6B7280;font-size:14px;">Showing last 100 appointments. Download CSV for full report.</p>';
            break;

        case 'activity':
            $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
            $totalClinics = $pdo->query("SELECT COUNT(*) FROM clinics")->fetchColumn();
            $totalAppointments = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
            $activeToday = $pdo->query("SELECT COUNT(*) FROM users WHERE DATE(last_login) = CURDATE()")->fetchColumn();
            $newUsersThisMonth = $pdo->query("SELECT COUNT(*) FROM users WHERE MONTH(created_at) = MONTH(CURDATE())")->fetchColumn();
            
            $html = '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:24px;">';
            $html .= '<div style="background:#F3F4F6;padding:20px;border-radius:12px;"><div style="font-size:14px;color:#6B7280;margin-bottom:8px;">Total Users</div><div style="font-size:32px;font-weight:700;color:#1F2937;">' . $totalUsers . '</div></div>';
            $html .= '<div style="background:#F3F4F6;padding:20px;border-radius:12px;"><div style="font-size:14px;color:#6B7280;margin-bottom:8px;">Total Clinics</div><div style="font-size:32px;font-weight:700;color:#1F2937;">' . $totalClinics . '</div></div>';
            $html .= '<div style="background:#F3F4F6;padding:20px;border-radius:12px;"><div style="font-size:14px;color:#6B7280;margin-bottom:8px;">Total Appointments</div><div style="font-size:32px;font-weight:700;color:#1F2937;">' . $totalAppointments . '</div></div>';
            $html .= '<div style="background:#F3F4F6;padding:20px;border-radius:12px;"><div style="font-size:14px;color:#6B7280;margin-bottom:8px;">Active Today</div><div style="font-size:32px;font-weight:700;color:#1F2937;">' . $activeToday . '</div></div>';
            $html .= '<div style="background:#F3F4F6;padding:20px;border-radius:12px;"><div style="font-size:14px;color:#6B7280;margin-bottom:8px;">New This Month</div><div style="font-size:32px;font-weight:700;color:#1F2937;">' . $newUsersThisMonth . '</div></div>';
            $html .= '</div>';
            $html .= '<p style="color:#6B7280;font-size:14px;">Report generated on ' . date('F j, Y \a\t h:i A') . '</p>';
            break;
    }

    echo json_encode(['success' => true, 'html' => $html]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>
