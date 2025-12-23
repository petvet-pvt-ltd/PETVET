/**
 * Pet Owner Cart Manager (Database Driven)
 */
class CartManager {
    constructor() {
        this.clinicId = this.getClinicId();
        this.cartModal = null;
        this.init();
    }

    getClinicId() {
        // Try to get from URL params
        const urlParams = new URLSearchParams(window.location.search);
        let id = urlParams.get('clinic_id');
        
        // If not in URL, maybe we are on product page, try to find a data attribute or hidden input
        if (!id) {
            // On shop-product page, we might need to pass clinic_id from PHP
            const meta = document.querySelector('meta[name="clinic-id"]');
            if (meta) id = meta.content;
        }
        return id;
    }

    init() {
        if (!this.clinicId) return; // Not in a shop context

        this.injectCartIcon();
        this.injectCartModal();
        this.setupEventListeners();
        this.updateCartCount(); // Initial fetch
    }

    injectCartIcon() {
        // Find header actions or create a spot
        // Mobile: same vertical level as sidebar toggle (left) but right corner.
        // Desktop: Top right.
        
        // We'll append to body and use fixed positioning for simplicity and requirement compliance
        const iconHTML = `
            <div id="petvet-cart-icon" class="cart-floating-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                <span class="cart-badge" id="cart-badge-count">0</span>
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
                        <div class="cart-total">
                            <span>Total:</span>
                            <span id="cart-total-price">Rs. 0</span>
                        </div>
                        <button class="btn-checkout" onclick="alert('Checkout feature coming soon!')">Checkout</button>
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
        await this.loadCartItems();
    }

    closeCart() {
        this.cartModal.style.display = 'none';
    }

    async loadCartItems() {
        const container = document.getElementById('cart-items-container');
        container.innerHTML = '<div class="cart-loader">Loading...</div>';

        try {
            const response = await fetch(`/PETVET/api/pet-owner/cart.php?clinic_id=${this.clinicId}`);
            const data = await response.json();

            if (data.success) {
                this.renderCart(data.items, data.total);
                this.updateBadge(data.items.length);
            } else {
                container.innerHTML = `<div class="cart-error">${data.error}</div>`;
            }
        } catch (e) {
            container.innerHTML = `<div class="cart-error">Failed to load cart</div>`;
        }
    }

    renderCart(items, total) {
        const container = document.getElementById('cart-items-container');
        const totalEl = document.getElementById('cart-total-price');
        
        totalEl.textContent = `Rs. ${total.toLocaleString()}`;

        if (items.length === 0) {
            container.innerHTML = '<div class="cart-empty">Your cart is empty</div>';
            return;
        }

        let html = '<ul class="cart-list">';
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
                this.updateBadge(data.items.length);
            }
        } catch (e) {}
    }

    updateBadge(count) {
        const badge = document.getElementById('cart-badge-count');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        }
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    window.cartManager = new CartManager();
});
