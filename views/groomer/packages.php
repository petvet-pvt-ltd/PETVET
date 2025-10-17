<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'groomer';
$GLOBALS['currentPage'] = 'packages.php';
$GLOBALS['module'] = 'groomer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Packages - Groomer - PetVet</title>
<link rel="stylesheet" href="/PETVET/public/css/groomer/packages.css">
<link rel="stylesheet" href="/PETVET/public/css/shared/confirm-modal.css">
</head>
<body>
<?php include __DIR__ . '/../shared/sidebar/sidebar.php'; ?>
<main class="main-content">
<div class="page-wrap">
<div class="packages-header">
<div>
<h1>My Packages</h1>
<p class="muted">Create combo packages with discounted pricing</p>
</div>
<button class="btn primary" id="addPackageBtn">+ Add New Package</button>
</div>

<div class="packages-grid">
<?php if (!empty($packages)): ?>
<?php foreach ($packages as $package): ?>
<div class="package-card <?php echo $package['available'] ? '' : 'unavailable'; ?>" data-package-id="<?php echo $package['id']; ?>">
<div class="package-ribbon">
<span class="discount-badge">Save <?php echo number_format($package['discount_percent'], 1); ?>%</span>
</div>
<div class="package-header">
<h3 class="package-name"><?php echo htmlspecialchars($package['name']); ?></h3>
<div class="package-actions">
<button class="btn-icon edit" title="Edit Package" data-action="edit">✏️</button>
<button class="btn-icon delete" title="Delete Package" data-action="delete">🗑️</button>
</div>
</div>
<div class="package-body">
<p class="package-description"><?php echo htmlspecialchars($package['description']); ?></p>
<div class="included-services">
<h4>Includes:</h4>
<ul>
<?php 
$services = explode(',', $package['included_services']);
foreach ($services as $service): ?>
<li>✓ <?php echo trim(htmlspecialchars($service)); ?></li>
<?php endforeach; ?>
</ul>
</div>
<div class="package-pricing">
<div class="original-price">
<span class="label">Regular:</span>
<span class="value crossed">LKR <?php echo number_format($package['original_price'], 2); ?></span>
</div>
<div class="discounted-price">
<span class="label">Package Price:</span>
<span class="value">LKR <?php echo number_format($package['discounted_price'], 2); ?></span>
</div>
</div>
<div class="package-meta">
<div class="meta-item">
<span class="meta-icon">⏱️</span>
<span class="meta-value"><?php echo htmlspecialchars($package['duration']); ?></span>
</div>
<div class="pet-types">
<?php if ($package['for_dogs']): ?>
<span class="badge dog">🐕 Dogs</span>
<?php endif; ?>
<?php if ($package['for_cats']): ?>
<span class="badge cat">🐈 Cats</span>
<?php endif; ?>
</div>
</div>
</div>
<div class="package-footer">
<label class="toggle-switch">
<input type="checkbox" <?php echo $package['available'] ? 'checked' : ''; ?> data-action="toggle">
<span class="toggle-slider"></span>
<span class="toggle-label"><?php echo $package['available'] ? 'Available' : 'Unavailable'; ?></span>
</label>
</div>
</div>
<?php endforeach; ?>
<?php else: ?>
<div class="empty-state">
<div class="empty-icon">📦</div>
<h3>No Packages Yet</h3>
<p>Create combo packages to offer better value to your clients</p>
<button class="btn primary" id="addFirstPackage">Create Your First Package</button>
</div>
<?php endif; ?>
</div>
</div>
</main>

<!-- Add/Edit Package Modal -->
<div id="packageModal" class="modal">
<div class="modal-content">
<div class="modal-header">
<h2 id="modalTitle">Add New Package</h2>
<button class="modal-close" id="closeModal">&times;</button>
</div>
<form id="packageForm" class="modal-body">
<input type="hidden" id="packageId" name="package_id">
<div class="form-group">
<label for="packageName">Package Name *</label>
<input type="text" id="packageName" name="name" required placeholder="e.g., Complete Care Package">
</div>
<div class="form-group">
<label for="packageDescription">Description</label>
<textarea id="packageDescription" name="description" rows="2" placeholder="Describe your package..."></textarea>
</div>
<div class="form-group">
<label for="includedServices">Included Services *</label>
<textarea id="includedServices" name="included_services" rows="3" required placeholder="List services separated by commas&#10;e.g., Bath & Brush, Full Grooming, Nail Trim"></textarea>
</div>
<div class="form-row">
<div class="form-group">
<label for="originalPrice">Regular Price (LKR) *</label>
<input type="number" id="originalPrice" name="original_price" step="0.01" min="0" required placeholder="0.00">
</div>
<div class="form-group">
<label for="discountedPrice">Package Price (LKR) *</label>
<input type="number" id="discountedPrice" name="discounted_price" step="0.01" min="0" required placeholder="0.00">
</div>
</div>
<div class="discount-preview">
<span id="discountPercent">Discount: 0%</span>
<span id="savings">You save: $0.00</span>
</div>
<div class="form-group">
<label for="packageDuration">Duration</label>
<input type="text" id="packageDuration" name="duration" placeholder="e.g., 2 hours">
</div>
<div class="form-group">
<label>Available For *</label>
<div class="pet-type-toggles">
<label class="pet-toggle">
<input type="checkbox" id="forDogs" name="for_dogs" value="true">
<span class="pet-toggle-btn">
<span class="icon">🐕</span>
<span>Dogs</span>
</span>
</label>
<label class="pet-toggle">
<input type="checkbox" id="forCats" name="for_cats" value="true">
<span class="pet-toggle-btn">
<span class="icon">🐈</span>
<span>Cats</span>
</span>
</label>
</div>
</div>
<div class="modal-footer">
<button type="button" class="btn outline" id="cancelBtn">Cancel</button>
<button type="submit" class="btn primary" id="saveBtn">Save Package</button>
</div>
</form>
</div>
</div>

<div id="toast" class="toast"></div>
<script src="/PETVET/public/js/shared/confirm-modal.js"></script>
<script src="/PETVET/public/js/groomer/packages.js"></script>
</body>
</html>
