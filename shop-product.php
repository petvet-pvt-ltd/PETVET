<?php
require_once 'redirectorToLoggedUser.php';

// Product data
$products = [
    1 => [
        'name' => 'Denta Fun Veggie Jaw Bone',
        'price' => 500,
        'image' => 'images/fproduct1.png',
        'description' => 'A healthy, delicious treat for your dog. Made from natural ingredients to support dental health while satisfying chewing needs. Composition sweet potato meal, pea starch, vegetable by-products, minerals, yeast, cellulose, oils and fats, rosemary | gluten-free formula | vegetarian | no added sugar',
        'category' => 'food'
    ],
    2 => [
        'name' => 'Trixie Litter Scoop',
        'price' => 900,
        'image' => 'images/fproduct2.png',
        'description' => 'High-quality litter scoop made from durable materials. Perfect for easy and hygienic litter box maintenance. Features comfortable grip handle and efficient scooping design.',
        'category' => 'litter'
    ],
    3 => [
        'name' => 'Dog Toy Tug Rope',
        'price' => 2100,
        'image' => 'images/fproduct3.png',
        'description' => 'Interactive rope toy perfect for playing tug-of-war with your dog. Made from durable cotton fibers that help clean teeth during play. Great for bonding and exercise.',
        'category' => 'toys'
    ],
    4 => [
        'name' => 'Trixie Aloe Vera Shampoo',
        'price' => 1900,
        'image' => 'images/fproduct4.png',
        'description' => 'Gentle pet shampoo enriched with Aloe Vera for sensitive skin. Cleanses thoroughly while moisturizing and soothing your pet\'s coat. Suitable for regular use.',
        'category' => 'grooming'
    ]
];

// Get product ID from URL parameter
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$product = isset($products[$productId]) ? $products[$productId] : $products[1];
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
        <?php foreach($products as $id => $relatedProduct): ?>
            <?php if($id != $productId): // Don't show the current product ?>
        <div class="product-card" data-category="<?php echo $relatedProduct['category']; ?>" data-product-id="<?php echo $id; ?>">
            <img src="<?php echo htmlspecialchars($relatedProduct['image']); ?>" alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>">
            <h3><?php echo htmlspecialchars($relatedProduct['name']); ?></h3>
            <p class="price">Rs. <?php echo number_format($relatedProduct['price']); ?></p>
            <div class="product-actions">
                <button class="add-to-cart" type="button">Add to Cart</button>
                <a href="shop-product.php?id=<?php echo $id; ?>" class="quick-view">Quick View</a>
            </div>
        </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</section>

<script src="scripts/shop-product.js"></script>
</body>
</html>
