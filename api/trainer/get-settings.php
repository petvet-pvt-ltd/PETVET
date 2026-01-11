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

function parse_service_areas($value) {
    if ($value === null) return [];
    $str = trim((string)$value);
    if ($str === '') return [];
    if (str_starts_with($str, '[')) {
        $decoded = json_decode($str, true);
        if (is_array($decoded)) {
            $areas = [];
            foreach ($decoded as $v) {
                $v = trim((string)$v);
                if ($v !== '') $areas[] = $v;
            }
            return $areas;
        }
    }
    // fallback: comma separated
    $parts = array_map('trim', explode(',', $str));
    return array_values(array_filter($parts, fn($p) => $p !== ''));
}

try {
    $pdo = db();
    // Get profile data from users table
    $stmt = $pdo->prepare("
        SELECT first_name, last_name, email, phone, address, avatar
        FROM users
        WHERE id = ?
    ");
    $stmt->execute([$user_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profile) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    // Get trainer-specific data from service_provider_profiles
    $stmt = $pdo->prepare("
        SELECT business_name, service_area, experience_years, certifications, 
               specializations, bio, phone_primary, phone_secondary,
               training_basic_enabled, training_basic_charge,
               training_intermediate_enabled, training_intermediate_charge,
               training_advanced_enabled, training_advanced_charge
        FROM service_provider_profiles
        WHERE user_id = ? AND role_type = 'trainer'
    ");
    $stmt->execute([$user_id]);
    $trainer_profile = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get preferences
    $stmt = $pdo->prepare("
        SELECT email_notifications, sms_notifications, push_notifications,
               auto_accept_bookings, require_deposit, show_availability_calendar,
               accept_emergency_bookings, show_phone_in_profile, 
               show_address_in_profile, accept_online_payments
        FROM service_provider_preferences
        WHERE user_id = ? AND role_type = 'trainer'
    ");
    $stmt->execute([$user_id]);
    $preferences = $stmt->fetch(PDO::FETCH_ASSOC);

    // If no trainer profile exists, return empty data
    if (!$trainer_profile) {
        $trainer_profile = [
            'business_name' => '',
            'service_area' => '',
            'service_areas' => [],
            'experience_years' => '',
            'certifications' => '',
            'specializations' => '',
            'bio' => '',
            'phone_primary' => '',
            'phone_secondary' => '',
            'training_basic_enabled' => false,
            'training_basic_charge' => '',
            'training_intermediate_enabled' => false,
            'training_intermediate_charge' => '',
            'training_advanced_enabled' => false,
            'training_advanced_charge' => ''
        ];
    } else {
        $areas = parse_service_areas($trainer_profile['service_area'] ?? '');
        $trainer_profile['service_areas'] = $areas;
        // Provide a user-friendly string for older clients
        $trainer_profile['service_area'] = implode(', ', $areas);
        // Convert boolean fields
        $trainer_profile['training_basic_enabled'] = (bool)$trainer_profile['training_basic_enabled'];
        $trainer_profile['training_intermediate_enabled'] = (bool)$trainer_profile['training_intermediate_enabled'];
        $trainer_profile['training_advanced_enabled'] = (bool)$trainer_profile['training_advanced_enabled'];
    }

    // If no preferences exist, use defaults
    if (!$preferences) {
        $preferences = [
            'email_notifications' => true,
            'sms_notifications' => true,
            'push_notifications' => true,
            'auto_accept_bookings' => false,
            'require_deposit' => false,
            'show_availability_calendar' => true,
            'accept_emergency_bookings' => false,
            'show_phone_in_profile' => true,
            'show_address_in_profile' => false,
            'accept_online_payments' => true
        ];
    } else {
        // Convert to booleans
        foreach ($preferences as $key => $value) {
            $preferences[$key] = (bool)$value;
        }
    }

    echo json_encode([
        'success' => true,
        'profile' => $profile,
        'trainer' => $trainer_profile,
        'preferences' => $preferences
    ]);

} catch (PDOException $e) {
    error_log("Get trainer settings error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
