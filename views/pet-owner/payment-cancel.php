<?php /* Payment Cancelled Page */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled | PetVet</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .cancel-container {
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
        
        .cancel-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ef4444, #dc2626);
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
        
        .cancel-icon svg {
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
        
        .cancel-message {
            color: #6b7280;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .info-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }
        
        .info-box h3 {
            color: #92400e;
            font-size: 16px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .info-box p {
            color: #78350f;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .info-box ul {
            color: #78350f;
            font-size: 14px;
            line-height: 1.8;
            margin-left: 20px;
            margin-top: 10px;
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
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(245, 87, 108, 0.3);
        }
        
        .btn-secondary {
            background: white;
            color: #f5576c;
            border: 2px solid #f5576c;
        }
        
        .btn-secondary:hover {
            background: #fef2f2;
        }
        
        .cart-info {
            background: #f0fdf4;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            color: #166534;
            font-size: 14px;
        }
        
        .cart-info strong {
            color: #15803d;
        }
    </style>
</head>
<body>
    <div class="cancel-container">
        <div class="cancel-icon">
            <svg viewBox="0 0 52 52">
                <line x1="16" y1="16" x2="36" y2="36"/>
                <line x1="36" y1="16" x2="16" y2="36"/>
            </svg>
        </div>
        
        <h1>Payment Cancelled</h1>
        <p class="cancel-message">
            Your payment was cancelled and no charges were made to your account.
            Your cart items have been saved and are still waiting for you!
        </p>
        
        <div class="cart-info">
            <strong>🛒 Your cart is safe!</strong><br>
            All items remain in your shopping cart.
        </div>
        
        <div class="info-box">
            <h3>💡 What happened?</h3>
            <p>The payment process was interrupted. This could be due to:</p>
            <ul>
                <li>Clicking the back button</li>
                <li>Closing the payment window</li>
                <li>Session timeout</li>
                <li>Voluntary cancellation</li>
            </ul>
        </div>
        
        <div class="btn-group">
            <a href="/PETVET/index.php?module=pet-owner&page=shop" class="btn btn-primary">
                Return to Cart & Try Again
            </a>
            <a href="/PETVET/index.php?module=pet-owner&page=shop" class="btn btn-secondary">
                Continue Shopping
            </a>
        </div>
    </div>
    
    <script>
        // Optional: Log cancellation for analytics
        console.log('Payment cancelled at:', new Date().toISOString());
        
        // Check if cart exists
        const cart = JSON.parse(localStorage.getItem('petvet_cart')) || [];
        if (cart.length === 0) {
            document.querySelector('.cart-info').style.display = 'none';
        }
    </script>
</body>
</html>
