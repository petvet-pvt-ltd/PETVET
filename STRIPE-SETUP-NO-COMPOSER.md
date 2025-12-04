# 🚀 STRIPE PAYMENT - QUICK SETUP (NO COMPOSER NEEDED!)

## ✅ UPDATED: No Composer Installation Required!

The system has been updated to work **without** the Stripe PHP library. It uses cURL directly to communicate with Stripe's API.

---

## 📋 SETUP STEPS:

### ✅ Step 1: Fix SSL Certificate (One-time fix)

**Option A - Automatic (Recommended):**

1. Double-click on `fix-ssl-certificate.bat` in your PETVET folder
2. Wait for it to complete
3. Restart Apache in XAMPP Control Panel

**Option B - Manual:**

1. Download: https://curl.se/ca/cacert.pem
2. Save it as `C:\xampp\apache\bin\curl-ca-bundle.crt`
3. Open `C:\xampp\php\php.ini`
4. Find `;curl.cainfo =` and change to: `curl.cainfo = "C:\xampp\apache\bin\curl-ca-bundle.crt"`
5. Find `;openssl.cafile=` and change to: `openssl.cafile="C:\xampp\apache\bin\curl-ca-bundle.crt"`
6. Save and restart Apache

---

### ✅ Step 2: Get Stripe API Keys

1. Go to https://stripe.com and sign up (free)
2. After login, go to: **Developers** → **API Keys**
3. Copy your **Publishable Key** (starts with `pk_test_`)
4. Copy your **Secret Key** (starts with `sk_test_`)

---

### ✅ Step 3: Add Your Stripe Keys

1. Open: `c:\xampp\htdocs\PETVET\config\stripe_config.php`
2. Replace these lines with your actual keys:

```php
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_YOUR_ACTUAL_KEY');
define('STRIPE_SECRET_KEY', 'sk_test_YOUR_ACTUAL_SECRET_KEY');
```

---

### ✅ Step 4: Test!

1. Restart Apache in XAMPP
2. Go to: http://localhost/PETVET/index.php?module=pet-owner&page=shop
3. Add items to cart
4. Click "Proceed to Checkout"
5. Use test card: **4242 4242 4242 4242**
   - Expiry: Any future date (12/25)
   - CVC: Any 3 digits (123)
   - ZIP: Any 5 digits (12345)

---

## ✨ THAT'S IT!

No Composer, no complex setup. Just:

1. Fix SSL certificate (one time)
2. Add Stripe keys
3. Test with demo card

---

## 💳 TEST CARD NUMBERS:

✅ **Success:** 4242 4242 4242 4242
❌ **Decline:** 4000 0000 0000 0002
🔐 **Auth Required:** 4000 0025 0000 3155

All other details: Any future date, any CVC, any ZIP

More: https://stripe.com/docs/testing

---

## 🔧 TROUBLESHOOTING:

**SSL Certificate Error:**
→ Run `fix-ssl-certificate.bat` and restart Apache

**"Invalid API Key" error:**
→ Double-check your keys in `config/stripe_config.php`

**Still having issues?**
→ Check browser console (F12) for detailed errors
→ Make sure Apache is running in XAMPP
→ Verify your Stripe account is activated

---

## 📁 CHANGES MADE:

✅ Updated to use cURL instead of Stripe library
✅ No Composer installation needed
✅ Created SSL certificate fix script
✅ Simplified setup process

---

## 🎯 WHAT WORKS:

✅ Full Stripe checkout
✅ Cart items displayed in Stripe
✅ Automatic total calculation  
✅ Success/cancel page handling
✅ Cart auto-clear after payment
✅ Order history tracking
✅ Mobile responsive
✅ Secure payment processing

---

**Everything is ready to use! Just fix SSL, add your keys, and go! 🚀**
