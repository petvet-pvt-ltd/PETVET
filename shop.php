<?php
require_once 'redirectorToLoggedUser.php';
?>
<!-- nethmi's first commit-->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Shop | PetVet</title>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/shop.css">
    <link rel="stylesheet" href="styles/shop-components.css">
</head>

<body>
<script src="redirectorToLoggedUser.js"></script>       <!-- redirecting javascript file -->

<?php require_once 'navbar.php' ?>

<!-- Banner Image -->
<div class="shop-banner">
  <img src="images/shop-banner.png" alt="Pet Shop Banner">
</div>

<!-- Info Cards Section -->
<section class="info-strip">
  <div class="info-box">
    <p>Best Rated Petshop<br>in Sri Lanka</p>
  </div>
  <div class="vertical-line"></div>

  <div class="info-box">
    <p>Easy and Secured<br>Payments</p>
  </div>
  <div class="vertical-line"></div>

  <div class="info-box">
    <p>Quick Customer<br>Support</p>
  </div>
</section>

<!-- Categories Section -->
<section class="categories">
  <h2>Shop by Categories</h2>
  <div class="category-list">
    <a class="category-card" data-category="food">
      <img src="images/category-food.png" alt="FOOD">
      <p>Food & Treats</p>
    </a>
    <a class="category-card" data-category="toys">
      <img src="images/category-toys.png" alt="TOYS">
      <p>Toys</p>
    </a>
    <a class="category-card" data-category="litter">
      <img src="images/category-litter.png" alt="LITTER">
      <p>Litter</p>
    </a>
    <a class="category-card" data-category="accessories">
      <img src="images/category-accessories.png" alt="ACCESSORIES">
      <p>Accessories</p>
    </a>
    <a class="category-card" data-category="grooming">
      <img src="images/category-grooming.png" alt="GROOMING">
      <p>Grooming</p>
    </a>
  </div>
</section>

<!-- Products Section -->
<section class="products">
  <h2>Products</h2>
  <div class="product-grid">
    <div class="product-card" data-category="food">
      <img src="images/fproduct1.png" alt="Dog Treats">
      <h3>Denta Fun Veggie Jaw Bone</h3>
      <p class="price">Rs. 500</p>
      <div class="product-actions">
        <button class="add-to-cart">Add to Cart</button>
        <a href="#" class="quick-view">Quick View</a>
      </div>
    </div>

    <div class="product-card" data-category="litter">
      <img src="images/fproduct2.png" alt="Trixie Litter Scoop">
      <h3>Trixie Litter Scoop</h3>
      <p class="price">Rs. 900</p>
      <div class="product-actions">
        <button class="add-to-cart">Add to Cart</button>
        <a href="#" class="quick-view">Quick View</a>
      </div>
    </div>

    <div class="product-card" data-category="toys">
      <img src="images/fproduct3.png" alt="Dog Toy Tug Rope">
      <h3>Dog Toy Tug Rope</h3>
      <p class="price">Rs. 2100</p>
      <div class="product-actions">
        <button class="add-to-cart">Add to Cart</button>
        <a href="#" class="quick-view">Quick View</a>
      </div>
    </div>

    <div class="product-card" data-category="grooming">
      <img src="images/fproduct4.png" alt="Trixie Aloe Vera Shampoo">
      <h3>Trixie Aloe Vera Shampoo</h3>
      <p class="price">Rs. 1900</p>
      <div class="product-actions">
        <button class="add-to-cart">Add to Cart</button>
        <a href="#" class="quick-view">Quick View</a>
      </div>
    </div>

    <div class="product-card" data-category="food">
      <img src="images/fproduct1.png" alt="Dog Treats">
      <h3>Denta Fun Veggie Jaw Bone</h3>
      <p class="price">Rs. 500</p>
      <div class="product-actions">
        <button class="add-to-cart">Add to Cart</button>
        <a href="#" class="quick-view">Quick View</a>
      </div>
    </div>

    <div class="product-card" data-category="litter">
      <img src="images/fproduct2.png" alt="Trixie Litter Scoop">
      <h3>Trixie Litter Scoop</h3>
      <p class="price">Rs. 900</p>
      <div class="product-actions">
        <button class="add-to-cart">Add to Cart</button>
        <a href="#" class="quick-view">Quick View</a>
      </div>
    </div>

    <div class="product-card" data-category="toys">
      <img src="images/fproduct3.png" alt="Dog Toy Tug Rope">
      <h3>Dog Toy Tug Rope</h3>
      <p class="price">Rs. 2100</p>
      <div class="product-actions">
        <button class="add-to-cart">Add to Cart</button>
        <a href="#" class="quick-view">Quick View</a>
      </div>
    </div>

    <div class="product-card" data-category="grooming">
      <img src="images/fproduct4.png" alt="Trixie Aloe Vera Shampoo">
      <h3>Trixie Aloe Vera Shampoo</h3>
      <p class="price">Rs. 1900</p>
      <div class="product-actions">
        <button class="add-to-cart">Add to Cart</button>
        <a href="#" class="quick-view">Quick View</a>
      </div>
    </div>
  </div>   
</section>

<script src="scripts/shop.js"></script>
</body>

</html>
