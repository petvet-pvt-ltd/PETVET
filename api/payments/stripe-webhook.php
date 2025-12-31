<?php
/**
 * Stripe Webhook Handler
 * Automatically saves orders when payment is confirmed
 */
require_once '../../config/connect.php';

header('Content-Type: application/json');

$db = db();
$payload = @file_get_contents('php://input');
$event = json_decode($payload, true);

// Log webhook event
error_log("Stripe Webhook - Event type: " . ($event['type'] ?? 'unknown'));

// Only handle successful payment events
if ($event['type'] === 'checkout.session.completed') {
    $session = $event['data']['object'];
    $sessionId = $session['id'];
    
    error_log("Stripe Webhook - Payment completed for session: $sessionId");
    
    // Extract metadata or customer details to find user
    // For now, we'll handle order creation on the success page
    // This webhook is here for future enhancements
    
    http_response_code(200);
    echo json_encode(['received' => true]);
} else {
    http_response_code(200);
    echo json_encode(['received' => true]);
}
