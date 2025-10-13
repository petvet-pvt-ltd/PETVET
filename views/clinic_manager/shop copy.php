<?php
$currentPage = basename($_SERVER['PHP_SELF']);

// Simulated products array
$products = [
    [
        'id' => 1,
        'title' => 'Dog Food Premium',
        'category' => 'Food',
        'stock' => 25,
        'price' => 3500,
        'description' => 'High quality dog food for all breeds.',
        'images' => [
            'https://bestcarepetshop.lk/web/image/product.product/21628/image_1024/%5BPC03007%5D%20Purina%20Pro%20Plan%20Adult%20Medium%20Breed%20Essential%20Health%203Kg%20%282%29%2C%20Rs%2010%2C900.00?unique=07c8065',
            'https://bestcarepetshop.lk/web/image/product.image/311/image_1024/Purina%20Pro%20Plan%20Adult%20Medium%20Breed%20Essential%20Health%203Kg?unique=1d70473',
            'https://bestcarepetshop.lk/web/image/product.image/313/image_1024/Purina%20Pro%20Plan%20Adult%20Medium%20Breed%20Essential%20Health%203Kg?unique=1d70473',
            'https://bestcarepetshop.lk/web/image/product.image/314/image_1024/Purina%20Pro%20Plan%20Adult%20Medium%20Breed%20Essential%20Health%203Kg?unique=1d70473'
        ]
    ],
    [
        'id' => 2,
        'title' => 'Cat Toy Mouse',
        'category' => 'Toys',
        'stock' => 60,
        'price' => 700,
        'description' => 'Fun mouse toy for cats.',
        'images' => [
            'https://images.unsplash.com/photo-1518717758536-85ae29035b6d?auto=format&fit=facearea&w=400&q=80'
        ]
    ],
];

// Simulated pending orders
$pendingOrders = [
    [
        'id' => 101,
        'customer' => 'Nimal Perera',
        'address' => '123 Main St, Colombo',
        'phone' => '077-1234567',
        'product' => 'Dog Food Premium',
        'qty' => 2,
        'date' => '2025-08-10'
    ],
    [
        'id' => 102,
        'customer' => 'Samanthi Silva',
        'address' => '456 Lake Rd, Kandy',
        'phone' => '071-9876543',
        'product' => 'Cat Toy Mouse',
        'qty' => 1,
        'date' => '2025-08-11'
    ],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Shop</title>
        <link rel="stylesheet" href="/PETVET/public/css/clinic-manager/shop.css">
    <style>
    .main-content {
    margin-left: 240px;
    padding: 24px;
    }

    @media (max-width: 768px) {
        .main-content {
        margin-left: 0;
        width: 100%;
        }
    }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="shop-header">
            <h2>Clinic Shop</h2>
            <div class="buttons">
                <button class="btn primary" onclick="openModal('ordersModal')">
                    Pending Orders
                    <span class="badge"><?= count($pendingOrders) ?></span>
                </button>
                <button class="btn success" onclick="openModal('addProductModal')">+ Add Product</button>
            </div>
        </div>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
            <div class="product-card" data-product-id="<?= $product['id'] ?>">
                <div class="product-image-wrap">
                    <button class="img-nav-btn left" onclick="prevImage(<?= $product['id'] ?>)">&#8592;</button>
                    <img class="product-img" id="img-<?= $product['id'] ?>" src="<?= htmlspecialchars($product['images'][0]) ?>" alt="<?= htmlspecialchars($product['title']) ?>">
                    <button class="img-nav-btn right" onclick="nextImage(<?= $product['id'] ?>)">&#8594;</button>
                </div>
                <div class="product-info">
                    <div class="product-title"><?= htmlspecialchars($product['title']) ?></div>
                    <div class="product-category"><?= htmlspecialchars($product['category']) ?></div>
                    <div class="product-desc"><?= htmlspecialchars($product['description']) ?></div>
                    <div class="product-stock-price">
                        <div class="product-stock">
                            Stock: <?= $product['stock'] ?>
                            <?php if ($product['stock'] < 10): ?><span class="stock-low"> (Low)</span><?php endif; ?>
                        </div>
                        <div class="product-price">Rs. <?= number_format($product['price']) ?></div>
                    </div>
                    <div class="product-actions">
                        <button class="btn primary" onclick="editProduct(<?= $product['id'] ?>)">Edit</button>
                        <button class="btn danger" onclick="deleteProduct(<?= $product['id'] ?>)">Delete</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Add/Edit Product Modal -->
    <div class="modal" id="addProductModal" role="dialog" aria-modal="true" aria-labelledby="addProductTitle">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="addProductTitle">Add New Product</h3>
                <button class="close" aria-label="Close" onclick="closeModal('addProductModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addProductForm" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-field">
                            <label>Title</label>
                            <input type="text" name="title" required>
                        </div>
                        <div class="form-field">
                            <label>Category</label>
                            <input type="text" name="category" required>
                        </div>
                        <div class="form-field form-col-span-2">
                            <label>Description</label>
                            <textarea name="description" rows="3"></textarea>
                        </div>
                        <div class="form-field">
                            <label>Stock</label>
                            <input type="number" name="stock" min="0" required>
                        </div>
                        <div class="form-field">
                            <label>Price (Rs.)</label>
                            <input type="number" name="price" min="0" required>
                        </div>
                        <div class="form-field form-col-span-2">
                            <label>Images</label>
                            <input class="file-input" type="file" name="images[]" accept="image/*" multiple>
                            <div class="images-preview" id="imagesPreview"></div>
                        </div>
                    </div>
                    <button type="submit" class="btn success full">Add Product</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Pending Orders Modal -->
    <div class="modal modal--wide" id="ordersModal" role="dialog" aria-modal="true" aria-labelledby="ordersTitle">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="ordersTitle">Pending Orders</h3>
                <button class="close" aria-label="Close" onclick="closeModal('ordersModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="table-wrap">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Address</th>
                                <th>Phone</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingOrders as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['customer']) ?></td>
                                <td><?= htmlspecialchars($order['address']) ?></td>
                                <td><?= htmlspecialchars($order['phone']) ?></td>
                                <td><?= htmlspecialchars($order['product']) ?></td>
                                <td style="text-align:center; width:60px;"><?= $order['qty'] ?></td>
                                <td><?= htmlspecialchars($order['date']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Modal functions
        function openModal(id) {
            document.getElementById(id).classList.add('active');
        }
        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }
        window.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                e.target.classList.remove('active');
            }
        });

        // Image preview
    document.querySelector('input[name="images[]"]').addEventListener('change', (e) => {
            const preview = document.getElementById('imagesPreview');
            preview.innerHTML = '';
            Array.from(e.target.files).forEach(file => {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                preview.appendChild(img);
            });
        });

        // Dummy edit/delete
        function editProduct(id) { alert(`Edit product ${id}`); }
        function deleteProduct(id) { if (confirm('Delete this product?')) alert(`Deleted product ${id}`); }

        // Image carousel
        const productImages = <?php echo json_encode(array_column($products, 'images', 'id')); ?>;
        const productImageIndexes = {};
        Object.keys(productImages).forEach(id => productImageIndexes[id] = 0);

        function updateImage(productId) {
            const img = document.getElementById(`img-${productId}`);
            img.src = productImages[productId][productImageIndexes[productId]];
        }

        function prevImage(productId) {
            productImageIndexes[productId] = (productImageIndexes[productId] - 1 + productImages[productId].length) % productImages[productId].length;
            updateImage(productId);
        }

        function nextImage(productId) {
            productImageIndexes[productId] = (productImageIndexes[productId] + 1) % productImages[productId].length;
            updateImage(productId);
        }
    </script>
</body>
</html>