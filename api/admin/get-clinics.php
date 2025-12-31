<?php
require_once '../../config/connect.php';
header('Content-Type: application/json');

try {
    // Get filter parameters
    $status = isset($_GET['status']) ? $_GET['status'] : 'all';
    
    $query = "SELECT 
        c.id,
        c.clinic_name,
        c.clinic_email,
        c.clinic_phone,
        c.clinic_address,
        c.district,
        c.city,
        c.verification_status,
        c.is_active,
        c.created_at,
        c.clinic_logo,
        COUNT(DISTINCT cs.id) as staff_count,
        COUNT(DISTINCT a.id) as appointment_count
    FROM clinics c
    LEFT JOIN clinic_staff cs ON c.id = cs.clinic_id
    LEFT JOIN appointments a ON c.id = a.clinic_id
    WHERE 1=1";
    
    // Apply status filter
    if ($status === 'pending') {
        $query .= " AND c.verification_status = 'pending'";
    } elseif ($status === 'approved') {
        $query .= " AND c.verification_status = 'approved'";
    } elseif ($status === 'rejected') {
        $query .= " AND c.verification_status = 'rejected'";
    }
    
    $query .= " GROUP BY c.id ORDER BY 
        CASE c.verification_status
            WHEN 'pending' THEN 1
            WHEN 'approved' THEN 2
            WHEN 'rejected' THEN 3
        END,
        c.created_at DESC";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception(mysqli_error($conn));
    }
    
    $clinics = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $clinics[] = $row;
    }
    
    // Get statistics
    $statsQuery = "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN verification_status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN verification_status = 'approved' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN verification_status = 'rejected' THEN 1 ELSE 0 END) as rejected,
        SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active
    FROM clinics";
    
    $statsResult = mysqli_query($conn, $statsQuery);
    $stats = mysqli_fetch_assoc($statsResult);
    
    echo json_encode([
        'success' => true,
        'clinics' => $clinics,
        'stats' => $stats
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
