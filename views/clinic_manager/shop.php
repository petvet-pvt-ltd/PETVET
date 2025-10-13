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
    [
        'id' => 3,
        'title' => 'Flea & Tick Shampoo',
        'category' => 'Grooming',
        'stock' => 12,
        'price' => 2450,
        'description' => 'Medicated shampoo to remove fleas and ticks safely.',
        'images' => [
            'https://images.unsplash.com/photo-1556228720-195a672e8a03?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1587300003388-59208cc962cb?q=80&w=1200&auto=format&fit=crop'
        ]
    ],
    [
        'id' => 4,
        'title' => 'Puppy Collar Set',
        'category' => 'Accessories',
        'stock' => 40,
        'price' => 1200,
        'description' => 'Adjustable soft collar set for puppies in multiple colors.',
        'images' => [
            'https://images.unsplash.com/photo-1525253013412-55c1a69a5738?q=80&w=1200&auto=format&fit=crop'
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
    [
        'id' => 103,
        'customer' => 'Kasun Fernando',
        'address' => '89 Temple Rd, Galle',
        'phone' => '075-5558899',
        'product' => 'Flea & Tick Shampoo',
        'qty' => 1,
        'date' => '2025-08-12'
    ],
    [
        'id' => 104,
        'customer' => 'Dilani Jayasuriya',
        'address' => '12 Park Ave, Negombo',
        'phone' => '077-1122334',
        'product' => 'Puppy Collar Set',
        'qty' => 3,
        'date' => '2025-08-14'
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
</head>
<body>
    <div class="main-content">
        <div class="shop-header">
            <div class="title-area">
                <h1>Clinic Shop Manager</h1>
                <p class="subtitle">Manage your clinic's products and review pending orders</p>
            </div>
            <div class="actions">
                <button id="btnPendingOrders" class="btn btn-secondary">
                    <span class="icon">📦</span>
                    Pending Orders
                    <span id="pendingBadge" class="badge"><?php echo count($pendingOrders); ?></span>
                </button>
                <button id="btnAddProduct" class="btn btn-primary">
                    <span class="icon">＋</span>
                    Add Product
                </button>
            </div>
        </div>

        <div class="products-grid">
            <?php foreach ($products as $p): ?>
                <div class="product-card" data-id="<?php echo htmlspecialchars($p['id']); ?>">
                    <div class="gallery" data-index="0">
                        <?php $firstImg = $p['images'][0] ?? ''; ?>
                        <img class="main-img" src="<?php echo htmlspecialchars($firstImg); ?>" alt="<?php echo htmlspecialchars($p['title']); ?>">
                        <?php if (count($p['images']) > 1): ?>
                            <button class="nav prev" aria-label="Previous image">❮</button>
                            <button class="nav next" aria-label="Next image">❯</button>
                        <?php endif; ?>
                        <?php if (!empty($p['images'])): ?>
                        <div class="thumbs">
                            <?php foreach ($p['images'] as $idx => $img): ?>
                                <img class="thumb <?php echo $idx === 0 ? 'active' : ''; ?>" data-i="<?php echo $idx; ?>" src="<?php echo htmlspecialchars($img); ?>" alt="thumb">
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="info">
                        <div class="top-row">
                            <h3 class="title"><?php echo htmlspecialchars($p['title']); ?></h3>
                            <span class="category chip"><?php echo htmlspecialchars($p['category']); ?></span>
                        </div>
                        <p class="desc"><?php echo htmlspecialchars($p['description']); ?></p>
                        <div class="meta">
                            <span class="stock">Stock: <strong><?php echo (int)$p['stock']; ?></strong></span>
                            <span class="price">LKR <?php echo number_format((float)$p['price'], 2); ?></span>
                        </div>
                        <div class="card-actions">
                            <button class="btn btn-light btn-edit" data-id="<?php echo (int)$p['id']; ?>">Edit</button>
                            <button class="btn btn-danger btn-delete" data-id="<?php echo (int)$p['id']; ?>">Delete</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Pending Orders Modal -->
    <div id="modalPending" class="modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-header">
                <h2>Pending Orders</h2>
                <button class="icon-btn modal-close" data-close>✕</button>
            </div>
            <div class="modal-body">
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Address</th>
                                <th>Phone</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="ordersTbody">
                            <?php foreach ($pendingOrders as $o): ?>
                                <tr>
                                    <td>#<?php echo (int)$o['id']; ?></td>
                                    <td><?php echo htmlspecialchars($o['customer']); ?></td>
                                    <td><?php echo htmlspecialchars($o['address']); ?></td>
                                    <td><?php echo htmlspecialchars($o['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($o['product']); ?></td>
                                    <td><?php echo (int)$o['qty']; ?></td>
                                    <td><?php echo htmlspecialchars($o['date']); ?></td>
                                    <td>
                                        <button class="btn btn-success btn-delivered" data-id="<?php echo (int)$o['id']; ?>">Delivered</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-close>Close</button>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div id="modalAdd" class="modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-header">
                <h2>Add New Product</h2>
                <button class="icon-btn modal-close" data-close>✕</button>
            </div>
            <div class="modal-body">
                <form id="addForm" class="form" enctype="multipart/form-data">
                    <div class="form-row">
                        <label>Title
                            <input type="text" name="title" required>
                        </label>
                        <label>Category
                            <select name="category" required>
                                <option value="Food">Food</option>
                                <option value="Toys">Toys</option>
                                <option value="Grooming">Grooming</option>
                                <option value="Accessories">Accessories</option>
                                <option value="Medicine">Medicine</option>
                            </select>
                        </label>
                    </div>
                    <label>Description
                        <textarea name="description" rows="3" required></textarea>
                    </label>
                    <div class="form-row">
                        <label>Stock
                            <input type="number" name="stock" min="0" step="1" required>
                        </label>
                        <label>Price (LKR)
                            <input type="number" name="price" min="0" step="0.01" required>
                        </label>
                    </div>
                    <div>
                        <div id="addImagePreview" class="image-preview-list"></div>
                        <label style="margin-top:8px">Images (URLs or Upload)
                            <div id="addImageList" class="image-url-list">
                                <div class="image-url-row">
                                    <input type="url" name="images[]" placeholder="https://...">
                                    <button type="button" class="icon-btn remove-url" title="Remove">−</button>
                                </div>
                            </div>
                        </label>
                        <button type="button" id="btnAddImageUrl" class="btn btn-light">Add another image</button>
                        <div style="margin:10px 0 0">
                            <input type="file" id="addImageUpload" name="image_uploads[]" accept="image/*" multiple style="display:block">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-close>Cancel</button>
                <button form="addForm" class="btn btn-primary" type="submit">Save (UI only)</button>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="modalEdit" class="modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-header">
                <h2>Edit Product</h2>
                <button class="icon-btn modal-close" data-close>✕</button>
            </div>
            <div class="modal-body">
                <form id="editForm" class="form" enctype="multipart/form-data">
                    <input type="hidden" name="id">
                    <div class="form-row">
                        <label>Title
                            <input type="text" name="title" required>
                        </label>
                        <label>Category
                            <select name="category" required>
                                <option value="Food">Food</option>
                                <option value="Toys">Toys</option>
                                <option value="Grooming">Grooming</option>
                                <option value="Accessories">Accessories</option>
                                <option value="Medicine">Medicine</option>
                            </select>
                        </label>
                    </div>
                    <label>Description
                        <textarea name="description" rows="3" required></textarea>
                    </label>
                    <div class="form-row">
                        <label>Stock
                            <input type="number" name="stock" min="0" step="1" required>
                        </label>
                        <label>Price (LKR)
                            <input type="number" name="price" min="0" step="0.01" required>
                        </label>
                    </div>
                    <div>
                        <div id="editImagePreview" class="image-preview-list"></div>
                        <label style="margin-top:8px">Images (URLs or Upload)
                            <div id="editImageList" class="image-url-list"></div>
                        </label>
                        <button type="button" id="btnEditAddImageUrl" class="btn btn-light">Add another image</button>
                        <div style="margin:10px 0 0">
                            <input type="file" id="editImageUpload" name="edit_image_uploads[]" accept="image/*" multiple style="display:block">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-close>Cancel</button>
                <button form="editForm" class="btn btn-primary" type="submit">Update (UI only)</button>
            </div>
        </div>
    </div>

    <!-- Delete Confirm Modal -->
    <div id="modalDelete" class="modal" aria-hidden="true">
        <div class="modal-dialog small">
            <div class="modal-header">
                <h2>Delete Product</h2>
                <button class="icon-btn modal-close" data-close>✕</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="delName">this product</strong>? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-close>No</button>
                <button id="btnConfirmDelete" class="btn btn-danger">Yes, Delete (UI only)</button>
            </div>
        </div>
    </div>

    <div id="toast" class="toast" role="status" aria-live="polite" aria-atomic="true"></div>

    <script>
    // Pass PHP arrays to JS for UI-only interactions
    const PRODUCTS = <?php echo json_encode($products, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
    const PENDING_ORDERS = <?php echo json_encode($pendingOrders, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;

    // Helpers
    const $ = (s, r = document) => r.querySelector(s);
    const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));
    const showToast = (msg) => {
        const t = $('#toast');
        t.textContent = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 1800);
    };

    // Modal handling
    const openModal = (el) => {
        el?.setAttribute('aria-hidden', 'false');
        document.body.classList.add('no-scroll');
    };
    const closeModal = (el) => {
        el?.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('no-scroll');
    };
    const wireModal = (id) => {
        const el = document.getElementById(id);
        el?.addEventListener('click', (e) => {
            if (e.target === el || e.target.hasAttribute('data-close')) closeModal(el);
        });
        return el;
    };

    const modalPending = wireModal('modalPending');
    const modalAdd = wireModal('modalAdd');
    const modalEdit = wireModal('modalEdit');
    const modalDelete = wireModal('modalDelete');

    // Header buttons
    $('#btnPendingOrders')?.addEventListener('click', () => openModal(modalPending));
    $('#btnAddProduct')?.addEventListener('click', () => openModal(modalAdd));

    // Image URL list handlers (Add)
    const addUrlRow = (container) => {
        const row = document.createElement('div');
        row.className = 'image-url-row';
        row.innerHTML = '<input type="url" name="images[]" placeholder="https://...">' +
                        '<button type="button" class="icon-btn remove-url" title="Remove">−</button>';
        container.appendChild(row);
    };
    const bindUrlList = (container) => {
        container.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-url')) {
                const row = e.target.closest('.image-url-row');
                if (row && container.children.length > 1) row.remove();
            }
        });
        // live preview from URL input
        container.addEventListener('input', (e) => {
            const input = e.target.closest('input[type="url"]');
            if (!input) return;
            const previewList = container.id === 'addImageList' ? document.getElementById('addImagePreview') : document.getElementById('editImagePreview');
            renderUrlPreviews(container, previewList);
        });
    };
    function renderUrlPreviews(container, previewList){
        if (!previewList) return;
        const urls = Array.from(container.querySelectorAll('input[type="url"]')).map(i=>i.value.trim()).filter(Boolean);
        // Keep uploaded file previews too; so append URLs after existing uploaded previews
        // Clear only URL-type previews by removing items with data-kind=url
        previewList.querySelectorAll('.image-preview-item[data-kind="url"]').forEach(n=>n.remove());
        urls.forEach((u)=>{
            const div = document.createElement('div');
            div.className = 'image-preview-item';
            div.dataset.kind = 'url';
            div.innerHTML = `<img src="${u}" alt="url">`;
            previewList.appendChild(div);
        });
    }
    bindUrlList(document.getElementById('addImageList'));
    document.getElementById('btnAddImageUrl')?.addEventListener('click', () => addUrlRow(document.getElementById('addImageList')));

    // Edit form image URL list
    bindUrlList(document.getElementById('editImageList'));
    document.getElementById('btnEditAddImageUrl')?.addEventListener('click', () => addUrlRow(document.getElementById('editImageList')));

    // Gallery controls per card
    const initGalleries = () => {
        $$('.product-card').forEach((card) => {
            const id = Number(card.dataset.id);
            const product = PRODUCTS.find(p => p.id === id);
            const gallery = $('.gallery', card);
            const mainImg = $('.main-img', card);
            const thumbs = $$('.thumb', card);
            let index = 0;
            const render = (i) => {
                index = i;
                if (!product?.images?.length) return;
                mainImg.src = product.images[index];
                thumbs.forEach((t, ti) => t.classList.toggle('active', ti === index));
            };
            $('.prev', card)?.addEventListener('click', () => {
                const next = (index - 1 + product.images.length) % product.images.length;
                render(next);
            });
            $('.next', card)?.addEventListener('click', () => {
                const next = (index + 1) % product.images.length;
                render(next);
            });
            thumbs.forEach((t) => t.addEventListener('click', () => render(Number(t.dataset.i))));
            // Optional: swipe support
            let startX = null;
            gallery.addEventListener('touchstart', (e) => startX = e.touches[0].clientX);
            gallery.addEventListener('touchend', (e) => {
                if (startX == null) return;
                const dx = e.changedTouches[0].clientX - startX;
                if (Math.abs(dx) > 30) {
                    if (dx < 0) $('.next', card)?.click(); else $('.prev', card)?.click();
                }
                startX = null;
            });
        });
    };
    initGalleries();

    // Edit button -> open modal with prefilled data
    $$('.btn-edit').forEach(btn => btn.addEventListener('click', () => {
        const id = Number(btn.dataset.id);
        const p = PRODUCTS.find(x => x.id === id);
        if (!p) return;
        const f = document.getElementById('editForm');
        f.id.value = p.id;
        f.title.value = p.title;
        f.category.value = p.category;
        f.description.value = p.description;
        f.stock.value = p.stock;
        f.price.value = p.price;
        const list = document.getElementById('editImageList');
        list.innerHTML = '';
        (p.images || []).forEach((url) => {
            const row = document.createElement('div');
            row.className = 'image-url-row';
            row.innerHTML = `<input type="url" name="images[]" value="${url}">` +
                            '<button type="button" class="icon-btn remove-url" title="Remove">−</button>';
            list.appendChild(row);
        });
        if (!p.images || p.images.length === 0) addUrlRow(list);
        // reset edit uploads preview each time
        if (window.editImageFiles) { window.editImageFiles = []; }
        const editImageUploadEl = document.getElementById('editImageUpload');
        if (editImageUploadEl) editImageUploadEl.value = '';
    if (typeof renderEditImagePreview === 'function') renderEditImagePreview();
    // render URL previews for existing URLs
    renderUrlPreviews(list, document.getElementById('editImagePreview'));
        openModal(modalEdit);
    }));

    // Delete button -> confirm modal
    let deleteTarget = null;
    $$('.btn-delete').forEach(btn => btn.addEventListener('click', () => {
        const id = Number(btn.dataset.id);
        deleteTarget = PRODUCTS.find(x => x.id === id) || null;
        $('#delName').textContent = deleteTarget ? deleteTarget.title : 'this product';
        openModal(modalDelete);
    }));
    document.getElementById('btnConfirmDelete')?.addEventListener('click', () => {
        closeModal(modalDelete);
        showToast('Delete simulated. (UI only)');
    });

    // Submit handlers (UI only)
    document.getElementById('addForm')?.addEventListener('submit', (e) => {
        e.preventDefault();
        closeModal(modalAdd);
        showToast('Product added (UI only)');
    });
    document.getElementById('editForm')?.addEventListener('submit', (e) => {
        e.preventDefault();
        closeModal(modalEdit);
        showToast('Product updated (UI only)');
    });

    // Image upload previews (UI only)
    const addImageUpload = document.getElementById('addImageUpload');
    const addImagePreview = document.getElementById('addImagePreview');
    let addImageFiles = [];
    function renderAddImagePreview() {
        if (!addImagePreview) return;
        addImagePreview.innerHTML = '';
        addImageFiles.forEach((file, idx) => {
            const url = URL.createObjectURL(file);
            const div = document.createElement('div');
            div.className = 'image-preview-item';
            div.innerHTML = `<img src="${url}" alt="uploaded">` +
                            `<span class="remove-preview" data-i="${idx}">×</span>`;
            addImagePreview.appendChild(div);
        });
    }
    addImageUpload?.addEventListener('change', (e) => {
        addImageFiles = Array.from(e.target.files || []);
        renderAddImagePreview();
    // also re-render URL previews to keep both
    const c = document.getElementById('addImageList');
    renderUrlPreviews(c, addImagePreview);
    });
    addImagePreview?.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-preview')) {
            const idx = Number(e.target.dataset.i);
            addImageFiles.splice(idx, 1);
            renderAddImagePreview();
            if (addImageUpload) addImageUpload.value = '';
        }
    });

    const editImageUpload = document.getElementById('editImageUpload');
    const editImagePreview = document.getElementById('editImagePreview');
    let editImageFiles = [];
    function renderEditImagePreview() {
        if (!editImagePreview) return;
        editImagePreview.innerHTML = '';
        editImageFiles.forEach((file, idx) => {
            const url = URL.createObjectURL(file);
            const div = document.createElement('div');
            div.className = 'image-preview-item';
            div.innerHTML = `<img src="${url}" alt="uploaded">` +
                            `<span class="remove-preview" data-i="${idx}">×</span>`;
            editImagePreview.appendChild(div);
        });
    }
    editImageUpload?.addEventListener('change', (e) => {
        editImageFiles = Array.from(e.target.files || []);
        renderEditImagePreview();
    const c = document.getElementById('editImageList');
    renderUrlPreviews(c, editImagePreview);
    });
    editImagePreview?.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-preview')) {
            const idx = Number(e.target.dataset.i);
            editImageFiles.splice(idx, 1);
            renderEditImagePreview();
            if (editImageUpload) editImageUpload.value = '';
        }
    });

    // Clear previews when closing modals
    [modalAdd, modalEdit].forEach((modal) => {
        modal?.addEventListener('click', (e) => {
            if (e.target === modal || e.target.hasAttribute('data-close')) {
                if (modal === modalAdd) {
                    addImageFiles = [];
                    if (addImageUpload) addImageUpload.value = '';
                    renderAddImagePreview();
                    renderUrlPreviews(document.getElementById('addImageList'), addImagePreview);
                } else if (modal === modalEdit) {
                    editImageFiles = [];
                    if (editImageUpload) editImageUpload.value = '';
                    renderEditImagePreview();
                    renderUrlPreviews(document.getElementById('editImageList'), editImagePreview);
                }
            }
        });
    });

    // Delivered action (UI only): mark row as delivered and decrease badge count
    $$('#ordersTbody .btn-delivered').forEach(btn=>{
        btn.addEventListener('click', ()=>{
            const tr = btn.closest('tr');
            if (!tr) return;
            tr.classList.add('is-delivered');
            btn.disabled = true; btn.textContent = 'Delivered';
            // Update badge
            const badge = document.getElementById('pendingBadge');
            if (badge) {
                const n = Math.max(0, (parseInt(badge.textContent,10) || 0) - 1);
                badge.textContent = String(n);
            }
            showToast('Order marked as delivered (UI only)');
        });
    });

    // Keyboard ESC to close any open modal
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            [modalPending, modalAdd, modalEdit, modalDelete].forEach(m => m && closeModal(m));
        }
    });
    </script>
</body>
</html>