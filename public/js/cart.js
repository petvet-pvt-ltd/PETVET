// Shopping Cart Management - No Frameworks/Libraries
console.log('Cart.js loaded successfully');

let cart = JSON.parse(localStorage.getItem('petvet_cart')) || [];
console.log('Initial cart:', cart);

// No image override - use actual product images
const CART_IMG_OVERRIDE = null;

// Don't initialize with demo items - start with empty cart
/*
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
*/

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
    console.log('addToCart called with:', {productId, productName, productPrice, productImage, quantity});
    console.log('Current cart before:', JSON.parse(JSON.stringify(cart)));
    
    const existingItem = cart.find(item => item.id === productId);
    
    if (existingItem) {
        existingItem.quantity += quantity;
        console.log('Updated existing item:', existingItem);
    } else {
        const newItem = {
            id: productId,
            name: productName,
            price: productPrice,
            image: productImage,
            quantity: quantity
        };
        cart.push(newItem);
        console.log('Added new item:', newItem);
    }
    
    console.log('Current cart after:', JSON.parse(JSON.stringify(cart)));
    
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
            const imgSrc = item.image || 'https://via.placeholder.com/150?text=No+Image';
            return `
            <div class="cart-item">
                <div class="col-product">
                    <img src="${imgSrc}" alt="${item.name}" class="cart-item-image" onerror="this.onerror=null;this.src='https://via.placeholder.com/150?text=No+Image';">
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

// Checkout - Redirect to Stripe Payment
async function checkout() {
    if (cart.length === 0) {
        alert('Your cart is empty!');
        return;
    }
    
    // Show delivery information modal first
    showDeliveryModal();
}

// Process checkout with delivery info
async function processCheckout(deliveryCity = null) {
    // Show loading state
    const checkoutBtn = document.querySelector('.btn-checkout');
    const originalText = checkoutBtn ? checkoutBtn.textContent : 'Proceed to Checkout';
    if (checkoutBtn) {
        checkoutBtn.textContent = 'Processing...';
        checkoutBtn.disabled = true;
    }
    
    try {
        // Calculate subtotal
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        
        // Prepare cart data for Stripe
        const cartData = {
            cart: cart.map(item => ({
                name: item.name,
                price: item.price,
                quantity: item.quantity,
                image: item.image || 'https://via.placeholder.com/150'
            })),
            deliveryCity: deliveryCity,
            subtotal: subtotal
        };
        
        // Call API to create Stripe checkout session
        const response = await fetch('/PETVET/api/payments/create-checkout-session.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(cartData)
        });
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Server returned invalid response. Please check if Stripe is configured correctly.');
        }
        
        const data = await response.json();
        
        if (data.success && data.url) {
            // Redirect to Stripe Checkout page
            window.location.href = data.url;
        } else {
            throw new Error(data.error || 'Failed to create checkout session');
        }
        
    } catch (error) {
        console.error('Checkout error:', error);
        
        let errorMessage = 'Unable to proceed to checkout.\n\n';
        errorMessage += 'Error: ' + error.message;
        
        alert(errorMessage);
        
        // Restore button state
        checkoutBtn.textContent = originalText;
        checkoutBtn.disabled = false;
    }
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
    console.log('DOMContentLoaded - Cart initialization starting');
    updateCartUI();
    
    console.log('Setting up Add to Cart click listener');
    // Handle Add to Cart buttons - use capture phase to run before other handlers
    document.addEventListener('click', function(e) {
        console.log('Document clicked:', e.target);
        
        if (e.target.classList.contains('add-to-cart') || e.target.closest('.add-to-cart')) {
            console.log('Add to cart button detected!');
            e.preventDefault(); // Prevent any default action
            e.stopPropagation(); // Stop event bubbling
            e.stopImmediatePropagation(); // Stop other listeners on the same element
            
            const button = e.target.classList.contains('add-to-cart') ? e.target : e.target.closest('.add-to-cart');
            
            // Get product card (works for both shop page and product detail page)
            const productCard = button.closest('.product-card');
            
            if (productCard) {
                // Shop page - get from product card
                const productId = parseInt(productCard.dataset.productId);
                const productName = productCard.querySelector('h3').textContent.trim();
                const priceText = productCard.querySelector('.price').textContent;
                const productPrice = parseInt(priceText.replace(/[^0-9]/g, ''));
                
                // Get the first visible image
                let productImage = '';
                const carouselImg = productCard.querySelector('.carousel-img.active');
                if (carouselImg) {
                    productImage = carouselImg.src;
                } else {
                    const regularImg = productCard.querySelector('.product-image-container img');
                    if (regularImg) {
                        productImage = regularImg.src;
                    }
                }
                
                console.log('Adding to cart:', {productId, productName, productPrice, productImage}); // Debug
                
                addToCart(productId, productName, productPrice, productImage, 1);
                
                // Visual feedback
                const originalText = button.textContent;
                button.textContent = 'Added! ✓';
                button.style.background = '#10b981';
                setTimeout(() => {
                    button.textContent = originalText;
                    button.style.background = '';
                }, 1500);
            } else if (button.hasAttribute('data-product-id')) {
                // Product detail page with full data attributes on button
                const productId = parseInt(button.getAttribute('data-product-id'));
                const productName = button.getAttribute('data-product-name');
                const productPrice = parseInt(button.getAttribute('data-product-price'));
                const productImage = button.getAttribute('data-product-image');
                
                // Get quantity from input if exists
                const quantityInput = document.getElementById('quantity');
                const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
                
                console.log('Adding to cart (detail page):', {productId, productName, productPrice, productImage, quantity}); // Debug
                
                addToCart(productId, productName, productPrice, productImage, quantity);
                
                // Visual feedback
                const originalText = button.textContent;
                button.textContent = 'Added! ✓';
                button.style.background = '#10b981';
                setTimeout(() => {
                    button.textContent = originalText;
                    button.style.background = '';
                }, 1500);
            }
        }
    }, true); // Use capture phase to intercept clicks before they reach the product card handler
    
    console.log('Add to Cart listener set up successfully');
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

// Delivery rates configuration
const deliveryRates = {
    'Colombo': 200,
    'Dehiwala-Mount Lavinia': 200,
    'Moratuwa': 250,
    'Sri Jayawardenepura Kotte': 200,
    'Gampaha': 300,
    'Negombo': 350,
    'Kalutara': 400,
    'Panadura': 350,
    'Kandy': 500,
    'Matale': 550,
    'Nuwara Eliya': 600,
    'Galle': 500,
    'Matara': 550,
    'Hambantota': 600,
    'Jaffna': 700,
    'Kilinochchi': 750,
    'Mannar': 750,
    'Vavuniya': 700,
    'Mullaitivu': 750,
    'Trincomalee': 650,
    'Batticaloa': 650,
    'Ampara': 600,
    'Kurunegala': 450,
    'Puttalam': 500,
    'Anuradhapura': 600,
    'Polonnaruwa': 650,
    'Badulla': 600,
    'Monaragala': 650,
    'Ratnapura': 500,
    'Kegalle': 450
};

const freeDeliveryThreshold = 5000;

// Show delivery information modal
function showDeliveryModal() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const isFreeDelivery = subtotal >= freeDeliveryThreshold;
    
    // Create modal HTML
    const modalHTML = `
        <div class="delivery-modal-overlay" id="deliveryModalOverlay">
            <div class="delivery-modal">
                <div class="delivery-modal-header">
                    <h2>🚚 Delivery Information</h2>
                    <button class="delivery-modal-close" onclick="closeDeliveryModal()">&times;</button>
                </div>
                
                <div class="delivery-modal-body">
                    <div class="order-summary-box">
                        <h3>Order Summary</h3>
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span class="summary-value">Rs. ${subtotal.toLocaleString()}</span>
                        </div>
                        ${isFreeDelivery ? `
                            <div class="free-delivery-banner">
                                🎉 You qualify for FREE delivery!
                            </div>
                        ` : ''}
                    </div>
                    
                    <div class="delivery-form">
                        <label for="deliveryCity">Select Your City/District:</label>
                        <select id="deliveryCity" onchange="updateDeliveryCharge()">
                            <option value="">-- Select City --</option>
                            ${Object.keys(deliveryRates).sort().map(city => 
                                `<option value="${city}">${city}</option>`
                            ).join('')}
                        </select>
                        
                        <div class="delivery-charge-info" id="deliveryChargeInfo" style="display: none;">
                            <div class="charge-row">
                                <span>Delivery Charge:</span>
                                <span class="charge-value" id="deliveryChargeValue">Rs. 0</span>
                            </div>
                            <div class="total-row">
                                <span>Total Amount:</span>
                                <span class="total-value" id="totalAmountValue">Rs. ${subtotal.toLocaleString()}</span>
                            </div>
                        </div>
                        
                        <div class="delivery-note">
                            <strong>Note:</strong> You'll be asked to enter your complete delivery address on the next page (Stripe payment page).
                        </div>
                    </div>
                </div>
                
                <div class="delivery-modal-footer">
                    <button class="btn-secondary" onclick="closeDeliveryModal()">Cancel</button>
                    <button class="btn-primary" onclick="confirmDeliveryAndCheckout()" id="confirmDeliveryBtn">
                        Continue to Payment
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('deliveryModalOverlay');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

// Update delivery charge when city is selected
function updateDeliveryCharge() {
    const citySelect = document.getElementById('deliveryCity');
    const selectedCity = citySelect.value;
    const deliveryChargeInfo = document.getElementById('deliveryChargeInfo');
    const deliveryChargeValue = document.getElementById('deliveryChargeValue');
    const totalAmountValue = document.getElementById('totalAmountValue');
    const confirmBtn = document.getElementById('confirmDeliveryBtn');
    
    if (!selectedCity) {
        deliveryChargeInfo.style.display = 'none';
        confirmBtn.disabled = true;
        return;
    }
    
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    let deliveryCharge = deliveryRates[selectedCity] || 400;
    
    // Check for free delivery
    if (subtotal >= freeDeliveryThreshold) {
        deliveryCharge = 0;
    }
    
    const total = subtotal + deliveryCharge;
    
    deliveryChargeInfo.style.display = 'block';
    
    if (deliveryCharge === 0) {
        deliveryChargeValue.innerHTML = '<span style="color: #10b981; font-weight: 600;">FREE</span>';
    } else {
        deliveryChargeValue.textContent = 'Rs. ' + deliveryCharge.toLocaleString();
    }
    
    totalAmountValue.textContent = 'Rs. ' + total.toLocaleString();
    confirmBtn.disabled = false;
}

// Close delivery modal
function closeDeliveryModal() {
    const modal = document.getElementById('deliveryModalOverlay');
    if (modal) {
        modal.remove();
    }
    document.body.style.overflow = '';
    
    // Restore checkout button
    const checkoutBtn = document.querySelector('.btn-checkout');
    if (checkoutBtn) {
        checkoutBtn.textContent = 'Proceed to Checkout';
        checkoutBtn.disabled = false;
    }
}

// Confirm delivery and proceed to checkout
function confirmDeliveryAndCheckout() {
    const citySelect = document.getElementById('deliveryCity');
    const selectedCity = citySelect.value;
    
    if (!selectedCity) {
        alert('Please select your city/district');
        return;
    }
    
    closeDeliveryModal();
    processCheckout(selectedCity);
}
