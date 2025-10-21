// Pet Owner Shop Page JavaScript - Routes to pet-owner module
// Based on guest shop.js with module routing fix

// DOM Content Loaded Event
document.addEventListener('DOMContentLoaded', function() {
    initializeShop();
});

function initializeShop() {
    // Reset any persistent states first
    resetPageStates();
    
    // Initialize all features
    setupProductCarousels();
    setupAddToCartButtons();
    setupCategoryFiltering();
    setupProductCardClickHandlers();
    setupScrollToTop();
    setupLoadingAnimations();
    setupImageLazyLoading();
    setupImageFallbacks();
    setupSearchAndFilters();
}

// Product Image Carousel
function setupProductCarousels() {
    document.querySelectorAll('.product-carousel').forEach(carousel => {
        const images = carousel.querySelectorAll('.carousel-img');
        const dots = carousel.querySelectorAll('.dot');
        const prevBtn = carousel.querySelector('.carousel-prev');
        const nextBtn = carousel.querySelector('.carousel-next');
        let currentIndex = 0;
        
        function showImage(index) {
            images.forEach((img, i) => {
                img.classList.toggle('active', i === index);
            });
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === index);
            });
            currentIndex = index;
        }
        
        prevBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            const newIndex = (currentIndex - 1 + images.length) % images.length;
            showImage(newIndex);
        });
        
        nextBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            const newIndex = (currentIndex + 1) % images.length;
            showImage(newIndex);
        });
        
        dots.forEach((dot, index) => {
            dot.addEventListener('click', (e) => {
                e.stopPropagation();
                showImage(index);
            });
        });
    });
}

// Reset any persistent states from previous visits
function resetPageStates() {
    // Reset all product cards to their initial state
    document.querySelectorAll('.product-card').forEach(card => {
        card.classList.remove('loading', 'hidden');
        card.style.display = '';
        card.style.opacity = '';
        card.style.transform = '';
        card.style.transition = '';
    });
    
    // Reset all category cards
    document.querySelectorAll('.category-card').forEach(card => {
        card.classList.remove('active', 'loading');
        card.style.opacity = '';
        card.style.transform = '';
        card.style.transition = '';
    });
    
    // Reset add-to-cart buttons
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.innerHTML = '🛒 Add to Cart';
        button.style.background = '';
        button.disabled = false;
    });
}

// Enhanced Add to Cart functionality
function setupAddToCartButtons() {
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent card click
            
            const productId = this.dataset.productId;
            const productCard = this.closest('.product-card');
            const productName = productCard.querySelector('h3').textContent;
            
            // Add visual feedback
            this.innerHTML = '✅ Added!';
            this.style.background = 'linear-gradient(135deg, #48bb78, #38a169)';
            this.disabled = true;
            
            // Create floating animation
            createFloatingEffect(this, '🛒');
            
            // Show notification
            showNotification(`${productName} added to cart!`, 'success');
            
            // Reset button after 2 seconds
            setTimeout(() => {
                this.innerHTML = '🛒 Add to Cart';
                this.style.background = '';
                this.disabled = false;
            }, 2000);
        });
    });
}

// Enhanced Category filtering with smooth animations
function setupCategoryFiltering() {
    document.querySelectorAll('.category-card').forEach(card => {
        card.addEventListener('click', function(e) {
            e.preventDefault();
            
            const selectedCategory = this.dataset.category;
            
            // Update active category visual state
            document.querySelectorAll('.category-card').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            
            // Apply all filters with new category
            applyAllFilters();
            
            // Scroll to products section
            document.querySelector('.products').scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        });
    });
}

function filterProducts(category) {
    const products = document.querySelectorAll('.product-card');
    
    products.forEach((product, index) => {
        const productCategory = product.dataset.category;
        const shouldShow = category === 'all' || productCategory === category;
        
        // Reset any existing animations/states
        product.style.transition = '';
        product.style.opacity = '';
        product.style.transform = '';
        
        if (shouldShow) {
            product.style.display = 'block';
            product.classList.remove('hidden');
            
            // Apply smooth show animation only if needed
            if (product.style.opacity === '0' || product.style.display === 'none') {
                product.style.opacity = '0';
                product.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    product.style.transition = 'all 0.4s ease';
                    product.style.opacity = '1';
                    product.style.transform = 'translateY(0)';
                }, index * 30);
            }
        } else {
            product.classList.add('hidden');
            product.style.transition = 'all 0.3s ease';
            product.style.opacity = '0';
            product.style.transform = 'translateY(-20px)';
            
            setTimeout(() => {
                product.style.display = 'none';
            }, 300);
        }
    });
}

// Enhanced product card click handlers - ROUTES TO PET-OWNER MODULE
function setupProductCardClickHandlers() {
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function(e) {
            // Don't trigger if clicking on buttons or links
            if (e.target.closest('button') || e.target.closest('a')) {
                return;
            }
            
            const productId = this.dataset.productId;
            if (productId) {
                // Navigate to pet-owner module (NOT guest)
                window.location.href = `/PETVET/index.php?module=pet-owner&page=shop-product&id=${productId}`;
            }
        });
        
        // Add hover effect enhancements (using CSS transforms, not persistent styles)
        card.addEventListener('mouseenter', function() {
            if (!this.style.transform.includes('translateY')) {
                this.style.transform = 'translateY(-15px) scale(1.02)';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            // Only reset hover effects, not other animations
            if (this.style.transform.includes('scale')) {
                this.style.transform = this.style.transform.replace(/translateY\([^)]*\)\s*scale\([^)]*\)/, '').trim();
            }
        });
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

// Loading animations for cards (with proper cleanup)
function setupLoadingAnimations() {
    // Disabled for instant product display
    // Products now appear immediately without animation delay
    const cards = document.querySelectorAll('.product-card, .category-card');
    cards.forEach(card => {
        card.style.opacity = '1';
        card.style.transform = 'translateY(0)';
        card.setAttribute('data-animated', 'true');
    });
}

// Lazy loading for images
function setupImageLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('loading');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => {
        img.classList.add('loading');
        imageObserver.observe(img);
    });
}

// Utility functions
function createFloatingEffect(element, emoji) {
    const floating = document.createElement('div');
    floating.innerHTML = emoji;
    floating.style.cssText = `
        position: absolute;
        font-size: 1.5rem;
        pointer-events: none;
        z-index: 1000;
        transition: all 1s ease;
    `;
    
    const rect = element.getBoundingClientRect();
    floating.style.left = rect.left + rect.width / 2 + 'px';
    floating.style.top = rect.top + 'px';
    
    document.body.appendChild(floating);
    
    // Animate
    setTimeout(() => {
        floating.style.transform = 'translateY(-50px)';
        floating.style.opacity = '0';
    }, 10);
    
    // Remove
    setTimeout(() => {
        floating.remove();
    }, 1000);
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
        z-index: 1000;
        transition: all 0.3s ease;
        transform: translateX(100%);
        opacity: 0;
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
    }, 3000);
}

// Setup search and filter functionality
function setupSearchAndFilters() {
    const searchInput = document.getElementById('productSearch');
    const minPriceInput = document.getElementById('minPrice');
    const maxPriceInput = document.getElementById('maxPrice');
    const sortBy = document.getElementById('sortBy');
    const clearFiltersBtn = document.getElementById('clearFilters');
    
    if (searchInput) {
        searchInput.addEventListener('input', debounce(applyAllFilters, 300));
    }
    
    if (minPriceInput) {
        minPriceInput.addEventListener('input', debounce(applyAllFilters, 500));
    }
    
    if (maxPriceInput) {
        maxPriceInput.addEventListener('input', debounce(applyAllFilters, 500));
    }
    
    if (sortBy) {
        sortBy.addEventListener('change', applyAllFilters);
    }
    
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', clearAllFilters);
    }
    
    // Initial filter application
    applyAllFilters();
}

// Clear all filters
function clearAllFilters() {
    // Clear search input
    const searchInput = document.getElementById('productSearch');
    if (searchInput) {
        searchInput.value = '';
    }
    
    // Clear price range inputs
    const minPriceInput = document.getElementById('minPrice');
    const maxPriceInput = document.getElementById('maxPrice');
    if (minPriceInput) {
        minPriceInput.value = '';
    }
    if (maxPriceInput) {
        maxPriceInput.value = '';
    }
    
    // Reset sort to default
    const sortBy = document.getElementById('sortBy');
    if (sortBy) {
        sortBy.value = 'default';
    }
    
    // Clear category filters
    const categoryButtons = document.querySelectorAll('.category-card');
    categoryButtons.forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Apply filters to show all products
    applyAllFilters();
}

// Apply all filters (search, price, category, sort)
function applyAllFilters() {
    const searchTerm = document.getElementById('productSearch')?.value.toLowerCase() || '';
    const minPrice = parseFloat(document.getElementById('minPrice')?.value) || 0;
    const maxPrice = parseFloat(document.getElementById('maxPrice')?.value) || Infinity;
    const sortBy = document.getElementById('sortBy')?.value || 'default';
    const activeCategory = document.querySelector('.category-card.active')?.dataset.category || 'all';
    
    const products = Array.from(document.querySelectorAll('.product-card'));
    let visibleProducts = [];
    
    products.forEach(product => {
        let shouldShow = true;
        
        // Category filter
        if (activeCategory !== 'all' && product.dataset.category !== activeCategory) {
            shouldShow = false;
        }
        
        // Search filter
        if (searchTerm && !product.dataset.name.toLowerCase().includes(searchTerm)) {
            shouldShow = false;
        }
        
        // Price range filter
        const price = parseFloat(product.dataset.price);
        if (isNaN(price) || price < minPrice || price > maxPrice) {
            shouldShow = false;
        }
        
        if (shouldShow) {
            visibleProducts.push(product);
            product.style.display = 'block';
            product.classList.remove('hidden');
        } else {
            product.style.display = 'none';
            product.classList.add('hidden');
        }
    });
    
    // Sort visible products
    if (sortBy !== 'default') {
        sortProducts(visibleProducts, sortBy);
    }
    
    // Update results info
    updateResultsInfo(visibleProducts.length, searchTerm, activeCategory, minPrice, maxPrice);
    
    // Show/hide no results message
    const noResults = document.getElementById('noResults');
    if (noResults) {
        noResults.style.display = visibleProducts.length === 0 ? 'block' : 'none';
    }
}

// Sort products
function sortProducts(products, sortBy) {
    const productGrid = document.getElementById('productGrid');
    if (!productGrid) return;
    
    products.sort((a, b) => {
        switch (sortBy) {
            case 'price-low':
                return parseInt(a.dataset.price) - parseInt(b.dataset.price);
            case 'price-high':
                return parseInt(b.dataset.price) - parseInt(a.dataset.price);
            case 'name':
                return a.dataset.name.localeCompare(b.dataset.name);
            default:
                return 0;
        }
    });
    
    // Reorder DOM elements
    products.forEach(product => {
        productGrid.appendChild(product);
    });
}

// Update results information
function updateResultsInfo(count, searchTerm, category, minPrice, maxPrice) {
    const resultsInfo = document.getElementById('resultsInfo');
    if (!resultsInfo) return;
    
    let message = `Showing ${count} product${count !== 1 ? 's' : ''}`;
    
    if (searchTerm) {
        message += ` for "${searchTerm}"`;
    }
    
    if (category !== 'all') {
        message += ` in ${category}`;
    }
    
    // Add price range info if filters are applied
    if (minPrice > 0 || maxPrice < Infinity) {
        let priceText = '';
        if (minPrice > 0 && maxPrice < Infinity) {
            priceText = `Rs. ${minPrice.toLocaleString()} - ${maxPrice.toLocaleString()}`;
        } else if (minPrice > 0) {
            priceText = `above Rs. ${minPrice.toLocaleString()}`;
        } else if (maxPrice < Infinity) {
            priceText = `below Rs. ${maxPrice.toLocaleString()}`;
        }
        message += ` (${priceText})`;
    }
    
    resultsInfo.textContent = message;
}

// Debounce function for search input
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Provide graceful fallback for missing category/product images
function setupImageFallbacks() {
    document.querySelectorAll('.category-card img, .product-card img').forEach(img => {
        img.addEventListener('error', function() {
            // Replace broken image with a gradient badge showing first letter (for products)
            const parent = this.parentElement;
            const wrapper = document.createElement('div');
            wrapper.style.cssText = `
                width: 100%;
                height: ${parent?.classList.contains('category-card') ? '150px' : '200px'};
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: ${parent?.classList.contains('category-card') ? '20px 20px 0 0' : '25px 25px 0 0'};
                background: linear-gradient(135deg, #667eea, #764ba2);
                color: white;
                font-weight: 700;
                font-size: 1.5rem;
            `;

            // Derive label
            let label = 'N/A';
            const titleEl = parent?.querySelector('p, h3');
            if (titleEl && titleEl.textContent) {
                label = titleEl.textContent.trim().charAt(0).toUpperCase();
            }

            wrapper.textContent = label;
            this.replaceWith(wrapper);
        }, { once: true });
    });
}
