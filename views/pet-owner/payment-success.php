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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .success-container {
            background: white;
            border-radius: 20px;
            padding: 50px 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.5s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .success-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981, #059669);
            margin: 0 auto 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: scaleIn 0.5s ease-out 0.2s backwards;
        }
        
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        
        .success-icon svg {
            width: 60px;
            height: 60px;
            stroke: white;
            stroke-width: 3;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        
        h1 {
            color: #1f2937;
            font-size: 32px;
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .success-message {
            color: #6b7280;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .order-details {
            background: #f9fafb;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            text-align: left;
        }
        
        .order-details h2 {
            color: #374151;
            font-size: 18px;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .detail-row:last-child {
            border-bottom: none;
            padding-top: 15px;
            font-weight: 600;
            font-size: 18px;
            color: #10b981;
        }
        
        .detail-label {
            color: #6b7280;
        }
        
        .detail-value {
            color: #1f2937;
            font-weight: 500;
        }
        
        .btn-group {
            display: flex;
            gap: 15px;
            flex-direction: column;
        }
        
        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }
        
        .btn-secondary:hover {
            background: #f3f4f6;
        }
        
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 10px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
        
        <h1>Payment Successful!</h1>
        <p class="success-message">
            Thank you for your purchase! Your order has been confirmed and will be processed shortly.
            A confirmation email will be sent to your registered email address.
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
            <a href="/PETVET/index.php?module=pet-owner&page=shop" class="btn btn-primary">
                Continue Shopping
            </a>
            <a href="/PETVET/index.php?module=pet-owner&page=orders" class="btn btn-secondary">
                View My Orders
            </a>
        </div>
    </div>
    
    <script>
        // Get session ID from URL
        const urlParams = new URLSearchParams(window.location.search);
        const sessionId = urlParams.get('session_id');
        
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
        
        // Get total from cart before clearing
        const cart = JSON.parse(localStorage.getItem('petvet_cart')) || [];
        const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        document.getElementById('totalAmount').textContent = 'Rs. ' + total.toLocaleString();
        
        // Clear the cart after successful payment
        localStorage.removeItem('petvet_cart');
        
        // Optional: Save order to localStorage for order history
        const orders = JSON.parse(localStorage.getItem('petvet_orders')) || [];
        orders.push({
            id: sessionId || 'ORDER_' + Date.now(),
            date: now.toISOString(),
            items: cart,
            total: total,
            status: 'Confirmed'
        });
        localStorage.setItem('petvet_orders', JSON.stringify(orders));
    </script>
</body>
</html>
