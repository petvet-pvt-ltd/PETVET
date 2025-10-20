// Enhanced Shop Product Page JavaScript

// DOM Content Loaded Event
document.addEventListener('DOMContentLoaded', function() {
    initializeProductPage();
});

function initializeProductPage() {
    setupQuantityControls();
    setupAddToCartButton();
    setupProductImageGallery();
    setupRelatedProductsInteraction();
    setupProductDetailsAnimations();
    setupScrollToTop();
}

// Enhanced quantity controls with validation
function setupQuantityControls() {
    const qtyInput = document.getElementById('quantity');
    const container = document.querySelector('.quantity-box');
    
    if (qtyInput && container) {
        // Remove inline onclick handlers and add proper event listeners in a robust way
        const buttons = Array.from(container.querySelectorAll('.qty-btn'));
        buttons.forEach(btn => {
            btn.removeAttribute('onclick');
            // Ensure it's not a submit button in case inside a form
            if (!btn.getAttribute('type')) btn.setAttribute('type', 'button');
        });

        const minusBtn = buttons[0];
        const plusBtn = buttons[buttons.length - 1];

        if (minusBtn) minusBtn.addEventListener('click', () => window.changeQty(-1));
        if (plusBtn && plusBtn !== minusBtn) plusBtn.addEventListener('click', () => window.changeQty(1));
        
        // Input validation
        qtyInput.addEventListener('input', function() {
            let value = parseInt(this.value);
            if (isNaN(value) || value < 1) {
                this.value = 1;
            }
            if (value > 99) {
                this.value = 99;
            }
        });
        
        // Add smooth animation to quantity changes
        qtyInput.addEventListener('change', function() {
            this.style.transform = 'scale(1.1)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
    }
}

// Make changeQty globally accessible for inline onclick handlers
window.changeQty = function(delta) {
    const qtyInput = document.getElementById('quantity');
    if (!qtyInput) return;
    
    let current = parseInt(qtyInput.value, 10);
    if (isNaN(current)) current = 1;
    
    const newQty = Math.max(1, Math.min(99, current + delta));
    qtyInput.value = newQty;
    
    // Visual feedback
    qtyInput.style.transition = 'transform 0.2s ease';
    qtyInput.style.transform = 'scale(1.1)';
    setTimeout(() => {
        qtyInput.style.transform = 'scale(1)';
    }, 200);
    
    // Update total price if price element exists
    updateTotalPrice(newQty);
}

function updateTotalPrice(quantity) {
    const priceElement = document.querySelector('.price');
    const totalPriceElement = document.getElementById('totalPrice');
    
    if (priceElement && totalPriceElement) {
        const unitPrice = parseFloat(priceElement.textContent.replace(/[^\d.]/g, ''));
        const total = unitPrice * quantity;
        totalPriceElement.textContent = `Rs. ${total.toLocaleString()}`;
    }
}

// Enhanced Add to Cart functionality
function setupAddToCartButton() {
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            
            const quantity = document.getElementById('quantity')?.value || 1;
            const productName = document.querySelector('h1')?.textContent || 'Product';
            
            // Create ripple effect
            createRippleEffect(this, e);
            
            // Button animation
            const originalText = this.innerHTML;
            this.innerHTML = '✅ Added to Cart!';
            this.style.background = 'linear-gradient(135deg, #48bb78, #38a169)';
            this.disabled = true;
            
            // Create floating cart icon
            createFloatingCartIcon(this);
            
            // Show success notification
            showNotification(
                `${quantity}x ${productName} added to cart!`, 
                'success'
            );
            
            // Reset button after 3 seconds
            setTimeout(() => {
                this.innerHTML = originalText;
                this.style.background = '';
                this.disabled = false;
            }, 3000);
        });
    });
}

// Product image gallery functionality
function setupProductImageGallery() {
    const mainImage = document.querySelector('.product-image img');
    const thumbnails = document.querySelectorAll('.thumbnail-image');
    
    if (mainImage && thumbnails.length > 0) {
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function() {
                // Update main image
                mainImage.src = this.src;
                mainImage.alt = this.alt;
                
                // Update active thumbnail
                thumbnails.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                // Add zoom effect
                mainImage.style.transform = 'scale(1.05)';
                setTimeout(() => {
                    mainImage.style.transform = 'scale(1)';
                }, 200);
            });
        });
    }
    
    // Image zoom on hover
    if (mainImage) {
        mainImage.addEventListener('mouseenter', function() {
            this.style.cursor = 'zoom-in';
            this.style.transform = 'scale(1.1)';
        });
        
        mainImage.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
        
        // Click to open full size (if modal exists)
        mainImage.addEventListener('click', function() {
            openImageModal(this.src);
        });
    }
}

// Related products interaction
function setupRelatedProductsInteraction() {
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (e.target.closest('button') || e.target.closest('a')) {
                return;
            }
            
            const productId = this.dataset.productId;
            if (productId) {
                // Add loading state
                this.classList.add('loading');
                
                // Navigate with smooth transition
                setTimeout(() => {
                    window.location.href = `shop-product.php?id=${productId}`;
                }, 300);
            }
        });
        
        // Enhanced hover effects
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
            this.style.boxShadow = '0 20px 40px rgba(0,0,0,0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = '';
            this.style.boxShadow = '';
        });
    });
}

// Product details animations
function setupProductDetailsAnimations() {
    const productInfo = document.querySelector('.product-info');
    const productImage = document.querySelector('.product-image');
    
    // Animate elements on load
    if (productImage) {
        productImage.style.opacity = '0';
        productImage.style.transform = 'translateX(-50px)';
        
        setTimeout(() => {
            productImage.style.transition = 'all 0.8s ease';
            productImage.style.opacity = '1';
            productImage.style.transform = 'translateX(0)';
        }, 100);
    }
    
    if (productInfo) {
        productInfo.style.opacity = '0';
        productInfo.style.transform = 'translateX(50px)';
        
        setTimeout(() => {
            productInfo.style.transition = 'all 0.8s ease';
            productInfo.style.opacity = '1';
            productInfo.style.transform = 'translateX(0)';
        }, 200);
    }
    
    // Animate related products
    const relatedProducts = document.querySelectorAll('.product-card');
    relatedProducts.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            card.style.transition = `all 0.6s ease ${index * 0.1}s`;
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 500 + index * 100);
    });
}

// Scroll to top functionality
function setupScrollToTop() {
    const scrollBtn = document.getElementById('scrollToTop');
    
    if (scrollBtn) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                scrollBtn.classList.add('visible');
            } else {
                scrollBtn.classList.remove('visible');
            }
        });
        
        scrollBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

// Utility functions
function createRippleEffect(button, event) {
    const ripple = document.createElement('span');
    const rect = button.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;
    
    ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        left: ${x}px;
        top: ${y}px;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 50%;
        transform: scale(0);
        animation: ripple 0.6s linear;
        pointer-events: none;
    `;
    
    button.style.position = 'relative';
    button.style.overflow = 'hidden';
    button.appendChild(ripple);
    
    setTimeout(() => ripple.remove(), 600);
}

function createFloatingCartIcon(element) {
    const cartIcon = document.createElement('div');
    cartIcon.innerHTML = '🛒';
    cartIcon.style.cssText = `
        position: absolute;
        font-size: 2rem;
        pointer-events: none;
        z-index: 1000;
        transition: all 1.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    `;
    
    const rect = element.getBoundingClientRect();
    cartIcon.style.left = rect.left + rect.width / 2 + 'px';
    cartIcon.style.top = rect.top + 'px';
    
    document.body.appendChild(cartIcon);
    
    // Animate to top right corner
    setTimeout(() => {
        cartIcon.style.transform = 'translate(300px, -200px) scale(0.5)';
        cartIcon.style.opacity = '0';
    }, 100);
    
    setTimeout(() => cartIcon.remove(), 1500);
}

function openImageModal(imageSrc) {
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        opacity: 0;
        transition: opacity 0.3s ease;
    `;
    
    const img = document.createElement('img');
    img.src = imageSrc;
    img.style.cssText = `
        max-width: 90%;
        max-height: 90%;
        border-radius: 10px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        transform: scale(0.8);
        transition: transform 0.3s ease;
    `;
    
    modal.appendChild(img);
    document.body.appendChild(modal);
    
    // Animate in
    setTimeout(() => {
        modal.style.opacity = '1';
        img.style.transform = 'scale(1)';
    }, 10);
    
    // Close on click
    modal.addEventListener('click', function() {
        modal.style.opacity = '0';
        img.style.transform = 'scale(0.8)';
        setTimeout(() => modal.remove(), 300);
    });
    
    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            modal.click();
        }
    });
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.innerHTML = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? 'linear-gradient(135deg, #48bb78, #38a169)' : 'linear-gradient(135deg, #667eea, #764ba2)'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        z-index: 1001;
        transition: all 0.3s ease;
        transform: translateX(100%);
        opacity: 0;
        font-family: 'Poppins', sans-serif;
        font-weight: 500;
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
        notification.style.opacity = '1';
    }, 10);
    
    // Animate out and remove
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

// Add CSS for ripple animation
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);