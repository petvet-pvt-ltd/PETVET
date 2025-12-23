<?php
/**
 * Update Vet Profile API
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
    
    // Handle avatar upload if present
    $avatarPath = null;
    if (isset($_FILES['vetAvatar']) && $_FILES['vetAvatar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../uploads/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $extension = pathinfo($_FILES['vetAvatar']['name'], PATHINFO_EXTENSION);
        $filename = 'vet_' . $userId . '_' . time() . '.' . $extension;
        $uploadPath = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['vetAvatar']['tmp_name'], $uploadPath)) {
            $avatarPath = '/PETVET/uploads/avatars/' . $filename;
        }
    }
    
    // Update users table
    $sql = "UPDATE users SET 
            first_name = ?,
            phone = ?";
    
    $params = [
        $_POST['first_name'],
        $_POST['phone'] ?? null
    ];
    
    if ($avatarPath) {
        $sql .= ", avatar = ?";
        $params[] = $avatarPath;
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $userId;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    // Update last name separately
    $stmt = $pdo->prepare("UPDATE users SET last_name = ? WHERE id = ?");
    $stmt->execute([$_POST['last_name'], $userId]);
    
    // Update vets table
    $stmt = $pdo->prepare("
        UPDATE vets SET
            specialization = ?,
            years_experience = ?,
            consultation_fee = ?,
            bio = ?
        WHERE user_id = ?
    ");
    
    $stmt->execute([
        $_POST['specialization'] ?? null,
        $_POST['years_experience'] ?? 0,
        $_POST['consultation_fee'] ?? 0.00,
        $_POST['bio'] ?? null,
        $userId
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully',
        'avatar' => $avatarPath
    ]);
    
} catch (Exception $e) {
    error_log("Update vet profile error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to update profile'
    ]);
}
