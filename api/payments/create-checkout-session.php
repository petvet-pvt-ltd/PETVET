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
    
    // Handle delivery data from new cart manager (with location-based delivery)
    $deliveryCharge = 0;
    if (isset($data['delivery']) && is_array($data['delivery'])) {
        // New format: delivery object with charge and distance
        $deliveryCharge = isset($data['delivery']['charge']) ? floatval($data['delivery']['charge']) : 0;
    } elseif ($deliveryCity) {
        // Old format: delivery city string
        $deliveryCharge = calculateDeliveryCharge($deliveryCity);
    }
    
    // Calculate cart subtotal
    $subtotal = 0;
    foreach ($cart as $item) {
        $itemPrice = isset($item['price']) ? floatval($item['price']) : 0;
        $itemQty = isset($item['quantity']) ? intval($item['quantity']) : 1;
        $subtotal += $itemPrice * $itemQty;
    }
    
    // Check for free delivery threshold (only if using old format)
    if ($deliveryCity && !isset($data['delivery'])) {
        if ($subtotal >= FREE_DELIVERY_THRESHOLD) {
            $deliveryCharge = 0;
        }
    }
    
    // Build Stripe API parameters (flattened for http_build_query)
    $params = [
        'mode' => 'payment',
        'success_url' => STRIPE_SUCCESS_URL . '&session_id={CHECKOUT_SESSION_ID}',
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
        $deliveryLabel = "Delivery Charge";
        
        // Add distance info if available
        if (isset($data['delivery']['distance'])) {
            $deliveryLabel .= " (" . number_format($data['delivery']['distance'], 1) . " km)";
        } elseif ($deliveryCity) {
            $deliveryLabel .= " - " . $deliveryCity;
        }
        
        $params["line_items[$index][price_data][currency]"] = strtolower(STRIPE_CURRENCY);
        $params["line_items[$index][price_data][product_data][name]"] = $deliveryLabel;
        $params["line_items[$index][price_data][unit_amount]"] = (int)($deliveryCharge * 100);
        $params["line_items[$index][quantity]"] = 1;
    } elseif ($deliveryCity && $subtotal >= FREE_DELIVERY_THRESHOLD) {
        // Show free delivery as line item (old format only)
        $params["line_items[$index][price_data][currency]"] = strtolower(STRIPE_CURRENCY);
        $params["line_items[$index][price_data][product_data][name]"] = "Delivery Charge - FREE (Order over Rs. " . number_format(FREE_DELIVERY_THRESHOLD) . ")";
        $params["line_items[$index][price_data][unit_amount]"] = 0;
        $params["line_items[$index][quantity]"] = 1;
    }
    
    // Add metadata
    $params['metadata[order_source]'] = 'petvet_shop';
    $params['metadata[subtotal]'] = $subtotal;
    $params['metadata[delivery_charge]'] = $deliveryCharge;
    
    // Add delivery location data if available
    if (isset($data['delivery'])) {
        if (isset($data['delivery']['distance'])) {
            $params['metadata[delivery_distance]'] = $data['delivery']['distance'];
        }
        if (isset($data['delivery']['latitude'])) {
            $params['metadata[delivery_latitude]'] = $data['delivery']['latitude'];
        }
        if (isset($data['delivery']['longitude'])) {
            $params['metadata[delivery_longitude]'] = $data['delivery']['longitude'];
        }
    }
    
    if ($deliveryCity) {
        $params['metadata[delivery_city]'] = $deliveryCity;
    }
    
    if (isset($data['clinic_id'])) {
        $params['metadata[clinic_id]'] = $data['clinic_id'];
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
