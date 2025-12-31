<?php
require_once '../../config/connect.php';
header('Content-Type: application/json');

try {
    // Get filter parameters
    $roleFilter = isset($_GET['role']) ? $_GET['role'] : 'all';
    
    $query = "SELECT DISTINCT
        u.id,
        u.email,
        u.first_name,
        u.last_name,
        u.phone,
        u.address,
        u.avatar,
        u.is_active,
        u.is_blocked,
        u.last_login,
        u.created_at,
        GROUP_CONCAT(DISTINCT r.role_display_name ORDER BY ur.is_primary DESC SEPARATOR ', ') as roles,
        GROUP_CONCAT(DISTINCT r.role_name ORDER BY ur.is_primary DESC SEPARATOR ',') as role_names,
        MAX(CASE WHEN ur.is_primary = 1 THEN r.role_display_name END) as primary_role
    FROM users u
    INNER JOIN user_roles ur ON u.id = ur.user_id
    INNER JOIN roles r ON ur.role_id = r.id
    WHERE r.role_name != 'admin' 
    AND ur.verification_status = 'approved'";
    
    // Apply role filter
    if ($roleFilter !== 'all') {
        $roleFilter = mysqli_real_escape_string($conn, $roleFilter);
        $query .= " AND u.id IN (
            SELECT user_id FROM user_roles ur2
            INNER JOIN roles r2 ON ur2.role_id = r2.id
            WHERE r2.role_name = '$roleFilter'
        )";
    }
    
    $query .= " GROUP BY u.id ORDER BY u.created_at DESC";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception(mysqli_error($conn));
    }
    
    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Get additional role-specific data
        $roleData = [];
        $roleNames = explode(',', $row['role_names']);
        
        // Get pet count for pet owners
        if (in_array('pet_owner', $roleNames)) {
            $petQuery = "SELECT COUNT(*) as pet_count FROM pets WHERE user_id = {$row['id']}";
            $petResult = mysqli_query($conn, $petQuery);
            $petData = mysqli_fetch_assoc($petResult);
            $roleData['pet_count'] = $petData['pet_count'];
        }
        
        // Get clinic for clinic-related roles
        if (in_array('vet', $roleNames) || in_array('clinic_manager', $roleNames)) {
            $clinicQuery = "SELECT c.clinic_name, c.id as clinic_id 
                           FROM clinic_staff cs 
                           INNER JOIN clinics c ON cs.clinic_id = c.id 
                           WHERE cs.user_id = {$row['id']} LIMIT 1";
            $clinicResult = mysqli_query($conn, $clinicQuery);
            if ($clinicData = mysqli_fetch_assoc($clinicResult)) {
                $roleData['clinic_name'] = $clinicData['clinic_name'];
                $roleData['clinic_id'] = $clinicData['clinic_id'];
            }
        }
        
        $row['role_data'] = $roleData;
        $users[] = $row;
    }
    
    // Get statistics
    $statsQuery = "SELECT 
        COUNT(DISTINCT u.id) as total_users,
        SUM(CASE WHEN r.role_name = 'pet_owner' THEN 1 ELSE 0 END) as pet_owners,
        SUM(CASE WHEN r.role_name = 'trainer' THEN 1 ELSE 0 END) as trainers,
        SUM(CASE WHEN r.role_name = 'sitter' THEN 1 ELSE 0 END) as sitters,
        SUM(CASE WHEN r.role_name = 'breeder' THEN 1 ELSE 0 END) as breeders,
        SUM(CASE WHEN r.role_name = 'groomer' THEN 1 ELSE 0 END) as groomers,
        SUM(CASE WHEN r.role_name = 'vet' THEN 1 ELSE 0 END) as vets
    FROM users u
    INNER JOIN user_roles ur ON u.id = ur.user_id
    INNER JOIN roles r ON ur.role_id = r.id
    WHERE r.role_name != 'admin' 
    AND ur.verification_status = 'approved'";
    
    $statsResult = mysqli_query($conn, $statsQuery);
    $stats = mysqli_fetch_assoc($statsResult);
    
    echo json_encode([
        'success' => true,
        'users' => $users,
        'stats' => $stats
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
