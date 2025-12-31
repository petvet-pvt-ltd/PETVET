<?php /* Pet Owner Orders History Page */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | PetVet</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f8fafc;
        }
        
        .main-content {
            padding: 2rem;
            padding-left: calc(240px + 2rem);
            max-width: calc(1200px + 240px);
            margin: 0 auto;
            width: 100%;
        }
        
        /* Navigation Bar */
        .nav-bar {
            background: white;
            padding: 1.2rem 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .nav-bar .back-btn {
            padding: 0.6rem 1.2rem;
            background: #f1f5f9;
            color: #475569;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            font-size: 14px;
        }
        
        .nav-bar .back-btn:hover {
            background: #e2e8f0;
            color: #1e293b;
        }
        
        .nav-bar .order-count {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-size: 14px;
            font-weight: 500;
        }
        
        .nav-bar .order-count .count-badge {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-weight: 600;
            min-width: 28px;
            text-align: center;
        }
        
        /* Page Header */
        .page-header {
            margin-bottom: 2rem;
        }
        
        .page-header h1 {
            color: #0f172a;
            font-size: 2rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .page-header p {
            color: #64748b;
            font-size: 15px;
        }
        
        /* Orders Container */
        .orders-container {
            display: grid;
            gap: 1.5rem;
        }
        
        /* Order Card */
        .order-card {
            background: white;
            border-radius: 16px;
            padding: 0;
            border: 1px solid #e2e8f0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            width: 100%;
            max-width: 100%;
        }
        
        .order-card:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
            border-color: #cbd5e1;
        }
        
        /* Order Header */
        .order-header {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .order-header-left {
            flex: 1;
        }
        
        .order-id {
            font-weight: 700;
            color: #0f172a;
            font-size: 16px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .order-id::before {
            content: '🛒';
            font-size: 18px;
        }
        
        .shop-name {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #2563eb;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            padding: 4px 10px;
            background: #eff6ff;
            border-radius: 6px;
            border: 1px solid #dbeafe;
        }
        
        .shop-name::before {
            content: '🏪';
            font-size: 14px;
        }
        
        .order-date {
            color: #64748b;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .order-date::before {
            content: '📅';
            font-size: 13px;
        }
        
        .order-status {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }
        
        .status-confirmed {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 1px solid #6ee7b7;
        }
        
        .status-confirmed::before {
            content: '✓';
            font-weight: 700;
            font-size: 14px;
        }
        
        /* Order Items */
        .order-items {
            padding: 1.5rem;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 1rem;
            margin-bottom: 0.75rem;
            background: #f8fafc;
            border-radius: 10px;
            transition: background 0.2s;
            gap: 1rem;
            width: 100%;
        }
        
        .order-item:hover {
            background: #f1f5f9;
        }
        
        .order-item:last-child {
            margin-bottom: 0;
        }
        
        .item-details {
            flex: 1;
            min-width: 0;
            overflow: hidden;
        }
        
        .item-name {
            color: #1e293b;
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 4px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .item-quantity {
            color: #64748b;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        
        .item-quantity::before {
            content: '×';
            font-weight: 700;
            color: #94a3b8;
        }
        
        .item-price {
            color: #0f172a;
            font-weight: 700;
            font-size: 16px;
            white-space: nowrap;
            flex-shrink: 0;
            min-width: 100px;
            text-align: right;
        }
        
        /* Order Footer */
        .order-footer {
            padding: 1.5rem;
            background: #fafbfc;
            border-top: 2px solid #e2e8f0;
        }
        
        .order-summary {
            margin-bottom: 1rem;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            color: #64748b;
            font-size: 14px;
        }
        
        .summary-label {
            font-weight: 500;
        }
        
        .summary-value {
            font-weight: 600;
            color: #475569;
        }
        
        .order-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 2px solid #e2e8f0;
        }
        
        .order-total-label {
            font-size: 16px;
            font-weight: 600;
            color: #475569;
        }
        
        .order-total-value {
            font-size: 24px;
            font-weight: 700;
            color: #10b981;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            flex-shrink: 0;
        }
        
        .btn-download-invoice {
            width: auto;
            margin-top: 0.75rem;
            padding: 0.4rem 0.8rem;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 1px 4px rgba(37, 99, 235, 0.15);
        }
        
        .btn-download-invoice:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 5rem 2rem;
            background: white;
            border-radius: 16px;
            border: 2px dashed #e2e8f0;
        }
        
        .empty-icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            opacity: 0.5;
        }
        
        .empty-state h2 {
            color: #1e293b;
            font-size: 24px;
            margin-bottom: 0.75rem;
            font-weight: 700;
        }
        
        .empty-state p {
            color: #64748b;
            margin-bottom: 2rem;
            font-size: 15px;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }
        
        /* Loading State */
        .loading-state {
            text-align: center;
            padding: 3rem;
        }
        
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #e2e8f0;
            border-top-color: #2563eb;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 1rem;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                padding: 1rem;
                padding-top: 70px;
                padding-left: 1rem;
                max-width: 100%;
            }
            
            .order-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .order-status {
                align-self: flex-start;
            }
            
            .order-item {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .item-price {
                align-self: flex-start;
            }
            
            .page-header h1 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../shared/sidebar/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Navigation Bar -->
        <div class="nav-bar">
            <a href="/PETVET/index.php?module=pet-owner&page=shop" class="back-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Back to Shop
            </a>
            <div class="order-count">
                <span class="count-badge" id="orderCount">0</span>
                <span>Order(s)</span>
            </div>
        </div>

        <!-- Page Header -->
        <div class="page-header">
            <h1>📦 My Orders</h1>
            <p>View your order history and track your purchases</p>
        </div>
        
        <!-- Orders Container -->
        <div class="orders-container" id="ordersContainer">
            <div class="loading-state">
                <div class="loading-spinner"></div>
                <p style="color: #64748b;">Loading your orders...</p>
            </div>
        </div>
    </div>
    
    <script>
        // Load orders from database
        const container = document.getElementById('ordersContainer');
        const orderCountEl = document.getElementById('orderCount');
        
        // Fetch orders from API
        fetch('/PETVET/api/pet-owner/orders.php')
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.error || 'Failed to load orders');
                }
                
                const orders = data.orders || [];
                console.log('Loaded orders from database:', orders);
                
                // Update order count
                if (orderCountEl) {
                    orderCountEl.textContent = orders.length;
                }
                
                if (orders.length > 0) {
                    container.innerHTML = orders.map(order => {
                        const orderDate = new Date(order.created_at);
                        const formattedDate = orderDate.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                        
                        // Get shop name from order
                        const shopName = order.clinic_name || 'PetVet Shop';
                        
                        // Ensure items exist and have proper structure
                        const items = order.items || [];
                        const itemsHtml = items.map(item => {
                            const itemName = item.name || 'Unknown Product';
                            const itemQuantity = parseInt(item.quantity) || 1;
                            const itemPrice = parseFloat(item.price) || 0;
                            const itemTotal = itemPrice * itemQuantity;
                            
                            return `
                                <div class="order-item">
                                    <div class="item-details">
                                        <div class="item-name">${itemName}</div>
                                        <div class="item-quantity">${itemQuantity}</div>
                                    </div>
                                    <div class="item-price">Rs. ${itemTotal.toLocaleString()}</div>
                                </div>
                            `;
                        }).join('');
                        
                        // Use total from database
                        const total = parseFloat(order.total_amount) || 0;
                        const deliveryCharge = parseFloat(order.delivery_charge) || 0;
                        const subtotal = total - deliveryCharge;
                        
                        return `
                            <div class="order-card">
                                <div class="order-header">
                                    <div class="order-header-left">
                                        <div class="order-id">Order #${order.order_number}</div>
                                        <div class="shop-name">${shopName}</div>
                                        <div class="order-date">${formattedDate}</div>
                                    </div>
                                    <div class="order-status status-confirmed">${order.status || 'Confirmed'}</div>
                                </div>
                                <div class="order-items">
                                    ${itemsHtml}
                                </div>
                                <div class="order-footer">
                                    <div class="order-summary">
                                        ${deliveryCharge > 0 ? `
                                            <div class="summary-row">
                                                <span class="summary-label">Subtotal</span>
                                                <span class="summary-value">Rs. ${subtotal.toLocaleString()}</span>
                                            </div>
                                            <div class="summary-row">
                                                <span class="summary-label">Delivery Charge</span>
                                                <span class="summary-value">Rs. ${deliveryCharge.toLocaleString()}</span>
                                            </div>
                                        ` : ''}
                                        <div class="order-total">
                                            <span class="order-total-label">Total Amount</span>
                                            <span class="order-total-value">Rs. ${total.toLocaleString()}</span>
                                        </div>
                                    </div>
                                    <button class="btn-download-invoice" onclick="downloadInvoice(${order.id}, '${order.order_number}')">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="7 10 12 15 17 10"></polyline>
                                            <line x1="12" y1="15" x2="12" y2="3"></line>
                                        </svg>
                                        Download Invoice
                                    </button>
                                </div>
                            </div>
                        `;
                    }).join('');
                } else {
                    container.innerHTML = `
                        <div class="empty-state">
                            <div class="empty-icon">🛒</div>
                            <h2>No orders yet</h2>
                            <p>Start shopping to see your orders here!</p>
                            <a href="/PETVET/index.php?module=pet-owner&page=shop" class="btn">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                                Start Shopping
                            </a>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading orders:', error);
                container.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon">⚠️</div>
                        <h2>Error loading orders</h2>
                        <p>Please try refreshing the page</p>
                        <button onclick="location.reload()" class="btn">Refresh Page</button>
                    </div>
                `;
            });
        
        // Download invoice function
        function downloadInvoice(orderId, orderNumber) {
            window.open('/PETVET/api/pet-owner/download-invoice-pdf.php?order_id=' + orderId, '_blank');
        }
    </script>
</body>
</html>
