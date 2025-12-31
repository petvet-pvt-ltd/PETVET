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

<!-- Banner Image -->
<div class="shop-banner">
  <img src="/PETVET/views/shared/images/shop-banner.png" alt="Pet Shop Banner - Best Pet Products">
</div>

<!-- Shop Navigation Bar -->
<div class="shop-nav-bar" style="background: white; padding: 1rem 2rem; border-radius: 12px; margin: 1.5rem 0; box-shadow: 0 2px 8px rgba(0,0,0,0.08); display: flex; justify-content: space-between; align-items: center;">
  <div>
    <h2 style="margin: 0; color: #1f2937; font-size: 1.5rem;">🛍️ Shop</h2>
    <p style="margin: 0.25rem 0 0 0; color: #6b7280; font-size: 0.9rem;">Browse products from nearby pet shops</p>
  </div>
  <a href="/PETVET/index.php?module=pet-owner&page=orders" class="btn-my-orders" style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 16px rgba(102,126,234,0.3)';" onmouseout="this.style.transform=''; this.style.boxShadow='';">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
      <line x1="3" y1="6" x2="21" y2="6"></line>
      <path d="M16 10a4 4 0 0 1-8 0"></path>
    </svg>
    My Orders
  </a>
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

<!-- Clinics/Shops Section -->
<section class="clinics-section">
  <h2>🏪 Available Pet Shops</h2>
  <p class="section-subtitle">Select a shop to view their products</p>
  
  <!-- Filters -->
  <div class="shop-filters">
    <div class="filter-group">
      <button class="filter-chip active" id="filterNearby" onclick="toggleFilter('nearby')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
          <circle cx="12" cy="10" r="3"></circle>
        </svg>
        Nearby
      </button>
      <button class="filter-chip" id="filterFavorites" onclick="toggleFilter('favorites')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
        </svg>
        Favourites
      </button>
    </div>
    <div class="search-group">
      <input type="text" id="shopSearch" placeholder="Search shops..." class="shop-search-input" onkeyup="filterClinics()">
    </div>
  </div>
  
  <div class="clinics-grid" id="clinicsGrid">
    <?php foreach($clinics as $clinic): ?>
    <a href="/PETVET/index.php?module=pet-owner&page=shop-clinic&clinic_id=<?php echo $clinic['id']; ?>" class="clinic-card" data-clinic-id="<?php echo $clinic['id']; ?>" data-map-location="<?php echo htmlspecialchars($clinic['map_location']); ?>">
      <div class="clinic-logo">
        <?php if (!empty($clinic['clinic_logo'])): ?>
          <img src="<?php echo htmlspecialchars($clinic['clinic_logo']); ?>" alt="<?php echo htmlspecialchars($clinic['clinic_name']); ?>">
        <?php else: ?>
          <div class="clinic-logo-placeholder">
            <span><?php echo strtoupper(substr($clinic['clinic_name'], 0, 2)); ?></span>
          </div>
        <?php endif; ?>
      </div>
      
      <div class="clinic-info">
        <div class="clinic-header">
          <h3 class="clinic-name"><?php echo htmlspecialchars($clinic['clinic_name']); ?></h3>
          <button class="favorite-btn" data-clinic-id="<?php echo $clinic['id']; ?>" onclick="event.preventDefault(); toggleFavorite(<?php echo $clinic['id']; ?>);">
            <svg class="heart-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" stroke="currentColor" stroke-width="2"/>
            </svg>
          </button>
        </div>
        
        <?php if (!empty($clinic['clinic_description'])): ?>
        <p class="clinic-description"><?php echo htmlspecialchars(substr($clinic['clinic_description'], 0, 100)); ?><?php echo strlen($clinic['clinic_description']) > 100 ? '...' : ''; ?></p>
        <?php endif; ?>
        
        <div class="clinic-meta">
          <?php if (!empty($clinic['city'])): ?>
          <span class="clinic-location">📍 <?php echo htmlspecialchars($clinic['city']); ?><?php echo !empty($clinic['district']) ? ', ' . htmlspecialchars($clinic['district']) : ''; ?></span>
          <?php endif; ?>
          
          <span class="clinic-distance" data-clinic-id="<?php echo $clinic['id']; ?>">
            <span class="distance-loader">⏳ Calculating...</span>
          </span>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
  
  <?php if (empty($clinics)): ?>
  <div class="no-clinics">
    <span class="no-clinics-icon">🏪</span>
    <p>No shops available at the moment.</p>
  </div>
  <?php endif; ?>
</section>

    </div> <!-- End main-content -->

<script src="/PETVET/public/js/pet-owner/shop-clinics.js"></script>
</body>
</html>
