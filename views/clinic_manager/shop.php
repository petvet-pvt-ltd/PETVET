<?php
// Session is already started by index.php - don't start again
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in as clinic manager
// The system uses 'current_role' not 'role'
$userRole = $_SESSION['current_role'] ?? $_SESSION['role'] ?? null;

if (!isset($_SESSION['user_id']) || $userRole !== 'clinic_manager') {
    // Redirect to login or show error
    header('Location: /PETVET/index.php');
    exit;
}

$currentPage = basename($_SERVER['PHP_SELF']);

// Products are passed from the controller as $products
// Orders are passed from the controller as $orders

// If products are not set (e.g. direct access), initialize empty
if (!isset($products)) {
    $products = [];
}

// If orders are not set, initialize empty
if (!isset($orders)) {
    $orders = [];
}

// Map controller orders to $pendingOrders variable used in view
$pendingOrders = $orders;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Manager | Clinic Manager</title>
    <link rel="stylesheet" href="/PETVET/public/css/clinic-manager/enhanced-global.css">
    <link rel="stylesheet" href="/PETVET/public/css/clinic-manager/shop.css">
</head>
<body>
    <div class="main-content">
        <div class="shop-header">
            <div class="title-area">
                <h1>Clinic Shop Manager</h1>
                <p class="subtitle">Manage your clinic's products</p>
            </div>
            <div class="actions">
                <button id="btnOrders" class="btn btn-secondary">
                    <span class="icon">📦</span>
                    Orders
                    <?php if (count($orders) > 0): ?>
                        <span class="order-badge"><?php echo count($orders); ?></span>
                    <?php endif; ?>
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

    <!-- Orders Modal -->
    <div id="modalOrders" class="modal pv-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="ordersModalTitle">
        <div class="modal-dialog modal-wide pv-modal__dialog pv-modal__dialog--fullscreen">
            <div class="modal-header pv-modal__header">
                <div class="pv-modal__title">
                    <h2 id="ordersModalTitle">Orders Pending Delivery</h2>
                    <p class="pv-modal__subtitle">Confirmed orders waiting to be delivered</p>
                </div>
                <button class="pv-icon-btn modal-close" type="button" data-close aria-label="Close">✕</button>
            </div>
            <div class="modal-body">
                <?php if (count($orders) > 0): ?>
                    <div class="orders-table-wrap">
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr data-order-id="<?php echo $order['id']; ?>">
                                        <td>
                                            <div class="pv-order-no"><?php echo htmlspecialchars($order['order_number']); ?></div>
                                        </td>
                                        <td><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($order['phone'] ?? 'N/A'); ?></td>
                                        <td>LKR <?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                        <td>
                                            <div class="pv-actions">
                                                <button class="pv-btn pv-btn--ghost btn-view-cart"
                                                        type="button"
                                                        data-order-id="<?php echo $order['id']; ?>"
                                                        data-order-number="<?php echo htmlspecialchars($order['order_number']); ?>"
                                                        data-customer="<?php echo htmlspecialchars(trim(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? ''))); ?>"
                                                        data-phone="<?php echo htmlspecialchars($order['phone'] ?? 'N/A'); ?>"
                                                        data-date="<?php echo htmlspecialchars($order['created_at']); ?>"
                                                        data-total="<?php echo htmlspecialchars((string)$order['total_amount']); ?>"
                                                        data-items='<?php echo htmlspecialchars(json_encode($order['items']), ENT_QUOTES); ?>'>
                                                    View Items
                                                </button>
                                                <button class="pv-btn pv-btn--primary btn-mark-delivered"
                                                        type="button"
                                                        data-order-id="<?php echo $order['id']; ?>"
                                                        data-order-number="<?php echo htmlspecialchars($order['order_number']); ?>">
                                                    Mark Delivered
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No pending orders at the moment.</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer pv-modal__footer">
                <button class="pv-btn pv-btn--soft" type="button" data-close>Close</button>
            </div>
        </div>
    </div>

    <!-- Order Items Modal -->
    <div id="modalCart" class="modal pv-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="cartModalTitle">
        <div class="modal-dialog pv-modal__dialog pv-modal__dialog--items">
            <div class="modal-header pv-modal__header">
                <div class="pv-modal__title">
                    <h2 id="cartModalTitle">Order Items</h2>
                    <p class="pv-modal__subtitle" id="cartModalSubtitle">Order details and item list</p>
                </div>
                <button class="pv-icon-btn modal-close" type="button" data-close aria-label="Close">✕</button>
            </div>
            <div class="modal-body pv-modal__body">
                <div class="pv-card">
                    <div class="pv-card__header">Order Details</div>
                    <div class="pv-card__body">
                        <div class="pv-kv-wrap">
                            <table class="pv-kv" id="cartOrderDetails" aria-label="Order details">
                                <tbody>
                                    <tr>
                                        <th>Order #</th>
                                        <td class="muted">—</td>
                                        <th>Date</th>
                                        <td class="muted">—</td>
                                    </tr>
                                    <tr>
                                        <th>Customer</th>
                                        <td class="muted">—</td>
                                        <th>Phone</th>
                                        <td class="muted">—</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td class="muted">—</td>
                                        <th>Total</th>
                                        <td class="muted">—</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="pv-card">
                    <div class="pv-card__header">Items</div>
                    <div class="pv-card__body" style="padding:0;">
                        <div class="pv-table-wrap">
                            <table class="pv-table" aria-label="Order items">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th class="pv-right">Qty</th>
                                        <th class="pv-right">Unit</th>
                                        <th class="pv-right">Line Total</th>
                                    </tr>
                                </thead>
                                <tbody id="cartItemsTbody">
                                    <tr>
                                        <td colspan="4" class="pv-empty">Select an order to view items</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="pv-right"><strong>Total</strong></td>
                                        <td class="pv-right"><strong id="cartTotalCell">LKR 0.00</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer pv-modal__footer">
                <button class="pv-btn pv-btn--soft" type="button" data-close>Close</button>
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
                                <option value="">-- Select Category --</option>
                                <option value="food">Food & Treats</option>
                                <option value="toys">Toys & Games</option>
                                <option value="litter">Litter & Training</option>
                                <option value="accessories">Accessories & Supplies</option>
                                <option value="grooming">Grooming & Health</option>
                                <option value="medicine">Medicine & Health</option>
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
                        <label>Product Images (Up to 5)
                            <input type="file" id="addImageUpload" name="product_images[]" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" multiple style="display:block; width:100%; padding:10px; border:2px dashed #ccc; border-radius:8px;">
                            <small class="muted">Upload up to 5 images (JPG, PNG, GIF, or WebP, max 5MB each)</small>
                        </label>
                        <div id="addImagePreview" class="image-preview-list" style="margin-top:10px; display:flex; flex-wrap:wrap; gap:10px;"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-close>Cancel</button>
                <button form="addForm" class="btn btn-primary" type="submit" id="btnSaveAdd">Save</button>
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
                                <option value="">-- Select Category --</option>
                                <option value="food">Food & Treats</option>
                                <option value="toys">Toys & Games</option>
                                <option value="litter">Litter & Training</option>
                                <option value="accessories">Accessories & Supplies</option>
                                <option value="grooming">Grooming & Health</option>
                                <option value="medicine">Medicine & Health</option>
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
                        <div id="editCurrentImages" style="margin-bottom:15px;"></div>
                        <label>Add More Images (Up to 5 total)
                            <input type="file" id="editImageUpload" name="product_images[]" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" multiple style="display:block; width:100%; padding:10px; border:2px dashed #ccc; border-radius:8px;">
                            <small class="muted">Upload additional images (JPG, PNG, GIF, or WebP, max 5MB each)</small>
                        </label>
                        <div id="editImagePreview" class="image-preview-list" style="margin-top:10px; display:flex; flex-wrap:wrap; gap:10px;"></div>
                        <input type="hidden" id="deletedImages" name="deleted_images" value="">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-close>Cancel</button>
                <button form="editForm" class="btn btn-primary" type="submit">Update</button>
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
                <button class="btn btn-secondary" data-close>No, Cancel</button>
                <button id="btnConfirmDelete" class="btn btn-danger">Yes, Delete</button>
            </div>
        </div>
    </div>

    <div id="toast" class="toast" role="status" aria-live="polite" aria-atomic="true"></div>

    <script>
    // Pass PHP arrays to JS for UI-only interactions
    const PRODUCTS = <?php echo json_encode($products, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
    const PENDING_ORDERS = <?php echo json_encode($pendingOrders, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;

    console.log('PRODUCTS loaded:', PRODUCTS.length, 'products');
    console.log('First product:', PRODUCTS[0]);

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

    const modalAdd = wireModal('modalAdd');
    const modalEdit = wireModal('modalEdit');
    const modalDelete = wireModal('modalDelete');
    const modalOrders = wireModal('modalOrders');
    const modalCart = wireModal('modalCart');

    // Header buttons
    $('#btnOrders')?.addEventListener('click', () => openModal(modalOrders));
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
        if (!container) return; // Add null check
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
    const editButtons = $$('.btn-edit');
    console.log('Found edit buttons:', editButtons.length);
    
    editButtons.forEach(btn => {
        console.log('Binding edit button for product ID:', btn.dataset.id);
        btn.addEventListener('click', () => {
            const id = Number(btn.dataset.id);
            console.log('Edit button clicked for ID:', id);
            
            const p = PRODUCTS.find(x => x.id === id);
            if (!p) {
                console.error('Product not found:', id);
                console.log('Available product IDs:', PRODUCTS.map(x => x.id));
                return;
            }
            
            console.log('Edit product:', p);
            
            const f = document.getElementById('editForm');
            f.id.value = p.id;
            f.title.value = p.title;
            f.category.value = p.category.toLowerCase(); // Convert to lowercase for dropdown
            f.description.value = p.description;
            f.stock.value = p.stock;
            f.price.value = p.price;
            
            // Show current images with delete buttons
            const currentImagesDiv = document.getElementById('editCurrentImages');
            deletedImagesList = []; // Reset deleted list
            document.getElementById('deletedImages').value = '';
            
            if (currentImagesDiv && p.images && p.images.length > 0) {
                currentImagesDiv.innerHTML = '<strong style="display:block; margin-bottom:10px;">Current Images:</strong>';
                const imagesContainer = document.createElement('div');
                imagesContainer.style.cssText = 'display:flex; flex-wrap:wrap; gap:10px;';
                
                p.images.forEach((img, idx) => {
                    const div = document.createElement('div');
                    div.className = 'current-image-item';
                    div.dataset.imageUrl = img;
                    div.dataset.imageIndex = idx;
                    div.style.cssText = 'position:relative; width:100px; height:100px;';
                    div.innerHTML = `
                        <img src="${img}" alt="Current ${idx+1}" style="width:100%; height:100%; object-fit:cover; border-radius:8px; border:2px solid #ddd;">
                        <button type="button" class="remove-current-image" data-index="${idx}" style="position:absolute; top:-5px; right:-5px; width:24px; height:24px; border-radius:50%; background:#f44336; color:white; border:2px solid white; cursor:pointer; font-size:16px; line-height:1; padding:0; display:flex; align-items:center; justify-content:center;">×</button>
                        <span style="position:absolute; bottom:2px; left:2px; background:rgba(0,0,0,0.6); color:white; padding:2px 6px; border-radius:4px; font-size:11px;">${idx+1}</span>
                    `;
                    imagesContainer.appendChild(div);
                });
                
                currentImagesDiv.appendChild(imagesContainer);
                
                // Add event listeners for delete buttons
                currentImagesDiv.querySelectorAll('.remove-current-image').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const imageUrl = btn.closest('.current-image-item').dataset.imageUrl;
                        const imageIndex = btn.closest('.current-image-item').dataset.imageIndex;
                        
                        // Mark for deletion
                        deletedImagesList.push(imageUrl);
                        document.getElementById('deletedImages').value = JSON.stringify(deletedImagesList);
                        
                        // Visual feedback
                        const item = btn.closest('.current-image-item');
                        item.style.opacity = '0.3';
                        item.style.pointerEvents = 'none';
                        btn.textContent = '✓';
                        btn.style.background = '#999';
                        
                        console.log('Marked for deletion:', imageUrl);
                    });
                });
            } else {
                currentImagesDiv.innerHTML = '<p style="color:#666; font-style:italic;">No images</p>';
            }
            
            // Reset file input
            const editImageUploadEl = document.getElementById('editImageUpload');
            if (editImageUploadEl) editImageUploadEl.value = '';
            
            const editPreview = document.getElementById('editImagePreview');
            if (editPreview) editPreview.innerHTML = '';
            
            console.log('Opening modal...');
            openModal(modalEdit);
        });
    });

    // Delete button -> confirm modal
    let deleteTarget = null;
    const deleteButtons = $$('.btn-delete');
    console.log('Found delete buttons:', deleteButtons.length);
    
    deleteButtons.forEach(btn => {
        console.log('Binding delete button for product ID:', btn.dataset.id);
        btn.addEventListener('click', () => {
            const id = Number(btn.dataset.id);
            console.log('Delete button clicked for ID:', id);
            
            deleteTarget = PRODUCTS.find(x => x.id === id) || null;
            console.log('Delete target:', deleteTarget);
            
            $('#delName').textContent = deleteTarget ? deleteTarget.title : 'this product';
            
            console.log('Opening delete modal...');
            openModal(modalDelete);
        });
    });
    
    document.getElementById('btnConfirmDelete')?.addEventListener('click', async () => {
        if (!deleteTarget) {
            console.error('No delete target');
            return;
        }
        
        console.log('Deleting product:', deleteTarget);
        
        const formData = new FormData();
        formData.append('id', deleteTarget.id);
        
        console.log('Sending to API...');
        
        try {
            // Use permanent delete - removes from database and deletes image file
            const response = await fetch('/PETVET/api/products/permanent-delete.php', {
                method: 'POST',
                body: formData
            });
            
            console.log('Response status:', response.status);
            
            const result = await response.json();
            
            console.log('API response:', result);
            
            if (result.success) {
                closeModal(modalDelete);
                showToast('Product permanently deleted');
                setTimeout(() => location.reload(), 1000);
            } else {
                closeModal(modalDelete);
                showToast('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Fetch error:', error);
            closeModal(modalDelete);
            showToast('Error: ' + error.message);
        }
    });

    // Submit handlers
    document.getElementById('addForm')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        
        console.log('Add form submitted');
        
        const formData = new FormData(this);
        
        // Rename title to name for API
        const title = formData.get('title');
        formData.set('name', title);
        formData.delete('title');
        
        // Handle multiple image uploads
        const fileInput = document.getElementById('addImageUpload');
        if (fileInput && fileInput.files.length > 0) {
            // Remove the old name and add files with new name
            formData.delete('product_images[]');
            Array.from(fileInput.files).forEach((file, index) => {
                formData.append('product_images[]', file);
            });
            console.log('Image files:', fileInput.files.length);
        }
        
        console.log('Sending to API...');
        
        try {
            const response = await fetch('/PETVET/api/products/add.php', {
                method: 'POST',
                body: formData
            });
            
            console.log('Response status:', response.status);
            
            const result = await response.json();
            
            console.log('API response:', result);
            
            if (result.success) {
                closeModal(modalAdd);
                showToast('Product added successfully');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Fetch error:', error);
            showToast('Error: ' + error.message);
        }
    });
    
    document.getElementById('editForm')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        
        console.log('Edit form submitted');
        
        const formData = new FormData(this);
        
        // Rename title to name for API
        const title = formData.get('title');
        formData.set('name', title);
        formData.delete('title');
        
        // Handle multiple new image uploads
        const fileInput = document.getElementById('editImageUpload');
        if (fileInput && fileInput.files.length > 0) {
            formData.delete('product_images[]');
            Array.from(fileInput.files).forEach((file, index) => {
                formData.append('product_images[]', file);
            });
            console.log('New image files:', fileInput.files.length);
        }
        
        // Add deleted images list
        formData.set('deleted_images', document.getElementById('deletedImages').value || '[]');
        
        console.log('Product ID:', formData.get('id'));
        console.log('Deleted images:', formData.get('deleted_images'));
        console.log('Sending to API...');
        
        try {
            const response = await fetch('/PETVET/api/products/update.php', {
                method: 'POST',
                body: formData
            });
            
            console.log('Response status:', response.status);
            
            const result = await response.json();
            
            console.log('API response:', result);
            
            if (result.success) {
                closeModal(modalEdit);
                showToast('Product updated successfully');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Fetch error:', error);
            showToast('Error: ' + error.message);
        }
    });

    // Add form image preview
    const addImageUpload = document.getElementById('addImageUpload');
    const addImagePreview = document.getElementById('addImagePreview');
    
    addImageUpload?.addEventListener('change', (e) => {
        if (!addImagePreview) return;
        addImagePreview.innerHTML = '';
        
        const files = Array.from(e.target.files || []);
        
        if (files.length > 5) {
            showToast('Maximum 5 images allowed');
            e.target.value = '';
            return;
        }
        
        files.forEach((file, index) => {
            const url = URL.createObjectURL(file);
            const div = document.createElement('div');
            div.style.cssText = 'position:relative; width:100px; height:100px;';
            div.innerHTML = `
                <img src="${url}" alt="Preview ${index+1}" style="width:100%; height:100%; object-fit:cover; border-radius:8px; border:2px solid #ddd;">
                <span style="position:absolute; top:2px; right:2px; background:rgba(0,0,0,0.6); color:white; padding:2px 6px; border-radius:4px; font-size:11px;">${index+1}</span>
            `;
            addImagePreview.appendChild(div);
        });
    });
    
    // Edit form image preview
    const editImageUpload = document.getElementById('editImageUpload');
    const editImagePreview = document.getElementById('editImagePreview');
    let deletedImagesList = [];
    
    editImageUpload?.addEventListener('change', (e) => {
        if (!editImagePreview) return;
        editImagePreview.innerHTML = '';
        
        const files = Array.from(e.target.files || []);
        const currentCount = document.querySelectorAll('#editCurrentImages .current-image-item').length - deletedImagesList.length;
        
        if (currentCount + files.length > 5) {
            showToast(`Maximum 5 images total. You can add ${5 - currentCount} more image(s).`);
            e.target.value = '';
            return;
        }
        
        files.forEach((file, index) => {
            const url = URL.createObjectURL(file);
            const div = document.createElement('div');
            div.style.cssText = 'position:relative; width:100px; height:100px;';
            div.innerHTML = `
                <img src="${url}" alt="New ${index+1}" style="width:100%; height:100%; object-fit:cover; border-radius:8px; border:2px solid #4CAF50;">
                <span style="position:absolute; top:2px; right:2px; background:rgba(76,175,80,0.9); color:white; padding:2px 6px; border-radius:4px; font-size:11px;">NEW</span>
            `;
            editImagePreview.appendChild(div);
        });
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

    const escapeHtml = (value) => {
        const s = String(value ?? '');
        return s
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };

    const formatPrettyDate = (isoOrDateTime) => {
        const d = new Date(isoOrDateTime);
        if (Number.isNaN(d.getTime())) return String(isoOrDateTime ?? '');
        return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: '2-digit' });
    };

    // View Items button handlers
    $$('.btn-view-cart').forEach(btn => {
        btn.addEventListener('click', () => {
            const items = JSON.parse(btn.getAttribute('data-items') || '[]');
            const orderId = btn.getAttribute('data-order-id');
            const orderNumber = btn.getAttribute('data-order-number') || '';
            const customer = btn.getAttribute('data-customer') || '';
            const phone = btn.getAttribute('data-phone') || 'N/A';
            const dateRaw = btn.getAttribute('data-date') || '';
            const createdPretty = formatPrettyDate(dateRaw);

            const detailsTable = document.getElementById('cartOrderDetails');
            const itemsTbody = document.getElementById('cartItemsTbody');
            const totalCell = document.getElementById('cartTotalCell');
            const subtitle = document.getElementById('cartModalSubtitle');
            if (!detailsTable || !itemsTbody || !totalCell) return;

            if (subtitle) subtitle.textContent = orderNumber ? `Order ${orderNumber}` : 'Order details and item list';

            // Render items
            itemsTbody.innerHTML = '';
            let total = 0;
            items.forEach((item) => {
                const qty = Number(item.quantity || 0);
                const unit = Number(item.price || 0);
                const lineTotal = qty * unit;
                total += lineTotal;

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <div class="pv-item-name">${escapeHtml(item.product_name ?? 'Item')}</div>
                    </td>
                    <td class="pv-right">${escapeHtml(qty)}</td>
                    <td class="pv-right">LKR ${unit.toFixed(2)}</td>
                    <td class="pv-right"><strong>LKR ${lineTotal.toFixed(2)}</strong></td>
                `;
                itemsTbody.appendChild(tr);
            });
            if (items.length === 0) {
                const tr = document.createElement('tr');
                tr.innerHTML = '<td colspan="4" class="pv-empty">No items found for this order.</td>';
                itemsTbody.appendChild(tr);
            }

            totalCell.textContent = `LKR ${total.toFixed(2)}`;

            // Render details table
            const statusHtml = '<span class="pv-pill pv-pill--pending">Confirmed</span>';
            detailsTable.querySelector('tbody').innerHTML = `
                <tr>
                    <th>Order #</th>
                    <td><span class="pv-mono">${escapeHtml(orderNumber)}</span></td>
                    <th>Date</th>
                    <td>${escapeHtml(createdPretty)}</td>
                </tr>
                <tr>
                    <th>Customer</th>
                    <td>${escapeHtml(customer)}</td>
                    <th>Phone</th>
                    <td>${escapeHtml(phone)}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>${statusHtml}</td>
                    <th>Total</th>
                    <td><strong class="pv-money">LKR ${total.toFixed(2)}</strong></td>
                </tr>
            `;

            openModal(modalCart);
        });
    });

    // Mark as Delivered button handlers
    $$('.btn-mark-delivered').forEach(btn => {
        btn.addEventListener('click', async () => {
            const orderId = btn.getAttribute('data-order-id');
            const orderNumber = btn.getAttribute('data-order-number');
            
            if (!confirm(`Mark order ${orderNumber} as delivered?`)) return;
            
            try {
                const formData = new FormData();
                formData.append('order_id', orderId);
                
                const response = await fetch('/PETVET/api/clinic-manager/mark-order-delivered.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Mark row as delivered
                    const row = btn.closest('tr');
                    if (row) {
                        row.classList.add('delivered');
                        btn.disabled = true;
                        btn.textContent = 'Delivered';
                    }
                    
                    // Update badge count
                    const badge = $('.order-badge');
                    if (badge) {
                        const count = Math.max(0, parseInt(badge.textContent || '0') - 1);
                        if (count > 0) {
                            badge.textContent = count;
                        } else {
                            badge.remove();
                        }
                    }
                    
                    showToast('Order marked as delivered');
                } else {
                    showToast('Failed to update order: ' + (result.message || 'Unknown error'));
                }
            } catch (error) {
                showToast('Error: ' + error.message);
            }
        });
    });

    // Keyboard ESC to close any open modal
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            [modalAdd, modalEdit, modalDelete, modalOrders, modalCart].forEach(m => m && closeModal(m));
        }
    });
    </script>
</body>
</html>