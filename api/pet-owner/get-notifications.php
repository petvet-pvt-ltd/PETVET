<?php
/**
 * Get Notifications API - Pet Owner
 * Returns unread notifications for the current pet owner
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/connect.php';

session_start();

$pet_owner_id = $_SESSION['user_id'] ?? null;

if (!$pet_owner_id) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Unauthorized'
    ]);
    exit;
}

try {
    $pdo = db();
    
    // Get unread notifications for this pet owner
    $sql = "SELECT 
                n.id,
                n.type,
                n.title,
                n.message,
                n.clinic_id,
                n.clinic_name,
                n.entity_id,
                n.entity_type,
                n.action_data,
                n.created_at,
                CASE WHEN nr.id IS NOT NULL THEN 1 ELSE 0 END as is_read
            FROM notifications n
            LEFT JOIN notification_reads nr ON n.id = nr.notification_id AND nr.pet_owner_id = ?
            WHERE n.pet_owner_id = ?
            ORDER BY n.created_at DESC
            LIMIT 50";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$pet_owner_id, $pet_owner_id]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Parse action_data JSON
    foreach ($notifications as &$notif) {
        $notif['is_read'] = (bool)$notif['is_read'];
        if ($notif['action_data']) {
            $notif['action_data'] = json_decode($notif['action_data'], true);
        }
    }
    
    // Count unread
    $unread_count = count(array_filter($notifications, fn($n) => !$n['is_read']));
    
    echo json_encode([
        'success' => true,
        'notifications' => $notifications,
        'unread_count' => $unread_count
    ]);
    
} catch (Exception $e) {
    error_log('Get notifications error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch notifications'
    ]);
}
