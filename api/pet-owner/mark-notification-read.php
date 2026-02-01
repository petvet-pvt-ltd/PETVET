<?php
/**
 * Mark Notification Read API - Pet Owner
 * Marks a notification as read
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/connect.php';

session_start();

$pet_owner_id = $_SESSION['user_id'] ?? null;
$notification_id = $_POST['notification_id'] ?? $_GET['notification_id'] ?? null;
$mark_all = $_POST['mark_all'] ?? $_GET['mark_all'] ?? false;

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
    
    if ($mark_all) {
        // Mark all notifications as read
        $sql = "INSERT INTO notification_reads (pet_owner_id, notification_id)
                SELECT ?, n.id 
                FROM notifications n
                WHERE n.pet_owner_id = ?
                AND NOT EXISTS (
                    SELECT 1 FROM notification_reads nr 
                    WHERE nr.notification_id = n.id 
                    AND nr.pet_owner_id = ?
                )
                ON DUPLICATE KEY UPDATE read_at = CURRENT_TIMESTAMP";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$pet_owner_id, $pet_owner_id, $pet_owner_id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    } else {
        // Mark single notification as read
        if (!$notification_id) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Notification ID required'
            ]);
            exit;
        }
        
        $sql = "INSERT INTO notification_reads (pet_owner_id, notification_id)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE read_at = CURRENT_TIMESTAMP";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$pet_owner_id, $notification_id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }
    
} catch (Exception $e) {
    error_log('Mark notification read error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to mark notification as read'
    ]);
}
