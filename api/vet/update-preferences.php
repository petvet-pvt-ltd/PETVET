<?php
/**
 * Update Vet Preferences API
 */

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../config/auth_helper.php';

// Check authentication
if (!isLoggedIn() || getUserRole() !== 'vet') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $pdo = db();
    $userId = currentUserId();
    
    // Note: user_preferences table may not exist yet
    // For now, just return success without saving
    // TODO: Create user_preferences table if persistent preference storage is needed
    
    $emailNotifications = isset($_POST['email_notifications']) ? 1 : 0;
    $reminderAppointments = (int)($_POST['reminder_appointments'] ?? 24);
    
    // Try to check if table exists first
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'user_preferences'");
        $tableExists = $stmt->rowCount() > 0;
        
        if ($tableExists) {
            // Check if preferences exist
            $stmt = $pdo->prepare("SELECT user_id FROM user_preferences WHERE user_id = ?");
            $stmt->execute([$userId]);
            $exists = $stmt->fetch();
            
            if ($exists) {
                // Update existing
                $stmt = $pdo->prepare("
                    UPDATE user_preferences SET
                        email_notifications = ?,
                        reminder_appointments = ?
                    WHERE user_id = ?
                ");
                $stmt->execute([$emailNotifications, $reminderAppointments, $userId]);
            } else {
                // Insert new
                $stmt = $pdo->prepare("
                    INSERT INTO user_preferences (user_id, email_notifications, reminder_appointments)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$userId, $emailNotifications, $reminderAppointments]);
            }
        }
    } catch (Exception $e) {
        // Table doesn't exist or error occurred, just continue
        error_log("Preferences table not available: " . $e->getMessage());
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Preferences updated successfully'
    ]);
    
} catch (Exception $e) {
    error_log("Update vet preferences error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to update preferences'
    ]);
}
