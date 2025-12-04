<?php
/**
 * Stripe Payment Gateway Configuration
 * 
 * SETUP INSTRUCTIONS:
 * 1. Sign up for a Stripe account at https://stripe.com
 * 2. Go to Developers > API Keys in your Stripe Dashboard
 * 3. Copy your Publishable Key and Secret Key
 * 4. Create a file named 'stripe_keys.php' in this directory (config/)
 * 5. Add your keys to stripe_keys.php (this file is git-ignored)
 * 6. For testing, use test keys (they start with pk_test_ and sk_test_)
 * 7. For production, use live keys (they start with pk_live_ and sk_live_)
 */

// Load API keys from separate file (not tracked in git)
$stripeKeysFile = __DIR__ . '/stripe_keys.php';
if (file_exists($stripeKeysFile)) {
    require_once $stripeKeysFile;
} else {
    // Default placeholder keys if stripe_keys.php doesn't exist
    define('STRIPE_PUBLISHABLE_KEY', 'pk_test_YOUR_PUBLISHABLE_KEY_HERE');
    define('STRIPE_SECRET_KEY', 'sk_test_YOUR_SECRET_KEY_HERE');
}

// Currency
define('STRIPE_CURRENCY', 'LKR'); // Sri Lankan Rupee

// Success and Cancel URLs
define('STRIPE_SUCCESS_URL', 'http://localhost/PETVET/index.php?module=pet-owner&page=payment-success');
define('STRIPE_CANCEL_URL', 'http://localhost/PETVET/index.php?module=pet-owner&page=payment-cancel');

// Company/Store Information
define('STORE_NAME', 'PetVet Shop');
define('STORE_EMAIL', 'shop@petvet.lk');

?>
