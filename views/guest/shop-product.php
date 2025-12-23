<?php /* public guest shop product page */ ?>
<?php
// Helper function for image URLs
function getProductImageUrl($url) {
    if (empty($url)) return '/PETVET/public/images/product-placeholder.png';
    if (preg_match('/^https?:\/\//i', $url)) return $url;
    if (strpos($url, '/PETVET/') === 0) return $url;
    return '/PETVET/' . ltrim($url, '/');
}
?>
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
            margin-top: 120px;
        }
        
        .product-image {
            flex: 1;
            max-width: 450px;
        }
        
        /* Only target the direct image child (single image view) to avoid conflict with carousel */
        .product-image > img {
            width: 100%;
            height: auto;
            max-height: 450px;
            object-fit: contain;
            border-radius: 8px;
            background-color: #fff;
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
            height: 350px;
            overflow: hidden;
            border-radius: 12px;
            margin: 0 auto;
            background: #e3e3e3;
            border: 1px solid #cdcdcd;
        }
        
        .main-carousel-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            opacity: 0;
            transition: opacity 0.3s ease;
            padding: 10px;
        }
        
        .main-carousel-img.active {
            opacity: 1;
        }
        
        .detail-carousel-prev,
        .detail-carousel-next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #eee;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            z-index: 2;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .detail-carousel-prev:hover,
        .detail-carousel-next:hover {
            background: white;
            transform: translateY(-50%) scale(1.05);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        
        .detail-carousel-prev:active,
        .detail-carousel-next:active {
            transform: translateY(-50%) scale(0.95);
        }
        
        .detail-carousel-prev {
            left: 10px;
        }
        
        .detail-carousel-next {
            right: 10px;
        }
        
        .thumbnail-strip {
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .thumbnail-img {
            width: 60px;
            height: 60px;
            object-fit: contain;
            border-radius: 6px;
            cursor: pointer;
            border: 1px solid #eee;
            background: #fff;
            transition: all 0.2s ease;
            opacity: 0.7;
            padding: 2px;
        }
        
        .thumbnail-img:hover {
            opacity: 1;
            border-color: #cbd5e1;
        }
        
        .thumbnail-img.active {
            opacity: 1;
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
        }
        
        .product-info {
            flex: 1;
            padding: 1rem;
        }
        
        .product-info h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #333;
            line-height: 1.2;
        }
        
        .product-info .price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 1rem;
        }
        
        .short-desc {
            font-size: 1rem;
            line-height: 1.6;
            color: #666;
            margin-bottom: 1.5rem;
        }
        
        .quantity-wrapper {
            margin-bottom: 1.5rem;
        }
        
        .quantity-wrapper label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .quantity-box {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .qty-btn {
            background: #2563eb;
            color: white;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .qty-btn:hover {
            background: #1d4ed8;
        }
        
        #quantity {
            width: 60px;
            height: 36px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .add-to-cart {
            background: #2563eb;
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            font-size: 1.1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s;
            width: 100%;
            max-width: 300px;
            font-weight: 600;
        }
        
        .add-to-cart:hover {
            background: #1d4ed8;
        }
        
        .products {
            margin-top: 3rem;
            padding: 0 1rem;
        }
        
        .products h2 {
            text-align: center;
            margin-bottom: 2rem;
            color: #1e293b;
        }
        
        @media (max-width: 768px) {
            .product-details {
                flex-direction: column;
                gap: 1.5rem;
                margin: 1rem auto;
                margin-top: 20px;
                padding: 0 1rem;
            }
            
            .product-image {
                max-width: 100%;
            }
            
            .main-image-wrapper {
                max-width: 100%;
                height: 280px;
                border-radius: 8px;
            }
            
            .product-info {
                padding: 0;
            }
            
            .product-info h1 {
                font-size: 1.5rem;
                margin-bottom: 0.5rem;
            }
            
            .product-info .price {
                font-size: 1.25rem;
                margin-bottom: 0.8rem;
            }
            
            .short-desc {
                font-size: 0.95rem;
                margin-bottom: 1.2rem;
            }
            
            .thumbnail-img {
                width: 60px;
                height: 60px;
            }
            
            .add-to-cart {
                width: 100%;
                max-width: none;
                padding: 1rem;
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
                        <img class="main-carousel-img <?php echo $idx === 0 ? 'active' : ''; ?>" src="<?php echo htmlspecialchars(getProductImageUrl($img)); ?>" alt="<?php echo htmlspecialchars($product['name']); ?> - Image <?php echo $idx + 1; ?>">
                    <?php endforeach; ?>
                    <button class="detail-carousel-prev">❮</button>
                    <button class="detail-carousel-next">❯</button>
                </div>
                <div class="thumbnail-strip">
                    <?php foreach ($product['images'] as $idx => $img): ?>
                        <img class="thumbnail-img <?php echo $idx === 0 ? 'active' : ''; ?>" src="<?php echo htmlspecialchars(getProductImageUrl($img)); ?>" alt="Thumbnail <?php echo $idx + 1; ?>" data-index="<?php echo $idx; ?>">
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <img src="<?php echo htmlspecialchars(getProductImageUrl($product['images'][0] ?? $product['image'])); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
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
             <input type="number" id="quantity" value="1" min="1" max="5" step="1" onkeydown="if(event.key==='.' || event.key==='-' || event.key==='e' || event.key==='E'){event.preventDefault();}" oninput="this.value = Math.min(5, Math.max(1, parseInt(this.value) || 1));">
             <button class="qty-btn" onclick="changeQty(1)">+</button>
           </div>
        </div>

        <button class="add-to-cart" 
                data-product-id="<?php echo $product['id']; ?>"
                data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                data-product-price="<?php echo $product['price']; ?>"
                data-product-image="<?php echo htmlspecialchars($product['image']); ?>">
            Add to Cart
        </button>
    </div>
</section>

<!-- Related Products removed per request -->

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
