// Pet Owner Shop Product Page JavaScript
// Based on guest/shop-product.js but without the login redirect

document.addEventListener('DOMContentLoaded', function() {
    initializeProductPage();
});

function initializeProductPage() {
    setupQuantityControls();
    // setupAddToCartButton(); // Removed: Handled by cart-manager.js
    setupProductImageGallery();
    // setupRelatedProductsInteraction(); // If this exists in guest script
    // setupProductDetailsAnimations(); // If this exists
}

// Enhanced quantity controls with validation
function setupQuantityControls() {
    const qtyInput = document.getElementById('quantity');
    const container = document.querySelector('.quantity-box');
    
    if (qtyInput && container) {
        // Remove inline onclick handlers and add proper event listeners
        const buttons = Array.from(container.querySelectorAll('.qty-btn'));
        buttons.forEach(btn => {
            btn.removeAttribute('onclick');
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
            if (value > 5) {
                this.value = 5;
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

// Make changeQty globally accessible
window.changeQty = function(delta) {
    const qtyInput = document.getElementById('quantity');
    if (!qtyInput) return;
    
    let current = parseInt(qtyInput.value, 10);
    if (isNaN(current)) current = 1;
    
    const newQty = Math.max(1, Math.min(5, current + delta));
    qtyInput.value = newQty;
    
    // Visual feedback
    qtyInput.style.transition = 'transform 0.2s ease';
    qtyInput.style.transform = 'scale(1.1)';
    setTimeout(() => {
        qtyInput.style.transform = 'scale(1)';
    }, 200);
}

function setupProductImageGallery() {
    const carousel = document.querySelector('.product-detail-carousel');
    if (!carousel) return;
    
    const mainImages = carousel.querySelectorAll('.main-carousel-img');
    const thumbnails = carousel.querySelectorAll('.thumbnail-img');
    const prevBtn = carousel.querySelector('.detail-carousel-prev');
    const nextBtn = carousel.querySelector('.detail-carousel-next');
    let currentIndex = 0;
    
    function showImage(index) {
        mainImages.forEach((img, i) => {
            img.classList.toggle('active', i === index);
        });
        thumbnails.forEach((thumb, i) => {
            thumb.classList.toggle('active', i === index);
        });
        currentIndex = index;
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            const newIndex = (currentIndex - 1 + mainImages.length) % mainImages.length;
            showImage(newIndex);
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            const newIndex = (currentIndex + 1) % mainImages.length;
            showImage(newIndex);
        });
    }
    
    thumbnails.forEach((thumb, index) => {
        thumb.addEventListener('click', () => {
            showImage(index);
        });
    });
}
