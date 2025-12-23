<?php /* public guest page */ ?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Shop | PetVet</title>
    <link rel="stylesheet" href="/PETVET/public/css/guest/navbar.css">
    <link rel="stylesheet" href="/PETVET/public/css/guest/shop.css">
    <meta name="description" content="Best Pet Shop in Sri Lanka - Food, Toys, Accessories, and more for your beloved pets">
    <meta name="keywords" content="pet shop, dog food, cat toys, pet accessories, pet care">
</head>
<body>

<?php require_once 'navbar.php' ?>

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
    </div>
    <div class="search-group">
      <input type="text" id="shopSearch" placeholder="Search shops..." class="shop-search-input" onkeyup="filterClinics()">
    </div>
  </div>
  
  <div class="clinics-grid" id="clinicsGrid">
    <?php foreach($clinics as $clinic): ?>
    <a href="/PETVET/index.php?module=guest&page=shop-clinic&clinic_id=<?php echo $clinic['id']; ?>" class="clinic-card" data-clinic-id="<?php echo $clinic['id']; ?>" data-map-location="<?php echo htmlspecialchars($clinic['map_location']); ?>">
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
        <h3 class="clinic-name"><?php echo htmlspecialchars($clinic['clinic_name']); ?></h3>
        
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

<script src="/PETVET/public/js/guest/shop-clinics.js"></script>
</body>

</html>