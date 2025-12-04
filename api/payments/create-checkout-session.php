<?php
/**
 * Stripe Checkout Session API
 * Creates a Stripe checkout session for cart items using cURL
 */

// Disable error display to prevent breaking JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

// Include configuration
require_once __DIR__ . '/../../config/stripe_config.php';
require_once __DIR__ . '/../../config/delivery_config.php';


try {
    // Get cart data from POST request
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!isset($data['cart']) || empty($data['cart'])) {
        throw new Exception('Cart is empty');
    }
    
    $cart = $data['cart'];
    $deliveryCity = isset($data['deliveryCity']) ? $data['deliveryCity'] : null;
    
    // Calculate cart subtotal
    $subtotal = 0;
    foreach ($cart as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    
    // Calculate delivery charge
    $deliveryCharge = 0;
    if ($deliveryCity) {
        $deliveryCharge = calculateDeliveryCharge($deliveryCity);
        // Free delivery for orders above threshold
        if ($subtotal >= FREE_DELIVERY_THRESHOLD) {
            $deliveryCharge = 0;
        }
    }
    
    // Build Stripe API parameters (flattened for http_build_query)
    $params = [
        'mode' => 'payment',
        'success_url' => STRIPE_SUCCESS_URL . '?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => STRIPE_CANCEL_URL,
        'billing_address_collection' => 'required',
        'phone_number_collection[enabled]' => 'true',
    ];
    
    // Enable shipping address collection for Sri Lanka
    $params['shipping_address_collection[allowed_countries][0]'] = 'LK';
    
    // Add payment method types
    $params['payment_method_types[0]'] = 'card';
    
    // Add line items (products)
    $index = 0;
    foreach ($cart as $item) {
        $params["line_items[$index][price_data][currency]"] = strtolower(STRIPE_CURRENCY);
        $params["line_items[$index][price_data][product_data][name]"] = $item['name'];
        $params["line_items[$index][price_data][unit_amount]"] = (int)($item['price'] * 100);
        $params["line_items[$index][quantity]"] = $item['quantity'];
        $index++;
    }
    
    // Add delivery charge as a separate line item if applicable
    if ($deliveryCharge > 0) {
        $params["line_items[$index][price_data][currency]"] = strtolower(STRIPE_CURRENCY);
        $params["line_items[$index][price_data][product_data][name]"] = "Delivery Charge - " . ($deliveryCity ?: 'Standard');
        $params["line_items[$index][price_data][unit_amount]"] = (int)($deliveryCharge * 100);
        $params["line_items[$index][quantity]"] = 1;
    } elseif ($subtotal >= FREE_DELIVERY_THRESHOLD) {
        // Show free delivery as line item
        $params["line_items[$index][price_data][currency]"] = strtolower(STRIPE_CURRENCY);
        $params["line_items[$index][price_data][product_data][name]"] = "Delivery Charge - FREE (Order over Rs. " . number_format(FREE_DELIVERY_THRESHOLD) . ")";
        $params["line_items[$index][price_data][unit_amount]"] = 0;
        $params["line_items[$index][quantity]"] = 1;
    }
    
    // Add metadata
    $params['metadata[order_source]'] = 'petvet_shop';
    $params['metadata[subtotal]'] = $subtotal;
    $params['metadata[delivery_charge]'] = $deliveryCharge;
    if ($deliveryCity) {
        $params['metadata[delivery_city]'] = $deliveryCity;
    }
    
    if (isset($data['email'])) {
        $params['customer_email'] = $data['email'];
    }
    
    // Make cURL request to Stripe API
    $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_USERPWD, STRIPE_SECRET_KEY . ':');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        throw new Exception('cURL Error: ' . curl_error($ch));
    }
    
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    if ($httpCode !== 200) {
        throw new Exception($result['error']['message'] ?? 'Failed to create checkout session');
    }
    
    echo json_encode([
        'success' => true,
        'sessionId' => $result['id'],
        'url' => $result['url']
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
