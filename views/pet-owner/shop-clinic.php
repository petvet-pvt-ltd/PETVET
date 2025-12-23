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
                             data-created="<?php echo strtotime($product['created_at']); ?>">
                            
                            <a href="/PETVET/index.php?module=pet-owner&page=shop-product&id=<?php echo $product['id']; ?>" style="text-decoration: none; color: inherit; display: block;">
                                <div class="product-image-container">
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
                                <button class="add-to-cart" data-product-id="<?php echo $product['id']; ?>">
                                    Add to Cart
                                </button>
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
</body>
</html>
