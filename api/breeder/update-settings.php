<?php
error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/auth_helper.php';

header('Content-Type: application/json');

// Check authentication
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = currentUserId();

// Check if this is FormData (multipart) or JSON request
$isFormData = !empty($_POST) || !empty($_FILES);

if ($isFormData) {
    // Parse FormData format: breeder[field_name]
    $data = [];
    if (!empty($_POST)) {
        foreach ($_POST as $key => $value) {
            // Parse nested form data like breeder[business_name]
            if (preg_match('/^(\w+)\[(\w+)\]$/', $key, $matches)) {
                $section = $matches[1];
                $field = $matches[2];
                if (!isset($data[$section])) {
                    $data[$section] = [];
                }
                $data[$section][$field] = $value;
            } else {
                $data[$key] = $value;
            }
        }
    }
} else {
    // JSON request
    $data = json_decode(file_get_contents('php://input'), true);
}

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit;
}

$pdo = db();
$pdo->beginTransaction();

try {
    $updatedAvatar = null;
    
    // Update profile section (users table) - same for all roles
    if (isset($data['profile'])) {
        $p = $data['profile'];
        
        // Handle avatar upload if present
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            require_once __DIR__ . '/../../config/ImageUploader.php';
            $uploader = new ImageUploader(__DIR__ . '/../../uploads/avatars/');
            $result = $uploader->upload($_FILES['avatar'], 'avatar_');
            
            if ($result['success']) {
                $p['avatar'] = '/PETVET/uploads/avatars/' . $result['path'];
                $updatedAvatar = $p['avatar'];
            }
        }
        
        // Update user profile
        if (isset($p['avatar'])) {
            $stmt = $pdo->prepare("
                UPDATE users 
                SET first_name = ?, last_name = ?, phone = ?, address = ?, avatar = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $p['first_name'], $p['last_name'], $p['phone'], $p['address'], $p['avatar'], $user_id
            ]);
        } else {
            $stmt = $pdo->prepare("
                UPDATE users 
                SET first_name = ?, last_name = ?, phone = ?, address = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $p['first_name'], $p['last_name'], $p['phone'], $p['address'], $user_id
            ]);
        }
    }

    // Update password if provided
    if (isset($data['password']) && !empty($data['password']['new_password'])) {
        // Verify current password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if (!password_verify($data['password']['current_password'], $user['password'])) {
            $pdo->rollBack();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
            exit;
        }

        // Update to new password
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([password_hash($data['password']['new_password'], PASSWORD_DEFAULT), $user_id]);
    }

    // Update or insert breeder profile
    if (isset($data['breeder'])) {
        $b = $data['breeder'];
        
        // Convert empty strings to null for numeric fields
        $b['experience_years'] = empty($b['experience_years']) ? null : $b['experience_years'];
        
        // Check if profile exists
        $stmt = $pdo->prepare("SELECT id FROM service_provider_profiles WHERE user_id = ? AND role_type = 'breeder'");
        $stmt->execute([$user_id]);
        $exists = $stmt->fetch();

        if ($exists) {
            // Update existing
            $stmt = $pdo->prepare("
                UPDATE service_provider_profiles 
                SET business_name = ?, license_number = ?, service_area = ?,
                    experience_years = ?, specializations = ?, services_description = ?,
                    phone_primary = ?, phone_secondary = ?,
                    location_latitude = ?, location_longitude = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE user_id = ? AND role_type = 'breeder'
            ");
            $stmt->execute([
                $b['business_name'], $b['license_number'], $b['service_area'],
                $b['experience_years'], $b['specializations'], $b['services_description'],
                $b['phone_primary'], $b['phone_secondary'],
                $b['location_latitude'] ?? null, $b['location_longitude'] ?? null,
                $user_id
            ]);
        } else {
            // Insert new
            $stmt = $pdo->prepare("
                INSERT INTO service_provider_profiles 
                (user_id, role_type, business_name, license_number, service_area,
                 experience_years, specializations, services_description,
                 phone_primary, phone_secondary, location_latitude, location_longitude)
                VALUES (?, 'breeder', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $user_id, $b['business_name'], $b['license_number'], $b['service_area'],
                $b['experience_years'], $b['specializations'], $b['services_description'],
                $b['phone_primary'], $b['phone_secondary'],
                $b['location_latitude'] ?? null, $b['location_longitude'] ?? null
            ]);
        }
    }

    // Update or insert preferences
    if (isset($data['preferences'])) {
        $pr = $data['preferences'];
        
        // Check if preferences exist
        $stmt = $pdo->prepare("SELECT id FROM service_provider_preferences WHERE user_id = ? AND role_type = 'breeder'");
        $stmt->execute([$user_id]);
        $exists = $stmt->fetch();

        if ($exists) {
            // Update existing
            $stmt = $pdo->prepare("
                UPDATE service_provider_preferences 
                SET email_notifications = ?, sms_notifications = ?, push_notifications = ?,
                    auto_accept_bookings = ?, require_deposit = ?,
                    show_availability_calendar = ?, accept_emergency_bookings = ?,
                    show_phone_in_profile = ?, show_address_in_profile = ?,
                    accept_online_payments = ?, updated_at = CURRENT_TIMESTAMP
                WHERE user_id = ? AND role_type = 'breeder'
            ");
            $stmt->execute([
                $pr['email_notifications'] ? 1 : 0,
                $pr['sms_notifications'] ? 1 : 0,
                $pr['push_notifications'] ? 1 : 0,
                $pr['auto_accept_bookings'] ? 1 : 0,
                $pr['require_deposit'] ? 1 : 0,
                $pr['show_availability_calendar'] ? 1 : 0,
                $pr['accept_emergency_bookings'] ? 1 : 0,
                $pr['show_phone_in_profile'] ? 1 : 0,
                $pr['show_address_in_profile'] ? 1 : 0,
                $pr['accept_online_payments'] ? 1 : 0,
                $user_id
            ]);
        } else {
            // Insert new
            $stmt = $pdo->prepare("
                INSERT INTO service_provider_preferences 
                (user_id, role_type, email_notifications, sms_notifications, push_notifications,
                 auto_accept_bookings, require_deposit, show_availability_calendar,
                 accept_emergency_bookings, show_phone_in_profile, show_address_in_profile,
                 accept_online_payments)
                VALUES (?, 'breeder', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $user_id,
                $pr['email_notifications'] ? 1 : 0,
                $pr['sms_notifications'] ? 1 : 0,
                $pr['push_notifications'] ? 1 : 0,
                $pr['auto_accept_bookings'] ? 1 : 0,
                $pr['require_deposit'] ? 1 : 0,
                $pr['show_availability_calendar'] ? 1 : 0,
                $pr['accept_emergency_bookings'] ? 1 : 0,
                $pr['show_phone_in_profile'] ? 1 : 0,
                $pr['show_address_in_profile'] ? 1 : 0,
                $pr['accept_online_payments'] ? 1 : 0
            ]);
        }
    }

    $pdo->commit();
    
    $response = ['success' => true, 'message' => 'Settings updated successfully'];
    if ($updatedAvatar) {
        $response['avatar'] = $updatedAvatar;
    }
    echo json_encode($response);

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Update breeder settings error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
