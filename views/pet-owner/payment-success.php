<?php /* Payment Success Page */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful | PetVet</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #f0fdf4 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        
        body::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        
        .success-container {
            background: white;
            border-radius: 24px;
            padding: 60px 50px;
            max-width: 550px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08), 0 0 1px rgba(0, 0, 0, 0.05);
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            z-index: 1;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.96);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .success-icon {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            margin: 0 auto 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: scaleIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.2s backwards;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
            position: relative;
        }
        
        .success-icon::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: inherit;
            animation: pulse 2s ease-out infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(1.4);
                opacity: 0;
            }
        }
        
        @keyframes scaleIn {
            from {
                transform: scale(0) rotate(-180deg);
            }
            to {
                transform: scale(1) rotate(0deg);
            }
        }
        
        .success-icon svg {
            width: 65px;
            height: 65px;
            stroke: white;
            stroke-width: 3.5;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }
        
        h1 {
            color: #111827;
            font-size: 36px;
            margin-bottom: 18px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        .success-message {
            color: #6b7280;
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 40px;
            max-width: 460px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .order-details {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 35px;
            text-align: left;
            border: 1px solid #e2e8f0;
        }
        
        .order-details h2 {
            color: #1e293b;
            font-size: 20px;
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .order-details h2::before {
            content: '📋';
            font-size: 22px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .detail-row:last-child {
            border-bottom: none;
            padding-top: 18px;
            margin-top: 8px;
            border-top: 2px solid #cbd5e1;
            font-weight: 600;
            font-size: 19px;
            color: #10b981;
        }
        
        .detail-label {
            color: #64748b;
            font-size: 15px;
            font-weight: 500;
        }
        
        .detail-value {
            color: #1e293b;
            font-weight: 600;
            font-size: 15px;
        }
        
        .btn-group {
            display: flex;
            gap: 12px;
            flex-direction: column;
        }
        
        .btn {
            padding: 16px 32px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.35);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .btn-secondary {
            background: white;
            color: #2563eb;
            border: 2px solid #e0e7ff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        
        .btn-secondary:hover {
            background: #f8fafc;
            border-color: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        
        /* Confetti Animation */
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background: #f0f;
            animation: confetti-fall 3s linear forwards;
        }
        
        @keyframes confetti-fall {
            to {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }
        
        .auto-redirect-notice {
            margin-top: 25px;
            padding: 14px 20px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-radius: 10px;
            color: #1e40af;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid #bfdbfe;
        }
        
        .auto-redirect-notice strong {
            color: #1e3a8a;
        }
        
        .auto-redirect-notice button {
            margin-left: 12px;
            padding: 6px 14px;
            background: white;
            border: 1px solid #93c5fd;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            color: #1e40af;
            transition: all 0.2s;
        }
        
        .auto-redirect-notice button:hover {
            background: #f0f9ff;
            border-color: #2563eb;
        }
        
        @media (max-width: 600px) {
            .success-container {
                padding: 40px 30px;
            }
            
            h1 {
                font-size: 28px;
            }
            
            .success-icon {
                width: 100px;
                height: 100px;
            }
            
            .success-icon svg {
                width: 55px;
                height: 55px;
            }
            
            .order-details {
                padding: 24px;
            }
            
            .btn {
                padding: 14px 24px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <svg viewBox="0 0 52 52">
                <path d="M14 27l8 8 16-16"/>
            </svg>
        </div>
        
        <h1>🎉 Payment Successful!</h1>
        <p class="success-message">
            Thank you for your purchase! Your order has been confirmed and is being prepared for delivery.
            We've sent a confirmation to your registered email. You can track your order status in "My Orders" section.
        </p>
        
        <div class="order-details" id="orderDetails">
            <h2>Order Summary</h2>
            <div class="detail-row">
                <span class="detail-label">Transaction ID:</span>
                <span class="detail-value" id="transactionId">Loading...</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span class="detail-value" id="orderDate">Loading...</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">Confirmed ✓</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total Amount:</span>
                <span class="detail-value" id="totalAmount">Loading...</span>
            </div>
        </div>
        
        <div class="btn-group">
            <a href="/PETVET/index.php?module=pet-owner&page=orders" class="btn btn-primary" style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
                View My Orders
            </a>
            <a href="/PETVET/index.php?module=pet-owner&page=shop" class="btn btn-secondary" style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                Continue Shopping
            </a>
        </div>
        
        <div class="auto-redirect-notice" id="autoRedirect" style="display: none;">
            ℹ️ Redirecting to My Orders in <strong id="countdown">10</strong> seconds...
            <button onclick="clearTimeout(redirectTimer); document.getElementById('autoRedirect').style.display='none';" style="margin-left: 10px; padding: 4px 12px; background: white; border: 1px solid #3b82f6; border-radius: 4px; cursor: pointer; font-size: 0.85rem;">Cancel</button>
        </div>
    </div>
    
    <script>
        // Create confetti effect
        function createConfetti() {
            const colors = ['#667eea', '#764ba2', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
            for (let i = 0; i < 50; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.className = 'confetti';
                    confetti.style.left = Math.random() * 100 + '%';
                    confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
                    confetti.style.animationDelay = Math.random() * 0.5 + 's';
                    confetti.style.animationDuration = (Math.random() * 2 + 2) + 's';
                    document.body.appendChild(confetti);
                    
                    setTimeout(() => confetti.remove(), 3000);
                }, i * 30);
            }
        }
        
        // Trigger confetti on load
        window.addEventListener('load', createConfetti);
        
        // Auto-redirect timer
        let redirectTimer;
        let countdownValue = 10;
        
        function startAutoRedirect() {
            const autoRedirectEl = document.getElementById('autoRedirect');
            const countdownEl = document.getElementById('countdown');
            
            if (autoRedirectEl && countdownEl) {
                autoRedirectEl.style.display = 'flex';
                
                const interval = setInterval(() => {
                    countdownValue--;
                    countdownEl.textContent = countdownValue;
                    
                    if (countdownValue <= 0) {
                        clearInterval(interval);
                        window.location.href = '/PETVET/index.php?module=pet-owner&page=orders';
                    }
                }, 1000);
                
                redirectTimer = interval;
            }
        }
        
        // Start auto-redirect after 2 seconds
        setTimeout(startAutoRedirect, 2000);
        
        // Get session ID from URL
        const urlParams = new URLSearchParams(window.location.search);
        const sessionId = urlParams.get('session_id');
        
        console.log('=== PAYMENT SUCCESS DEBUG ===');
        console.log('Session ID from URL:', sessionId);
        
        if (sessionId) {
            document.getElementById('transactionId').textContent = sessionId.substring(0, 20) + '...';
        } else {
            document.getElementById('transactionId').textContent = 'N/A';
        }
        
        // Set current date
        const now = new Date();
        document.getElementById('orderDate').textContent = now.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        // Get clinic ID and name from sessionStorage (set during checkout)
        const clinicId = sessionStorage.getItem('petvet_checkout_clinic');
        const clinicName = sessionStorage.getItem('petvet_checkout_clinic_name');
        
        console.log('Payment Success - Clinic ID:', clinicId);
        console.log('Payment Success - Clinic Name:', clinicName);
        
        if (!clinicId) {
            console.error('✗ No clinic_id found in sessionStorage!');
            console.log('Attempting to extract clinic_id from URL or other sources...');
            // Try to get from URL params
            const urlClinicId = urlParams.get('clinic_id');
            if (urlClinicId) {
                console.log('Found clinic_id in URL:', urlClinicId);
                sessionStorage.setItem('petvet_checkout_clinic', urlClinicId);
            }
        }
        
        // Always try to fetch and save from database
        const finalClinicId = sessionStorage.getItem('petvet_checkout_clinic');
        
        if (finalClinicId) {
            console.log('Fetching cart from database for clinic:', finalClinicId);
            
            // Fetch cart items from database
            fetch('/PETVET/api/pet-owner/cart.php?clinic_id=' + finalClinicId)
                .then(response => response.json())
                .then(cartData => {
                    console.log('Cart data received:', cartData);
                    
                    if (cartData.success && cartData.items && cartData.items.length > 0) {
                        // Calculate total
                        const cartTotal = cartData.items.reduce((sum, item) => 
                            sum + (parseFloat(item.price) * parseInt(item.quantity)), 0
                        );
                        
                        // Display total
                        document.getElementById('totalAmount').textContent = 'Rs. ' + cartTotal.toLocaleString();
                        
                        // Get delivery charge from sessionStorage if available
                        const deliveryCharge = parseFloat(sessionStorage.getItem('petvet_delivery_charge') || '0');
                        
                        // Prepare order data
                        const orderData = {
                            clinic_id: finalClinicId,
                            session_id: sessionId,
                            total: cartTotal,
                            delivery_charge: deliveryCharge,
                            items: cartData.items.map(item => ({
                                product_id: item.product_id || item.id,
                                name: item.name,
                                quantity: parseInt(item.quantity),
                                price: parseFloat(item.price)
                            }))
                        };
                        
                        console.log('Sending order data to API:', orderData);
                        
                        // Save order to database
                        return fetch('/PETVET/api/pet-owner/orders.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(orderData)
                        });
                    } else {
                        console.log('No cart items found or cart already cleared');
                        document.getElementById('totalAmount').textContent = 'Payment Confirmed';
                        return null;
                    }
                })
                .then(response => {
                    if (response) {
                        console.log('Order API response status:', response.status);
                        return response.json();
                    }
                    return null;
                })
                .then(orderData => {
                    console.log('Order API response data:', orderData);
                    
                    if (orderData && orderData.success) {
                        console.log('✓ Order saved to database successfully!', orderData);
                        
                        // Now clear the cart
                        return fetch('/PETVET/api/pet-owner/cart.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                action: 'clear',
                                clinic_id: finalClinicId
                            })
                        });
                    } else if (orderData && !orderData.success) {
                        console.error('✗ Failed to save order:', orderData.error);
                    }
                    return null;
                })
                .then(response => {
                    if (response) return response.json();
                    return null;
                })
                .then(data => {
                    if (data) {
                        console.log('Cart cleared:', data);
                        sessionStorage.removeItem('petvet_checkout_clinic');
                        sessionStorage.removeItem('petvet_checkout_clinic_name');
                    }
                })
                .catch(error => {
                    console.error('Error processing order:', error);
                    document.getElementById('totalAmount').textContent = 'Payment Confirmed';
                });
        } else {
            console.error('✗ Could not find clinic_id anywhere!');
            document.getElementById('totalAmount').textContent = 'Payment Confirmed';
        }
    </script>
</body>
</html>
