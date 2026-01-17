<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../config/auth_helper.php';

// Check if user is logged in and is a clinic manager
if (!isLoggedIn() || !hasRole('clinic_manager')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$orderId = $_POST['order_id'] ?? null;

if (!$orderId) {
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit;
}

try {
    $pdo = db();
    
    // Get clinic_id for verification
    $userId = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT clinic_id FROM clinic_manager_profiles WHERE user_id = ?");
    $stmt->execute([$userId]);
    $clinicId = $stmt->fetchColumn();
    
    if (!$clinicId) {
        echo json_encode(['success' => false, 'message' => 'Clinic not found']);
        exit;
    }
    
    // Verify order belongs to this clinic and update status
    $stmt = $pdo->prepare("
        UPDATE orders 
        SET status = 'Delivered', updated_at = NOW()
        WHERE id = ? AND clinic_id = ? AND status = 'Confirmed'
    ");
    $stmt->execute([$orderId, $clinicId]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Order marked as delivered'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Order not found or already delivered'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
