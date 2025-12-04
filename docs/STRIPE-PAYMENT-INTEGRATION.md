# Stripe Payment Integration - Setup Guide

## Overview

Stripe payment gateway has been integrated into the PetVet shop checkout system. When users click "Proceed to Checkout", they will be redirected to Stripe's secure payment page where they can complete their purchase.

## Files Created/Modified

### New Files:

1. **config/stripe_config.php** - Stripe API keys and configuration
2. **api/payments/create-checkout-session.php** - API endpoint to create Stripe sessions
3. **views/pet-owner/payment-success.php** - Success page after payment
4. **views/pet-owner/payment-cancel.php** - Cancellation page

### Modified Files:

1. **public/js/cart.js** - Updated checkout() function to redirect to Stripe

---

## Setup Instructions

### Step 1: Install Stripe PHP Library

You need to install the Stripe PHP library using Composer. Open PowerShell in your project root and run:

```powershell
cd c:\xampp\htdocs\PETVET
composer require stripe/stripe-php
```

If you don't have Composer installed:

1. Download from: https://getcomposer.org/download/
2. Install it globally on your system
3. Then run the above command

### Step 2: Get Stripe API Keys

1. Go to https://stripe.com and create a free account
2. After signing up, go to **Developers > API Keys**
3. You'll see two types of keys:
   - **Publishable key** (starts with `pk_test_`)
   - **Secret key** (starts with `sk_test_`)
4. Copy both keys

### Step 3: Configure Stripe Keys

Open `config/stripe_config.php` and replace the placeholder values:

```php
// Replace these with your actual keys from Stripe Dashboard
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_YOUR_ACTUAL_KEY_HERE');
define('STRIPE_SECRET_KEY', 'sk_test_YOUR_ACTUAL_SECRET_KEY_HERE');
```

### Step 4: Update URLs (if needed)

If your local server URL is different from `http://localhost/PETVET/`, update these in `config/stripe_config.php`:

```php
define('STRIPE_SUCCESS_URL', 'http://your-url/PETVET/index.php?module=pet-owner&page=payment-success');
define('STRIPE_CANCEL_URL', 'http://your-url/PETVET/index.php?module=pet-owner&page=payment-cancel');
```

### Step 5: Test the Integration

1. Start your XAMPP server
2. Go to the shop page: `http://localhost/PETVET/index.php?module=pet-owner&page=shop`
3. Add items to cart
4. Click "Proceed to Checkout"
5. You should be redirected to Stripe's payment page

---

## Testing the Payment

Stripe provides test card numbers for testing:

### Successful Payment:

- **Card Number:** 4242 4242 4242 4242
- **Expiry:** Any future date (e.g., 12/34)
- **CVC:** Any 3 digits (e.g., 123)
- **ZIP:** Any 5 digits (e.g., 12345)

### Declined Payment:

- **Card Number:** 4000 0000 0000 0002
- **Expiry:** Any future date
- **CVC:** Any 3 digits
- **ZIP:** Any 5 digits

### Authentication Required:

- **Card Number:** 4000 0025 0000 3155
- **Expiry:** Any future date
- **CVC:** Any 3 digits
- **ZIP:** Any 5 digits

More test cards: https://stripe.com/docs/testing

---

## How It Works

1. **User adds items to cart** - Items stored in localStorage
2. **User clicks "Proceed to Checkout"** - Cart data sent to backend
3. **Backend creates Stripe session** - API call to `create-checkout-session.php`
4. **User redirected to Stripe** - Secure payment page hosted by Stripe
5. **User completes payment** - Enters card details on Stripe's page
6. **Stripe processes payment** - Real-time validation and processing
7. **User redirected back** - Either to success or cancel page
8. **Cart cleared** - Only on successful payment

---

## Currency Configuration

The system is configured to use **Sri Lankan Rupees (LKR)** by default.

To change currency, edit `config/stripe_config.php`:

```php
define('STRIPE_CURRENCY', 'USD'); // or 'EUR', 'GBP', etc.
```

**Note:** Stripe supports 135+ currencies. Check: https://stripe.com/docs/currencies

---

## Security Features

✅ All payment processing happens on Stripe's secure servers
✅ No credit card data touches your server
✅ PCI compliance handled by Stripe
✅ HTTPS required for production (Stripe enforces this)
✅ API keys stored in separate config file

---

## Production Checklist

Before going live:

- [ ] Get live API keys from Stripe (starts with `pk_live_` and `sk_live_`)
- [ ] Replace test keys with live keys in `config/stripe_config.php`
- [ ] Enable HTTPS on your domain (SSL certificate required)
- [ ] Update success/cancel URLs to production domain
- [ ] Test with real small transactions
- [ ] Set up Stripe webhook for order notifications (optional)
- [ ] Configure email notifications
- [ ] Review Stripe's production checklist: https://stripe.com/docs/development/checklist

---

## Troubleshooting

### Error: "Class 'Stripe\Stripe' not found"

**Solution:** Run `composer require stripe/stripe-php`

### Error: "Invalid API Key"

**Solution:** Check that you've copied the full API key correctly in `stripe_config.php`

### Payment page doesn't load

**Solution:** Check browser console for errors. Ensure `create-checkout-session.php` is accessible

### Redirect URLs not working

**Solution:** Verify the URLs in `stripe_config.php` match your server setup

---

## Additional Features (Future Enhancements)

You can add these features later:

1. **Order History** - Save completed orders to database
2. **Email Notifications** - Send confirmation emails after purchase
3. **Webhooks** - Get real-time payment status updates from Stripe
4. **Customer Portal** - Allow users to view past orders
5. **Refunds** - Process refunds through Stripe dashboard
6. **Subscriptions** - If you want recurring payments
7. **Multiple Payment Methods** - Add wallets, bank transfers, etc.

---

## Support Links

- Stripe Documentation: https://stripe.com/docs
- Stripe API Reference: https://stripe.com/docs/api
- Stripe Dashboard: https://dashboard.stripe.com
- Test Card Numbers: https://stripe.com/docs/testing
- Stripe Support: https://support.stripe.com

---

## Summary

The integration is complete! Just install Composer, install the Stripe library, and add your API keys. Everything else is ready to work.

**Quick Start:**

```powershell
cd c:\xampp\htdocs\PETVET
composer require stripe/stripe-php
```

Then update your API keys in `config/stripe_config.php` and you're good to go! 🚀
