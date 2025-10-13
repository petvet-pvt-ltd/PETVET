<?php /* public guest shop product page */ ?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details | PetVet</title>
    <link rel="stylesheet" href="/PETVET/public/css/guest/navbar.css">
    <link rel="stylesheet" href="/PETVET/public/css/guest/shop.css">
    <style>
        .product-details {
            display: flex;
            gap: 2rem;
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .product-image {
            flex: 1;
        }
        
        .product-image img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }
        
        .product-info {
            flex: 1;
            padding: 1rem;
        }
        
        .product-info h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #333;
        }
        
        .product-info .price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 1rem;
        }
        
        .short-desc {
            font-size: 1rem;
            line-height: 1.6;
            color: #666;
            margin-bottom: 2rem;
        }
        
        .quantity-wrapper {
            margin-bottom: 2rem;
        }
        
        .quantity-wrapper label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        
        .quantity-box {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .qty-btn {
            background: #3498db;
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1.2rem;
        }
        
        .qty-btn:hover {
            background: #2980b9;
        }
        
        #quantity {
            width: 80px;
            height: 40px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .add-to-cart {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .add-to-cart:hover {
            background: #c0392b;
        }
        
        .products {
            margin-top: 3rem;
        }
        
        @media (max-width: 768px) {
            .product-details {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<?php require_once 'navbar.php'; ?>

<!-- Product Details Section -->
<section class="product-details">
    <div class="product-image">
        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
    </div>

    <div class="product-info">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <p class="price">Rs. <?php echo number_format($product['price']); ?></p>
        <p class="short-desc">
            <?php echo htmlspecialchars($product['description']); ?>
        </p>

        <div class="quantity-wrapper">
         <label for="quantity">Quantity:</label>
           <div class="quantity-box">
             <button class="qty-btn" onclick="changeQty(-1)">−</button>
             <input type="number" id="quantity" value="1" min="1">
             <button class="qty-btn" onclick="changeQty(1)">+</button>
           </div>
        </div>

        <button class="add-to-cart">Add to Cart</button>
    </div>
</section>

<!-- Related Products -->
<section class="products">
    <h2>Related Products</h2>
    <div class="product-grid">
        <?php foreach($relatedProducts as $relatedProduct): ?>
        <div class="product-card" data-category="<?php echo htmlspecialchars($relatedProduct['category']); ?>" data-product-id="<?php echo $relatedProduct['id']; ?>">
            <img src="<?php echo htmlspecialchars($relatedProduct['image']); ?>" alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>">
            <h3><?php echo htmlspecialchars($relatedProduct['name']); ?></h3>
            <p class="price">Rs. <?php echo number_format($relatedProduct['price']); ?></p>
            <div class="product-actions">
                <button class="add-to-cart" type="button">Add to Cart</button>
                <a href="/PETVET/index.php?module=guest&page=shop-product&id=<?php echo $relatedProduct['id']; ?>" class="quick-view">Quick View</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Footer -->
<footer class="site-footer">
  <div class="footer-content">
    <div class="about">
      <h3>ABOUT US</h3>
      <p><em>petvet.lk is your One-stop shop for all things Pet related, selling a range of top quality, correctly formulated industry-trusted pet supplies brands. We only work with official product agents in Sri Lanka and offer online payment and islandwide delivery.</em></p>

      <div class="social-icons">
        <a href="#">Facebook</a>
        <a href="#">Twitter</a>
        <a href="#">Instagram</a>
        <a href="#">LinkedIn</a>
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

<script src="/PETVET/public/js/guest/shop-product.js"></script>
</body>
</html>