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

    // Get breeder-specific data from service_provider_profiles
    $stmt = $pdo->prepare("
        SELECT business_name, license_number, service_area, experience_years,
               specializations, services_description, phone_primary, phone_secondary
        FROM service_provider_profiles
        WHERE user_id = ? AND role_type = 'breeder'
    ");
    $stmt->execute([$user_id]);
    $breeder_profile = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get preferences
    $stmt = $pdo->prepare("
        SELECT email_notifications, sms_notifications, push_notifications,
               auto_accept_bookings, require_deposit, show_availability_calendar,
               accept_emergency_bookings, show_phone_in_profile, 
               show_address_in_profile, accept_online_payments
        FROM service_provider_preferences
        WHERE user_id = ? AND role_type = 'breeder'
    ");
    $stmt->execute([$user_id]);
    $preferences = $stmt->fetch(PDO::FETCH_ASSOC);

    // If no breeder profile exists, return empty data
    if (!$breeder_profile) {
        $breeder_profile = [
            'business_name' => '',
            'license_number' => '',
            'service_area' => '',
            'experience_years' => '',
            'specializations' => '',
            'services_description' => '',
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
        'breeder' => $breeder_profile,
        'preferences' => $preferences
    ]);

} catch (PDOException $e) {
    error_log("Get breeder settings error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
