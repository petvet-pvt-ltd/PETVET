<?php
/**
 * Delivery/Shipping Configuration for Sri Lanka
 */

// Delivery charges by district (in LKR)
// You can customize these rates based on your delivery service
define('DELIVERY_RATES', [
    // Colombo District
    'Colombo' => 200,
    'Dehiwala-Mount Lavinia' => 200,
    'Moratuwa' => 250,
    'Sri Jayawardenepura Kotte' => 200,
    
    // Western Province (other)
    'Gampaha' => 300,
    'Negombo' => 350,
    'Kalutara' => 400,
    'Panadura' => 350,
    
    // Central Province
    'Kandy' => 500,
    'Matale' => 550,
    'Nuwara Eliya' => 600,
    
    // Southern Province
    'Galle' => 500,
    'Matara' => 550,
    'Hambantota' => 600,
    
    // Northern Province
    'Jaffna' => 700,
    'Kilinochchi' => 750,
    'Mannar' => 750,
    'Vavuniya' => 700,
    'Mullaitivu' => 750,
    
    // Eastern Province
    'Trincomalee' => 650,
    'Batticaloa' => 650,
    'Ampara' => 600,
    
    // North Western Province
    'Kurunegala' => 450,
    'Puttalam' => 500,
    
    // North Central Province
    'Anuradhapura' => 600,
    'Polonnaruwa' => 650,
    
    // Uva Province
    'Badulla' => 600,
    'Monaragala' => 650,
    
    // Sabaragamuwa Province
    'Ratnapura' => 500,
    'Kegalle' => 450,
]);

// Free delivery threshold (in LKR)
define('FREE_DELIVERY_THRESHOLD', 5000);

// Default delivery charge if location not found
define('DEFAULT_DELIVERY_CHARGE', 400);

/**
 * Calculate delivery charge based on city/district
 */
function calculateDeliveryCharge($city) {
    // Check for free delivery threshold (will be checked in cart total)
    if (isset(DELIVERY_RATES[$city])) {
        return DELIVERY_RATES[$city];
    }
    return DEFAULT_DELIVERY_CHARGE;
}

/**
 * Get all available delivery locations
 */
function getDeliveryLocations() {
    return array_keys(DELIVERY_RATES);
}

?>
