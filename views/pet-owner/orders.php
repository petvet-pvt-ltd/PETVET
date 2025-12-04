<?php /* Pet Owner Orders History Page */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | PetVet</title>
    <style>
        .main-content {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .page-header {
            margin-bottom: 2rem;
        }
        
        .page-header h1 {
            color: #1f2937;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .page-header p {
            color: #6b7280;
        }
        
        .orders-container {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .order-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        
        .order-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .order-id {
            font-weight: 600;
            color: #1f2937;
        }
        
        .order-date {
            color: #6b7280;
            font-size: 0.9rem;
        }
        
        .order-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .status-confirmed {
            background: #d1fae5;
            color: #065f46;
        }
        
        .order-items {
            margin-bottom: 1rem;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
        }
        
        .item-name {
            color: #374151;
        }
        
        .item-quantity {
            color: #6b7280;
            font-size: 0.9rem;
        }
        
        .item-price {
            color: #1f2937;
            font-weight: 500;
        }
        
        .order-total {
            display: flex;
            justify-content: space-between;
            padding-top: 1rem;
            border-top: 2px solid #e5e7eb;
            font-size: 1.1rem;
            font-weight: 600;
            color: #10b981;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }
        
        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .empty-state h2 {
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        
        .empty-state p {
            color: #6b7280;
            margin-bottom: 2rem;
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../shared/sidebar/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1>📦 My Orders</h1>
            <p>View your order history and track your purchases</p>
        </div>
        
        <div class="orders-container" id="ordersContainer">
            <div class="empty-state">
                <div class="empty-icon">📦</div>
                <h2>No orders yet</h2>
                <p>Start shopping to see your orders here!</p>
                <a href="/PETVET/index.php?module=pet-owner&page=shop" class="btn">Start Shopping</a>
            </div>
        </div>
    </div>
    
    <script>
        // Load orders from localStorage
        const orders = JSON.parse(localStorage.getItem('petvet_orders')) || [];
        const container = document.getElementById('ordersContainer');
        
        if (orders.length > 0) {
            // Sort orders by date (newest first)
            orders.sort((a, b) => new Date(b.date) - new Date(a.date));
            
            container.innerHTML = orders.map(order => {
                const orderDate = new Date(order.date);
                const formattedDate = orderDate.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
                const itemsHtml = order.items.map(item => `
                    <div class="order-item">
                        <div>
                            <div class="item-name">${item.name}</div>
                            <div class="item-quantity">Quantity: ${item.quantity}</div>
                        </div>
                        <div class="item-price">Rs. ${(item.price * item.quantity).toLocaleString()}</div>
                    </div>
                `).join('');
                
                return `
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <div class="order-id">Order #${order.id.substring(0, 15)}${order.id.length > 15 ? '...' : ''}</div>
                                <div class="order-date">${formattedDate}</div>
                            </div>
                            <div class="order-status status-confirmed">${order.status}</div>
                        </div>
                        <div class="order-items">
                            ${itemsHtml}
                        </div>
                        <div class="order-total">
                            <span>Total Amount</span>
                            <span>Rs. ${order.total.toLocaleString()}</span>
                        </div>
                    </div>
                `;
            }).join('');
        }
    </script>
</body>
</html>
