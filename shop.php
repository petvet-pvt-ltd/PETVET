<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Shop | PetVet</title>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/shop.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

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
    <a href="products.php?category=food" class="category-card">
      <img src="images/category-food.png" alt="FOOD">
      <p>Food & Treats</p>
    </a>
    <a href="products.php?category=toys" class="category-card">
      <img src="images/category-toys.png" alt="TOYS">
      <p>Toys</p>
    </a>
    <a href="products.php?category=accessories" class="category-card">
      <img src="images/category-litter.png" alt="LITTER">
      <p>Litter</p>
    </a>
    <a href="products.php?category=grooming" class="category-card">
      <img src="images/category-accessories.png" alt="ACCESSORIES">
      <p>Accessories</p>
    </a>
    <a href="products.php?category=grooming" class="category-card">
      <img src="images/category-grooming.png" alt="GROOMING">
      <p>Grooming</p>
    </a>
  </div>
</section>

<!-- Featured Products Section -->
<section class="featured-products">
  <h2>Featured Products</h2>
  <div class="product-grid">

    <div class="product-card">
      <img src="images/fproduct1.png" alt="Dog Treats">
      <h3>Denta Fun Veggie Jaw Bone, bulk, 12 cm, 35 g</h3>
      <p class="price">Rs. 500</p>
      <div class="product-actions">
        <button class="add-to-cart">Add to Cart</button>
        <a href="#" class="quick-view">Quick View</a>
      </div>
    </div>

    <div class="product-card">
      <img src="images/fproduct2.png" alt="Trixie Litter Scoop for Clumping Litter">
      <h3>Trixie Litter Scoop for Clumping Litter</h3>
      <p class="price">Rs. 900</p>
      <div class="product-actions">
        <button class="add-to-cart">Add to Cart</button>
        <a href="#" class="quick-view">Quick View</a>
      </div>
    </div>

    <div class="product-card">
      <img src="images/fproduct3.png" alt="Dog Toy Tug Rope with Ball">
      <h3>Dog Toy Tug Rope with Ball</h3>
      <p class="price">Rs. 2100</p>
      <div class="product-actions">
        <button class="add-to-cart">Add to Cart</button>
        <a href="#" class="quick-view">Quick View</a>
      </div>
    </div>

    <div class="product-card">
      <img src="images/fproduct4.png" alt="Trixie Aloe Vera Shampoo">
      <h3>Trixie Aloe Vera Shampoo</h3>
      <p class="price">Rs. 1900</p>
      <div class="product-actions">
        <button class="add-to-cart">Add to Cart</button>
        <a href="#" class="quick-view">Quick View</a>
      </div>
    </div>

    <div class="product-card">
      <img src="images/fproduct4.png" alt="Trixie Aloe Vera Shampoo">
      <h3>Trixie Aloe Vera Shampoo</h3>
      <p class="price">Rs. 1900</p>
      <div class="product-actions">
        <button class="add-to-cart">Add to Cart</button>
        <a href="#" class="quick-view">Quick View</a>
      </div>
    </div>
</div>

  </div>
</section>

<!-- Footer -->
<footer class="site-footer">
  <div class="footer-content">
    <div class="about">
      <h3>ABOUT US</h3>
      <p><em>petvet.lk is your One-stop shop for all things Pet related, selling a range of top quality, correctly formulated industry-trusted pet supplies brands. We only work with official product agents in Sri Lanka and offer online payment and islandwide delivery.</em></p>

      <div class="social-icons">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-linkedin-in"></i></a>
      </div>
    </div>
  </div>

  <hr>

  <div class="payment-icons">
    <img src="images/visa.png" alt="Visa">
    <img src="images/mastercard.png" alt="Mastercard">
    <img src="images/amex.png" alt="American Express">
    <img src="images/discover.png" alt="Discover">
    <img src="images/genie.png" alt="Genie">
    <img src="images/frimi.png" alt="Frimi">
    <img src="images/ezcash.png" alt="EzCash">
    <img src="images/mcash.png" alt="MCash">
    <img src="images/sampath.png" alt="Sampath Bank">
  </div>
</footer>





<script src="scripts/shop.js"></script>
</body>

</html>