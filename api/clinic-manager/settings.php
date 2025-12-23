<?php
/**
 * Clinic Manager Settings API
 * Handles all settings updates for clinic managers
 */

session_start();
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../config/Auth.php';
require_once __DIR__ . '/../../config/ImageUploader.php';

header('Content-Type: application/json');

// Check authentication
$auth = new Auth();
if (!$auth->isLoggedIn() || !$auth->hasRole('clinic_manager')) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = $auth->getUserId();
$pdo = db();

// Get clinic_id for this manager
$stmt = $pdo->prepare("SELECT clinic_id FROM clinic_manager_profiles WHERE user_id = ?");
$stmt->execute([$userId]);
$clinicId = $stmt->fetchColumn();

if (!$clinicId) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Clinic not found']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'update_profile':
            $firstName = $_POST['first_name'] ?? '';
            $lastName = $_POST['last_name'] ?? '';
            $phone = $_POST['phone'] ?? '';
            
            $stmt = $pdo->prepare("
                UPDATE users 
                SET first_name = ?, last_name = ?, phone = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$firstName, $lastName, $phone, $userId]);
            
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
            break;
            
        case 'update_password':
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            if ($newPassword !== $confirmPassword) {
                throw new Exception('Passwords do not match');
            }
            
            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $hashedPassword = $stmt->fetchColumn();
            
            if (!password_verify($currentPassword, $hashedPassword)) {
                throw new Exception('Current password is incorrect');
            }
            
            // Update password
            $newHashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$newHashed, $userId]);
            
            echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
            break;
            
        case 'update_clinic':
            $clinicName = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $address = $_POST['address'] ?? '';
            $mapPin = $_POST['map_pin'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $email = $_POST['email'] ?? '';
            
            // Handle clinic logo upload
            $logoPath = null;
            if (isset($_FILES['clinicLogo']) && $_FILES['clinicLogo']['error'] === UPLOAD_ERR_OK) {
                $logoDir = __DIR__ . '/../../uploads/clinics/';
                if (!is_dir($logoDir)) {
                    mkdir($logoDir, 0755, true);
                }
                $uploader = new ImageUploader($logoDir);
                $uploadResult = $uploader->upload($_FILES['clinicLogo'], 'logo_');
                
                if ($uploadResult['success']) {
                    $logoPath = '/PETVET/uploads/clinics/' . $uploadResult['path'];
                } else {
                    throw new Exception('Failed to upload logo: ' . $uploadResult['message']);
                }
            }
            
            // Handle clinic cover upload
            $coverPath = null;
            if (isset($_FILES['clinicCover']) && $_FILES['clinicCover']['error'] === UPLOAD_ERR_OK) {
                $coverDir = __DIR__ . '/../../uploads/clinics/';
                if (!is_dir($coverDir)) {
                    mkdir($coverDir, 0755, true);
                }
                $uploader = new ImageUploader($coverDir);
                $uploadResult = $uploader->upload($_FILES['clinicCover'], 'cover_');
                
                if ($uploadResult['success']) {
                    $coverPath = '/PETVET/uploads/clinics/' . $uploadResult['path'];
                } else {
                    throw new Exception('Failed to upload cover: ' . $uploadResult['message']);
                }
            }
            
            // Build update query dynamically
            $updateFields = [
                'clinic_name = ?',
                'clinic_description = ?',
                'clinic_address = ?',
                'map_location = ?',
                'clinic_phone = ?',
                'clinic_email = ?',
                'updated_at = NOW()'
            ];
            $params = [$clinicName, $description, $address, $mapPin, $phone, $email];
            
            if ($logoPath !== null) {
                $updateFields[] = 'clinic_logo = ?';
                $params[] = $logoPath;
            }
            
            if ($coverPath !== null) {
                $updateFields[] = 'clinic_cover = ?';
                $params[] = $coverPath;
            }
            
            $params[] = $clinicId;
            
            $sql = "UPDATE clinics SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            echo json_encode(['success' => true, 'message' => 'Clinic profile updated successfully']);
            break;
            
        case 'update_preferences':
            $emailNotifications = isset($_POST['email_notifications']) ? 1 : 0;
            $slotTime = intval($_POST['slot_time'] ?? 20);
            
            $stmt = $pdo->prepare("
                INSERT INTO clinic_preferences (clinic_id, email_notifications, slot_duration_minutes)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                    email_notifications = VALUES(email_notifications),
                    slot_duration_minutes = VALUES(slot_duration_minutes),
                    updated_at = NOW()
            ");
            $stmt->execute([$clinicId, $emailNotifications, $slotTime]);
            
            echo json_encode(['success' => true, 'message' => 'Preferences saved successfully']);
            break;
            
        case 'update_weekly_schedule':
            $scheduleData = json_decode(file_get_contents('php://input'), true);
            
            if (!$scheduleData || !isset($scheduleData['schedule'])) {
                throw new Exception('Invalid schedule data');
            }
            
            $pdo->beginTransaction();
            
            foreach ($scheduleData['schedule'] as $day => $data) {
                $stmt = $pdo->prepare("
                    INSERT INTO clinic_weekly_schedule 
                    (clinic_id, day_of_week, is_enabled, start_time, end_time)
                    VALUES (?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                        is_enabled = VALUES(is_enabled),
                        start_time = VALUES(start_time),
                        end_time = VALUES(end_time),
                        updated_at = NOW()
                ");
                $stmt->execute([
                    $clinicId,
                    $day,
                    $data['enabled'] ? 1 : 0,
                    $data['start'],
                    $data['end']
                ]);
            }
            
            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Weekly schedule saved successfully']);
            break;
            
        case 'save_blocked_days':
            $blockedDays = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($blockedDays['dates'])) {
                throw new Exception('Invalid blocked days data');
            }
            
            $pdo->beginTransaction();
            
            // Clear existing blocked days
            $stmt = $pdo->prepare("DELETE FROM clinic_blocked_days WHERE clinic_id = ?");
            $stmt->execute([$clinicId]);
            
            // Insert new blocked days
            foreach ($blockedDays['dates'] as $blocked) {
                $stmt = $pdo->prepare("
                    INSERT INTO clinic_blocked_days (clinic_id, blocked_date, reason)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$clinicId, $blocked['date'], $blocked['reason']]);
            }
            
            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Blocked days saved successfully']);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
