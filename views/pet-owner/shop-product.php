<?php /* Pet Owner Shop Product Page */ ?>
<?php
// Helper function for image URLs
function getProductImageUrl($url) {
    if (empty($url)) return '/PETVET/public/images/product-placeholder.png';
    if (preg_match('/^https?:\/\//i', $url)) return $url;
    if (strpos($url, '/PETVET/') === 0) return $url;
    return '/PETVET/' . ltrim($url, '/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="clinic-id" content="<?php echo $product['clinic_id']; ?>">
    <title>Product Details | PetVet</title>
    <link rel="stylesheet" href="/PETVET/public/css/guest/shop.css">
    <link rel="stylesheet" href="/PETVET/public/css/cart.css">
    <style>
        .main-content{
            margin-top: 150px;
        }
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
            position: relative;
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
            .main-content {
                margin-top: 0px;
            }
            
            .product-details {
                flex-direction: column;
                gap: 1.5rem;
                margin: 1rem auto;
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
    <?php include __DIR__ . '/../shared/sidebar/sidebar.php'; ?>
    
    <div class="main-content">

<!-- Product Details Section -->
<section class="product-details">
    <div class="product-image">
        <?php if (!empty($product['images']) && count($product['images']) > 1): ?>
            <div class="product-detail-carousel">
                <div class="main-image-wrapper">
                    <?php if ($product['stock'] <= 0): ?>
                        <div class="out-of-stock-banner">Out of Stock</div>
                    <?php endif; ?>
                    
                    <div class="product-wishlist-indicator-detail" 
                         data-product-id="<?php echo $product['id']; ?>"
                         style="display: none;">
                        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                            <line x1="4" y1="22" x2="4" y2="15"></line>
                        </svg>
                    </div>
                    
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
            <?php if ($product['stock'] <= 0): ?>
                <div class="out-of-stock-banner">Out of Stock</div>
            <?php endif; ?>
            
            <div class="product-wishlist-indicator-detail" 
                 data-product-id="<?php echo $product['id']; ?>"
                 style="display: none;">
                <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                    <line x1="4" y1="22" x2="4" y2="15"></line>
                </svg>
            </div>
            
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

        <?php if ($product['stock'] <= 0): ?>
            <button class="add-to-wishlist-detail" 
                    data-product-id="<?php echo $product['id']; ?>"
                    data-clinic-id="<?php echo $product['clinic_id']; ?>"
                    onclick="addToWishlist(<?php echo $product['id']; ?>, <?php echo $product['clinic_id']; ?>, this)"
                    style="display: none;">
                Add to Wishlist
            </button>
            <button class="remove-from-wishlist-detail" 
                    data-product-id="<?php echo $product['id']; ?>"
                    onclick="removeFromWishlist(<?php echo $product['id']; ?>, this)"
                    style="display: none;">
                Remove from Wishlist
            </button>
        <?php else: ?>
            <button class="add-to-cart" 
                    data-product-id="<?php echo $product['id']; ?>"
                    data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                    data-product-price="<?php echo $product['price']; ?>"
                    data-product-image="<?php echo htmlspecialchars($product['image']); ?>">
                Add to Cart
            </button>
        <?php endif; ?>
    </div>
</section>

    </div> <!-- End main-content -->

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
        mainImages.forEach((img, i) => {
            img.classList.toggle('active', i === index);
        });
        thumbnails.forEach((thumb, i) => {
            thumb.classList.toggle('active', i === index);
        });
        currentIndex = index;
    }
    
    prevBtn?.addEventListener('click', () => {
        const newIndex = (currentIndex - 1 + mainImages.length) % mainImages.length;
        showImage(newIndex);
    });
    
    nextBtn?.addEventListener('click', () => {
        const newIndex = (currentIndex + 1) % mainImages.length;
        showImage(newIndex);
    });
    
    thumbnails.forEach((thumb, index) => {
        thumb.addEventListener('click', () => {
            showImage(index);
        });
    });
});
</script>
<script src="/PETVET/public/js/pet-owner/shop-product.js?v=<?php echo time(); ?>"></script>
<link rel="stylesheet" href="/PETVET/public/css/pet-owner/cart-manager.css?v=<?php echo time(); ?>">
<script src="/PETVET/public/js/pet-owner/cart-manager.js?v=<?php echo time(); ?>"></script>

<!-- Wishlist Functionality -->
<style>
    /* Product Wishlist Indicator (Top Left of Product Image) - Non-clickable */
    .product-wishlist-indicator-detail {
        position: absolute;
        top: 12px;
        left: 12px;
        width: 36px;
        height: 36px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        z-index: 10;
        pointer-events: none;
    }
    
    .product-wishlist-indicator-detail svg {
        width: 20px;
        height: 20px;
        fill: #fbbf24;
        stroke: #f59e0b;
    }
    
    /* Out of Stock Banner */
    .out-of-stock-banner {
        position: absolute;
        top: 12px;
        right: 12px;
        background: rgba(239, 68, 68, 0.95);
        color: white;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        z-index: 10;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }
    
    /* Add to Wishlist Button */
    .add-to-wishlist-detail {
        background: #ff00a3;
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
    
    .add-to-wishlist-detail:hover {
        background: #d6008a;
        box-shadow: 0 4px 12px rgba(255, 0, 163, 0.3);
    }
    
    /* Remove from Wishlist Button */
    .remove-from-wishlist-detail {
        background: #dc2626;
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
    
    .remove-from-wishlist-detail:hover {
        background: #b91c1c;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }
</style>

<script>
    const PRODUCT_ID = <?php echo $product['id']; ?>;
    const CLINIC_ID = <?php echo $product['clinic_id']; ?>;
    let isInWishlist = false;
    
    // Load wishlist status on page load
    document.addEventListener('DOMContentLoaded', async function() {
        await checkWishlistStatus();
    });
    
    // Check if product is in wishlist
    async function checkWishlistStatus() {
        try {
            const response = await fetch(`/PETVET/api/pet-owner/shop-wishlist.php?action=check&product_id=${PRODUCT_ID}`);
            const data = await response.json();
            
            if (data.success) {
                isInWishlist = data.in_wishlist;
                updateWishlistButtonUI();
            }
        } catch (error) {
            console.error('Error checking wishlist status:', error);
        }
    }
    
    // Update wishlist button UI
    function updateWishlistButtonUI() {
        const indicator = document.querySelector('.product-wishlist-indicator-detail');
        const addBtn = document.querySelector('.add-to-wishlist-detail');
        const removeBtn = document.querySelector('.remove-from-wishlist-detail');
        
        // Show/hide indicator
        if (indicator) {
            indicator.style.display = isInWishlist ? 'flex' : 'none';
        }
        
        // Show/hide add/remove buttons for out-of-stock items
        if (addBtn && removeBtn) {
            if (isInWishlist) {
                addBtn.style.display = 'none';
                removeBtn.style.display = 'block';
            } else {
                addBtn.style.display = 'block';
                removeBtn.style.display = 'none';
            }
        }
    }
    
    // Remove from wishlist
    async function removeFromWishlist(productId, button) {
        try {
            const formData = new FormData();
            formData.append('action', 'remove');
            formData.append('product_id', productId);
            
            const response = await fetch('/PETVET/api/pet-owner/shop-wishlist.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                isInWishlist = false;
                updateWishlistButtonUI();
            }
        } catch (error) {
            console.error('Error removing from wishlist:', error);
        }
    }
    
    // Add to wishlist (for out-of-stock items)
    async function addToWishlist(productId, clinicId, button) {
        try {
            const formData = new FormData();
            formData.append('action', 'add');
            formData.append('product_id', productId);
            formData.append('clinic_id', clinicId);
            
            const response = await fetch('/PETVET/api/pet-owner/shop-wishlist.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                isInWishlist = true;
                updateWishlistButtonUI();
            }
        } catch (error) {
            console.error('Error adding to wishlist:', error);
        }
    }
</script>

</body>
</html>
