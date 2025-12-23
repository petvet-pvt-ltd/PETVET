# 🚀 QUICK SETUP - Stripe Payment Integration

## ✅ MANUAL STEPS YOU NEED TO DO:

### 1️⃣ Install Composer (if not already installed)

- Download from: https://getcomposer.org/download/
- Run the installer
- Restart your terminal/PowerShell

### 2️⃣ Install Stripe PHP Library

Open PowerShell and run:

```powershell
cd c:\xampp\htdocs\PETVET
composer require stripe/stripe-php
```

### 3️⃣ Create Stripe Account & Get API Keys

1. Go to https://stripe.com
2. Sign up for a free account
3. After login, go to: **Developers** → **API Keys**
4. Copy your **Publishable Key** (starts with `pk_test_`)
5. Copy your **Secret Key** (starts with `sk_test_`)

### 4️⃣ Add Your Stripe Keys

Open this file: `c:\xampp\htdocs\PETVET\config\stripe_config.php`

Replace these lines:

```php
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_YOUR_PUBLISHABLE_KEY_HERE');
define('STRIPE_SECRET_KEY', 'sk_test_YOUR_SECRET_KEY_HERE');
```

With your actual keys from Stripe dashboard.

### 5️⃣ Test It!

1. Start XAMPP (Apache must be running)
2. Go to: http://localhost/PETVET/index.php?module=pet-owner&page=shop
3. Add items to cart
4. Click "Proceed to Checkout"
5. Use test card: **4242 4242 4242 4242**
   - Expiry: Any future date (e.g., 12/25)
   - CVC: Any 3 digits (e.g., 123)
   - ZIP: Any 5 digits (e.g., 12345)

---

## 🎯 THAT'S IT!

If everything works:

- ✅ Cart items will be shown in Stripe payment page
- ✅ Total amount will be calculated automatically
- ✅ After payment, redirects to success page
- ✅ Cart is cleared after successful payment
- ✅ Order is saved in "My Orders" page

---

## 📝 FILES CREATED:

1. ✅ `config/stripe_config.php` - Stripe configuration
2. ✅ `api/payments/create-checkout-session.php` - Payment API
3. ✅ `views/pet-owner/payment-success.php` - Success page
4. ✅ `views/pet-owner/payment-cancel.php` - Cancel page
5. ✅ `views/pet-owner/orders.php` - Order history page
6. ✅ `public/js/cart.js` - Updated checkout function

---

## 🔧 TROUBLESHOOTING:

**Error: "Class 'Stripe\Stripe' not found"**
→ Run: `composer require stripe/stripe-php`

**Error: "Invalid API Key"**
→ Check that you copied the complete API key in `stripe_config.php`

**Payment page doesn't load**
→ Open browser console (F12) to see errors

**Need help?**
→ Read full documentation: `docs/STRIPE-PAYMENT-INTEGRATION.md`

---

## 💳 TEST CARD NUMBERS:

✅ **Success:** 4242 4242 4242 4242
❌ **Decline:** 4000 0000 0000 0002
🔐 **Auth Required:** 4000 0025 0000 3155

All other details: Any future date, any CVC, any ZIP

More test cards: https://stripe.com/docs/testing

---

**Ready to go live?**
Get production API keys from Stripe and replace test keys!
