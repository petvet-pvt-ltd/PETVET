<?php 
/* Guest Clinic Shop Page */ 
// Ensure variables are available from globals if not set locally
if (!isset($clinic) && isset($GLOBALS['clinic'])) $clinic = $GLOBALS['clinic'];
if (!isset($products) && isset($GLOBALS['products'])) $products = $GLOBALS['products'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title><?php echo isset($clinic) && $clinic ? htmlspecialchars($clinic['clinic_name']) : 'Shop'; ?> | PetVet Shop</title>
    <link rel="stylesheet" href="/PETVET/public/css/guest/navbar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/PETVET/public/css/guest/shop.css?v=<?php echo time(); ?>">
        <style>
            .icon-inline{display:inline-flex;align-items:center;justify-content:center;margin-right:6px;vertical-align:middle;}
            .icon-inline svg{width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}
        </style>
</head>
<body>
    <?php include __DIR__ . '/navbar.php'; ?>
    
    <div class="container">
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
                    <?php if (isset($clinic) && !empty($clinic['clinic_logo'])): ?>
                        <?php 
                            $logoUrl = $clinic['clinic_logo'];
                            // Only add /PETVET/ prefix if it's not already there and not an external URL
                            if (!preg_match('/^https?:\/\//i', $logoUrl) && strpos($logoUrl, '/PETVET/') !== 0) {
                                $logoUrl = '/PETVET/' . ltrim($logoUrl, '/');
                            }
                        ?>
                        <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="<?php echo htmlspecialchars($clinic['clinic_name']); ?>">
                    <?php elseif (isset($clinic)): ?>
                        <div class="clinic-logo-placeholder-large">
                            <span><?php echo strtoupper(substr($clinic['clinic_name'], 0, 2)); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="clinic-details">
                    <h1><?php echo isset($clinic) && $clinic ? htmlspecialchars($clinic['clinic_name']) : 'Shop'; ?></h1>
                    <p class="clinic-desc"><?php echo isset($clinic) && $clinic ? htmlspecialchars($clinic['clinic_description']) : ''; ?></p>
                    
                    <div class="clinic-info-grid">
                        <?php if (isset($clinic) && !empty($clinic['clinic_address'])): ?>
                            <div class="info-item">
                                <span class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                </span>
                                <span><?php echo htmlspecialchars($clinic['clinic_address']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($clinic) && !empty($clinic['clinic_phone'])): ?>
                            <div class="info-item">
                                <span class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                </span>
                                <span><?php echo htmlspecialchars($clinic['clinic_phone']); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($clinic) && !empty($clinic['clinic_email'])): ?>
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
            <h2><span class="icon-inline" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M20 12v10H4V12"/><path d="M2 7h20v5H2z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 1 1 0-5C11 2 12 7 12 7Z"/><path d="M12 7h4.5a2.5 2.5 0 1 0 0-5C13 2 12 7 12 7Z"/></svg></span>All Products</h2>
            
            <!-- Search and Filter Section -->
            <div class="search-filter-section">
                <div class="search-filter-container">
                    <div class="search-box">
                        <label for="productSearch"><span class="icon-inline" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg></span>Search:</label>
                        <input type="text" id="productSearch" placeholder="Search products..." onkeyup="filterProducts()" />
                    </div>
                    <div class="filter-controls">
                        <div class="sort-control">
                            <label for="sortBy"><span class="icon-inline" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 19V5"/><path d="M10 19V9"/><path d="M16 19v-6"/><path d="M22 19V11"/></svg></span>Sort by:</label>
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
                <div class="product-grid guest-view" id="productGrid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card" 
                             data-product-id="<?php echo $product['id']; ?>"
                             data-name="<?php echo htmlspecialchars(strtolower($product['name'])); ?>"
                             data-price="<?php echo $product['price']; ?>"
                             data-created="<?php echo strtotime($product['created_at']); ?>">
                            
                            <a href="/PETVET/index.php?module=guest&page=shop-product&id=<?php echo $product['id']; ?>" style="text-decoration: none; color: inherit; display: block;">
                                <div class="product-image-container">
                                    <?php if (!empty($product['images']) && count($product['images']) > 1): ?>
                                      <div class="product-carousel" data-current="0">
                                        <?php foreach ($product['images'] as $idx => $img): ?>
                                          <img class="carousel-img <?php echo $idx === 0 ? 'active' : ''; ?>" 
                                               src="<?php echo htmlspecialchars($img); ?>" 
                                               alt="<?php echo htmlspecialchars($product['name']); ?>">
                                        <?php endforeach; ?>
                                        <button class="carousel-prev" onclick="event.preventDefault(); event.stopPropagation();" aria-label="Previous image"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg></button>
                                        <button class="carousel-next" onclick="event.preventDefault(); event.stopPropagation();" aria-label="Next image"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg></button>
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
                                    <p class="seller"><span class="icon-inline" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 9l1-5h16l1 5"/><path d="M5 22V10"/><path d="M19 22V10"/><path d="M9 22V14h6v8"/></svg></span><?php echo isset($clinic) ? htmlspecialchars($clinic['clinic_name']) : 'PetVet Shop'; ?></p>
                                    <p class="price">Rs. <?php echo number_format($product['price']); ?></p>
                                    <div class="product-meta">
                                        <span class="stock-info"><span class="icon-inline" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73L13 2.27a2 2 0 0 0-2 0L4 6.27A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="M3.3 7l8.7 5 8.7-5"/><path d="M12 22V12"/></svg></span><?php echo $product['stock'] ?? 0; ?> in stock</span>
                                    </div>
                                </div>
                            </a>

                            <div class="product-actions">
                                <button class="add-to-cart" onclick="window.location.href='/PETVET/index.php?module=guest&page=login&redirect=shop'">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="no-results" id="noResults" style="display: none;">
                    <span class="no-results-icon" aria-hidden="true"><svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg></span>
                    <p>No products found matching your criteria.</p>
                    <button class="clear-filters-btn" onclick="clearFilters()">Clear Filters</button>
                </div>
            <?php endif; ?>
        </section>
    </div>
    
    <script src="/PETVET/public/js/shop.js?v=<?php echo time(); ?>"></script>
</body>
</html>
