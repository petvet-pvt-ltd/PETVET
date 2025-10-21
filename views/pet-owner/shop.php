<?php /* Pet Owner Shop Page - Uses exact same shop module as guest */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Shop | PetVet</title>
    <link rel="stylesheet" href="/PETVET/public/css/guest/shop.css">
    <link rel="stylesheet" href="/PETVET/public/css/cart.css">
</head>
<body>
    <?php include __DIR__ . '/../shared/sidebar/sidebar.php'; ?>
    
    <div class="main-content">

<!-- Cart Icon -->
<div class="cart-icon-wrapper">
  <button class="cart-icon" onclick="toggleCart()" aria-label="Shopping Cart">
    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
      <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
    </svg>
    <span class="cart-badge" id="cartBadge">0</span>
  </button>
  
  <div class="cart-dropdown" id="cartDropdown">
    <div class="cart-header">
      <h3>Shopping Cart</h3>
      <button class="cart-close" onclick="toggleCart()" aria-label="Close Cart">&times;</button>
    </div>
    
    <div class="cart-items" id="cartItems">
      <div class="cart-empty">
        <div class="cart-empty-icon">🛒</div>
        <p>Your cart is empty</p>
        <p style="font-size: 0.85rem; color: #9ca3af; margin-top: 0.5rem;">Add items from the shop to get started</p>
      </div>
    </div>
    
    <div class="cart-footer" id="cartFooter" style="display: none;">
      <div class="cart-total">
        <span class="cart-total-label">Total:</span>
        <span class="cart-total-value" id="cartTotal">Rs. 0</span>
      </div>
      <div class="cart-actions">
        <button class="btn-cart btn-checkout" onclick="checkout()">Proceed to Checkout</button>
        <button class="btn-cart" onclick="clearCart()">Clear Cart</button>
      </div>
    </div>
  </div>
</div>

<!-- Banner Image -->
<div class="shop-banner">
  <img src="/PETVET/views/shared/images/shop-banner.png" alt="Pet Shop Banner - Best Pet Products">
</div>

<!-- Info Cards Section -->
<section class="info-strip">
  <div class="info-box">
    <p>🏆 Best Rated Petshop<br>in Sri Lanka</p>
  </div>
  <div class="vertical-line"></div>

  <div class="info-box">
    <p>🔒 Easy and Secured<br>Payments</p>
  </div>
  <div class="vertical-line"></div>

  <div class="info-box">
    <p>⚡ Quick Customer<br>Support</p>
  </div>
</section>

<!-- Categories Section -->
<section class="categories">
  <h2>🛍️ Shop by Categories</h2>
  <div class="category-list">
    <a class="category-card" data-category="all">
      <img src="https://img.freepik.com/premium-photo/cheerful-overhead-shot-assorted-pet-supply-products-pastel-pink-background_1223049-5710.jpg" alt="ALL">
      <p>All Products</p>
    </a>
    <?php foreach($categories as $categoryKey => $categoryName): ?>
    <a class="category-card" data-category="<?php echo htmlspecialchars($categoryKey); ?>">
      <img src="/PETVET/views/shared/images/category-<?php echo htmlspecialchars($categoryKey); ?>.png" alt="<?php echo strtoupper($categoryKey); ?>">
      <p><?php echo htmlspecialchars($categoryName); ?></p>
    </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- Search and Filter Section -->
<section class="search-filter-section">
  <div class="search-filter-container">
    <div class="search-box">
      <input type="text" id="productSearch" placeholder="🔍 Search products..." />
    </div>
    <div class="filter-controls">
      <div class="price-filter">
        <label for="minPrice">💰 Price Range:</label>
        <div class="price-range-inputs">
          <input type="number" id="minPrice" placeholder="Min" min="0" />
          <span>to</span>
          <input type="number" id="maxPrice" placeholder="Max" min="0" />
        </div>
      </div>
      <div class="sort-control">
        <label for="sortBy">📊 Sort by:</label>
        <select id="sortBy">
          <option value="default">Default</option>
          <option value="price-low">Price: Low to High</option>
          <option value="price-high">Price: High to Low</option>
          <option value="name">Name: A to Z</option>
        </select>
      </div>
    </div>
  </div>
</section>

<!-- Products Section -->
<section class="products">
  <h2>🎁 Featured Products</h2>
  <div class="results-info" id="resultsInfo">Showing all products</div>
  <div class="product-grid" id="productGrid">
    <?php foreach($products as $product): ?>
    <div class="product-card" data-category="<?php echo htmlspecialchars($product['category']); ?>" data-product-id="<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>" data-name="<?php echo htmlspecialchars(strtolower($product['name'])); ?>" data-stock="<?php echo $product['stock']; ?>">
      <div class="product-image-container">
        <?php if (!empty($product['images']) && count($product['images']) > 1): ?>
          <div class="product-carousel" data-current="0">
            <?php foreach ($product['images'] as $idx => $img): ?>
              <img class="carousel-img <?php echo $idx === 0 ? 'active' : ''; ?>" src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <?php endforeach; ?>
            <button class="carousel-prev">❮</button>
            <button class="carousel-next">❯</button>
            <div class="carousel-dots">
              <?php foreach ($product['images'] as $idx => $img): ?>
                <span class="dot <?php echo $idx === 0 ? 'active' : ''; ?>" data-index="<?php echo $idx; ?>"></span>
              <?php endforeach; ?>
            </div>
          </div>
        <?php else: ?>
          <img src="<?php echo htmlspecialchars($product['images'][0] ?? $product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        <?php endif; ?>
      </div>
      <div class="product-info">
        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
        <p class="seller">🏪 <?php echo htmlspecialchars($product['seller']); ?></p>
        <p class="price">Rs. <?php echo number_format($product['price']); ?></p>
        <div class="product-meta">
          <span class="stock-info">📦 <?php echo $product['stock']; ?> in stock</span>
          <span class="sold-info"><?php echo $product['sold']; ?> sold</span>
        </div>
        <div class="product-actions">
          <button class="add-to-cart" data-product-id="<?php echo $product['id']; ?>">Add to Cart</button>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <div class="no-results" id="noResults" style="display: none;">
    <span class="no-results-icon">🔍</span>
    <p>No products found matching your criteria.</p>
    <button class="clear-filters-btn" id="clearFilters">Clear All Filters</button>
  </div>   
</section>

<!-- Footer -->
<footer class="site-footer">
  <div class="footer-content">
    <div class="about">
      <h3>🐾 ABOUT US</h3>
      <p><em>petvet.lk is your One-stop shop for all things Pet related, selling a range of top quality, correctly formulated industry-trusted pet supplies brands. We only work with official product agents in Sri Lanka and offer online payment and islandwide delivery.</em></p>

      <div class="social-icons">
        <a href="#" title="Facebook">📘</a>
        <a href="#" title="Twitter">🐦</a>
        <a href="#" title="Instagram">📷</a>
        <a href="#" title="LinkedIn">💼</a>
      </div>
    </div>
  </div>

  <hr>

  <div class="payment-icons">
  <img src="/PETVET/views/shared/images/visa.png" alt="Visa">
  <img src="/PETVET/views/shared/images/mastercard.png" alt="Mastercard">
  <img src="/PETVET/views/shared/images/amex.png" alt="American Express">
  <img src="/PETVET/views/shared/images/discover.png" alt="Discover">
  <img src="/PETVET/views/shared/images/genie.png" alt="Genie">
  <img src="/PETVET/views/shared/images/frimi.png" alt="Frimi">
  <img src="/PETVET/views/shared/images/ezcash.png" alt="EzCash">
  <img src="/PETVET/views/shared/images/mcash.png" alt="MCash">
  <img src="/PETVET/views/shared/images/sampath.png" alt="Sampath Bank">
  </div>
</footer>

<!-- Scroll to Top Button -->
<button class="scroll-to-top" id="scrollToTop" title="Back to top">↑</button>

    </div> <!-- End main-content -->

<script src="/PETVET/public/js/pet-owner/shop.js"></script>
<script>
// Override module for pet-owner
document.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('click', function(e) {
        if (e.target.closest('button') || e.target.closest('a')) {
            return;
        }
        const productId = this.dataset.productId;
        if (productId) {
            window.location.href = `/PETVET/index.php?module=pet-owner&page=shop-product&id=${productId}`;
        }
    });
});
</script>
<script src="/PETVET/public/js/cart.js"></script>
</body>
</html>
