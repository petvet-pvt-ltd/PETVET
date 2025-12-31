/**
 * Pet Owner Cart Manager (Database Driven)
 */
class CartManager {
    constructor() {
        this.clinicId = this.getClinicId();
        this.cartModal = null;
        this.userLocation = null;
        this.deliveryInfo = null;
        this.init();
    }

    getClinicId() {
        // Try to get from URL params
        const urlParams = new URLSearchParams(window.location.search);
        let id = urlParams.get('clinic_id');
        let name = null;
        
        // If not in URL, maybe we are on product page, try to find a data attribute or hidden input
        if (!id) {
            // On shop-product page, we might need to pass clinic_id from PHP
            const meta = document.querySelector('meta[name="clinic-id"]');
            if (meta) {
                id = meta.content;
                // Also get clinic name
                const nameMeta = document.querySelector('meta[name="clinic-name"]');
                if (nameMeta) name = nameMeta.content;
            }
        }
        
        // Store clinic name in sessionStorage for later use
        if (name) {
            sessionStorage.setItem('petvet_clinic_name', name);
        }
        
        return id;
    }

    init() {
        if (!this.clinicId) return; // Not in a shop context

        this.injectCartIcon();
        this.injectCartModal();
        this.setupEventListeners();
        this.requestUserLocation(); // Get user location for delivery calculation
        this.updateCartCount(); // Initial fetch
    }

    requestUserLocation() {
        if (!navigator.geolocation) {
            console.warn('Geolocation not supported');
            return;
        }

        const options = {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 300000 // Cache for 5 minutes
        };

        navigator.geolocation.getCurrentPosition(
            (position) => {
                this.userLocation = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude
                };
                console.log('User location obtained for delivery calculation');
            },
            (error) => {
                console.warn('Location error:', error.message);
            },
            options
        );
    }

    injectCartIcon() {
        const iconHTML = `
            <div id="petvet-cart-icon" class="cart-floating-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                <span class="cart-badge" id="cart-badge-count">0/10</span>
                <div class="cart-progress-bar">
                    <div class="cart-progress-fill" id="cart-progress-fill" style="width: 0%"></div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', iconHTML);
    }

    injectCartModal() {
        const modalHTML = `
            <div id="petvet-cart-modal" class="cart-modal-overlay" style="display:none;">
                <div class="cart-modal-content">
                    <div class="cart-header">
                        <h3>Shopping Cart</h3>
                        <button class="close-cart">&times;</button>
                    </div>
                    <div class="cart-body" id="cart-items-container">
                        <div class="cart-loader">Loading...</div>
                    </div>
                    <div class="cart-footer">
                        <div class="cart-summary" id="cart-summary">
                            <!-- Summary will be populated by JS -->
                        </div>
                        <button class="btn-checkout" id="petvet-checkout-btn">Checkout</button>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.cartModal = document.getElementById('petvet-cart-modal');
    }

    setupEventListeners() {
        // Toggle Cart
        document.getElementById('petvet-cart-icon').addEventListener('click', () => this.openCart());
        this.cartModal.querySelector('.close-cart').addEventListener('click', () => this.closeCart());
        this.cartModal.addEventListener('click', (e) => {
            if (e.target === this.cartModal) this.closeCart();
        });

        // Checkout Button
        const checkoutBtn = document.getElementById('petvet-checkout-btn');
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', () => this.checkout());
        }

        // Add to Cart Buttons (Global delegation)
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('add-to-cart') || e.target.closest('.add-to-cart')) {
                e.preventDefault();
                e.stopPropagation();
                const btn = e.target.classList.contains('add-to-cart') ? e.target : e.target.closest('.add-to-cart');
                this.handleAddToCart(btn);
            }
        });
    }

    async openCart() {
        this.cartModal.style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
        await this.loadCartItems();
    }

    closeCart() {
        this.cartModal.style.display = 'none';
        document.body.style.overflow = ''; // Restore background scrolling
    }

    async loadCartItems() {
        const container = document.getElementById('cart-items-container');
        const summaryEl = document.getElementById('cart-summary');
        container.innerHTML = '<div class="cart-loader">Loading...</div>';
        if (summaryEl) {
            summaryEl.innerHTML = '<div class="cart-loader" style="text-align: center;">Calculating delivery...</div>';
        }

        try {
            const response = await fetch(`/PETVET/api/pet-owner/cart.php?clinic_id=${this.clinicId}`);
            const data = await response.json();

            if (data.success) {
                // Calculate delivery charges if location available
                if (this.userLocation) {
                    await this.calculateDelivery();
                } else {
                    console.log('Waiting for user location...');
                    // Wait a bit for location to be available
                    await new Promise(resolve => setTimeout(resolve, 1000));
                    if (this.userLocation) {
                        await this.calculateDelivery();
                    }
                }
                
                this.renderCart(data.items, data.total, data.totalQuantity || 0, data.maxItemsPerOrder || 10);
                this.updateBadge(data.totalQuantity || 0, data.maxItemsPerOrder || 10);
            } else {
                container.innerHTML = `<div class="cart-error">${data.error}</div>`;
            }
        } catch (e) {
            container.innerHTML = `<div class="cart-error">Failed to load cart</div>`;
        }
    }

    async calculateDelivery() {
        // Reset delivery info
        this.deliveryInfo = null;
        
        if (!this.userLocation) {
            console.log('User location not available for delivery calculation');
            return;
        }

        try {
            const response = await fetch(
                `/PETVET/api/pet-owner/calculate-delivery.php?clinic_id=${this.clinicId}&latitude=${this.userLocation.latitude}&longitude=${this.userLocation.longitude}`
            );
            const data = await response.json();

            if (data.success) {
                this.deliveryInfo = data;
            } else {
                console.error('Failed to calculate delivery:', data.error);
            }
        } catch (e) {
            console.error('Error calculating delivery:', e);
        }
    }

    renderCart(items, total, totalQuantity, maxItems) {
        const container = document.getElementById('cart-items-container');
        const summaryEl = document.getElementById('cart-summary');
        const checkoutBtn = document.querySelector('.btn-checkout');

        if (items.length === 0) {
            container.innerHTML = '<div class="cart-empty">Your cart is empty</div>';
            summaryEl.innerHTML = '<div style="text-align: center; color: #94a3b8; font-size: 0.9rem;">No items</div>';
            if (checkoutBtn) {
                checkoutBtn.disabled = true;
                checkoutBtn.textContent = 'Checkout';
            }
            return;
        }

        // Check if cart exceeds limit
        const exceedsLimit = totalQuantity > maxItems;
        
        // Check if delivery exceeds max distance
        const exceedsDistance = this.deliveryInfo && this.deliveryInfo.exceeds_max_distance;
        
        // Calculate grand total
        const deliveryCharge = this.deliveryInfo ? this.deliveryInfo.delivery_charge : 0;
        const grandTotal = total + deliveryCharge;
        
        // Update summary display with breakdown
        if (this.deliveryInfo) {
            const distance = this.deliveryInfo.distance;
            summaryEl.innerHTML = `
                <div class="cart-summary-row">
                    <span class="summary-label">Items Total</span>
                    <span class="summary-value">Rs. ${total.toLocaleString()}</span>
                </div>
                <div class="cart-summary-row">
                    <span class="summary-label">
                        Delivery Charges
                        <span class="distance-badge">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            ${distance} km
                        </span>
                    </span>
                    <span class="summary-value">Rs. ${deliveryCharge.toLocaleString()}</span>
                </div>
                <div class="cart-summary-row grand-total">
                    <span class="summary-label">Grand Total</span>
                    <span class="summary-value">Rs. ${grandTotal.toLocaleString()}</span>
                </div>
            `;
        } else {
            // Show items total and a note about delivery
            summaryEl.innerHTML = `
                <div class="cart-summary-row">
                    <span class="summary-label">Items Total</span>
                    <span class="summary-value">Rs. ${total.toLocaleString()}</span>
                </div>
                <div class="cart-summary-row" style="font-size: 0.85rem; color: #f59e0b; font-style: italic; padding: 8px; background: #fffbeb; border-radius: 6px; margin-top: 8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0; margin-right: 6px;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <span>Enable location access to calculate delivery charges</span>
                </div>
            `;
        }
        
        // Update checkout button state
        if (checkoutBtn) {
            if (exceedsLimit) {
                checkoutBtn.disabled = true;
                checkoutBtn.textContent = `Limit Exceeded (${totalQuantity}/${maxItems})`;
                checkoutBtn.style.opacity = '0.5';
                checkoutBtn.style.cursor = 'not-allowed';
            } else if (exceedsDistance) {
                checkoutBtn.disabled = true;
                checkoutBtn.textContent = 'Delivery Not Available';
                checkoutBtn.style.opacity = '0.5';
                checkoutBtn.style.cursor = 'not-allowed';
            } else {
                checkoutBtn.disabled = false;
                checkoutBtn.textContent = 'Checkout';
                checkoutBtn.style.opacity = '1';
                checkoutBtn.style.cursor = 'pointer';
            }
        }

        let html = '';
        
        // Show warning if limit exceeded
        if (exceedsLimit) {
            html += `
                <div class="cart-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    <div>
                        <strong>Cart limit exceeded!</strong><br>
                        This shop allows maximum ${maxItems} items per order. Please remove ${totalQuantity - maxItems} item(s) to continue.
                    </div>
                </div>
            `;
        }
        
        // Show warning if exceeds delivery distance
        if (exceedsDistance && this.deliveryInfo) {
            const distance = this.deliveryInfo.distance;
            const maxDistance = this.deliveryInfo.max_delivery_distance;
            html += `
                <div class="cart-warning" style="background: #fef2f2; border-color: #fca5a5;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                        <line x1="2" y1="2" x2="22" y2="22"></line>
                    </svg>
                    <div>
                        <strong>Delivery not available!</strong><br>
                        Your location is ${distance} km away. This shop only delivers within ${maxDistance} km radius.
                    </div>
                </div>
            `;
        }
        
        html += '<ul class="cart-list">';
        items.forEach(item => {
            html += `
                <li class="cart-item" data-id="${item.product_id}">
                    <img src="${this.getImageUrl(item.image)}" alt="${item.name}" class="cart-item-img">
                    <div class="cart-item-details">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price">Rs. ${parseInt(item.price).toLocaleString()}</div>
                        <div class="cart-item-controls">
                            <button class="qty-btn minus" onclick="cartManager.updateQuantity(${item.product_id}, ${item.quantity - 1})">-</button>
                            <span class="qty-val">${item.quantity}</span>
                            <button class="qty-btn plus" onclick="cartManager.updateQuantity(${item.product_id}, ${item.quantity + 1})">+</button>
                            <button class="remove-btn" onclick="cartManager.removeItem(${item.product_id})" aria-label="Remove item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        </div>
                    </div>
                </li>
            `;
        });
        html += '</ul>';
        container.innerHTML = html;
    }

    getImageUrl(url) {
        if (!url) return '/PETVET/public/images/product-placeholder.png';
        if (url.startsWith('http') || url.startsWith('/PETVET/')) return url;
        return '/PETVET/' + url.replace(/^\//, '');
    }

    async handleAddToCart(btn) {
        // Prevent default behavior (like navigation if it's a link)
        // But wait, if it's a button inside a link (like in shop-clinic cards), we need to stop propagation
        // The event listener already does this if attached correctly, but let's be safe
        
        const productId = btn.dataset.productId;
        // If on product page, get quantity from input, else 1
        let quantity = 1;
        const qtyInput = document.getElementById('quantity');
        if (qtyInput && window.location.href.includes('shop-product')) {
            quantity = parseInt(qtyInput.value) || 1;
        }

        // Visual feedback
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Adding...';
        btn.disabled = true;

        try {
            const response = await fetch('/PETVET/api/pet-owner/cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'add',
                    clinic_id: this.clinicId,
                    product_id: productId,
                    quantity: quantity
                })
            });
            const data = await response.json();

            if (data.success) {
                btn.innerHTML = 'Added!';
                this.updateCartCount(); // Refresh badge
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, 2000);
            } else {
                alert(data.error);
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        } catch (e) {
            console.error(e);
            alert('Failed to add to cart');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    async updateQuantity(productId, newQty) {
        if (newQty < 1) return; // Or ask to remove?
        
        try {
            const response = await fetch('/PETVET/api/pet-owner/cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'update',
                    clinic_id: this.clinicId,
                    product_id: productId,
                    quantity: newQty
                })
            });
            const data = await response.json();
            
            if (data.success) {
                this.loadCartItems(); // Reload to reflect changes/totals
            } else {
                alert(data.error);
            }
        } catch (e) {
            console.error(e);
        }
    }

    async removeItem(productId) {
        if (!confirm('Remove this item?')) return;

        try {
            const response = await fetch('/PETVET/api/pet-owner/cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'remove',
                    clinic_id: this.clinicId,
                    product_id: productId
                })
            });
            const data = await response.json();
            
            if (data.success) {
                this.loadCartItems();
                this.updateCartCount();
            } else {
                alert(data.error);
            }
        } catch (e) {
            console.error(e);
        }
    }

    async updateCartCount() {
        try {
            const response = await fetch(`/PETVET/api/pet-owner/cart.php?clinic_id=${this.clinicId}`);
            const data = await response.json();
            if (data.success) {
                this.updateBadge(data.totalQuantity || 0, data.maxItemsPerOrder || 10);
            }
        } catch (e) {}
    }

    updateBadge(quantity, maxItems) {
        const badge = document.getElementById('cart-badge-count');
        const progressFill = document.getElementById('cart-progress-fill');
        const cartIcon = document.getElementById('petvet-cart-icon');
        
        if (badge) {
            badge.textContent = `${quantity}/${maxItems}`;
            badge.style.display = 'flex';
            
            // Update progress bar
            if (progressFill) {
                const percentage = (quantity / maxItems) * 100;
                progressFill.style.width = `${Math.min(percentage, 100)}%`;
                
                // Change color based on percentage
                if (percentage >= 100) {
                    progressFill.style.backgroundColor = '#ef4444'; // Red when full
                } else if (percentage >= 80) {
                    progressFill.style.backgroundColor = '#f59e0b'; // Orange when near limit
                } else {
                    progressFill.style.backgroundColor = '#10b981'; // Green otherwise
                }
            }
            
            // Add warning class if at limit
            if (cartIcon) {
                if (quantity >= maxItems) {
                    cartIcon.classList.add('cart-limit-reached');
                } else {
                    cartIcon.classList.remove('cart-limit-reached');
                }
            }
        }
    }

    async checkout() {
        const checkoutBtn = document.getElementById('petvet-checkout-btn');
        
        if (!checkoutBtn || checkoutBtn.disabled) {
            return;
        }

        // Show loading state
        const originalText = checkoutBtn.textContent;
        checkoutBtn.textContent = 'Processing...';
        checkoutBtn.disabled = true;

        try {
            // First, get the current cart items from database
            const cartResponse = await fetch(`/PETVET/api/pet-owner/cart.php?clinic_id=${this.clinicId}`);
            const cartData = await cartResponse.json();

            if (!cartData.success || !cartData.items || cartData.items.length === 0) {
                throw new Error('Your cart is empty');
            }

            // Prepare cart data for Stripe
            const checkoutData = {
                cart: cartData.items.map(item => ({
                    name: item.name,
                    price: parseFloat(item.price),
                    quantity: parseInt(item.quantity),
                    image: item.image || 'https://via.placeholder.com/150'
                })),
                clinic_id: this.clinicId,
                subtotal: cartData.total
            };

            // Add delivery info if available
            if (this.deliveryInfo && !this.deliveryInfo.exceeds_max_distance) {
                checkoutData.delivery = {
                    charge: this.deliveryInfo.delivery_charge,
                    distance: this.deliveryInfo.distance,
                    latitude: this.userLocation.latitude,
                    longitude: this.userLocation.longitude
                };
            }

            // Create Stripe checkout session
            const stripeResponse = await fetch('/PETVET/api/payments/create-checkout-session.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(checkoutData)
            });

            // Check if response is JSON
            const contentType = stripeResponse.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Server returned invalid response. Please check if Stripe is configured correctly.');
            }

            const stripeData = await stripeResponse.json();

            if (stripeData.success && stripeData.url) {
                // Store cart info in sessionStorage for success page
                sessionStorage.setItem('petvet_checkout_clinic', this.clinicId);
                
                // Store clinic name if available
                const clinicName = sessionStorage.getItem('petvet_clinic_name');
                if (clinicName) {
                    sessionStorage.setItem('petvet_checkout_clinic_name', clinicName);
                }
                
                // Store delivery charge if available
                if (this.deliveryInfo && this.deliveryInfo.delivery_charge) {
                    sessionStorage.setItem('petvet_delivery_charge', this.deliveryInfo.delivery_charge);
                }
                
                // Redirect to Stripe Checkout
                window.location.href = stripeData.url;
            } else {
                throw new Error(stripeData.error || 'Failed to create checkout session');
            }

        } catch (error) {
            console.error('Checkout error:', error);
            
            let errorMessage = 'Unable to proceed to checkout.\n\n';
            errorMessage += 'Error: ' + error.message;
            
            if (error.message.includes('Stripe is configured')) {
                errorMessage += '\n\nPlease ensure Stripe API keys are properly configured.';
            }
            
            alert(errorMessage);
            
            // Restore button state
            checkoutBtn.textContent = originalText;
            checkoutBtn.disabled = false;
        }
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    window.cartManager = new CartManager();
});
