<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'groomer';
$GLOBALS['currentPage'] = 'services.php';
$GLOBALS['module'] = 'groomer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Services - Groomer - PetVet</title>
<link rel="stylesheet" href="/PETVET/public/css/groomer/services.css">
<link rel="stylesheet" href="/PETVET/public/css/shared/confirm-modal.css">
</head>
<body>
<?php include __DIR__ . '/../shared/sidebar/sidebar.php'; ?>
<main class="main-content">
<div class="page-wrap">
<div class="services-header">
<div>
<h1>My Services</h1>
<p class="muted">Manage your grooming services and pricing</p>
</div>
<button class="btn primary" id="addServiceBtn">+ Add New Service</button>
</div>

<div class="services-grid">
<?php if (!empty($services)): ?>
<?php foreach ($services as $service): ?>
<div class="service-card <?php echo $service['available'] ? '' : 'unavailable'; ?>" data-service-id="<?php echo $service['id']; ?>">
<div class="service-header">
<h3 class="service-name"><?php echo htmlspecialchars($service['name']); ?></h3>
<div class="service-actions">
<button class="btn-icon edit" title="Edit Service" data-action="edit">✏️</button>
<button class="btn-icon delete" title="Delete Service" data-action="delete">🗑️</button>
</div>
</div>
<div class="service-body">
<p class="service-description"><?php echo htmlspecialchars($service['description']); ?></p>
<div class="service-meta">
<div class="meta-item">
<span class="meta-label">Duration:</span>
<span class="meta-value"><?php echo htmlspecialchars($service['duration']); ?></span>
</div>
<div class="meta-item">
<span class="meta-label">Price:</span>
<span class="meta-value price">LKR <?php echo number_format($service['price'], 2); ?></span>
</div>
</div>
<div class="pet-types">
<span class="pet-type-label">Available for:</span>
<div class="pet-type-badges">
<?php if ($service['for_dogs']): ?>
<span class="badge dog">🐕 Dogs</span>
<?php endif; ?>
<?php if ($service['for_cats']): ?>
<span class="badge cat">🐈 Cats</span>
<?php endif; ?>
</div>
</div>
</div>
<div class="service-footer">
<label class="toggle-switch">
<input type="checkbox" <?php echo $service['available'] ? 'checked' : ''; ?> data-action="toggle">
<span class="toggle-slider"></span>
<span class="toggle-label"><?php echo $service['available'] ? 'Available' : 'Unavailable'; ?></span>
</label>
</div>
</div>
<?php endforeach; ?>
<?php else: ?>
<div class="empty-state">
<div class="empty-icon">✂️</div>
<h3>No Services Yet</h3>
<p>Start adding your grooming services to attract more clients</p>
<button class="btn primary" id="addFirstService">Add Your First Service</button>
</div>
<?php endif; ?>
</div>
</div>
</main>

<!-- Add/Edit Service Modal -->
<div id="serviceModal" class="modal">
<div class="modal-content">
<div class="modal-header">
<h2 id="modalTitle">Add New Service</h2>
<button class="modal-close" id="closeModal">&times;</button>
</div>
<form id="serviceForm" class="modal-body">
<input type="hidden" id="serviceId" name="service_id">
<div class="form-group">
<label for="serviceName">Service Name *</label>
<input type="text" id="serviceName" name="name" required placeholder="e.g., Bath & Brush">
</div>
<div class="form-group">
<label for="serviceDescription">Description</label>
<textarea id="serviceDescription" name="description" rows="3" placeholder="Describe your service..."></textarea>
</div>
<div class="form-row">
<div class="form-group">
<label for="servicePrice">Price (LKR) *</label>
<input type="number" id="servicePrice" name="price" step="0.01" min="0" required placeholder="0.00">
</div>
<div class="form-group">
<label for="serviceDuration">Duration</label>
<input type="text" id="serviceDuration" name="duration" placeholder="e.g., 45 min">
</div>
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
<button type="submit" class="btn primary" id="saveBtn">Save Service</button>
</div>
</form>
</div>
</div>

<div id="toast" class="toast"></div>
<script src="/PETVET/public/js/shared/confirm-modal.js"></script>
<script src="/PETVET/public/js/groomer/services.js"></script>
</body>
</html>
