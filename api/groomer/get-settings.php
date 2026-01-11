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

    // Get groomer-specific data from service_provider_profiles
    $stmt = $pdo->prepare("
        SELECT business_name, business_logo, service_area, experience_years, specializations, certifications,
               bio, location_latitude, location_longitude, phone_primary, phone_secondary
        FROM service_provider_profiles
        WHERE user_id = ? AND role_type = 'groomer'
    ");
    $stmt->execute([$user_id]);
    $groomer_profile = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get preferences
    $stmt = $pdo->prepare("
        SELECT email_notifications, sms_notifications, push_notifications,
               auto_accept_bookings, require_deposit, show_availability_calendar,
               accept_emergency_bookings, show_phone_in_profile, 
               show_address_in_profile, accept_online_payments
        FROM service_provider_preferences
        WHERE user_id = ? AND role_type = 'groomer'
    ");
    $stmt->execute([$user_id]);
    $preferences = $stmt->fetch(PDO::FETCH_ASSOC);

    // If no groomer profile exists, return empty data
    if (!$groomer_profile) {
        $groomer_profile = [
            'service_area' => '',
            'experience_years' => '',
            'specializations' => '',
            'certifications' => '',
            'bio' => '',
            'location_latitude' => '',
            'location_longitude' => '',
            'phone_primary' => '',
            'phone_secondary' => ''
        ];
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
        'groomer' => $groomer_profile,
        'preferences' => $preferences
    ]);

} catch (PDOException $e) {
    error_log("Get groomer settings error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
