# 🎯 STRIPE CHECKOUT - QUICK START GUIDE

## ✅ READY TO USE - NO SETUP REQUIRED!

Everything is already configured and ready to test!

---

## 🚀 How to Test Right Now

### Step 1: Open Your Shop

```
http://localhost/PETVET/index.php?module=pet-owner&page=shop&clinic_id=1
```

(Replace `clinic_id=1` with your actual clinic ID)

### Step 2: Add Items to Cart

- Click "Add to Cart" on any product
- The cart icon (top right) will show item count

### Step 3: Open Cart

- Click the cart icon
- Review your items
- See delivery charges (if location enabled)

### Step 4: Checkout

- Click **"Checkout"** button
- You'll be redirected to **Stripe Payment Page**

### Step 5: Complete Payment

Use these **test card numbers**:

| Card Number           | Result                     |
| --------------------- | -------------------------- |
| `4242 4242 4242 4242` | ✅ Success                 |
| `4000 0000 0000 0002` | ❌ Declined                |
| `4000 0025 0000 3155` | 🔐 Requires Authentication |

- **Expiry:** Any future date (e.g., `12/25`)
- **CVC:** Any 3 digits (e.g., `123`)
- **Name:** Any name
- **Postal Code:** Any code

### Step 6: After Payment

- **Success** → Redirected to success page, cart cleared
- **Cancel** → Redirected to cancel page, cart preserved

---

## 📋 What's Included in Stripe Payment

When user clicks checkout, Stripe shows:

✅ **All cart items** (name, price, quantity)  
✅ **Delivery charge** with distance (e.g., "13.5 km")  
✅ **Total amount** (items + delivery)  
✅ **Billing address** form  
✅ **Phone number** field  
✅ **Shipping address** (Sri Lanka only)  
✅ **Card payment** form

---

## 🔧 Technical Details

### Files Modified:

1. **cart-manager.js** - Added `checkout()` method
2. **create-checkout-session.php** - Enhanced for database cart
3. **payment-success.php** - Clears database cart after payment

### Stripe Keys (Already Configured):

- Location: `config/stripe_keys.php`
- Mode: **TEST MODE** ✅
- Keys: `pk_test_51SaYay...` (valid)

### Payment Flow:

```
User clicks Checkout
    ↓
Fetch cart from database
    ↓
Calculate delivery charges
    ↓
Create Stripe session
    ↓
Redirect to Stripe
    ↓
User pays
    ↓
Redirect to success page
    ↓
Clear cart from database
```

---

## 🎨 What You'll See

### In Shopping Cart:

- Items with images, names, prices
- Quantity controls
- Delete buttons
- **Items Total**
- **Delivery Charge (with distance)**
- **Grand Total**
- **Checkout Button** (blue, full width)

### In Stripe Payment Page:

- PetVet Shop branding
- All line items clearly listed
- Delivery charge with distance
- Total amount in **LKR (Sri Lankan Rupees)**
- Secure card payment form
- Address collection forms

### After Payment:

- Success animation
- Order confirmation
- Order ID
- Date & time
- Total amount paid
- Links to continue shopping or view orders

---

## 🐛 Troubleshooting

### "Cart is empty"

- Make sure items are in cart before clicking checkout
- Check browser console for errors

### "Stripe is not configured"

- Verify `config/stripe_keys.php` exists
- Check keys start with `pk_test_` and `sk_test_`

### Redirect doesn't work

- Check if success/cancel pages exist:
  - `views/pet-owner/payment-success.php` ✅
  - `views/pet-owner/payment-cancel.php` ✅

### Delivery not showing

- Enable location access in browser
- Location is optional - checkout still works without it

---

## 📱 Browser Compatibility

✅ Chrome  
✅ Firefox  
✅ Edge  
✅ Safari  
✅ Mobile browsers

---

## 🔐 Security

- All payments processed by Stripe (PCI compliant)
- No card details stored on your server
- HTTPS required for production (test mode works on localhost)
- API keys not exposed to frontend

---

## 🎉 You're All Set!

Just open your shop and click **Checkout** - it works! 🚀

---

**Questions?** Check the browser console and network tab for debugging.

**Next Steps:**

1. Test with different products
2. Test with/without location enabled
3. Test all test cards
4. When ready, switch to live Stripe keys for production
