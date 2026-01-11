<?php
session_start();
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../config/auth_helper.php';

header('Content-Type: application/json');

// Check authentication
if (!isLoggedIn() || currentRole() !== 'clinic_manager') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['staff']) || !isset($input['date'])) {
        throw new Exception('Missing required fields');
    }
    
    $staff = $input['staff'];
    $date = $input['date'];
    $userId = $_SESSION['user_id'];
    
    // Get clinic_id
    $pdo = db();
    $stmt = $pdo->prepare("SELECT clinic_id FROM clinic_manager_profiles WHERE user_id = ?");
    $stmt->execute([$userId]);
    $clinicId = $stmt->fetchColumn();
    
    if (!$clinicId) {
        throw new Exception('Clinic not found');
    }
    
    // Validate date is today
    if ($date !== date('Y-m-d')) {
        throw new Exception('Can only schedule for today');
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Delete existing schedule for today
    $stmt = $pdo->prepare("DELETE FROM staff_duty_schedule WHERE clinic_id = ? AND duty_date = ?");
    $stmt->execute([$clinicId, $date]);
    
    // Insert new schedule
    $stmt = $pdo->prepare("
        INSERT INTO staff_duty_schedule (clinic_id, staff_id, duty_date, shift_time)
        VALUES (?, ?, ?, ?)
    ");
    
    foreach ($staff as $member) {
        $stmt->execute([
            $clinicId,
            $member['id'],
            $date,
            $member['time'] ?? '08:00 – 16:00'
        ]);
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Staff schedule saved successfully',
        'count' => count($staff)
    ]);
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("Save duty staff error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error' => $e->getMessage()
    ]);
}
?>
