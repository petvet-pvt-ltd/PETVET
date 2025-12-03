<?php
require_once '../../config/connect.php';
require_once '../../config/auth_helper.php';

header('Content-Type: application/json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is authenticated
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get pet_id from query parameter
if (!isset($_GET['pet_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Pet ID is required']);
    exit;
}

$pet_id = intval($_GET['pet_id']);
$user_id = currentUserId();

try {
    $pdo = db();
    
    // Verify pet belongs to user
    $stmt = $pdo->prepare("SELECT id FROM pets WHERE id = ? AND user_id = ?");
    $stmt->execute([$pet_id, $user_id]);
    
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['error' => 'Pet not found or access denied']);
        exit;
    }
    
    // Check for upcoming appointments (pending or approved)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as pending_count 
        FROM appointments 
        WHERE pet_id = ? 
        AND status IN ('pending', 'approved')
        AND appointment_date >= CURDATE()
    ");
    $stmt->execute([$pet_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'has_pending' => $result['pending_count'] > 0,
        'pending_count' => intval($result['pending_count'])
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
