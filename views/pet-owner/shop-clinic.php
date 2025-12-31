<?php /* Pet Owner Clinic Shop Page */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title><?php echo htmlspecialchars($clinic['clinic_name']); ?> | PetVet Shop</title>
    <link rel="stylesheet" href="/PETVET/public/css/guest/shop.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/PETVET/public/css/cart.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include __DIR__ . '/../shared/sidebar/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Shop Navigation Bar -->
        <div class="shop-nav-bar" style="background: white; padding: 1rem 2rem; border-radius: 12px; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); display: flex; justify-content: space-between; align-items: center;">
            <a href="/PETVET/index.php?module=pet-owner&page=shop" style="padding: 0.5rem 1rem; background: #f3f4f6; color: #374151; border: none; border-radius: 6px; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.2s ease;" onmouseover="this.style.background='#e5e7eb';" onmouseout="this.style.background='#f3f4f6';">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Back to Shops
            </a>
            <a href="/PETVET/index.php?module=pet-owner&page=orders" style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 16px rgba(102,126,234,0.3)';" onmouseout="this.style.transform=''; this.style.boxShadow='';">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
                My Orders
            </a>
        </div>

        <!-- Clinic Header -->
        <div class="clinic-header-card">
            <?php 
                $coverUrl = isset($clinic['clinic_cover']) && !empty($clinic['clinic_cover']) ? $clinic['clinic_cover'] : '';
                // Only add /PETVET/ prefix if it's not already there and not an external URL
                if ($coverUrl && !preg_match('/^https?:\/\//i', $coverUrl) && strpos($coverUrl, '/PETVET/') !== 0) {
                    $coverUrl = '/PETVET/' . ltrim($coverUrl, '/');
                }
            ?>
            <div class="clinic-cover" style="<?php echo $coverUrl ? "background-image: url('" . htmlspecialchars($coverUrl) . "');" : ''; ?>"></div>
            
            <div class="clinic-brand">
                <div class="clinic-logo-large">
                    <?php if (!empty($clinic['clinic_logo'])): ?>
                        <?php 
                            $logoUrl = $clinic['clinic_logo'];
                            // Only add /PETVET/ prefix if it's not already there and not an external URL
                            if (!preg_match('/^https?:\/\//i', $logoUrl) && strpos($logoUrl, '/PETVET/') !== 0) {
                                $logoUrl = '/PETVET/' . ltrim($logoUrl, '/');
                            }
                        ?>
                        <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="<?php echo htmlspecialchars($clinic['clinic_name']); ?>">
                    <?php else: ?>
                        <div class="clinic-logo-placeholder-large">
                            <span><?php echo strtoupper(substr($clinic['clinic_name'], 0, 2)); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="clinic-details">
                    <h1><?php echo htmlspecialchars($clinic['clinic_name']); ?></h1>
                    <p class="clinic-desc"><?php echo htmlspecialchars($clinic['clinic_description']); ?></p>
                    
                    <div class="clinic-info-grid">
                        <?php if (!empty($clinic['clinic_address'])): ?>
                            <div class="info-item">
                                <span class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                </span>
                                <span><?php echo htmlspecialchars($clinic['clinic_address']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($clinic['clinic_phone'])): ?>
                            <div class="info-item">
                                <span class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                </span>
                                <span><?php echo htmlspecialchars($clinic['clinic_phone']); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($clinic['clinic_email'])): ?>
                            <div class="info-item">
                                <span class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                </span>
                                <span><?php echo htmlspecialchars($clinic['clinic_email']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <section class="products">
            <h2>🎁 All Products</h2>
            
            <!-- Search and Filter Section -->
            <div class="search-filter-section">
                <div class="search-filter-container">
                    <div class="search-box">
                        <label for="productSearch">🔍 Search:</label>
                        <input type="text" id="productSearch" placeholder="Search products..." onkeyup="filterProducts()" />
                    </div>
                    <div class="filter-controls">
                        <button class="wishlist-toggle-btn" id="wishlistToggle" onclick="toggleWishlistFilter()">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                                <line x1="4" y1="22" x2="4" y2="15"></line>
                            </svg>
                            <span>Wishlist</span>
                        </button>
                        <div class="sort-control">
                            <label for="sortBy">📊 Sort by:</label>
                            <select id="sortBy" onchange="filterProducts()">
                                <option value="default">Default</option>
                                <option value="price-low">Price: Low to High</option>
                                <option value="price-high">Price: High to Low</option>
                                <option value="name">Name: A to Z</option>
                                <option value="newest">Newest First</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="results-info" id="resultsInfo">Showing all products</div>

            <?php if (empty($products)): ?>
                <div class="no-products">
                    <p>No products available in this shop yet.</p>
                </div>
            <?php else: ?>
                <div class="product-grid" id="productGrid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card" 
                             data-product-id="<?php echo $product['id']; ?>"
                             data-name="<?php echo htmlspecialchars(strtolower($product['name'])); ?>"
                             data-price="<?php echo $product['price']; ?>"
                             data-created="<?php echo strtotime($product['created_at']); ?>"
                             data-stock="<?php echo $product['stock'] ?? 0; ?>">
                            
                            <a href="/PETVET/index.php?module=pet-owner&page=shop-product&id=<?php echo $product['id']; ?>" style="text-decoration: none; color: inherit; display: block;">
                                <div class="product-image-container">
                                    <?php if ($product['stock'] <= 0): ?>
                                        <div class="out-of-stock-banner">Out of Stock</div>
                                    <?php endif; ?>
                                    
                                    <div class="product-wishlist-indicator" 
                                         data-product-id="<?php echo $product['id']; ?>"
                                         style="display: none;">
                                        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                                            <line x1="4" y1="22" x2="4" y2="15"></line>
                                        </svg>
                                    </div>
                                    
                                    <?php if (!empty($product['images']) && count($product['images']) > 1): ?>
                                      <div class="product-carousel" data-current="0">
                                        <?php foreach ($product['images'] as $idx => $img): ?>
                                          <img class="carousel-img <?php echo $idx === 0 ? 'active' : ''; ?>" 
                                               src="<?php echo htmlspecialchars($img); ?>" 
                                               alt="<?php echo htmlspecialchars($product['name']); ?>">
                                        <?php endforeach; ?>
                                        <button class="carousel-prev" onclick="event.preventDefault(); event.stopPropagation();">❮</button>
                                        <button class="carousel-next" onclick="event.preventDefault(); event.stopPropagation();">❯</button>
                                        <div class="carousel-dots" onclick="event.preventDefault(); event.stopPropagation();">
                                          <?php foreach ($product['images'] as $idx => $img): ?>
                                            <span class="dot <?php echo $idx === 0 ? 'active' : ''; ?>" data-index="<?php echo $idx; ?>"></span>
                                          <?php endforeach; ?>
                                        </div>
                                      </div>
                                    <?php else: ?>
                                      <img src="<?php echo htmlspecialchars($product['images'][0] ?? 'public/images/product-placeholder.png'); ?>" 
                                           alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <p class="seller">🏪 <?php echo htmlspecialchars($clinic['clinic_name']); ?></p>
                                    <p class="price">Rs. <?php echo number_format($product['price']); ?></p>
                                    <div class="product-meta">
                                        <span class="stock-info">📦 <?php echo $product['stock'] ?? 0; ?> in stock</span>
                                    </div>
                                </div>
                            </a>

                            <div class="product-actions">
                                <?php if ($product['stock'] <= 0): ?>
                                    <button class="add-to-wishlist" 
                                            data-product-id="<?php echo $product['id']; ?>"
                                            data-clinic-id="<?php echo $clinic['id']; ?>"
                                            onclick="addToWishlist(<?php echo $product['id']; ?>, <?php echo $clinic['id']; ?>, this)"
                                            style="display: none;">
                                        Add to Wishlist
                                    </button>
                                    <button class="remove-from-wishlist" 
                                            data-product-id="<?php echo $product['id']; ?>"
                                            data-clinic-id="<?php echo $clinic['id']; ?>"
                                            onclick="removeFromWishlist(<?php echo $product['id']; ?>, this)"
                                            style="display: none;">
                                        Remove from Wishlist
                                    </button>
                                <?php else: ?>
                                    <button class="add-to-cart" data-product-id="<?php echo $product['id']; ?>">
                                        Add to Cart
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="no-results" id="noResults" style="display: none;">
                    <span class="no-results-icon">🔍</span>
                    <p>No products found matching your criteria.</p>
                    <button class="clear-filters-btn" onclick="clearFilters()">Clear Filters</button>
                </div>
            <?php endif; ?>
        </section>
    </div>
    
    <script src="/PETVET/public/js/shop.js?v=<?php echo time(); ?>"></script>
    <!-- <script src="/PETVET/public/js/cart.js?v=<?php echo time(); ?>"></script> -->
    <link rel="stylesheet" href="/PETVET/public/css/pet-owner/cart-manager.css?v=<?php echo time(); ?>">
    <script src="/PETVET/public/js/pet-owner/cart-manager.js?v=<?php echo time(); ?>"></script>
    
    <!-- Wishlist Functionality -->
    <style>
        /* Wishlist Toggle Button */
        .wishlist-toggle-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            color: #64748b;
            height: fit-content;
            align-self: flex-end;
        }
        
        .wishlist-toggle-btn svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: currentColor;
            transition: all 0.2s ease;
        }
        
        .wishlist-toggle-btn:hover {
            border-color: #fbbf24;
            color: #f59e0b;
            background: #fffbeb;
        }
        
        .wishlist-toggle-btn.active {
            background: #fbbf24;
            border-color: #f59e0b;
            color: white;
        }
        
        .wishlist-toggle-btn.active svg {
            fill: white;
            stroke: white;
        }
        
        /* Product Wishlist Indicator (Top Left of Product Image) - Non-clickable */
        .product-wishlist-indicator {
            position: absolute;
            top: 12px;
            left: 12px;
            width: 32px;
            height: 32px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            z-index: 10;
            pointer-events: none;
        }
        
        .product-wishlist-indicator svg {
            width: 18px;
            height: 18px;
            fill: #fbbf24;
            stroke: #f59e0b;
        }
        
        /* Out of Stock Banner */
        .out-of-stock-banner {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(239, 68, 68, 0.95);
            color: white;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 10;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        
        /* Add to Wishlist Button */
        .add-to-wishlist {
            background: #ff00a3;
            color: white;
            border: none;
            padding: 0.65rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }
        
        .add-to-wishlist:hover {
            background: #d6008a;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 0, 163, 0.3);
        }
        
        .add-to-wishlist:active {
            transform: translateY(0);
        }
        
        /* Remove from Wishlist Button */
        .remove-from-wishlist {
            background: #dc2626;
            color: white;
            border: none;
            padding: 0.65rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s ease;
            font-size: 0.9rem;
            white-space: nowrap;
        }
        
        @media (max-width: 480px) {
            .remove-from-wishlist {
                font-size: 0.7rem;
                padding: 0.6rem 0.8rem;
            }
        }
        
        .remove-from-wishlist:hover {
            background: #b91c1c;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }
        
        .remove-from-wishlist:active {
            transform: translateY(0);
        }
        
        /* Filter controls container */
        .filter-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            .wishlist-toggle-btn {
                padding: 8px 12px;
                font-size: 0.85rem;
                gap: 6px;
            }
            
            .wishlist-toggle-btn svg {
                width: 16px;
                height: 16px;
            }
            
            .sort-control {
                flex: 1;
            }
            
            .sort-control label {
                font-size: 0.8rem;
            }
            
            .sort-control select {
                font-size: 0.85rem;
                padding: 0.5rem;
            }
        }
        
        @media (max-width: 480px) {
            .wishlist-toggle-btn {
                padding: 8px 10px;
                font-size: 0.8rem;
            }
            
            .wishlist-toggle-btn span {
                display: none;
            }
            
            .wishlist-toggle-btn svg {
                width: 20px;
                height: 20px;
            }
        }
    </style>
    
    <script>
        const CLINIC_ID = <?php echo $clinic['id']; ?>;
        let wishlistedProducts = new Set();
        let showWishlistOnly = false;
        
        // Load wishlisted products on page load
        document.addEventListener('DOMContentLoaded', async function() {
            await loadWishlistedProducts();
        });
        
        // Load wishlisted product IDs
        async function loadWishlistedProducts() {
            try {
                const response = await fetch(`/PETVET/api/pet-owner/shop-wishlist.php?action=get_ids&clinic_id=${CLINIC_ID}`);
                const data = await response.json();
                
                if (data.success) {
                    wishlistedProducts = new Set(data.product_ids.map(id => parseInt(id)));
                    updateWishlistUI();
                }
            } catch (error) {
                console.error('Error loading wishlist:', error);
            }
        }
        
        // Update UI to show wishlisted items
        function updateWishlistUI() {
            document.querySelectorAll('.product-card').forEach(card => {
                const productId = parseInt(card.dataset.productId);
                const stock = parseInt(card.dataset.stock);
                const isWishlisted = wishlistedProducts.has(productId);
                
                // Show/hide wishlist indicator (only for wishlisted items)
                const indicator = card.querySelector('.product-wishlist-indicator');
                if (indicator) {
                    indicator.style.display = isWishlisted ? 'flex' : 'none';
                }
                
                // Show/hide add/remove buttons for out-of-stock items
                if (stock <= 0) {
                    const addBtn = card.querySelector('.add-to-wishlist');
                    const removeBtn = card.querySelector('.remove-from-wishlist');
                    
                    if (addBtn && removeBtn) {
                        if (isWishlisted) {
                            addBtn.style.display = 'none';
                            removeBtn.style.display = 'block';
                        } else {
                            addBtn.style.display = 'block';
                            removeBtn.style.display = 'none';
                        }
                    }
                }
            });
        }
        
        // Remove from wishlist
        async function removeFromWishlist(productId, button) {
            try {
                const formData = new FormData();
                formData.append('action', 'remove');
                formData.append('product_id', productId);
                
                const response = await fetch('/PETVET/api/pet-owner/shop-wishlist.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    wishlistedProducts.delete(productId);
                    updateWishlistUI();
                    
                    // If wishlist filter is active, re-filter
                    if (showWishlistOnly) {
                        filterProducts();
                    }
                }
            } catch (error) {
                console.error('Error removing from wishlist:', error);
            }
        }
        
        // Add to wishlist (for out-of-stock items)
        async function addToWishlist(productId, clinicId, button) {
            try {
                const formData = new FormData();
                formData.append('action', 'add');
                formData.append('product_id', productId);
                formData.append('clinic_id', clinicId);
                
                const response = await fetch('/PETVET/api/pet-owner/shop-wishlist.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    wishlistedProducts.add(productId);
                    updateWishlistUI();
                }
            } catch (error) {
                console.error('Error adding to wishlist:', error);
            }
        }
        
        // Toggle wishlist filter
        function toggleWishlistFilter() {
            showWishlistOnly = !showWishlistOnly;
            const toggleBtn = document.getElementById('wishlistToggle');
            
            if (showWishlistOnly) {
                toggleBtn.classList.add('active');
            } else {
                toggleBtn.classList.remove('active');
            }
            
            filterProducts();
        }
        
        // Override or extend the existing filterProducts function
        const originalFilterProducts = window.filterProducts;
        window.filterProducts = function() {
            if (typeof originalFilterProducts === 'function') {
                originalFilterProducts();
            }
            
            // Apply wishlist filter
            if (showWishlistOnly) {
                const cards = document.querySelectorAll('.product-card');
                cards.forEach(card => {
                    const productId = parseInt(card.dataset.productId);
                    if (!wishlistedProducts.has(productId)) {
                        card.style.display = 'none';
                    }
                });
            }
        };
        
        // Store clinic name in sessionStorage for checkout
        (function() {
            const clinicName = '<?php echo htmlspecialchars($clinic['clinic_name'] ?? 'PetVet Shop', ENT_QUOTES); ?>';
            if (clinicName) {
                sessionStorage.setItem('petvet_clinic_name', clinicName);
            }
        })();
    </script>
</body>
</html>
