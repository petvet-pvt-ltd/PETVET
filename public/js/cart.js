// Shopping Cart Management - No Frameworks/Libraries
let cart = JSON.parse(localStorage.getItem('petvet_cart')) || [];

// Global image override for cart items (user-specified)
const CART_IMG_OVERRIDE = 'https://m.media-amazon.com/images/I/41A-2VUQHsL.jpg';

// Initialize with demo items if cart is empty (first time)
if (cart.length === 0 && !localStorage.getItem('petvet_cart_initialized')) {
    cart = [
        {
            id: 1,
            name: 'Premium Dog Food - 5kg',
            price: 2500,
            image: '/PETVET/public/images/products/dog-food.jpg',
            quantity: 2
        },
        {
            id: 5,
            name: 'Interactive Cat Toy Set',
            price: 850,
            image: '/PETVET/public/images/products/cat-toy.jpg',
            quantity: 1
        },
        {
            id: 10,
            name: 'Professional Grooming Kit',
            price: 3200,
            image: '/PETVET/public/images/products/grooming-kit.jpg',
            quantity: 1
        },
        {
            id: 15,
            name: 'Luxury Pet Bed - Large',
            price: 4500,
            image: '/PETVET/public/images/products/pet-bed.jpg',
            quantity: 1
        }
    ];
    localStorage.setItem('petvet_cart', JSON.stringify(cart));
    localStorage.setItem('petvet_cart_initialized', 'true');
}

// Toggle cart dropdown (full-screen modal behavior)
function toggleCart() {
    const dropdown = document.getElementById('cartDropdown');
    const willOpen = !dropdown.classList.contains('active');
    dropdown.classList.toggle('active');

    // Body scroll lock when cart is open
    if (willOpen) {
        document.documentElement.style.overflow = 'hidden';
        document.body.style.overflow = 'hidden';
        // Hide scroll-to-top button when cart is open
        const scrollBtn = document.getElementById('scrollToTop');
        if (scrollBtn) {
            scrollBtn.style.display = 'none';
        }
    } else {
        document.documentElement.style.overflow = '';
        document.body.style.overflow = '';
        // Restore scroll-to-top button visibility based on scroll position
        const scrollBtn = document.getElementById('scrollToTop');
        if (scrollBtn && window.pageYOffset > 300) {
            scrollBtn.style.display = '';
        }
    }
}

// Close cart when clicking outside
// Close only when clicking fully outside the cart wrapper
document.addEventListener('mousedown', function(e) {
    const cartWrapper = document.querySelector('.cart-icon-wrapper');
    if (!cartWrapper) return;
    const dropdown = document.getElementById('cartDropdown');
    // If click is outside the wrapper entirely, close
    if (!cartWrapper.contains(e.target)) {
        dropdown.classList.remove('active');
        document.documentElement.style.overflow = '';
        document.body.style.overflow = '';
    }
});

// Close on overlay click (only if the exact overlay element is clicked)
document.addEventListener('mousedown', function(e) {
    const dropdown = document.getElementById('cartDropdown');
    if (!dropdown) return;
    if (dropdown.classList.contains('active') && e.target === dropdown) {
        toggleCart();
    }
});

// Close on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const dropdown = document.getElementById('cartDropdown');
        if (dropdown && dropdown.classList.contains('active')) {
            toggleCart();
        }
    }
});

// Add item to cart
function addToCart(productId, productName, productPrice, productImage, quantity = 1) {
    const existingItem = cart.find(item => item.id === productId);
    
    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({
            id: productId,
            name: productName,
            price: productPrice,
            image: productImage,
            quantity: quantity
        });
    }
    
    saveCart();
    updateCartUI();
    showNotification(`Added ${productName} to cart`);
}

// Remove item from cart
function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    saveCart();
    updateCartUI();
}

// Clear entire cart
function clearCart() {
    if (confirm('Are you sure you want to clear the cart?')) {
        cart = [];
        saveCart();
        updateCartUI();
        showNotification('Cart cleared');
    }
}

// Update item quantity
function updateQuantity(productId, change) {
    const item = cart.find(item => item.id === productId);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            removeFromCart(productId);
        } else {
            saveCart();
            updateCartUI();
        }
    }
}

// Save cart to localStorage
function saveCart() {
    localStorage.setItem('petvet_cart', JSON.stringify(cart));
}

// Update cart UI
function updateCartUI() {
    const badge = document.getElementById('cartBadge');
    const cartItems = document.getElementById('cartItems');
    const cartFooter = document.getElementById('cartFooter');
    const cartTotal = document.getElementById('cartTotal');
    
    // Update badge
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    badge.textContent = totalItems;
    badge.style.display = totalItems > 0 ? 'flex' : 'none';
    
    // Update cart items
    if (cart.length === 0) {
        cartItems.innerHTML = `
            <div class="cart-empty">
                <div class="cart-empty-icon">🛒</div>
                <p>Your cart is empty</p>
                <p style="font-size: 0.85rem; color: #9ca3af; margin-top: 0.5rem;">Add items from the shop to get started</p>
            </div>
        `;
        cartFooter.style.display = 'none';
    } else {
        const head = `
            <div class="cart-table-head">
                <div>Product</div>
                <div>Unit Price</div>
                <div>Quantity</div>
                <div>Total</div>
            </div>`;
        const rows = cart.map(item => {
            const lineTotal = item.price * item.quantity;
            const imgSrc = CART_IMG_OVERRIDE || item.image;
            return `
            <div class="cart-item">
                <div class="col-product">
                    <img src="${imgSrc}" alt="${item.name}" class="cart-item-image" onerror="this.onerror=null;this.src='${CART_IMG_OVERRIDE}';">
                    <div class="cart-item-details">
                        <div class="cart-item-name">${item.name}</div>
                    </div>
                </div>
                <div class="col-unit" data-label="Unit">
                    Rs. ${formatPrice(item.price)}
                </div>
                <div class="col-qty" data-label="Qty">
                    <div class="cart-item-quantity" role="group" aria-label="Quantity">
                        <button class="qty-btn-small" onclick="updateQuantity(${item.id}, -1)" aria-label="Decrease quantity">−</button>
                        <span class="qty-value" aria-live="polite">${item.quantity}</span>
                        <button class="qty-btn-small" onclick="updateQuantity(${item.id}, 1)" aria-label="Increase quantity">+</button>
                    </div>
                </div>
                <div class="col-total" data-label="Total">
                    <span class="line-total">Rs. ${formatPrice(lineTotal)}</span>
                    <button class="cart-item-remove" onclick="removeFromCart(${item.id})" aria-label="Remove item">&times;</button>
                </div>
            </div>`;
        }).join('');
        cartItems.innerHTML = head + rows;
        
        cartFooter.style.display = 'block';
        
        // Update total
        const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        cartTotal.textContent = `Rs. ${formatPrice(total)}`;
    }
}

// Format price with commas
function formatPrice(price) {
    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// View cart page
function viewCart() {
    // Close dropdown and keep cart open for full view
    const dropdown = document.getElementById('cartDropdown');
    if (dropdown.classList.contains('active')) {
        // Already viewing cart
        return;
    }
}

// Checkout
function checkout() {
    if (cart.length === 0) {
        alert('Your cart is empty!');
        return;
    }
    alert('Checkout functionality - Coming soon!');
}

// Show notification
function showNotification(message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        background: #10b981;
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 11000; /* above cart modal */
        animation: slideIn 0.3s ease;
        font-weight: 500;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(400px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(400px); opacity: 0; }
    }
`;
document.head.appendChild(style);

// Initialize cart UI on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartUI();
    
    // Handle Add to Cart buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-to-cart') || e.target.closest('.add-to-cart')) {
            const button = e.target.classList.contains('add-to-cart') ? e.target : e.target.closest('.add-to-cart');
            
            // Check if this is a product detail page (has data attributes on button)
            if (button.hasAttribute('data-product-id')) {
                const productId = parseInt(button.getAttribute('data-product-id'));
                const productName = button.getAttribute('data-product-name');
                const productPrice = parseInt(button.getAttribute('data-product-price'));
                const productImage = button.getAttribute('data-product-image');
                
                // Get quantity from input if exists
                const quantityInput = document.getElementById('quantity');
                const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
                
                addToCart(productId, productName, productPrice, productImage, quantity);
                
                // Visual feedback
                button.textContent = 'Added!';
                button.style.background = '#10b981';
                setTimeout(() => {
                    button.textContent = 'Add to Cart';
                    button.style.background = '';
                }, 1500);
            } else {
                // Shop page - get from product card
                const productCard = button.closest('.product-card');
                
                if (productCard) {
                    const productId = parseInt(productCard.dataset.productId);
                    const productName = productCard.querySelector('h3').textContent;
                    const priceText = productCard.querySelector('.price').textContent;
                    const productPrice = parseInt(priceText.replace(/[^0-9]/g, ''));
                    const productImage = productCard.querySelector('img').src;
                    
                    addToCart(productId, productName, productPrice, productImage, 1);
                    
                    // Visual feedback
                    button.textContent = 'Added!';
                    button.style.background = '#10b981';
                    setTimeout(() => {
                        button.textContent = 'Add to Cart';
                        button.style.background = '';
                    }, 1500);
                }
            }
        }
    });
});

// Quantity controls for product detail page
function changeQty(delta) {
    const input = document.getElementById('quantity');
    if (input) {
        const newValue = parseInt(input.value) + delta;
        if (newValue >= 1) {
            input.value = newValue;
        }
    }
}
