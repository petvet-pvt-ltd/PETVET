<?php
session_start();
require_once '../../config/connect.php';
header('Content-Type: application/json');

// Add error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    // Get total users (approved only, excluding admin)
    $totalUsersQuery = "SELECT COUNT(DISTINCT u.id) as total
        FROM users u
        INNER JOIN user_roles ur ON u.id = ur.user_id
        INNER JOIN roles r ON ur.role_id = r.id
        WHERE r.role_name != 'admin' 
        AND ur.verification_status = 'approved'";
    $totalUsersResult = mysqli_query($conn, $totalUsersQuery);
    if (!$totalUsersResult) {
        throw new Exception('Total users query failed: ' . mysqli_error($conn));
    }
    $totalUsers = mysqli_fetch_assoc($totalUsersResult)['total'];

    // Get users from last month for growth calculation
    $lastMonthQuery = "SELECT COUNT(DISTINCT u.id) as total
        FROM users u
        INNER JOIN user_roles ur ON u.id = ur.user_id
        INNER JOIN roles r ON ur.role_id = r.id
        WHERE r.role_name != 'admin' 
        AND ur.verification_status = 'approved'
        AND u.created_at < DATE_SUB(NOW(), INTERVAL 1 MONTH)";
    $lastMonthResult = mysqli_query($conn, $lastMonthQuery);
    $lastMonthUsers = mysqli_fetch_assoc($lastMonthResult)['total'];
    
    // Calculate growth percentage
    $usersGrowth = 0;
    if ($lastMonthUsers > 0) {
        $usersGrowth = round((($totalUsers - $lastMonthUsers) / $lastMonthUsers) * 100);
    }

    // Get active users today (users who logged in today)
    $activeUsersQuery = "SELECT COUNT(DISTINCT id) as active
        FROM users
        WHERE DATE(last_login) = CURDATE()";
    $activeUsersResult = mysqli_query($conn, $activeUsersQuery);
    $activeUsersToday = mysqli_fetch_assoc($activeUsersResult)['active'];

    // Get pending clinic approvals
    $pendingClinicsQuery = "SELECT COUNT(*) as pending
        FROM clinics
        WHERE verification_status = 'pending'";
    $pendingClinicsResult = mysqli_query($conn, $pendingClinicsQuery);
    $pendingRequests = mysqli_fetch_assoc($pendingClinicsResult)['pending'];

    // Get total clinics
    $totalClinicsQuery = "SELECT COUNT(*) as total FROM clinics";
    $totalClinicsResult = mysqli_query($conn, $totalClinicsQuery);
    $totalClinics = mysqli_fetch_assoc($totalClinicsResult)['total'];

    // Get role distribution
    $roleDistQuery = "SELECT 
        r.role_name,
        r.role_display_name,
        COUNT(DISTINCT ur.user_id) as count
        FROM user_roles ur
        INNER JOIN roles r ON ur.role_id = r.id
        WHERE r.role_name != 'admin' 
        AND ur.verification_status = 'approved'
        GROUP BY r.id, r.role_name, r.role_display_name";
    $roleDistResult = mysqli_query($conn, $roleDistQuery);
    
    $roleDistribution = [];
    $totalRoleUsers = 0;
    while ($row = mysqli_fetch_assoc($roleDistResult)) {
        $roleDistribution[] = $row;
        $totalRoleUsers += $row['count'];
    }

    // Calculate percentages
    foreach ($roleDistribution as &$role) {
        $role['percentage'] = $totalRoleUsers > 0 
            ? round(($role['count'] / $totalRoleUsers) * 100) 
            : 0;
    }

    // Get user growth data (last 12 months)
    $growthDataQuery = "SELECT 
        DATE_FORMAT(u.created_at, '%Y-%m') as month,
        COUNT(DISTINCT u.id) as count
        FROM users u
        INNER JOIN user_roles ur ON u.id = ur.user_id
        INNER JOIN roles r ON ur.role_id = r.id
        WHERE r.role_name != 'admin' 
        AND ur.verification_status = 'approved'
        AND u.created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(u.created_at, '%Y-%m')
        ORDER BY month ASC";
    $growthDataResult = mysqli_query($conn, $growthDataQuery);
    
    $growthData = [];
    while ($row = mysqli_fetch_assoc($growthDataResult)) {
        $growthData[] = $row;
    }

    // Get recent appointments
    $recentAppointmentsQuery = "SELECT 
        a.id,
        a.appointment_date,
        a.appointment_time,
        a.status,
        u.first_name,
        u.last_name,
        u.email,
        c.clinic_name,
        p.name as pet_name
        FROM appointments a
        LEFT JOIN users u ON a.user_id = u.id
        LEFT JOIN clinics c ON a.clinic_id = c.id
        LEFT JOIN pets p ON a.pet_id = p.id
        ORDER BY a.created_at DESC
        LIMIT 10";
    $recentAppointmentsResult = mysqli_query($conn, $recentAppointmentsQuery);
    
    $recentAppointments = [];
    while ($row = mysqli_fetch_assoc($recentAppointmentsResult)) {
        $recentAppointments[] = $row;
    }

    // Get today's appointments count
    $todayAppointmentsQuery = "SELECT COUNT(*) as count
        FROM appointments
        WHERE DATE(appointment_date) = CURDATE()";
    $todayAppointmentsResult = mysqli_query($conn, $todayAppointmentsQuery);
    $todayAppointments = mysqli_fetch_assoc($todayAppointmentsResult)['count'];

    // Get new users today
    $newUsersTodayQuery = "SELECT COUNT(DISTINCT u.id) as count
        FROM users u
        INNER JOIN user_roles ur ON u.id = ur.user_id
        INNER JOIN roles r ON ur.role_id = r.id
        WHERE r.role_name != 'admin' 
        AND DATE(u.created_at) = CURDATE()";
    $newUsersTodayResult = mysqli_query($conn, $newUsersTodayQuery);
    $newUsersToday = mysqli_fetch_assoc($newUsersTodayResult)['count'];

    echo json_encode([
        'success' => true,
        'stats' => [
            'totalUsers' => (int)$totalUsers,
            'usersGrowth' => $usersGrowth >= 0 ? "+$usersGrowth%" : "$usersGrowth%",
            'activeUsersToday' => (int)$activeUsersToday,
            'pendingRequests' => (int)$pendingRequests,
            'totalClinics' => (int)$totalClinics,
            'todayAppointments' => (int)$todayAppointments,
            'newUsersToday' => (int)$newUsersToday
        ],
        'roleDistribution' => $roleDistribution,
        'growthData' => $growthData,
        'recentAppointments' => $recentAppointments
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
