<?php
require_once '../../config/connect.php';
require_once '../../config/auth_helper.php';

header('Content-Type: application/json');

if (!isLoggedIn() || getUserRole() !== 'pet_owner') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$userId = currentUserId();
$method = $_SERVER['REQUEST_METHOD'];
$db = db();

if ($method === 'GET') {
    try {
        $stmt = $db->prepare("SELECT clinic_id FROM favorite_shops WHERE user_id = ?");
        $stmt->execute([$userId]);
        $favorites = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo json_encode(['success' => true, 'favorites' => $favorites]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $clinicId = $input['clinic_id'] ?? null;
    
    if (!$clinicId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Clinic ID required']);
        exit;
    }
    
    try {
        // Check if already exists
        $check = $db->prepare("SELECT id FROM favorite_shops WHERE user_id = ? AND clinic_id = ?");
        $check->execute([$userId, $clinicId]);
        
        if ($check->fetch()) {
            // Remove
            $stmt = $db->prepare("DELETE FROM favorite_shops WHERE user_id = ? AND clinic_id = ?");
            $stmt->execute([$userId, $clinicId]);
            $action = 'removed';
        } else {
            // Add
            $stmt = $db->prepare("INSERT INTO favorite_shops (user_id, clinic_id) VALUES (?, ?)");
            $stmt->execute([$userId, $clinicId]);
            $action = 'added';
        }
        
        echo json_encode(['success' => true, 'action' => $action]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
