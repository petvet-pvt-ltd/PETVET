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
    // Parse FormData format: groomer[field_name]
    $data = [];
    if (!empty($_POST)) {
        foreach ($_POST as $key => $value) {
            // Parse nested form data like groomer[business_name]
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
    // Update profile section (users table) - same for all roles
    if (isset($data['profile'])) {
        $p = $data['profile'];
        $stmt = $pdo->prepare("
            UPDATE users 
            SET first_name = ?, last_name = ?, phone = ?, address = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $p['first_name'], $p['last_name'], $p['phone'], $p['address'], $user_id
        ]);
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

    // Update or insert groomer profile
    if (isset($data['groomer'])) {
        $g = $data['groomer'];
        
        // Convert empty strings to null for numeric fields
        $g['experience_years'] = empty($g['experience_years']) ? null : $g['experience_years'];
        
        // Handle business logo upload if present
        if (isset($_FILES['business_logo']) && $_FILES['business_logo']['error'] === UPLOAD_ERR_OK) {
            require_once __DIR__ . '/../../config/ImageUploader.php';
            $uploader = new ImageUploader(__DIR__ . '/../../uploads/business_logos/');
            $result = $uploader->upload($_FILES['business_logo'], 'logo_');
            
            if ($result['success']) {
                $g['business_logo'] = '/PETVET/uploads/business_logos/' . $result['path'];
            }
        }
        
        // Check if profile exists
        $stmt = $pdo->prepare("SELECT id FROM service_provider_profiles WHERE user_id = ? AND role_type = 'groomer'");
        $stmt->execute([$user_id]);
        $exists = $stmt->fetch();

        if ($exists) {
            // Preserve existing business_logo when user saves without uploading a new file.
            // (Otherwise we unintentionally wipe the logo and the UI falls back to avatar.)
            if (!array_key_exists('business_logo', $g)) {
                $stmt = $pdo->prepare("SELECT business_logo FROM service_provider_profiles WHERE user_id = ? AND role_type = 'groomer'");
                $stmt->execute([$user_id]);
                $current = $stmt->fetch(PDO::FETCH_ASSOC);
                $g['business_logo'] = $current['business_logo'] ?? null;
            }

            // Update existing
            $stmt = $pdo->prepare("
                UPDATE service_provider_profiles 
                SET business_name = ?, business_logo = ?, service_area = ?, experience_years = ?, specializations = ?,
                    certifications = ?, bio = ?,
                    phone_primary = ?, phone_secondary = ?,
                    location_latitude = ?, location_longitude = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE user_id = ? AND role_type = 'groomer'
            ");
            $stmt->execute([
                $g['business_name'], $g['business_logo'] ?? null, $g['service_area'], $g['experience_years'], $g['specializations'],
                $g['certifications'], $g['bio'],
                $g['phone_primary'], $g['phone_secondary'],
                $g['location_latitude'] ?? null, $g['location_longitude'] ?? null,
                $user_id
            ]);
        } else {
            // Insert new
            $stmt = $pdo->prepare("
                INSERT INTO service_provider_profiles 
                (user_id, role_type, business_name, business_logo, service_area, experience_years, specializations,
                 certifications, bio, phone_primary, phone_secondary, location_latitude, location_longitude)
                VALUES (?, 'groomer', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $user_id, $g['business_name'], $g['business_logo'] ?? null, $g['service_area'], $g['experience_years'], $g['specializations'],
                $g['certifications'], $g['bio'],
                $g['phone_primary'], $g['phone_secondary'],
                $g['location_latitude'] ?? null, $g['location_longitude'] ?? null
            ]);
        }
    }

    // Update or insert preferences
    if (isset($data['preferences'])) {
        $pr = $data['preferences'];
        
        // Check if preferences exist
        $stmt = $pdo->prepare("SELECT id FROM service_provider_preferences WHERE user_id = ? AND role_type = 'groomer'");
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
                WHERE user_id = ? AND role_type = 'groomer'
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
                VALUES (?, 'groomer', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
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
    echo json_encode(['success' => true, 'message' => 'Settings updated successfully']);

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Update groomer settings error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
