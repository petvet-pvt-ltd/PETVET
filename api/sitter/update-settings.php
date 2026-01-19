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
    // Parse FormData format: sitter[field_name]
    $data = [];
    if (!empty($_POST)) {
        foreach ($_POST as $key => $value) {
            // Parse nested form data like sitter[business_name]
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

function normalize_service_areas($input) {
    $allowed = [
        'Ampara','Anuradhapura','Badulla','Batticaloa','Colombo','Galle','Gampaha','Hambantota',
        'Jaffna','Kalutara','Kandy','Kegalle','Kilinochchi','Kurunegala','Mannar','Matale',
        'Matara','Monaragala','Mullaitivu','Nuwara Eliya','Polonnaruwa','Puttalam','Ratnapura',
        'Trincomalee','Vavuniya'
    ];
    $allowedMap = [];
    foreach ($allowed as $d) {
        $allowedMap[strtolower($d)] = $d;
    }

    $areas = [];
    if (is_array($input)) {
        $areas = $input;
    } else {
        $str = trim((string)($input ?? ''));
        if ($str !== '' && str_starts_with($str, '[')) {
            $decoded = json_decode($str, true);
            if (is_array($decoded)) {
                $areas = $decoded;
            } else {
                $areas = array_map('trim', explode(',', $str));
            }
        } else {
            $areas = $str === '' ? [] : array_map('trim', explode(',', $str));
        }
    }

    $clean = [];
    $seen = [];
    foreach ($areas as $a) {
        $a = trim((string)$a);
        if ($a === '') continue;
        $key = strtolower($a);
        if (isset($allowedMap[$key])) {
            $a = $allowedMap[$key];
        } else {
            continue;
        }
        $k = strtolower($a);
        if (!isset($seen[$k])) {
            $seen[$k] = true;
            $clean[] = $a;
        }
    }

    if (count($clean) > 5) {
        return ['error' => 'You can select up to 5 working areas.'];
    }

    return ['areas' => $clean];
}

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

    // Update or insert sitter profile
    if (isset($data['sitter'])) {
        $s = $data['sitter'];
        
        // Convert empty strings to null for numeric fields
        $s['experience_years'] = empty($s['experience_years']) ? null : $s['experience_years'];
        
        // Normalize home_type to match enum values
        if (!empty($s['home_type'])) {
            $allowed_home_types = ['apartment', 'house_with_yard', 'house_without_yard', 'farm', 'other'];
            if (!in_array($s['home_type'], $allowed_home_types)) {
                $s['home_type'] = 'other';
            }
        }
        
        // Check if profile exists
        $stmt = $pdo->prepare("SELECT id FROM service_provider_profiles WHERE user_id = ? AND role_type = 'sitter'");
        $stmt->execute([$user_id]);
        $exists = $stmt->fetch();

        if ($exists) {
            // Update existing
            $stmt = $pdo->prepare("
                UPDATE service_provider_profiles 
                SET service_area = ?, experience_years = ?, bio = ?,
                    pet_types = ?, home_type = ?,
                    phone_primary = ?, phone_secondary = ?,
                    location_latitude = ?, location_longitude = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE user_id = ? AND role_type = 'sitter'
            ");
            $stmt->execute([
                $s['work_area'] ?? null, 
                $s['experience_years'], $s['bio'] ?? null,
                $s['pet_types'] ?? null, $s['home_type'] ?? null,
                $s['phone_primary'], $s['phone_secondary'] ?? null,
                $s['location_latitude'] ?? null, $s['location_longitude'] ?? null,
                $user_id
            ]);
        } else {
            // Insert new
            $stmt = $pdo->prepare("
                INSERT INTO service_provider_profiles 
                (user_id, role_type, service_area, experience_years, bio,
                 pet_types, home_type, phone_primary, phone_secondary, location_latitude, location_longitude)
                VALUES (?, 'sitter', ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $user_id, $s['work_area'] ?? null,
                $s['experience_years'], $s['bio'] ?? null,
                $s['pet_types'] ?? null, $s['home_type'] ?? null,
                $s['phone_primary'], $s['phone_secondary'] ?? null,
                $s['location_latitude'] ?? null, $s['location_longitude'] ?? null
            ]);
        }
    }

    // Update or insert preferences
    if (isset($data['preferences'])) {
        $pr = $data['preferences'];
        
        // Check if preferences exist
        $stmt = $pdo->prepare("SELECT id FROM service_provider_preferences WHERE user_id = ? AND role_type = 'sitter'");
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
                WHERE user_id = ? AND role_type = 'sitter'
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
                VALUES (?, 'sitter', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
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
    error_log("Update sitter settings error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
