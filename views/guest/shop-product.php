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
            max-width: 450px;
        }
        
        .product-image img {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        /* Product Detail Carousel */
        .product-detail-carousel {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .main-image-wrapper {
            position: relative;
            width: 100%;
            max-width: 450px;
            height: 400px;
            overflow: hidden;
            border-radius: 12px;
            background: #f1f5f9;
            margin: 0 auto;
        }
        
        .main-carousel-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .main-carousel-img.active {
            opacity: 1;
        }
        
        .detail-carousel-prev,
        .detail-carousel-next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.95);
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 20px;
            z-index: 2;
            transition: all 0.2s ease;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .detail-carousel-prev:hover,
        .detail-carousel-next:hover {
            background: white;
            transform: translateY(-50%) scale(1.1);
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
        }
        
        .detail-carousel-prev:active,
        .detail-carousel-next:active {
            transform: translateY(-50%) scale(0.95);
        }
        
        .detail-carousel-prev {
            left: 15px;
        }
        
        .detail-carousel-next {
            right: 15px;
        }
        
        .thumbnail-strip {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        
        .thumbnail-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s ease;
            opacity: 0.6;
        }
        
        .thumbnail-img:hover {
            opacity: 1;
            border-color: #cbd5e1;
            transform: scale(1.05);
        }
        
        .thumbnail-img.active {
            opacity: 1;
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
            transform: scale(1.05);
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
        <?php if (!empty($product['images']) && count($product['images']) > 1): ?>
            <div class="product-detail-carousel">
                <div class="main-image-wrapper">
                    <?php foreach ($product['images'] as $idx => $img): ?>
                        <img class="main-carousel-img <?php echo $idx === 0 ? 'active' : ''; ?>" src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($product['name']); ?> - Image <?php echo $idx + 1; ?>">
                    <?php endforeach; ?>
                    <button class="detail-carousel-prev">❮</button>
                    <button class="detail-carousel-next">❯</button>
                </div>
                <div class="thumbnail-strip">
                    <?php foreach ($product['images'] as $idx => $img): ?>
                        <img class="thumbnail-img <?php echo $idx === 0 ? 'active' : ''; ?>" src="<?php echo htmlspecialchars($img); ?>" alt="Thumbnail <?php echo $idx + 1; ?>" data-index="<?php echo $idx; ?>">
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <img src="<?php echo htmlspecialchars($product['images'][0] ?? $product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        <?php endif; ?>
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

<!-- Related Products removed per request -->

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
<script>
// Product Detail Carousel
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.querySelector('.product-detail-carousel');
    if (!carousel) return;
    const mainImages = carousel.querySelectorAll('.main-carousel-img');
    const thumbnails = carousel.querySelectorAll('.thumbnail-img');
    const prevBtn = carousel.querySelector('.detail-carousel-prev');
    const nextBtn = carousel.querySelector('.detail-carousel-next');
    let currentIndex = 0;
    function showImage(index) {
        mainImages.forEach((img, i) => { img.classList.toggle('active', i === index); });
        thumbnails.forEach((thumb, i) => { thumb.classList.toggle('active', i === index); });
        currentIndex = index;
    }
    prevBtn?.addEventListener('click', () => { showImage((currentIndex - 1 + mainImages.length) % mainImages.length); });
    nextBtn?.addEventListener('click', () => { showImage((currentIndex + 1) % mainImages.length); });
    thumbnails.forEach((thumb, index) => { thumb.addEventListener('click', () => { showImage(index); }); });
});
</script>
