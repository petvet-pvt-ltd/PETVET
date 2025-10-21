<?php
/**
 * API Endpoint: Check Email Availability
 * Returns whether an email is already registered
 */

require_once __DIR__ . '/../config/connect.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get email from request
$input = json_decode(file_get_contents('php://input'), true);
$email = $input['email'] ?? '';

// Validate email format
if (empty($email)) {
    http_response_code(400);
    echo json_encode(['error' => 'Email is required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email format']);
    exit;
}

try {
    // Check if email exists in database
    $db = db();
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $exists = $stmt->fetch() !== false;
    
    // Return result
    echo json_encode([
        'exists' => $exists,
        'available' => !$exists,
        'message' => $exists ? 'This email is already registered' : 'Email is available'
    ]);
    
} catch (PDOException $e) {
    error_log("Check email error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
