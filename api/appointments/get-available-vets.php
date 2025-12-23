<?php
/**
 * Get Available Vets API
 * Returns vets who are NOT booked at a specific date/time
 * 
 * Input: clinic_id, appointment_date, appointment_time, exclude_appointment_id (optional)
 * Output: Array of available vets
 */

header('Content-Type: application/json');

// Get parameters
$clinic_id = isset($_GET['clinic_id']) ? intval($_GET['clinic_id']) : 0;
$appointment_date = isset($_GET['appointment_date']) ? $_GET['appointment_date'] : '';
$appointment_time = isset($_GET['appointment_time']) ? $_GET['appointment_time'] : '';
$exclude_appointment_id = isset($_GET['exclude_appointment_id']) ? intval($_GET['exclude_appointment_id']) : 0;

if (!$clinic_id || !$appointment_date || !$appointment_time) {
    echo json_encode([
        'success' => false,
        'error' => 'Missing required parameters'
    ]);
    exit;
}

try {
    require_once '../../config/connect.php';
    global $conn;
    
    // Get all vets at this specific clinic
    $vetsQuery = "
        SELECT DISTINCT u.id, u.first_name, u.last_name, CONCAT(u.first_name, ' ', u.last_name) as name, u.avatar
        FROM users u
        JOIN vets v ON u.id = v.user_id
        WHERE v.available = 1
        AND u.is_active = 1
        AND v.clinic_id = ?
        ORDER BY u.first_name, u.last_name
    ";
    
    $stmt = mysqli_prepare($conn, $vetsQuery);
    mysqli_stmt_bind_param($stmt, "i", $clinic_id);
    mysqli_stmt_execute($stmt);
    $vetsResult = mysqli_stmt_get_result($stmt);
    
    if (!$vetsResult) {
        throw new Exception('Failed to fetch vets: ' . mysqli_error($conn));
    }
    
    $allVets = [];
    while ($row = mysqli_fetch_assoc($vetsResult)) {
        $allVets[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'avatar' => !empty($row['avatar']) ? $row['avatar'] : '/PETVET/public/images/emptyProfPic.png'
        ];
    }
    
    mysqli_stmt_close($stmt);
    
    // Get vets who are already booked at this time
    $bookedQuery = "
        SELECT DISTINCT vet_id
        FROM appointments
        WHERE clinic_id = ?
        AND appointment_date = ?
        AND appointment_time = ?
        AND status NOT IN ('declined', 'cancelled')
        AND vet_id IS NOT NULL
    ";
    
    if ($exclude_appointment_id > 0) {
        $bookedQuery .= " AND id != ?";
    }
    
    $stmt = mysqli_prepare($conn, $bookedQuery);
    
    if ($exclude_appointment_id > 0) {
        mysqli_stmt_bind_param($stmt, "issi", $clinic_id, $appointment_date, $appointment_time, $exclude_appointment_id);
    } else {
        mysqli_stmt_bind_param($stmt, "iss", $clinic_id, $appointment_date, $appointment_time);
    }
    
    mysqli_stmt_execute($stmt);
    $bookedResult = mysqli_stmt_get_result($stmt);
    
    $bookedVetIds = [];
    while ($row = mysqli_fetch_assoc($bookedResult)) {
        $bookedVetIds[] = $row['vet_id'];
    }
    
    mysqli_stmt_close($stmt);
    
    // Filter out booked vets
    $availableVets = array_filter($allVets, function($vet) use ($bookedVetIds) {
        return !in_array($vet['id'], $bookedVetIds);
    });
    
    // Re-index array
    $availableVets = array_values($availableVets);
    
    echo json_encode([
        'success' => true,
        'available_vets' => $availableVets,
        'booked_vet_ids' => $bookedVetIds,
        'total_vets' => count($allVets),
        'available_count' => count($availableVets)
    ]);
    
} catch (Exception $e) {
    error_log("Get available vets error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
