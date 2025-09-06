<?php
require_once 'redirectorToLoggedUser.php';
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details | PetVet</title>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/shop-product.css">
    <link rel="stylesheet" href="styles/shop-components.css">
</head>

<body>
<script src="redirectorToLoggedUser.js"></script>

<?php require_once 'navbar.php'; ?>

<!-- Product Details Section -->
<section class="product-details">
    <div class="product-image">
        <img src="images/fproduct1.png" alt="Denta Fun Veggie Jaw Bone">
    </div>

    <div class="product-info">
        <h1>Denta Fun Veggie Jaw Bone</h1>
        <p class="price">Rs. 500</p>
        <p class="short-desc">
            A healthy, delicious treat for your dog. Made from natural ingredients to support dental health while satisfying chewing needs. Composition sweet potato meal, pea starch, vegetable by-products, minerals, yeast, cellulose, oils and fats, rosemary | gluten-free formula | vegetarian | no added sugar 
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
        <div class="product-card" data-category="food">
            <img src="images/fproduct1.png" alt="Dog Treats">
            <h3>Denta Fun Veggie Jaw Bone</h3>
            <p class="price">Rs. 500</p>
            <div class="product-actions">
                <button class="add-to-cart" type="button">Add to Cart</button>
                <a href="#" class="quick-view">Quick View</a>
            </div>
        </div>

        <div class="product-card" data-category="litter">
            <img src="images/fproduct2.png" alt="Trixie Litter Scoop">
            <h3>Trixie Litter Scoop</h3>
            <p class="price">Rs. 900</p>
            <div class="product-actions">
                <button class="add-to-cart" type="button">Add to Cart</button>
                <a href="#" class="quick-view">Quick View</a>
            </div>
        </div>

        <div class="product-card" data-category="toys">
            <img src="images/fproduct3.png" alt="Dog Toy Tug Rope">
            <h3>Dog Toy Tug Rope</h3>
            <p class="price">Rs. 2100</p>
            <div class="product-actions">
                <button class="add-to-cart" type="button">Add to Cart</button>
                <a href="#" class="quick-view">Quick View</a>
            </div>
        </div>

        <div class="product-card" data-category="grooming">
            <img src="images/fproduct4.png" alt="Trixie Aloe Vera Shampoo">
            <h3>Trixie Aloe Vera Shampoo</h3>
            <p class="price">Rs. 1900</p>
            <div class="product-actions">
                <button class="add-to-cart" type="button">Add to Cart</button>
                <a href="#" class="quick-view">Quick View</a>
            </div>
        </div>
    </div>
</section>

<script src="scripts/shop-product.js"></script>
</body>
</html>
