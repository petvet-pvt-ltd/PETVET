<?php 
/* Services Discovery Page - Browse and filter service providers */ 
$GLOBALS['module'] = 'pet-owner';
$GLOBALS['currentPage'] = 'services.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - PetVet</title>
    <link rel="stylesheet" href="/PETVET/public/css/shared/bookings.css">
    <link rel="stylesheet" href="/PETVET/public/css/pet-owner/services.css">

    <!-- Leaflet (used for map location selection) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
</head>
<body>
    <?php include __DIR__ . '/../shared/sidebar/sidebar.php'; ?>
    
    <div class="main-content">
        <?php
        // Data from controller
        $serviceType = $serviceType ?? 'trainers';
        $providers = $providers ?? [];
        $cities = $cities ?? [];
        $filters = $filters ?? [];

        // Active booking provider IDs (used to prevent duplicate bookings)
        $activeBookingProviderIds = $activeBookingProviderIds ?? ['trainers' => [], 'sitters' => [], 'breeders' => []];
        $activeTrainerProviderIds = array_map('intval', $activeBookingProviderIds['trainers'] ?? []);
        $activeSitterProviderIds = array_map('intval', $activeBookingProviderIds['sitters'] ?? []);
        $activeBreederProviderIds = array_map('intval', $activeBookingProviderIds['breeders'] ?? []);
        
        // Service type labels
        $serviceLabels = [
            'trainers' => 'Trainers',
            'sitters' => 'Sitters',
            'breeders' => 'Breeders',
            'groomers' => 'Groomers'
        ];
        ?>
        
        <header class="page-header">
            <div class="title-wrap">
                <h2>Discover Pet Services</h2>
                <p class="subtitle">Find professionals for your pet care needs</p>
            </div>

            <div class="header-actions">
                <button type="button" class="btn outline owner-bookings-btn" onclick="openOwnerBookingsModal()">My Bookings</button>
            </div>
        </header>

        <!-- Service Type Selection -->
        <section class="service-selector">
            <button class="service-btn <?= $serviceType === 'trainers' ? 'active' : '' ?>" 
                    data-service="trainers"
                    onclick="changeService('trainers')">
                <div class="service-icon">🎓</div>
                <div class="service-label">Trainers</div>
            </button>
            <button class="service-btn <?= $serviceType === 'sitters' ? 'active' : '' ?>" 
                    data-service="sitters"
                    onclick="changeService('sitters')">
                <div class="service-icon">🏠</div>
                <div class="service-label">Sitters</div>
            </button>
            <button class="service-btn <?= $serviceType === 'breeders' ? 'active' : '' ?>" 
                    data-service="breeders"
                    onclick="changeService('breeders')">
                <div class="service-icon">🐾</div>
                <div class="service-label">Breeders</div>
            </button>
            <button class="service-btn <?= $serviceType === 'groomers' ? 'active' : '' ?>" 
                    data-service="groomers"
                    onclick="changeService('groomers')">
                <div class="service-icon">✂️</div>
                <div class="service-label">Groomers</div>
            </button>
        </section>

        <!-- Mobile Filter Toggle Button -->
        <button class="mobile-filter-toggle" id="mobileFilterToggle" onclick="toggleMobileFilters()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
            </svg>
            <span class="filter-badge" id="filterBadge" style="display:none;">0</span>
        </button>

        <!-- Filters Section -->
        <section class="filters-section" id="filtersSection">
            <div class="filters-header">
                <h3>Filter Options</h3>
                <button type="button" class="filters-close" onclick="toggleMobileFilters()">&times;</button>
            </div>
            <form id="filterForm" method="GET" action="/PETVET/index.php">
                <input type="hidden" name="module" value="pet-owner">
                <input type="hidden" name="page" value="services">
                <input type="hidden" name="type" id="serviceTypeInput" value="<?= htmlspecialchars($serviceType) ?>">
                
                <!-- Common Filters -->
                <div class="filters-row">
                    <div class="filter-group">
                        <label for="searchInput">Search</label>
                        <input type="text" 
                               id="searchInput" 
                               name="search" 
                               placeholder="Search by name, location..." 
                               value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label for="cityFilter">City</label>
                        <select id="cityFilter" name="city">
                            <option value="">All Cities</option>
                            <?php foreach($cities as $city): ?>
                                <option value="<?= htmlspecialchars($city) ?>" 
                                        <?= ($filters['city'] ?? '') === $city ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($city) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="experienceFilter">Experience</label>
                        <select id="experienceFilter" name="experience">
                            <option value="">Any Experience</option>
                            <option value="5" <?= ($filters['experience'] ?? '') == '5' ? 'selected' : '' ?>>5+ Years</option>
                            <option value="3" <?= ($filters['experience'] ?? '') == '3' ? 'selected' : '' ?>>3+ Years</option>
                            <option value="1" <?= ($filters['experience'] ?? '') == '1' ? 'selected' : '' ?>>1+ Year</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="sortFilter">Sort</label>
                        <?php
                            $sortValue = $filters['sort'] ?? '';
                            if ($sortValue === '') {
                                $sortValue = ($serviceType === 'trainers') ? 'az' : 'nearest';
                            }
                        ?>
                        <select id="sortFilter" name="sort">
                            <?php if ($serviceType !== 'trainers'): ?>
                                <option value="nearest" <?= $sortValue === 'nearest' ? 'selected' : '' ?>>Nearest</option>
                            <?php endif; ?>
                            <option value="az" <?= $sortValue === 'az' ? 'selected' : '' ?>>A-Z</option>
                        </select>
                    </div>
                </div>

                <!-- Service-Specific Filters -->
                <div class="filters-row specific-filters" id="trainerFilters" <?= $serviceType !== 'trainers' ? 'style="display:none;"' : '' ?>>
                    <div class="filter-group">
                        <label for="trainingTypeFilter">Training Type</label>
                        <select id="trainingTypeFilter" name="training_type">
                            <option value="">All Training Types</option>
                            <option value="Basic" <?= ($filters['training_type'] ?? '') === 'Basic' ? 'selected' : '' ?>>Basic Training</option>
                            <option value="Intermediate" <?= ($filters['training_type'] ?? '') === 'Intermediate' ? 'selected' : '' ?>>Intermediate Training</option>
                            <option value="Advanced" <?= ($filters['training_type'] ?? '') === 'Advanced' ? 'selected' : '' ?>>Advanced Training</option>
                        </select>
                    </div>
                </div>

                <div class="filters-row specific-filters" id="sitterFilters" <?= $serviceType !== 'sitters' ? 'style="display:none;"' : '' ?>>
                    <div class="filter-group">
                        <label for="petTypeFilter">Pet Type</label>
                        <select id="petTypeFilter" name="pet_type">
                            <option value="">All Pet Types</option>
                            <option value="Dogs" <?= ($filters['pet_type'] ?? '') === 'Dogs' ? 'selected' : '' ?>>Dogs</option>
                            <option value="Cats" <?= ($filters['pet_type'] ?? '') === 'Cats' ? 'selected' : '' ?>>Cats</option>
                            <option value="Birds" <?= ($filters['pet_type'] ?? '') === 'Birds' ? 'selected' : '' ?>>Birds</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="homeTypeFilter">Home Type</label>
                        <select id="homeTypeFilter" name="home_type">
                            <option value="">Any Home Type</option>
                            <option value="House with Yard" <?= ($filters['home_type'] ?? '') === 'House with Yard' ? 'selected' : '' ?>>House with Yard</option>
                            <option value="Apartment" <?= ($filters['home_type'] ?? '') === 'Apartment' ? 'selected' : '' ?>>Apartment</option>
                        </select>
                    </div>
                </div>

                <div class="filters-row specific-filters" id="breederFilters" <?= $serviceType !== 'breeders' ? 'style="display:none;"' : '' ?>>
                    <div class="filter-group">
                        <label for="breedFilter">Breed</label>
                        <input type="text" 
                               id="breedFilter" 
                               name="breed" 
                               placeholder="e.g., Golden Retriever" 
                               value="<?= htmlspecialchars($filters['breed'] ?? '') ?>">
                    </div>
                    <div class="filter-group">
                        <label for="genderFilter">Gender</label>
                        <select id="genderFilter" name="gender">
                            <option value="">Any Gender</option>
                            <option value="Male" <?= ($filters['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= ($filters['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                        </select>
                    </div>
                </div>

                <div class="filters-row specific-filters" id="groomerFilters" <?= $serviceType !== 'groomers' ? 'style="display:none;"' : '' ?>>
                    <div class="filter-group">
                        <label for="showFilter">Show</label>
                        <select id="showFilter" name="show" onchange="toggleGroomerServiceType(this.value)">
                            <option value="providers" <?= ($filters['show'] ?? 'providers') === 'providers' ? 'selected' : '' ?>>Providers</option>
                            <option value="services" <?= ($filters['show'] ?? '') === 'services' ? 'selected' : '' ?>>Services</option>
                            <option value="packages" <?= ($filters['show'] ?? '') === 'packages' ? 'selected' : '' ?>>Packages</option>
                        </select>
                    </div>
                </div>

                <div class="filters-actions">
                    <button type="submit" class="btn primary">Apply Filters</button>
                    <button type="button" class="btn outline" onclick="clearFilters()">Clear All</button>
                </div>
            </form>
        </section>
        <div class="filters-overlay" id="filtersOverlay" onclick="toggleMobileFilters()"></div>

        <!-- Results Section -->
        <section class="results-section">
            <div class="results-header">
                <?php if ($serviceType === 'groomers' && !empty($filters['groomer_id']) && !empty($providers)): ?>
                    <?php 
                    // Get groomer name from first provider
                    $groomerName = $providers[0]['groomer_business'] ?? $providers[0]['groomer_name'] ?? 'Groomer';
                    ?>
                    <h3><?= htmlspecialchars($groomerName) ?>'s Services & Packages (<?= count($providers) ?>)</h3>
                    <p style="font-size: 14px; color: var(--muted); margin-top: 8px;">
                        <a href="?module=pet-owner&page=services&type=groomers" style="color: var(--primary); text-decoration: none;">← Back to all groomers</a>
                    </p>
                <?php else: ?>
                    <h3><?= count($providers) ?> <?= $serviceLabels[$serviceType] ?> Found</h3>
                <?php endif; ?>
            </div>

            <div class="providers-grid">
                <?php if (empty($providers)): ?>
                    <div class="no-results">
                        <div class="no-results-icon">🔍</div>
                        <h3>No providers found</h3>
                        <p>Try adjusting your filters or search for a different service type.</p>
                    </div>
                <?php else: ?>
                    <?php 
                    // Check if we're showing services/packages or providers
                    $showingServices = $serviceType === 'groomers' && !empty($filters['show']) && ($filters['show'] === 'services' || $filters['show'] === 'packages');
                    ?>
                    
                    <?php foreach($providers as $provider): ?>
                        <?php if ($showingServices): ?>
                            <!-- Display Service/Package Card -->
                            <?php 
                            $isPackage = !empty($provider['package_name']) || !empty($provider['services_included']);
                            ?>
                            
                            <?php if ($isPackage): ?>
                                <!-- Package Card (Compact Version) -->
                                <article class="package-card <?= (!empty($provider['available']) && $provider['available']) ? '' : 'unavailable' ?>" data-groomer-id="<?= (int)($provider['groomer_id'] ?? 0) ?>" data-sort-name="<?= htmlspecialchars($provider['package_name'] ?? 'Package') ?>">
                                    <?php if (!empty($provider['discount_percent']) && $provider['discount_percent'] > 0): ?>
                                        <div class="package-ribbon">
                                            <span class="discount-badge">Save <?= number_format($provider['discount_percent'], 1) ?>%</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="package-header">
                                        <h3 class="package-name"><?= htmlspecialchars($provider['package_name'] ?? $provider['service_name'] ?? 'Package') ?></h3>
                                    </div>
                                    
                                    <div class="package-body">
                                        <p class="package-description"><?= htmlspecialchars($provider['description']) ?></p>
                                        
                                        <?php if (!empty($provider['services_included'])): ?>
                                            <div class="included-services">
                                                <h4>Includes:</h4>
                                                <ul>
                                                    <?php 
                                                    $services = explode(',', $provider['services_included']);
                                                    foreach ($services as $service): ?>
                                                        <li><?= trim(htmlspecialchars($service)) ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="package-pricing">
                                            <?php if (!empty($provider['original_price']) && $provider['original_price'] > $provider['price']): ?>
                                                <div class="original-price">
                                                    <span class="label">Regular:</span>
                                                    <span class="value crossed">LKR <?= number_format($provider['original_price'], 2) ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <div class="discounted-price">
                                                <span class="label">Package Price:</span>
                                                <span class="value">LKR <?= number_format($provider['price'], 2) ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="package-meta">
                                            <?php if (!empty($provider['duration'])): ?>
                                                <div class="meta-item">
                                                    <span class="meta-icon">⏱️</span>
                                                    <span class="meta-value"><?= htmlspecialchars($provider['duration']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <div class="pet-types">
                                                <?php if (!empty($provider['for_dogs'])): ?>
                                                    <span class="badge dog">🐕 Dogs</span>
                                                <?php endif; ?>
                                                <?php if (!empty($provider['for_cats'])): ?>
                                                    <span class="badge cat">🐈 Cats</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Provider Info -->
                                        <div class="provider-info-compact">
                                            <img src="<?= htmlspecialchars($provider['groomer_avatar']) ?>" 
                                                 alt="<?= htmlspecialchars($provider['groomer_business'] ?? $provider['groomer_name']) ?>" 
                                                 class="provider-avatar-small">
                                            <div class="provider-details-small">
                                                <p class="provider-name-small"><?= htmlspecialchars($provider['groomer_business'] ?? $provider['groomer_name']) ?></p>
                                                <p class="provider-location-small">📍 <?= htmlspecialchars($provider['groomer_city']) ?></p>
                                            </div>
                                        </div>
										<?php if (!empty($provider['groomer_id'])): ?>
											<span class="clinic-distance groomer-distance" data-groomer-id="<?= (int)$provider['groomer_id'] ?>">
												<span class="distance-loader">⏳ Calculating...</span>
											</span>
									<?php endif; ?>
                                    </div>
                                    
                                    <div class="package-footer">
                                        <button class="btn-contact-service" 
                                                onclick="showContactModal('<?= htmlspecialchars($provider['groomer_business'] ?? $provider['groomer_name']) ?>', '<?= htmlspecialchars($provider['groomer_phone']) ?>', '')">
                                            Contact
                                        </button>
                                    </div>
                                </article>
                            <?php else: ?>
                                <!-- Single Service Card (Compact Version) -->
                                <article class="service-card <?= (!empty($provider['available']) && $provider['available']) ? '' : 'unavailable' ?>" data-groomer-id="<?= (int)($provider['groomer_id'] ?? 0) ?>" data-sort-name="<?= htmlspecialchars($provider['service_name'] ?? 'Service') ?>">
                                    <div class="service-header">
                                        <h3 class="service-name"><?= htmlspecialchars($provider['service_name'] ?? 'Service') ?></h3>
                                    </div>
                                    
                                    <div class="service-body">
                                        <p class="service-description"><?= htmlspecialchars($provider['description']) ?></p>
                                        
                                        <div class="service-meta">
                                            <?php if (!empty($provider['duration'])): ?>
                                                <div class="meta-item">
                                                    <span class="meta-label">Duration:</span>
                                                    <span class="meta-value"><?= htmlspecialchars($provider['duration']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <div class="meta-item">
                                                <span class="meta-label">Price:</span>
                                                <span class="meta-value price">LKR <?= number_format($provider['price'], 2) ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="pet-types">
                                            <span class="pet-type-label">Available for:</span>
                                            <div class="pet-type-badges">
                                                <?php if (!empty($provider['for_dogs'])): ?>
                                                    <span class="badge dog">🐕 Dogs</span>
                                                <?php endif; ?>
                                                <?php if (!empty($provider['for_cats'])): ?>
                                                    <span class="badge cat">🐈 Cats</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Provider Info -->
                                        <div class="provider-info-compact">
                                            <img src="<?= htmlspecialchars($provider['groomer_avatar']) ?>" 
                                                 alt="<?= htmlspecialchars($provider['groomer_business'] ?? $provider['groomer_name']) ?>" 
                                                 class="provider-avatar-small">
                                            <div class="provider-details-small">
                                                <p class="provider-name-small"><?= htmlspecialchars($provider['groomer_business'] ?? $provider['groomer_name']) ?></p>
                                                <p class="provider-location-small">📍 <?= htmlspecialchars($provider['groomer_city']) ?></p>
                                            </div>
                                        </div>
										<?php if (!empty($provider['groomer_id'])): ?>
											<span class="clinic-distance groomer-distance" data-groomer-id="<?= (int)$provider['groomer_id'] ?>">
												<span class="distance-loader">⏳ Calculating...</span>
											</span>
									<?php endif; ?>
                                    </div>
                                    
                                    <div class="service-footer">
                                        <button class="btn-contact-service" 
                                                onclick="showContactModal('<?= htmlspecialchars($provider['groomer_business'] ?? $provider['groomer_name']) ?>', '<?= htmlspecialchars($provider['groomer_phone']) ?>', '')">
                                            Contact
                                        </button>
                                    </div>
                                </article>
                            <?php endif; ?>
                        <?php else: ?>
                            <!-- Display Provider Card -->
                            <?php
                            $providerSortName = $provider['name'] ?? '';
                            if ($serviceType === 'groomers') {
                                $providerSortName = $provider['business_name'] ?? ($provider['name'] ?? '');
                            }
                            ?>
                            <article class="provider-card <?= $serviceType === 'trainers' ? 'trainer-card' : '' ?>" data-groomer-id="<?= $serviceType === 'groomers' ? (int)$provider['id'] : 0 ?>" data-sitter-id="<?= $serviceType === 'sitters' ? (int)$provider['id'] : 0 ?>" data-breeder-id="<?= $serviceType === 'breeders' ? (int)$provider['id'] : 0 ?>" data-sort-name="<?= htmlspecialchars($providerSortName) ?>">
                                <div class="provider-header">
                                    <?php if ($serviceType === 'groomers'): ?>
                                        <span class="clinic-distance groomer-distance" data-groomer-id="<?= (int)$provider['id'] ?>">
                                            <span class="distance-loader">⏳ Calculating...</span>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($serviceType === 'sitters'): ?>
                                        <span class="clinic-distance sitter-distance" data-sitter-id="<?= (int)$provider['id'] ?>">
                                            <span class="distance-loader">⏳ Calculating...</span>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($serviceType === 'breeders'): ?>
                                        <span class="clinic-distance breeder-distance" data-breeder-id="<?= (int)$provider['id'] ?>">
                                            <span class="distance-loader">⏳ Calculating...</span>
                                        </span>
                                    <?php endif; ?>
                                    <div class="provider-avatar">
                                        <?php
                                        $providerImg = $provider['avatar'] ?? '/PETVET/public/images/default-avatar.png';
                                        if ($serviceType === 'groomers') {
                                            $providerImg = !empty($provider['business_logo'])
                                                ? $provider['business_logo']
                                                : ($provider['avatar'] ?? '/PETVET/public/images/emptyProfPic.png');
                                        }
                                        ?>
                                        <img src="<?= htmlspecialchars($providerImg) ?>" 
                                             alt="<?= htmlspecialchars($provider['name']) ?>">
                                    </div>
                                    <div class="provider-info">
                                        <?php if ($serviceType === 'groomers'): ?>
                                            <h4 class="provider-name"><?= htmlspecialchars($provider['business_name'] ?? $provider['name']) ?></h4>
                                            <?php if (!empty($provider['name']) && !empty($provider['business_name'])): ?>
                                                <p class="business-name">Owner: <?= htmlspecialchars($provider['name']) ?></p>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <h4 class="provider-name"><?= htmlspecialchars($provider['name']) ?></h4>
                                            <?php if (!empty($provider['business_name'])): ?>
                                                <p class="business-name"><?= htmlspecialchars($provider['business_name']) ?></p>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <div class="provider-meta">
                                            <?php if ($serviceType !== 'trainers' && !empty($provider['city'])): ?>
                                                <span class="location">📍 <?= htmlspecialchars($provider['city']) ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($provider['experience_years'])): ?>
                                                <span class="experience">⭐ <?= (int)$provider['experience_years'] ?> years experience</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="provider-body">
                                    <?php if ($serviceType === 'trainers'): ?>
                                        <?php if (!empty($provider['specialization'])): ?>
                                            <div class="trainer-specialization">
                                                <span class="specialization-icon">🎯</span>
                                                <div class="specialization-content">
                                                    <!-- <span class="specialization-label">Specialization</span> -->
                                                    <span class="specialization-value"><?= htmlspecialchars($provider['specialization']) ?></span>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php
                                        $areas = $provider['service_areas'] ?? [];
                                        if (!is_array($areas)) { $areas = []; }
                                        $areas = array_values(array_filter(array_map('strval', $areas)));
                                        ?>
                                        <?php if (!empty($areas)): ?>
                                            <div class="work-areas">
                                                <span class="work-areas-label" aria-label="Working areas">
                                                    <!-- <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                                                        <path d="M12 22s7-4.5 7-11a7 7 0 1 0-14 0c0 6.5 7 11 7 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M12 14a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg> -->
                                                </span>
                                                <div class="work-areas-chips">
                                                    <?php foreach (array_slice($areas, 0, 3) as $a): ?>
                                                        <span class="area-chip"><?= htmlspecialchars($a) ?></span>
                                                    <?php endforeach; ?>
                                                    <?php if (count($areas) > 3): ?>
                                                        <span class="area-chip more">+<?= (int)(count($areas) - 3) ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Training Types Offered -->
                                        <div class="training-types-offered">
                                            <!-- <span class="training-types-label">Training Types:</span> -->
                                            <div class="training-types-list">
                                                <?php 
                                                // Assume all trainers offer all types by default
                                                $trainingTypes = $provider['training_types'] ?? ['Basic', 'Intermediate', 'Advanced'];
                                                foreach ($trainingTypes as $type): 
                                                ?>
                                                    <span class="training-type-badge training-type-<?= strtolower($type) ?>"><?= htmlspecialchars($type) ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($serviceType === 'sitters'): ?>
                                        <?php if (!empty($provider['description'])): ?>
                                            <div class="sitter-description">
                                                <p><?= htmlspecialchars($provider['description']) ?></p>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($provider['pet_types'])): ?>
                                            <p class="pet-types"><strong>Pet Types:</strong> <?= htmlspecialchars($provider['pet_types']) ?></p>
                                        <?php endif; ?>

                                        <?php if (!empty($provider['home_type_display'])): ?>
                                            <p class="home-type"><strong>Home:</strong> <?= htmlspecialchars($provider['home_type_display']) ?></p>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if ($serviceType === 'breeders'): ?>
                                        <?php if (!empty($provider['specialization'])): ?>
                                            <div class="breeder-specialization">
                                                <span class="specialization-icon">🏆</span>
                                                <div class="specialization-content">
                                                    <span class="specialization-label">Specialization</span>
                                                    <span class="specialization-value"><?= htmlspecialchars($provider['specialization']) ?></span>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($provider['description'])): ?>
                                            <div class="breeder-description">
                                                <p class="description-text"><?= htmlspecialchars($provider['description']) ?></p>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($provider['breeding_pets'])): ?>
                                            <?php
                                            // Process active breeding pets
                                            $activePets = [];
                                            foreach ($provider['breeding_pets'] as $pet) {
                                                if ($pet['is_active']) {
                                                    $key = $pet['breed'] . '_' . $pet['gender'];
                                                    if (!isset($activePets[$key])) {
                                                        $activePets[$key] = [
                                                            'breed' => $pet['breed'],
                                                            'gender' => $pet['gender']
                                                        ];
                                                    }
                                                }
                                            }
                                            ?>
                                            <?php if (!empty($activePets)): ?>
                                                <div class="breeding-pets">
                                                    <div class="breeding-pets-title">Available Breeding Pets:</div>
                                                    <div class="breeding-pets-list">
                                                        <?php foreach ($activePets as $pet): ?>
                                                            <span class="breeding-pet-badge">
                                                                <?= htmlspecialchars($pet['breed']) ?>
                                                                <span class="gender-indicator gender-<?= strtolower($pet['gender']) ?>">
                                                                    <?= $pet['gender'] === 'Male' ? 'M' : 'F' ?>
                                                                </span>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if ($serviceType === 'groomers' && !empty($provider['specializations'])): ?>
                                        <p class="specializations"><strong>Specializations:</strong> <?= htmlspecialchars($provider['specializations']) ?></p>
                                    <?php endif; ?>

                                    <?php if (!empty($provider['certifications'])): ?>
                                        <p class="certifications"><strong>Certifications:</strong> <?= htmlspecialchars($provider['certifications']) ?></p>
                                    <?php endif; ?>
                                </div>

                                <div class="provider-actions">
                                    <?php if ($serviceType === 'trainers'): ?>
                                        <button class="btn primary" onclick='openTrainerDetails(<?= json_encode([
                                            "id" => $provider["id"],
                                            "name" => $provider["name"],
                                            "business_name" => $provider["business_name"] ?? "",
                                            "avatar" => $provider["avatar"] ?? "/PETVET/public/images/default-avatar.png",
                                            "specialization" => $provider["specialization"] ?? "",
                                            "city" => $provider["city"] ?? "",
                                            "service_areas" => $provider["service_areas"] ?? [],
                                            "experience_years" => $provider["experience_years"] ?? 0,
                                            "phone_primary" => $provider["phone_primary"] ?? "",
                                            "bio" => $provider["bio"] ?? "",
                                            "certifications" => $provider["certifications"] ?? "",
                                            "training_basic_enabled" => $provider["training_basic_enabled"] ?? false,
                                            "training_basic_charge" => $provider["training_basic_charge"] ?? 0,
                                            "training_intermediate_enabled" => $provider["training_intermediate_enabled"] ?? false,
                                            "training_intermediate_charge" => $provider["training_intermediate_charge"] ?? 0,
                                            "training_advanced_enabled" => $provider["training_advanced_enabled"] ?? false,
                                            "training_advanced_charge" => $provider["training_advanced_charge"] ?? 0,
                                            "training_types" => $provider["training_types"] ?? ["Basic", "Intermediate", "Advanced"]
                                        ]) ?>, event); return false;'>
                                            View Details
                                        </button>
                                    <?php elseif ($serviceType === 'sitters'): ?>
                                        <?php $isSitterBooked = in_array((int)$provider['id'], $activeSitterProviderIds, true); ?>
                                        <button
                                            class="btn primary sitter-book-btn<?= $isSitterBooked ? ' is-booked' : '' ?>"
                                            data-booking-type="sitter"
                                            data-provider-id="<?= (int)$provider['id'] ?>"
                                            <?= $isSitterBooked ? 'disabled' : '' ?>
                                            onclick='openSitterBooking(<?= json_encode([
                                            "id" => $provider["id"],
                                            "name" => $provider["name"],
                                            "avatar" => $provider["avatar"] ?? "/PETVET/public/images/default-avatar.png",
                                            "pet_types" => $provider["pet_types"] ?? "",
                                            "home_type" => $provider["home_type_display"] ?? "",
                                            "city" => $provider["city"] ?? "",
                                            "experience_years" => $provider["experience_years"] ?? 0
                                        ]) ?>, event); return false;'>
                                            Book Now
                                        </button>
                                    <?php elseif ($serviceType === 'breeders'): ?>
                                        <button class="btn primary" onclick='openBreederDetails(<?= json_encode([
                                            "id" => $provider["id"],
                                            "name" => $provider["name"],
                                            "business_name" => $provider["business_name"] ?? "",
                                            "avatar" => $provider["avatar"] ?? "/PETVET/public/images/default-avatar.png",
                                            "city" => $provider["city"] ?? "",
                                            "phone" => $provider["phone_primary"] ?? "",
                                            "phone2" => $provider["phone_secondary"] ?? "",
                                            "experience_years" => $provider["experience_years"] ?? 0,
                                            "description" => $provider["description"] ?? "",
                                            "breeding_pets" => array_filter($provider["breeding_pets"] ?? [], function($pet) {
                                                return $pet['is_active'];
                                            })
                                        ]) ?>, event); return false;'>
                                            View Details
                                        </button>
                                    <?php elseif ($serviceType === 'groomers'): ?>
                                        <button class="btn primary" onclick="viewGroomerServices(<?= (int)$provider['id'] ?>)">
                                            Services
                                        </button>
                                        <button class="btn primary" onclick="viewGroomerPackages(<?= (int)$provider['id'] ?>)">
                                            Packages
                                        </button>
                                    <?php else: ?>
                                        <button class="btn primary" onclick="viewProvider(<?= (int)$provider['id'] ?>, '<?= $serviceType ?>')">
                                            View Profile
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn outline" onclick='contactProvider(<?= json_encode([
                                        "name" => $provider["name"],
                                        "business_name" => $provider["business_name"] ?? "",
                                        "phone" => $provider["phone_primary"] ?? "",
                                        "phone2" => $provider["phone_secondary"] ?? ""
                                    ]) ?>, event); return false;'>
                                        Contact
                                    </button>
                                </div>
                            </article>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <!-- Contact Modal (match Trainer modal) -->
    <div id="contactModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Contact</h3>
                <button class="modal-close" onclick="closeContactModal()">&times;</button>
            </div>
            <div class="owner-name" id="modalOwnerName"></div>
            <div class="phone-list" id="phoneList"></div>
        </div>
    </div>

    <!-- My Bookings Modal (trainers, sitters, breeders only) -->
    <div id="ownerBookingsModal" class="modal-overlay">
        <div class="modal-content owner-bookings-content">
            <div class="modal-header">
                <h3>My Bookings</h3>
                <button class="modal-close" onclick="closeOwnerBookingsModal()">&times;</button>
            </div>

            <div class="owner-bookings-tabs" role="tablist" aria-label="My bookings">
                <button type="button" class="owner-bookings-tab active" data-tab="trainers" onclick="setOwnerBookingsTab('trainers')">Trainers</button>
                <button type="button" class="owner-bookings-tab" data-tab="sitters" onclick="setOwnerBookingsTab('sitters')">Sitters</button>
                <button type="button" class="owner-bookings-tab" data-tab="breeders" onclick="setOwnerBookingsTab('breeders')">Breeders</button>
            </div>

            <div class="owner-bookings-body">
                <div id="ownerBookingsLoading" class="owner-bookings-loading" style="display:none;">Loading...</div>
                <div id="ownerBookingsError" class="owner-bookings-error" style="display:none;"></div>
                <div id="ownerBookingsList" class="owner-bookings-list"></div>
            </div>
        </div>
    </div>

    <!-- Cancel Booking Confirmation (for My Bookings) -->
    <div id="ownerCancelBookingDialog" class="modal-overlay confirmation-overlay">
        <div class="modal-content confirmation-dialog">
            <div class="confirmation-header">
                <h3 class="confirmation-title">Cancel Appointment</h3>
            </div>
            <div class="confirmation-body">
                <p class="confirmation-message" id="ownerCancelBookingMessage">Are you sure you want to cancel this booking?</p>
            </div>
            <div class="confirmation-actions">
                <button type="button" class="btn outline" onclick="closeOwnerCancelBookingDialog()">Keep Booking</button>
                <button type="button" class="btn primary" onclick="confirmOwnerCancelBooking()">Yes, Cancel Appointment</button>
            </div>
        </div>
    </div>

    <script>
        // Mobile Filter Toggle Functions
        function toggleMobileFilters() {
            const filtersSection = document.getElementById('filtersSection');
            const filtersOverlay = document.getElementById('filtersOverlay');
            const body = document.body;
            
            filtersSection.classList.toggle('active');
            filtersOverlay.classList.toggle('active');
            body.classList.toggle('filters-open');
        }

        function updateFilterBadge() {
            const form = document.getElementById('filterForm');
            const inputs = form.querySelectorAll('input[type="text"], select');
            let activeFilters = 0;
            
            inputs.forEach(input => {
                if (input.name !== 'module' && input.name !== 'page' && input.name !== 'type' && input.value) {
                    activeFilters++;
                }
            });
            
            const badge = document.getElementById('filterBadge');
            if (activeFilters > 0) {
                badge.textContent = activeFilters;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }

        // Initialize filter badge on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateFilterBadge();
            updateSitterCardButtons();
        });

        // Active bookings from server (used for duplicate booking prevention)
        window.__activeBookingProviderIds = <?php echo json_encode($activeBookingProviderIds, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;

        function isProviderAlreadyBooked(serviceKey, providerId) {
            const ids = (window.__activeBookingProviderIds && Array.isArray(window.__activeBookingProviderIds[serviceKey]))
                ? window.__activeBookingProviderIds[serviceKey]
                : [];
            const pid = parseInt(providerId, 10);
            if (!Number.isFinite(pid)) return false;
            return ids.map(x => parseInt(x, 10)).includes(pid);
        }

        function updateSitterCardButtons() {
            const buttons = document.querySelectorAll('button.sitter-book-btn[data-provider-id]');
            buttons.forEach(btn => {
                const providerId = parseInt(btn.getAttribute('data-provider-id') || '0', 10);
                const booked = isProviderAlreadyBooked('sitters', providerId);
                btn.disabled = booked;
                btn.classList.toggle('is-booked', booked);
            });
        }

        function changeService(serviceType) {
            // Update the hidden input and submit the form
            document.getElementById('serviceTypeInput').value = serviceType;
            
            // Clear service-specific filters
            const form = document.getElementById('filterForm');
            const specificInputs = form.querySelectorAll('.specific-filters input, .specific-filters select');
            specificInputs.forEach(input => {
                if (input.type === 'checkbox') {
                    input.checked = false;
                } else {
                    input.value = '';
                }
            });
            
            // Submit the form
            form.submit();
        }

        function clearFilters() {
            const form = document.getElementById('filterForm');
            const inputs = form.querySelectorAll('input[type="text"], select');
            inputs.forEach(input => {
                if (input.name !== 'module' && input.name !== 'page' && input.name !== 'type') {
                    input.value = '';
                }
            });
            form.submit();
        }

        function toggleGroomerServiceType(showValue) {
            const form = document.getElementById('filterForm');
            // Submit the form to apply the show filter (preserves groomer_id if present)
            form.submit();
        }

        function viewProvider(providerId, serviceType) {
            // Navigate to provider detail page (to be implemented)
            alert('Provider detail page coming soon! ID: ' + providerId);
        }

        function viewGroomerServices(groomerId) {
            // Redirect to services page with groomer filter and show=services
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('type', 'groomers');
            currentUrl.searchParams.set('show', 'services');
            currentUrl.searchParams.set('groomer_id', groomerId);
            window.location.href = currentUrl.toString();
        }

        function viewGroomerPackages(groomerId) {
            // Redirect to services page with groomer filter and show=packages
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('type', 'groomers');
            currentUrl.searchParams.set('show', 'packages');
            currentUrl.searchParams.set('groomer_id', groomerId);
            window.location.href = currentUrl.toString();
        }

        function contactProvider(providerData, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            const displayName = (providerData.business_name && String(providerData.business_name).trim() !== '')
                ? providerData.business_name
                : providerData.name;
            showContactModal(displayName, providerData.phone, providerData.phone2 || '');
        }

        // Contact Modal Functions (match Trainer modal)
        function showContactModal(ownerName, phone1, phone2) {
            const modal = document.getElementById('contactModal');
            const modalOwnerName = document.getElementById('modalOwnerName');
            const phoneList = document.getElementById('phoneList');
            
            // Set owner name
            modalOwnerName.textContent = ownerName;
            
            // Clear previous phone numbers
            phoneList.innerHTML = '';
            
            // Add phone 1
            if (phone1) {
                const phoneItem = document.createElement('div');
                phoneItem.className = 'phone-item';
                phoneItem.innerHTML = `
                    <div class="phone-number">${phone1}</div>
                    <a href="tel:${phone1}" class="call-btn">Call</a>
                `;
                phoneList.appendChild(phoneItem);
            }
            
            // Add phone 2 if exists
            if (phone2 && phone2.trim() !== '') {
                const phoneItem = document.createElement('div');
                phoneItem.className = 'phone-item';
                phoneItem.innerHTML = `
                    <div class="phone-number">${phone2}</div>
                    <a href="tel:${phone2}" class="call-btn">Call</a>
                `;
                phoneList.appendChild(phoneItem);
            }
            
            // Show modal and freeze background
            modal.classList.add('active');
            document.body.classList.add('modal-open');
        }

        function closeContactModal() {
            const modal = document.getElementById('contactModal');
            modal.classList.remove('active');
            document.body.classList.remove('modal-open');
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('contactModal');
            if (event.target === modal) {
                closeContactModal();
            }

            const ownerBookingsModal = document.getElementById('ownerBookingsModal');
            if (event.target === ownerBookingsModal) {
                closeOwnerBookingsModal();
            }

            const ownerCancelModal = document.getElementById('ownerCancelBookingDialog');
            if (event.target === ownerCancelModal) {
                closeOwnerCancelBookingDialog();
            }
            
            const trainerModal = document.getElementById('trainerDetailsModal');
            if (event.target === trainerModal) {
                closeTrainerDetails();
            }

            const breederModal = document.getElementById('breederDetailsModal');
            if (event.target === breederModal) {
                closeBreederDetails();
            }
        });

        // My Bookings Modal
        let ownerBookingsTab = 'trainers';
        let ownerBookingsCache = null;

        let pendingOwnerCancel = { tab: null, bookingId: null };

        function openOwnerBookingsModal() {
            const modal = document.getElementById('ownerBookingsModal');
            modal.classList.add('active');
            document.body.classList.add('modal-open');
            setOwnerBookingsTab(ownerBookingsTab || 'trainers');
            loadOwnerBookings();
        }

        function closeOwnerBookingsModal() {
            const modal = document.getElementById('ownerBookingsModal');
            modal.classList.remove('active');
            const ownerCancelModal = document.getElementById('ownerCancelBookingDialog');
            const cancelStillOpen = ownerCancelModal && ownerCancelModal.classList.contains('active');
            if (!cancelStillOpen) document.body.classList.remove('modal-open');
        }

        function setOwnerBookingsTab(tab) {
            ownerBookingsTab = tab;
            const tabs = document.querySelectorAll('#ownerBookingsModal .owner-bookings-tab');
            tabs.forEach(t => t.classList.toggle('active', t.getAttribute('data-tab') === tab));
            renderOwnerBookings();
        }

        async function loadOwnerBookings() {
            const loading = document.getElementById('ownerBookingsLoading');
            const errorEl = document.getElementById('ownerBookingsError');

            if (loading) loading.style.display = 'block';
            if (errorEl) {
                errorEl.style.display = 'none';
                errorEl.textContent = '';
            }

            try {
                const res = await fetch('/PETVET/api/pet-owner/get-my-bookings.php', { credentials: 'same-origin' });
                const json = await res.json();
                if (!res.ok || !json || json.success !== true) {
                    throw new Error((json && json.message) ? json.message : 'Failed to load bookings');
                }

                ownerBookingsCache = json.data || { trainers: [], sitters: [], breeders: [] };
                if (json.active_provider_ids) {
                    window.__activeBookingProviderIds = json.active_provider_ids;
                    updateSitterCardButtons();
                }
                renderOwnerBookings();
            } catch (e) {
                if (errorEl) {
                    errorEl.textContent = e && e.message ? e.message : 'Failed to load bookings';
                    errorEl.style.display = 'block';
                }
            } finally {
                if (loading) loading.style.display = 'none';
            }
        }

        function renderOwnerBookings() {
            const listEl = document.getElementById('ownerBookingsList');
            if (!listEl) return;
            listEl.innerHTML = '';

            const data = ownerBookingsCache || { trainers: [], sitters: [], breeders: [] };
            const items = Array.isArray(data[ownerBookingsTab]) ? data[ownerBookingsTab] : [];

            if (items.length === 0) {
                const empty = document.createElement('div');
                empty.className = 'owner-bookings-empty';
                empty.textContent = 'No bookings found.';
                listEl.appendChild(empty);
                return;
            }

            items.forEach(item => {
                const card = document.createElement('div');
                card.className = 'owner-bookings-item';

                const top = document.createElement('div');
                top.className = 'owner-bookings-item-top';

                const left = document.createElement('div');
                left.className = 'owner-bookings-item-main';

                const providerRow = document.createElement('div');
                providerRow.className = 'owner-bookings-provider';

                const avatar = document.createElement('img');
                avatar.className = 'owner-bookings-avatar';
                avatar.alt = 'Provider';
                avatar.src = (item.provider_avatar && String(item.provider_avatar).trim() !== '')
                    ? String(item.provider_avatar)
                    : '/PETVET/public/images/default-avatar.png';
                avatar.onerror = function() {
                    this.onerror = null;
                    this.src = '/PETVET/public/images/default-avatar.png';
                };
                providerRow.appendChild(avatar);

                const title = document.createElement('div');
                title.className = 'owner-bookings-item-title';
                title.textContent = (item.provider_display || item.provider_name || 'Provider');
                providerRow.appendChild(title);

                left.appendChild(providerRow);

                const meta = document.createElement('div');
                meta.className = 'owner-bookings-item-meta';

                const parts = [];
                if (item.service_label) parts.push(String(item.service_label));
                if (item.pet_name) parts.push(String(item.pet_name));
                if (item.pet_breed) parts.push(String(item.pet_breed));
                const dt = [];
                if (item.start_date) dt.push(String(item.start_date));
                if (item.start_time) dt.push(String(item.start_time));
                if (dt.length) parts.push(dt.join(' '));
                meta.textContent = parts.filter(Boolean).join(' • ');
                left.appendChild(meta);

                top.appendChild(left);

                const right = document.createElement('div');
                right.className = 'owner-bookings-item-actions';

                const status = document.createElement('span');
                status.className = 'owner-bookings-status';
                status.textContent = String(item.status || '').toUpperCase();
                right.appendChild(status);

                if (String(item.status || '').toLowerCase() === 'pending') {
                    const cancelBtn = document.createElement('button');
                    cancelBtn.type = 'button';
                    cancelBtn.className = 'btn outline owner-bookings-cancel';
                    cancelBtn.textContent = 'Cancel';
                    const providerName = (item.provider_display || item.provider_name || 'this provider');
                    cancelBtn.addEventListener('click', () => openOwnerCancelBookingDialog(ownerBookingsTab, item.id, providerName));
                    right.appendChild(cancelBtn);
                }

                top.appendChild(right);
                card.appendChild(top);
                listEl.appendChild(card);
            });
        }

        async function cancelOwnerBooking(tab, bookingId) {
            const map = { trainers: 'trainer', sitters: 'sitter', breeders: 'breeder' };
            const type = map[tab];
            if (!type) return;

            try {
                const res = await fetch('/PETVET/api/pet-owner/cancel-booking.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'same-origin',
                    body: JSON.stringify({ type, bookingId })
                });
                const json = await res.json();
                if (!res.ok || !json || json.success !== true) {
                    throw new Error((json && json.message) ? json.message : 'Failed to cancel');
                }
                await loadOwnerBookings();
            } catch (e) {
                alert(e && e.message ? e.message : 'Failed to cancel booking');
            }
        }

        function openOwnerCancelBookingDialog(tab, bookingId, providerName) {
            pendingOwnerCancel = { tab, bookingId };

            const msgEl = document.getElementById('ownerCancelBookingMessage');
            if (msgEl) {
                const name = (providerName && String(providerName).trim() !== '') ? String(providerName).trim() : 'this booking';
                msgEl.textContent = `Are you sure you want to cancel your booking with ${name}?`;
            }

            const modal = document.getElementById('ownerCancelBookingDialog');
            if (modal) {
                modal.classList.add('active');
                document.body.classList.add('modal-open');
            }
        }

        function closeOwnerCancelBookingDialog() {
            const modal = document.getElementById('ownerCancelBookingDialog');
            if (modal) modal.classList.remove('active');
            const ownerBookingsModal = document.getElementById('ownerBookingsModal');
            const bookingsStillOpen = ownerBookingsModal && ownerBookingsModal.classList.contains('active');
            if (!bookingsStillOpen) document.body.classList.remove('modal-open');
            pendingOwnerCancel = { tab: null, bookingId: null };
        }

        async function confirmOwnerCancelBooking() {
            const tab = pendingOwnerCancel.tab;
            const bookingId = pendingOwnerCancel.bookingId;
            closeOwnerCancelBookingDialog();
            if (!tab || !bookingId) return;
            await cancelOwnerBooking(tab, bookingId);
        }

        // Trainer Details Modal Functions
        let currentTrainer = null;
        let pendingAppointmentData = null;
        let isTrainerAlreadyBooked = false;

        function openTrainerDetails(trainerData, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            currentTrainer = trainerData;
            const modal = document.getElementById('trainerDetailsModal');

            isTrainerAlreadyBooked = isProviderAlreadyBooked('trainers', trainerData.id);
            
            // Populate trainer info
            document.getElementById('trainerModalAvatar').src = trainerData.avatar;
            document.getElementById('trainerModalName').textContent = trainerData.name;
            document.getElementById('trainerModalBusiness').textContent = trainerData.business_name || '';
            document.getElementById('trainerModalSpecialization').textContent = trainerData.specialization;
            document.getElementById('trainerModalExperience').textContent = trainerData.experience_years + ' years';

            // Render working areas (compact chips)
            const areasWrap = document.getElementById('trainerModalAreas');
            if (areasWrap) {
                const areas = Array.isArray(trainerData.service_areas) ? trainerData.service_areas : [];
                areasWrap.innerHTML = '';
                areas.slice(0, 5).forEach(a => {
                    const chip = document.createElement('span');
                    chip.className = 'area-chip';
                    chip.textContent = a;
                    areasWrap.appendChild(chip);
                });
            }
            
            // Populate training rates with real data and hide/show based on what's enabled
            const basicOption = document.querySelector('label[class="rate-option"]:nth-child(1)');
            const intermediateOption = document.querySelector('label[class="rate-option"]:nth-child(2)');
            const advancedOption = document.querySelector('label[class="rate-option"]:nth-child(3)');
            
            // Basic Training
            if (trainerData.training_basic_enabled && parseFloat(trainerData.training_basic_charge) > 0) {
                basicOption.style.display = 'block';
                document.getElementById('basicRate').textContent = 'LKR ' + parseFloat(trainerData.training_basic_charge).toFixed(2) + '/hr';
            } else {
                basicOption.style.display = 'none';
            }
            
            // Intermediate Training
            if (trainerData.training_intermediate_enabled && parseFloat(trainerData.training_intermediate_charge) > 0) {
                intermediateOption.style.display = 'block';
                document.getElementById('intermediateRate').textContent = 'LKR ' + parseFloat(trainerData.training_intermediate_charge).toFixed(2) + '/hr';
            } else {
                intermediateOption.style.display = 'none';
            }
            
            // Advanced Training
            if (trainerData.training_advanced_enabled && parseFloat(trainerData.training_advanced_charge) > 0) {
                advancedOption.style.display = 'block';
                document.getElementById('advancedRate').textContent = 'LKR ' + parseFloat(trainerData.training_advanced_charge).toFixed(2) + '/hr';
            } else {
                advancedOption.style.display = 'none';
            }
            
            // Reset form
            document.getElementById('appointmentForm').style.display = 'none';
            document.querySelectorAll('input[name="trainingType"]').forEach(radio => radio.checked = false);
            document.getElementById('bookingForm').reset();
            
            // Add input listeners for submit button state
            const formFields = document.querySelectorAll('#bookingForm input, #bookingForm select, #bookingForm textarea');
            formFields.forEach(field => {
                field.removeEventListener('input', updateSubmitButtonState);
                field.removeEventListener('change', updateSubmitButtonState);
                field.addEventListener('input', updateSubmitButtonState);
                field.addEventListener('change', updateSubmitButtonState);
            });
            
            // Reset submit button state
            const submitBtn = document.getElementById('trainerSubmitBtn');
            submitBtn.disabled = true;
            isDateTimeValid = false;
            
            // Load owner name and pets
            loadOwnerAndPets();
            
            // Lock scroll and save position
            savedScrollPosition = window.pageYOffset || document.documentElement.scrollTop;
            document.documentElement.style.overflow = 'hidden';
            document.body.style.overflow = 'hidden';
            document.body.style.position = 'relative';
            modal.classList.add('active');
            document.body.classList.add('modal-open');
        }

        // Load owner name and pets from API
        let userPetsData = []; // Store pets data globally for breed auto-fill
        
        function loadOwnerAndPets() {
            const ownerNameField = document.getElementById('ownerName');
            const petNamesList = document.getElementById('petNamesList');
            const petNameField = document.getElementById('petName');

            const sitterOwnerNameField = document.getElementById('sitterOwnerName');
            const sitterPetNamesList = document.getElementById('sitterPetNamesList');
            const sitterPetNameField = document.getElementById('sitterPetName');

            const breederOwnerNameField = document.getElementById('breederOwnerName');
            const breederPetNamesList = document.getElementById('breederPetNamesList');
            const breederPetNameField = document.getElementById('breedingPetName');
            
            // Show loading state
            if (ownerNameField) ownerNameField.value = 'Loading...';
            if (sitterOwnerNameField) sitterOwnerNameField.value = 'Loading...';
            if (breederOwnerNameField) breederOwnerNameField.value = 'Loading...';
            
            fetch('/PETVET/api/pet-owner/get-my-pets.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Set owner name
                        if (ownerNameField) ownerNameField.value = data.owner_name || '';
                        if (sitterOwnerNameField) sitterOwnerNameField.value = data.owner_name || '';
                        if (breederOwnerNameField) breederOwnerNameField.value = data.owner_name || '';
                        
                        // Store pets data for later use
                        userPetsData = data.pets || [];
                        
                        // Populate pet names datalist
                        if (petNamesList) petNamesList.innerHTML = '';
                        if (sitterPetNamesList) sitterPetNamesList.innerHTML = '';
                        if (breederPetNamesList) breederPetNamesList.innerHTML = '';

                        if (data.pets && data.pets.length > 0) {
                            data.pets.forEach(pet => {
                                const name = (pet && pet.name) ? String(pet.name) : '';
                                if (!name) return;

                                if (petNamesList) {
                                    const option = document.createElement('option');
                                    option.value = name;
                                    petNamesList.appendChild(option);
                                }

                                if (sitterPetNamesList) {
                                    const option2 = document.createElement('option');
                                    option2.value = name;
                                    sitterPetNamesList.appendChild(option2);
                                }

                                if (breederPetNamesList) {
                                    const option3 = document.createElement('option');
                                    option3.value = name;
                                    breederPetNamesList.appendChild(option3);
                                }
                            });
                        }
                        
                        // Add event listener to auto-fill breed when pet is selected
                        if (petNameField) {
                            petNameField.removeEventListener('input', handlePetNameChange);
                            petNameField.addEventListener('input', handlePetNameChange);
                        }

                        if (sitterPetNameField) {
                            sitterPetNameField.removeEventListener('input', handleSitterPetNameChange);
                            sitterPetNameField.addEventListener('input', handleSitterPetNameChange);
                        }

                        if (breederPetNameField) {
                            breederPetNameField.removeEventListener('input', handleBreederPetNameChange);
                            breederPetNameField.addEventListener('input', handleBreederPetNameChange);
                        }

                        if (typeof updateSubmitButtonState === 'function') updateSubmitButtonState();
                        if (typeof updateSitterSubmitButtonState === 'function') updateSitterSubmitButtonState();
                        if (typeof updateBreederSubmitButtonState === 'function') updateBreederSubmitButtonState();
                    } else {
                        if (ownerNameField) ownerNameField.value = '';
                        if (sitterOwnerNameField) sitterOwnerNameField.value = '';
                        if (breederOwnerNameField) breederOwnerNameField.value = '';
                        console.error('Failed to load owner data');

                        if (typeof updateSubmitButtonState === 'function') updateSubmitButtonState();
                        if (typeof updateSitterSubmitButtonState === 'function') updateSitterSubmitButtonState();
                        if (typeof updateBreederSubmitButtonState === 'function') updateBreederSubmitButtonState();
                    }
                })
                .catch(error => {
                    console.error('Error loading owner and pets:', error);
                    if (ownerNameField) ownerNameField.value = '';
                    if (sitterOwnerNameField) sitterOwnerNameField.value = '';
                    if (breederOwnerNameField) breederOwnerNameField.value = '';

                    if (typeof updateSubmitButtonState === 'function') updateSubmitButtonState();
                    if (typeof updateSitterSubmitButtonState === 'function') updateSitterSubmitButtonState();
                    if (typeof updateBreederSubmitButtonState === 'function') updateBreederSubmitButtonState();
                });
        }
        
        // Handle pet name selection to auto-fill breed
        function handlePetNameChange(e) {
            const selectedPetName = e.target.value;
            const breedField = document.getElementById('dogBreed');
            
            // Find the pet with matching name
            const selectedPet = userPetsData.find(pet => String(pet.name || '').toLowerCase() === String(selectedPetName || '').toLowerCase());
            
            if (selectedPet && selectedPet.breed) {
                // Auto-fill breed field
                breedField.value = selectedPet.breed;
            }
        }

        // Handle sitter pet name selection to auto-fill pet type and breed
        function handleSitterPetNameChange(e) {
            const selectedPetName = String(e.target.value || '');
            if (!selectedPetName) return;

            const selectedPet = userPetsData.find(pet => String(pet.name || '').toLowerCase() === selectedPetName.toLowerCase());
            if (!selectedPet) return;

            const petTypeSelect = document.getElementById('petType');
            const breedSelect = document.getElementById('petBreed');
            if (!petTypeSelect || !breedSelect) return;

            const speciesRaw = String(selectedPet.species || '').trim().toLowerCase();
            let typeValue = 'other';
            if (speciesRaw === 'dog') typeValue = 'dog';
            else if (speciesRaw === 'cat') typeValue = 'cat';
            else if (speciesRaw === 'bird') typeValue = 'bird';

            petTypeSelect.value = typeValue;
            updateBreedOptions();

            const breedRaw = String(selectedPet.breed || '').trim();
            if (typeValue === 'other') {
                // Breed selector is hidden for other; keep current behavior
                updateSitterSubmitButtonState();
                return;
            }

            if (breedRaw) {
                // Try to select existing option (case-insensitive). If missing, add it.
                let matched = false;
                for (const opt of Array.from(breedSelect.options)) {
                    if (String(opt.value || '').trim().toLowerCase() === breedRaw.toLowerCase()) {
                        breedSelect.value = opt.value;
                        matched = true;
                        break;
                    }
                }
                if (!matched) {
                    const opt = document.createElement('option');
                    opt.value = breedRaw;
                    opt.textContent = breedRaw;
                    breedSelect.appendChild(opt);
                    breedSelect.value = breedRaw;
                }
            }

            updateSitterSubmitButtonState();
        }

        // Handle breeder pet name selection to auto-fill breed and gender
        function handleBreederPetNameChange(e) {
            const selectedPetName = String(e.target.value || '');
            if (!selectedPetName) {
                if (typeof updateBreederSubmitButtonState === 'function') updateBreederSubmitButtonState();
                return;
            }

            const selectedPet = userPetsData.find(pet => String(pet.name || '').toLowerCase() === selectedPetName.toLowerCase());
            if (!selectedPet) {
                if (typeof updateBreederSubmitButtonState === 'function') updateBreederSubmitButtonState();
                return;
            }

            const breedField = document.getElementById('breedingPetBreed');
            const genderField = document.getElementById('breedingPetGender');

            const breedRaw = String(selectedPet.breed || '').trim();
            if (breedField && breedRaw) {
                breedField.value = breedRaw;
            }

            // Pets API uses `sex`; keep compatibility with other potential field names
            const genderCandidate = (
                selectedPet.sex ??
                selectedPet.gender ??
                selectedPet.pet_gender ??
                selectedPet.petGender ??
                ''
            );
            const genderRaw = String(genderCandidate || '').trim().toLowerCase();
            if (genderField && genderRaw) {
                if (['male', 'm'].includes(genderRaw)) genderField.value = 'Male';
                else if (['female', 'f'].includes(genderRaw)) genderField.value = 'Female';
            }

            if (typeof updateBreederSubmitButtonState === 'function') updateBreederSubmitButtonState();
        }

        function updateSitterSubmitButtonState() {
            const form = document.getElementById('sitterBookingForm');
            const btn = document.getElementById('sitterSubmitBtn');
            if (!form || !btn) return;

            const ok = form.checkValidity() && isSitterDateTimeValid && !isSitterAlreadyBooked;
            btn.disabled = !ok;
        }

        function updateBreederSubmitButtonState() {
            const form = document.getElementById('breedingBookingForm');
            const btn = document.getElementById('breederSubmitBtn');
            if (!form || !btn) return;

            const ok = form.checkValidity() && isBreederDateTimeValid && !isBreederAlreadyBooked;
            btn.disabled = !ok;
        }

        // Sitter appointment availability (weekly schedule + blocked dates)
        let sitterAvailabilityCheckTimeout = null;
        let isSitterDateTimeValid = false;
        let isSitterAlreadyBooked = false;

        // Breeder appointment availability (weekly schedule + blocked dates)
        let breederAvailabilityCheckTimeout = null;
        let isBreederDateTimeValid = false;

        function isSitterSelectedDateTimeNotPast(date, time) {
            if (!date || !time) return false;

            const now = new Date();
            const selectedDate = new Date(date + 'T00:00:00');
            const todayStart = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const isPastDate = selectedDate < todayStart;
            if (isPastDate) return false;

            // If booking is for today, ensure selected time is not in the past
            if (selectedDate.toDateString() === now.toDateString()) {
                const parts = String(time).split(':');
                const hh = parseInt(parts[0] || '0', 10);
                const mm = parseInt(parts[1] || '0', 10);
                const selectedMinutes = (hh * 60) + mm;
                const nowMinutes = (now.getHours() * 60) + now.getMinutes();
                if (selectedMinutes < nowMinutes) return false;
            }

            return true;
        }

        function isBreederSelectedDateTimeNotPast(date, time) {
            return isSitterSelectedDateTimeNotPast(date, time);
        }

        function checkBreederAvailability() {
            if (breederAvailabilityCheckTimeout) {
                clearTimeout(breederAvailabilityCheckTimeout);
            }

            const dateField = document.getElementById('breedingDate');
            const timeField = document.getElementById('breedingTime');
            const dateMsg = document.getElementById('breederDateAvailabilityMsg');
            const timeMsg = document.getElementById('breederTimeAvailabilityMsg');
            const locationField = document.getElementById('breedingLocation');

            if (!dateField || !timeField) {
                isBreederDateTimeValid = false;
                updateBreederSubmitButtonState();
                return;
            }

            if (dateMsg) dateMsg.style.display = 'none';
            if (timeMsg) timeMsg.style.display = 'none';
            dateField.setCustomValidity('');
            timeField.setCustomValidity('');
            isBreederDateTimeValid = false;

            const date = dateField.value;
            const time = timeField.value;

            if (!date || !currentBreeder) {
                if (locationField) locationField.disabled = true;
                updateBreederSubmitButtonState();
                return;
            }

            // If date is selected, allow selecting time (unless API marks date unavailable)
            timeField.disabled = false;
            if (!time) {
                // Time not picked yet => location should remain disabled
                if (locationField) locationField.disabled = true;
            }

            // Build API URL (time optional, like trainer flow)
            let apiUrl = `/PETVET/api/check-breeder-availability.php?breeder_id=${currentBreeder.id}&date=${encodeURIComponent(date)}`;
            if (time) {
                apiUrl += `&time=${encodeURIComponent(time)}`;
            }

            // Disable location during check
            if (locationField) locationField.disabled = true;

            breederAvailabilityCheckTimeout = setTimeout(() => {
                fetch(apiUrl)
                    .then(r => r.json())
                    .then(data => {
                        if (data && data.success) {
                            if (!data.available) {
                                const message = data.message || 'Selected slot is not available';
                                const isFullDayBlock = String(message).includes('unavailable on this date') || String(message).includes('not available on');

                                if (isFullDayBlock) {
                                    if (dateMsg) {
                                        dateMsg.textContent = message;
                                        dateMsg.style.display = 'block';
                                    }
                                    dateField.setCustomValidity(message);
                                    // Disable time selection entirely for unavailable day/date
                                    timeField.disabled = true;
                                    timeField.value = '';
                                    if (locationField) locationField.disabled = true;
                                    dateField.focus();
                                } else {
                                    // Time-specific issue (only meaningful when a time is selected)
                                    if (time) {
                                        if (timeMsg) {
                                            timeMsg.textContent = message;
                                            timeMsg.style.display = 'block';
                                        }
                                        timeField.setCustomValidity(message);
                                        if (locationField) locationField.disabled = true;
                                        timeField.focus();
                                    } else {
                                        // If API returns a non-full-day message without a time, keep time enabled
                                        timeField.disabled = false;
                                        if (locationField) locationField.disabled = true;
                                    }
                                }

                                isBreederDateTimeValid = false;
                            } else {
                                dateField.setCustomValidity('');
                                timeField.setCustomValidity('');
                                timeField.disabled = false;
                                if (dateMsg) dateMsg.style.display = 'none';
                                if (timeMsg) timeMsg.style.display = 'none';

                                // Only mark valid after both date + time are selected
                                if (time) {
                                    isBreederDateTimeValid = true;
                                    if (locationField) locationField.disabled = false;
                                } else {
                                    isBreederDateTimeValid = false;
                                    if (locationField) locationField.disabled = true;
                                }
                            }
                        } else {
                            // On unexpected response, don't hard-block input, but keep booking disabled
                            timeField.disabled = false;
                            if (locationField) locationField.disabled = false;
                            isBreederDateTimeValid = false;
                        }

                        updateBreederSubmitButtonState();
                    })
                    .catch(err => {
                        console.error('Breeder availability check error:', err);
                        timeField.disabled = false;
                        if (locationField) locationField.disabled = false;

                        // Keep booking disabled until a successful availability check
                        isBreederDateTimeValid = false;
                        updateBreederSubmitButtonState();
                    });
            }, 500);

            updateBreederSubmitButtonState();
        }

        function syncSitterDurationStartFromAppointment() {
            const apptDate = document.getElementById('sitterAppointmentDate')?.value || '';
            const apptTime = document.getElementById('sitterAppointmentTime')?.value || '';
            const durationType = document.getElementById('durationType')?.value;

            if (durationType === 'multiple') {
                const d = document.getElementById('startDate');
                const t = document.getElementById('startTimeMulti');
                if (d) d.value = apptDate;
                if (t) t.value = apptTime;
            }
        }

        function checkSitterAvailability() {
            if (sitterAvailabilityCheckTimeout) {
                clearTimeout(sitterAvailabilityCheckTimeout);
            }

            const locationField = document.getElementById('sitterLocation');

            const dateMsg = document.getElementById('sitterApptDateAvailabilityMsg');
            const timeMsg = document.getElementById('sitterApptTimeAvailabilityMsg');

            // Reset messages and validity
            if (dateMsg) dateMsg.style.display = 'none';
            if (timeMsg) timeMsg.style.display = 'none';
            isSitterDateTimeValid = false;

            const dateField = document.getElementById('sitterAppointmentDate');
            const timeField = document.getElementById('sitterAppointmentTime');

            if (!dateField || !timeField) {
                if (locationField) locationField.disabled = true;
                updateSitterSubmitButtonState();
                return;
            }

            dateField.setCustomValidity('');
            timeField.setCustomValidity('');

            const date = dateField.value;
            const time = timeField.value;

            // Keep duration start fields in sync (so existing submit payload stays unchanged)
            syncSitterDurationStartFromAppointment();

            // Need at least a date to check
            if (!date || !currentSitter) {
                timeField.disabled = true;
                if (locationField) locationField.disabled = true;
                updateSitterSubmitButtonState();
                return;
            }

            // Enable time once a date is selected
            timeField.disabled = false;

            // Default: disable location until we have a valid date + time
            if (locationField) locationField.disabled = true;

            // UX fix: if user already picked a date+time that is not in the past,
            // allow them to continue filling the form immediately, even while the
            // availability check is still pending (we still hard-block on API-unavailable
            // responses and also re-check on submit).
            if (time) {
                isSitterDateTimeValid = isSitterSelectedDateTimeNotPast(date, time);
                if (locationField) locationField.disabled = !isSitterDateTimeValid;
                updateSitterSubmitButtonState();
            }

            // Build API URL - only include time if it's selected
            let apiUrl = `/PETVET/api/check-sitter-availability.php?sitter_id=${currentSitter.id}&date=${encodeURIComponent(date)}`;
            if (time) {
                apiUrl += `&time=${encodeURIComponent(time)}`;
            }

            sitterAvailabilityCheckTimeout = setTimeout(() => {
                fetch(apiUrl)
                    .then(r => r.json())
                    .then(data => {
                        if (data && data.success) {
                            if (!data.available) {
                                const message = data.message || 'Sitter is unavailable for the selected date/time.';
                                const isFullDayBlock = message.includes('unavailable on this date') || message.includes('not available on') || message.includes('not available');

                                if (isFullDayBlock) {
                                    if (dateMsg) {
                                        dateMsg.textContent = message;
                                        dateMsg.style.display = 'block';
                                    }
                                    dateField.setCustomValidity(message);
                                    timeField.disabled = true;
                                    timeField.value = '';
                                    syncSitterDurationStartFromAppointment();
                                    if (locationField) locationField.disabled = true;
                                    dateField.focus();
                                } else if (time) {
                                    if (timeMsg) {
                                        timeMsg.textContent = message;
                                        timeMsg.style.display = 'block';
                                    }
                                    timeField.setCustomValidity(message);
                                    if (locationField) locationField.disabled = true;
                                    timeField.focus();
                                } else {
                                    timeField.disabled = false;
                                }
                                isSitterDateTimeValid = false;
                            } else {
                                dateField.setCustomValidity('');
                                timeField.setCustomValidity('');
                                timeField.disabled = false;
                                if (time) {
                                    if (locationField) locationField.disabled = false;
                                    if (dateMsg) dateMsg.style.display = 'none';
                                    if (timeMsg) timeMsg.style.display = 'none';
                                    isSitterDateTimeValid = true;
                                }
                            }
                        } else {
                            // On unexpected response, don't hard-block user
                            if (locationField) locationField.disabled = false;
                            timeField.disabled = false;

                            // Fallback: allow if date/time are not in the past
                            isSitterDateTimeValid = isSitterSelectedDateTimeNotPast(date, time);
                        }

                        updateSitterSubmitButtonState();
                    })
                    .catch(err => {
                        console.error('Sitter availability check error:', err);
                        if (locationField) locationField.disabled = false;
                        timeField.disabled = false;

                        // Fallback: allow if date/time are not in the past
                        isSitterDateTimeValid = isSitterSelectedDateTimeNotPast(date, time);
                        updateSitterSubmitButtonState();
                    });
            }, 500);

            updateSitterSubmitButtonState();
        }
        
        // Check trainer availability for selected date and time
        let availabilityCheckTimeout = null;
        let isDateTimeValid = false;
        
        function checkTrainerAvailability() {
            // Clear previous timeout
            if (availabilityCheckTimeout) {
                clearTimeout(availabilityCheckTimeout);
            }
            
            const dateField = document.getElementById('appointmentDate');
            const timeField = document.getElementById('appointmentTime');
            const dateMsg = document.getElementById('dateAvailabilityMsg');
            const timeMsg = document.getElementById('timeAvailabilityMsg');
            const locationField = document.getElementById('trainingLocation');
            const submitBtn = document.getElementById('trainerSubmitBtn');
            
            // Reset messages and validity
            dateMsg.style.display = 'none';
            timeMsg.style.display = 'none';
            dateField.setCustomValidity('');
            timeField.setCustomValidity('');
            isDateTimeValid = false;
            updateSubmitButtonState();
            
            const date = dateField.value;
            const time = timeField.value;
            
            // Need at least a date to check
            if (!date || !currentTrainer) {
                return;
            }
            
            // If no time yet, enable time field but disable location
            if (!time) {
                timeField.disabled = false;
                locationField.disabled = true;
            }
            
            // Build API URL - only include time if it's selected
            let apiUrl = `/PETVET/api/check-trainer-availability.php?trainer_id=${currentTrainer.id}&date=${date}`;
            if (time) {
                apiUrl += `&time=${time}`;
            }
            
            console.log('Checking availability:', apiUrl);
            
            // Disable next fields during check
            locationField.disabled = true;
            
            // Debounce the API call
            availabilityCheckTimeout = setTimeout(() => {
                fetch(apiUrl)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Availability response:', data);
                        
                        if (data.success) {
                            if (!data.available) {
                                // Check if it's a full day block or specific issue
                                const isFullDayBlock = data.message.includes('unavailable on this date') || 
                                                      data.message.includes('not available on');
                                
                                if (isFullDayBlock) {
                                    // Show error on date field
                                    dateMsg.textContent = data.message;
                                    dateMsg.style.display = 'block';
                                    dateField.setCustomValidity(data.message);
                                    
                                    // Disable time and location fields
                                    timeField.disabled = true;
                                    timeField.value = '';
                                    locationField.disabled = true;
                                    
                                    // Focus back to date field
                                    dateField.focus();
                                } else if (time) {
                                    // Time-specific issue - show on time field
                                    timeMsg.textContent = data.message;
                                    timeMsg.style.display = 'block';
                                    timeField.setCustomValidity(data.message);
                                    
                                    // Keep location field disabled
                                    locationField.disabled = true;
                                    
                                    // Focus back to time field
                                    timeField.focus();
                                } else {
                                    // Date is problematic for default time, enable time selection
                                    timeField.disabled = false;
                                }
                                isDateTimeValid = false;
                            } else {
                                // Available
                                dateField.setCustomValidity('');
                                timeField.setCustomValidity('');
                                timeField.disabled = false;
                                
                                // Only enable location if time is also selected
                                if (time) {
                                    locationField.disabled = false;
                                    dateMsg.style.display = 'none';
                                    timeMsg.style.display = 'none';
                                    isDateTimeValid = true;
                                }
                            }
                        }
                        updateSubmitButtonState();
                    })
                    .catch(error => {
                        console.error('Availability check error:', error);
                        // Enable fields on error to not block user
                        timeField.disabled = false;
                        locationField.disabled = false;
                    });
            }, 500); // Wait 500ms after user stops typing
        }
        
        // Update submit button state based on form validity
        function updateSubmitButtonState() {
            const submitBtn = document.getElementById('trainerSubmitBtn');
            const form = document.getElementById('bookingForm');
            
            // Check if all required fields are filled and date/time are valid
            const allFieldsFilled = form.checkValidity() && isDateTimeValid;
            const ok = allFieldsFilled && !isTrainerAlreadyBooked;
            submitBtn.disabled = !ok;
        }

        function closeTrainerDetails() {
            const modal = document.getElementById('trainerDetailsModal');
            modal.classList.remove('active');
            document.body.classList.remove('modal-open');
            
            // Restore scroll and position
            document.documentElement.style.overflow = '';
            document.body.style.overflow = '';
            document.body.style.position = '';
            window.scrollTo(0, savedScrollPosition);
            currentTrainer = null;
            isTrainerAlreadyBooked = false;
        }

        function selectTrainingType() {
            const selectedType = document.querySelector('input[name="trainingType"]:checked');
            if (selectedType) {
                document.getElementById('appointmentForm').style.display = 'block';
                document.getElementById('selectedTrainingType').textContent = selectedType.value;
            }
        }

        // Leaflet map location pickers (Trainer + Sitter)
        const SL_DEFAULT_LAT = 6.9271;
        const SL_DEFAULT_LNG = 79.8612;

        async function reverseGeocodeForLocation(lat, lng) {
            try {
                const url = `/PETVET/api/pet-owner/reverse-geocode.php?lat=${encodeURIComponent(lat)}&lng=${encodeURIComponent(lng)}`;
                const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!response.ok) return null;
                const data = await response.json();
                if (!data || data.success !== true) return null;

                return {
                    location: data.location || data.full_address || `Lat: ${Number(lat).toFixed(6)}, Lng: ${Number(lng).toFixed(6)}`,
                    district: data.district || null
                };
            } catch (e) {
                console.error('Reverse geocode failed:', e);
                return null;
            }
        }

        function setDistrictAuto(selectEl, rowEl, msgEl, districtName) {
            if (!selectEl || !rowEl) return false;

            rowEl.style.display = 'flex';
            if (msgEl) msgEl.style.display = 'none';

            if (!districtName) {
                selectEl.required = true;
                selectEl.style.pointerEvents = 'auto';
                selectEl.style.backgroundColor = '';
                if (msgEl) {
                    msgEl.textContent = 'Could not detect district automatically. Please select your district.';
                    msgEl.style.display = 'block';
                }
                return false;
            }

            const district = String(districtName).trim();
            // Try exact value match
            let matched = false;
            for (const opt of Array.from(selectEl.options)) {
                if (String(opt.value).trim().toLowerCase() === district.toLowerCase()) {
                    selectEl.value = opt.value;
                    matched = true;
                    break;
                }
            }

            if (!matched) {
                // If missing (unexpected district string), add it to keep auto working.
                const opt = document.createElement('option');
                opt.value = district;
                opt.textContent = district;
                selectEl.appendChild(opt);
                selectEl.value = district;
            }

            selectEl.required = true;
            // "Readonly" feel while still submitting value
            selectEl.style.pointerEvents = 'none';
            selectEl.style.backgroundColor = '#f5f5f5';
            return true;
        }

        // Trainer map state
        let trainerMap = null;
        let trainerMarker = null;
        let trainerLastMapInteractionAt = 0;

        function initTrainerLocationMap() {
            const container = document.getElementById('trainerMapContainer');
            if (!container) return;
            if (typeof L === 'undefined') {
                console.error('Leaflet is not loaded (L is undefined)');
                return;
            }

            if (!trainerMap) {
                const latInput = document.getElementById('trainerLocationLat');
                const lngInput = document.getElementById('trainerLocationLng');
                const displayInput = document.getElementById('trainerLocationDisplay');
                const districtRow = document.getElementById('trainerDistrictRow');
                const districtSelect = document.getElementById('locationDistrict');
                const districtMsg = document.getElementById('trainerDistrictMsg');

                const centerLat = parseFloat(latInput?.value) || SL_DEFAULT_LAT;
                const centerLng = parseFloat(lngInput?.value) || SL_DEFAULT_LNG;

                trainerMap = L.map('trainerMapContainer').setView([centerLat, centerLng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 19
                }).addTo(trainerMap);

                const markInteraction = () => { trainerLastMapInteractionAt = Date.now(); };
                trainerMap.on('dragstart', markInteraction);
                trainerMap.on('dragend', markInteraction);
                trainerMap.on('zoomstart', markInteraction);
                trainerMap.on('zoomend', markInteraction);
                trainerMap.on('movestart', markInteraction);
                trainerMap.on('moveend', markInteraction);

                async function applyTrainerLocation(lat, lng, shouldCenter = true) {
                    if (!latInput || !lngInput || !displayInput) return;
                    latInput.value = Number(lat).toFixed(6);
                    lngInput.value = Number(lng).toFixed(6);

                    if (trainerMarker) {
                        trainerMarker.setLatLng([lat, lng]);
                    } else {
                        trainerMarker = L.marker([lat, lng]).addTo(trainerMap);
                    }

                    if (shouldCenter) {
                        trainerMap.setView([lat, lng], Math.max(trainerMap.getZoom() || 13, 15));
                    }

                    // Always mark the location field as filled (required gating)
                    const geo = await reverseGeocodeForLocation(lat, lng);
                    displayInput.value = (geo && geo.location) ? geo.location : `Lat: ${Number(lat).toFixed(6)}, Lng: ${Number(lng).toFixed(6)}`;

                    // Show and auto-set district (or allow manual fallback)
                    setDistrictAuto(districtSelect, districtRow, districtMsg, geo ? geo.district : null);

                    // Update submit button state
                    updateSubmitButtonState();
                }

                // Existing values
                if (latInput?.value && lngInput?.value) {
                    const lat = parseFloat(latInput.value);
                    const lng = parseFloat(lngInput.value);
                    applyTrainerLocation(lat, lng, false);
                }

                trainerMap.on('click', async function(e) {
                    if (Date.now() - trainerLastMapInteractionAt < 450) return;
                    e.originalEvent.preventDefault();
                    e.originalEvent.stopPropagation();
                    await applyTrainerLocation(e.latlng.lat, e.latlng.lng, false);
                });

                const useBtn = document.getElementById('trainerUseMyLocationBtn');
                if (useBtn) {
                    useBtn.addEventListener('click', (ev) => {
                        ev.preventDefault();
                        ev.stopPropagation();

                        if (!navigator.geolocation) {
                            alert('Geolocation is not supported by your browser.');
                            return;
                        }

                        useBtn.disabled = true;
                        const oldText = useBtn.textContent;
                        useBtn.textContent = 'Getting location...';

                        navigator.geolocation.getCurrentPosition(
                            async (pos) => {
                                const lat = pos.coords.latitude;
                                const lng = pos.coords.longitude;
                                await applyTrainerLocation(lat, lng, true);
                                useBtn.disabled = false;
                                useBtn.textContent = oldText;
                            },
                            (err) => {
                                console.error('Geolocation error:', err);
                                alert('Unable to get your current location. Please click on the map to select.');
                                useBtn.disabled = false;
                                useBtn.textContent = oldText;
                            },
                            { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
                        );
                    });
                }
            }

            // If container was hidden (modal), invalidate size so tiles render correctly
            setTimeout(() => {
                if (trainerMap) trainerMap.invalidateSize();
            }, 120);
        }

        function toggleLocationFields() {
            const locationSelect = document.getElementById('trainingLocation');
            const additionalFields = document.getElementById('additionalLocationFields');
            const districtField = document.getElementById('locationDistrict');
            const districtRow = document.getElementById('trainerDistrictRow');
            const districtMsg = document.getElementById('trainerDistrictMsg');
            const mapDisplayField = document.getElementById('trainerLocationDisplay');
            const latField = document.getElementById('trainerLocationLat');
            const lngField = document.getElementById('trainerLocationLng');
            
            // Show additional fields for: home, park, or other
            // Hide for: trainer's location
            const showFields = ['home', 'park', 'other'].includes(locationSelect.value);
            
            if (showFields) {
                additionalFields.style.display = 'block';

                // Map location must be selected; district becomes required after we select a point
                if (mapDisplayField) mapDisplayField.required = true;
                if (districtField) districtField.required = false;

                // Reset district UI until a point is selected
                if (districtRow) districtRow.style.display = 'none';
                if (districtMsg) districtMsg.style.display = 'none';
                if (districtField) {
                    districtField.style.pointerEvents = 'none';
                    districtField.style.backgroundColor = '#f5f5f5';
                }

                // Init map after showing section
                initTrainerLocationMap();
            } else {
                additionalFields.style.display = 'none';
                // Remove required attribute
                if (mapDisplayField) mapDisplayField.required = false;
                if (districtField) districtField.required = false;

                // Clear values
                if (districtField) districtField.value = '';
                if (mapDisplayField) mapDisplayField.value = '';
                if (latField) latField.value = '';
                if (lngField) lngField.value = '';
                if (districtRow) districtRow.style.display = 'none';
                if (districtMsg) districtMsg.style.display = 'none';
                if (districtField) {
                    districtField.style.pointerEvents = 'none';
                    districtField.style.backgroundColor = '#f5f5f5';
                }

                // Keep marker on map (if any) but do not block form when hidden
            }

            updateSubmitButtonState();
        }

        async function submitTrainerAppointment(event) {
            event.preventDefault();
            
            // Check availability one more time before submitting
            const date = document.getElementById('appointmentDate').value;
            const time = document.getElementById('appointmentTime').value;
            
            try {
                let url = `/PETVET/api/check-trainer-availability.php?trainer_id=${currentTrainer.id}&date=${encodeURIComponent(date)}`;
                if (time) url += `&time=${encodeURIComponent(time)}`;
                const response = await fetch(url);
                const data = await response.json();
                
                if (data.success && !data.available) {
                    alert(data.message + '\n\nPlease select a different date or time.');
                    document.getElementById('appointmentTime').focus();
                    return false;
                }
            } catch (error) {
                console.error('Final availability check failed:', error);
            }
            
            // Gather form data
            pendingAppointmentData = {
                trainerId: currentTrainer.id,
                trainerName: currentTrainer.name,
                trainerBusiness: currentTrainer.business_name,
                trainingType: document.querySelector('input[name="trainingType"]:checked').value,
                petName: document.getElementById('petName').value,
                appointmentDate: document.getElementById('appointmentDate').value,
                appointmentTime: document.getElementById('appointmentTime').value,
                dogBreed: document.getElementById('dogBreed').value,
                trainingLocation: document.getElementById('trainingLocation').value,
                additionalNotes: document.getElementById('additionalNotes').value
            };
            
            // Add location details if applicable
            if (['home', 'park', 'other'].includes(pendingAppointmentData.trainingLocation)) {
                pendingAppointmentData.locationDistrict = document.getElementById('locationDistrict').value;
                pendingAppointmentData.mapLocation = document.getElementById('trainerLocationDisplay').value;
                pendingAppointmentData.mapLat = document.getElementById('trainerLocationLat').value;
                pendingAppointmentData.mapLng = document.getElementById('trainerLocationLng').value;
            }
            
            // Show confirmation dialog
            showConfirmation();
        }

        function showConfirmation() {
            const confirmDialog = document.getElementById('confirmationDialog');
            const detailsDiv = document.getElementById('confirmationDetails');
            
            // Format location text
            let locationText = pendingAppointmentData.trainingLocation;
            const locationMap = {
                'home': 'At My Home',
                'trainer': "At Trainer's Location",
                'park': 'At Nearby Park',
                'other': 'Other Location'
            };
            locationText = locationMap[locationText] || locationText;
            
            // Build confirmation details HTML
            let detailsHTML = `
                <div class="detail-item">
                    <span class="detail-label">Trainer:</span>
                    <span class="detail-value">${pendingAppointmentData.trainerBusiness || pendingAppointmentData.trainerName}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Training Type:</span>
                    <span class="detail-value training-type-badge training-type-${pendingAppointmentData.trainingType.toLowerCase()}">${pendingAppointmentData.trainingType}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Pet Name:</span>
                    <span class="detail-value">${pendingAppointmentData.petName}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Breed:</span>
                    <span class="detail-value">${pendingAppointmentData.dogBreed}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Date & Time:</span>
                    <span class="detail-value">${formatDate(pendingAppointmentData.appointmentDate)} at ${formatTime(pendingAppointmentData.appointmentTime)}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Location:</span>
                    <span class="detail-value">${locationText}</span>
                </div>
            `;
            
            // Add district and map if applicable
            if (pendingAppointmentData.locationDistrict) {
                detailsHTML += `
                    <div class="detail-item">
                        <span class="detail-label">District:</span>
                        <span class="detail-value">${pendingAppointmentData.locationDistrict}</span>
                    </div>
                `;
            }

            if (pendingAppointmentData.mapLocation) {
                detailsHTML += `
                    <div class="detail-item">
                        <span class="detail-label">Map Location:</span>
                        <span class="detail-value">${pendingAppointmentData.mapLocation}</span>
                    </div>
                `;
            }
            
            detailsDiv.innerHTML = detailsHTML;
            
            // Show confirmation dialog
            confirmDialog.classList.add('active');
        }

        function closeConfirmation() {
            const confirmDialog = document.getElementById('confirmationDialog');
            confirmDialog.classList.remove('active');
            pendingAppointmentData = null;
        }

        function confirmBooking() {
            const payload = pendingAppointmentData;
            if (!payload) return;

            const bookedTrainerId = Number(payload.trainerId);

            // Persist the request so it appears on trainer's Pending appointments
            fetch('/PETVET/api/pet-owner/create-training-request.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(async (res) => {
                const data = await res.json().catch(() => null);
                if (!res.ok || !data || data.success !== true) {
                    const msg = (data && data.message) ? data.message : 'Failed to submit booking. Please try again.';
                    throw new Error(msg);
                }
                return data;
            })
            .then(() => {
                // Close both modals
                closeConfirmation();
                closeTrainerDetails();

                // Update active booking ids so duplicate booking is blocked immediately
                if (!window.__activeBookingProviderIds) {
                    window.__activeBookingProviderIds = { trainers: [], sitters: [], breeders: [] };
                }
                const tList = Array.isArray(window.__activeBookingProviderIds.trainers) ? window.__activeBookingProviderIds.trainers : [];
                if (Number.isFinite(bookedTrainerId) && bookedTrainerId > 0 && !tList.map(Number).includes(bookedTrainerId)) {
                    tList.push(bookedTrainerId);
                }
                window.__activeBookingProviderIds.trainers = tList;

                alert('Appointment request submitted successfully!\n\nThe trainer will review it under Pending appointments.');
                pendingAppointmentData = null;
            })
            .catch((err) => {
                console.error('Booking submit failed:', err);
                alert((err && err.message) ? err.message : 'Failed to submit booking. Please try again.');
            });
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        }

        function formatTime(timeString) {
            const [hours, minutes] = timeString.split(':');
            const hour = parseInt(hours);
            const ampm = hour >= 12 ? 'PM' : 'AM';
            const displayHour = hour % 12 || 12;
            return `${displayHour}:${minutes} ${ampm}`;
        }

        // Sitter Booking Modal Functions
        let currentSitter = null;
        let pendingSitterBookingData = null;

        const breedOptions = {
            dog: [
                "Labrador Retriever", "German Shepherd", "Golden Retriever", "Rottweiler",
                "Doberman", "Bulldog", "Poodle", "Beagle", "Boxer", "Dachshund",
                "Siberian Husky", "Great Dane", "Chihuahua", "Yorkshire Terrier",
                "Shih Tzu", "Mixed Breed", "Other"
            ],
            cat: [
                "Persian", "Maine Coon", "Siamese", "British Shorthair",
                "Ragdoll", "Bengal", "Sphynx", "American Shorthair",
                "Russian Blue", "Mixed Breed", "Other"
            ],
            bird: [
                "Parrot", "Cockatiel", "Budgerigar", "Canary",
                "Lovebird", "Cockatoo", "African Grey", "Macaw",
                "Finch", "Other"
            ],
            other: ["Please specify in notes"]
        };

        function updateBreedOptions() {
            const petType = document.getElementById('petType').value;
            const breedSelect = document.getElementById('petBreed');
            const breedGroup = document.querySelector('[for="petBreed"]').parentElement;
            
            if (petType === 'other') {
                // Hide breed selection for 'other' and set a default value
                breedGroup.style.display = 'none';
                breedSelect.value = 'Other';
                breedSelect.required = false;
            } else {
                // Show breed selection and make it required for other pet types
                breedGroup.style.display = 'block';
                breedSelect.required = true;
                
                // Clear existing options
                breedSelect.innerHTML = '<option value="">Select breed</option>';
                
                if (petType && breedOptions[petType]) {
                    breedOptions[petType].forEach(breed => {
                        const option = document.createElement('option');
                        option.value = breed;
                        option.textContent = breed;
                        breedSelect.appendChild(option);
                    });
                } else {
                    breedSelect.innerHTML = '<option value="">Select pet type first</option>';
                }
            }

            updateSitterSubmitButtonState();
        }

        function openSitterBooking(sitterData, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            currentSitter = sitterData;
            const modal = document.getElementById('sitterBookingModal');

            isSitterAlreadyBooked = isProviderAlreadyBooked('sitters', sitterData.id);
            
            // Populate sitter info
            document.getElementById('sitterModalAvatar').src = sitterData.avatar;
            document.getElementById('sitterModalName').textContent = sitterData.name;
            document.getElementById('sitterModalBusiness').textContent = sitterData.business_name || '';
            document.getElementById('sitterModalHomeType').textContent = sitterData.home_type;
            document.getElementById('sitterModalCity').textContent = sitterData.city;
            document.getElementById('sitterModalExperience').textContent = sitterData.experience_years + ' years';
            
            // Reset form
            document.getElementById('sitterBookingForm').reset();
            const multiFields = document.getElementById('multipleDaysFields');
            if (multiFields) multiFields.style.display = 'none';
            document.getElementById('sitterAdditionalLocationFields').style.display = 'none';
            document.getElementById('petBreed').innerHTML = '<option value="">Select pet type first</option>';
            document.getElementById('petBreed').required = true;
            document.querySelector('[for="petBreed"]').parentElement.style.display = 'block';

            // Gate location until appointment date/time is valid (like trainer flow)
            const sitterLocationField = document.getElementById('sitterLocation');
            if (sitterLocationField) sitterLocationField.disabled = true;

            // Reset appointment date/time fields
            const apptDate = document.getElementById('sitterAppointmentDate');
            const apptTime = document.getElementById('sitterAppointmentTime');
            const apptDateMsg = document.getElementById('sitterApptDateAvailabilityMsg');
            const apptTimeMsg = document.getElementById('sitterApptTimeAvailabilityMsg');
            if (apptDate) apptDate.value = '';
            if (apptTime) {
                apptTime.value = '';
                apptTime.disabled = true;
            }
            if (apptDateMsg) apptDateMsg.style.display = 'none';
            if (apptTimeMsg) apptTimeMsg.style.display = 'none';

            // Reset sitter availability state
            isSitterDateTimeValid = false;
            const dateMsgMulti = document.getElementById('sitterDateAvailabilityMsgMulti');
            const timeMsgMulti = document.getElementById('sitterTimeAvailabilityMsgMulti');
            if (dateMsgMulti) dateMsgMulti.style.display = 'none';
            if (timeMsgMulti) timeMsgMulti.style.display = 'none';

            // Keep duration start fields in sync and non-editable
            const startDate = document.getElementById('startDate');
            const startTimeMulti = document.getElementById('startTimeMulti');
            if (startDate) startDate.disabled = true;
            if (startTimeMulti) startTimeMulti.disabled = true;

            // Add input listeners for submit button state
            const formFields = document.querySelectorAll('#sitterBookingForm input, #sitterBookingForm select, #sitterBookingForm textarea');
            formFields.forEach(field => {
                field.removeEventListener('input', updateSitterSubmitButtonState);
                field.removeEventListener('change', updateSitterSubmitButtonState);
                field.addEventListener('input', updateSitterSubmitButtonState);
                field.addEventListener('change', updateSitterSubmitButtonState);
            });

            // Ensure availability checks run reliably for appointment fields
            if (apptDate) {
                apptDate.removeEventListener('input', checkSitterAvailability);
                apptDate.removeEventListener('change', checkSitterAvailability);
                apptDate.addEventListener('input', checkSitterAvailability);
                apptDate.addEventListener('change', checkSitterAvailability);
            }
            if (apptTime) {
                apptTime.removeEventListener('input', checkSitterAvailability);
                apptTime.removeEventListener('change', checkSitterAvailability);
                apptTime.addEventListener('input', checkSitterAvailability);
                apptTime.addEventListener('change', checkSitterAvailability);
            }

            // Reset submit button state
            const submitBtn = document.getElementById('sitterSubmitBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
            }

            // Load owner name and pets for datalist + auto-fill
            loadOwnerAndPets();
            updateSitterSubmitButtonState();
            
            // Lock scroll and save position
            savedScrollPosition = window.pageYOffset || document.documentElement.scrollTop;
            document.documentElement.style.overflow = 'hidden';
            document.body.style.overflow = 'hidden';
            document.body.style.position = 'relative';
            modal.classList.add('active');
            document.body.classList.add('modal-open');
        }

        function closeSitterBooking() {
            const modal = document.getElementById('sitterBookingModal');
            modal.classList.remove('active');
            document.body.classList.remove('modal-open');
            
            // Restore scroll and position
            document.documentElement.style.overflow = '';
            document.body.style.overflow = '';
            document.body.style.position = '';
            window.scrollTo(0, savedScrollPosition);
            currentSitter = null;
            isSitterAlreadyBooked = false;
        }

        function toggleDurationFields() {
            const durationType = document.getElementById('durationType').value;
            const singleFields = document.getElementById('singleDayFields');
            const multiFields = document.getElementById('multipleDaysFields');
            const daysField = document.getElementById('sitterNumberOfDays');
            
            // Reset all fields first
            if (singleFields) singleFields.style.display = 'none';
            if (multiFields) multiFields.style.display = 'none';
            
            // Clear required attributes
            const singleDateEl = document.getElementById('singleDate');
            const startTimeEl = document.getElementById('startTime');
            const endTimeEl = document.getElementById('endTime');
            if (singleDateEl) singleDateEl.required = false;
            if (startTimeEl) startTimeEl.required = false;
            if (endTimeEl) endTimeEl.required = false;
            if (daysField) daysField.required = false;
            
            if (durationType === 'single') {
                if (singleFields) singleFields.style.display = 'block';
            } else if (durationType === 'multiple') {
                if (multiFields) multiFields.style.display = 'block';
                if (daysField) daysField.required = true;
            }

            // Reset and re-check sitter availability
            isSitterDateTimeValid = false;
            const sitterLocationField = document.getElementById('sitterLocation');
            if (sitterLocationField) sitterLocationField.disabled = true;
            syncSitterDurationStartFromAppointment();
            checkSitterAvailability();
            updateSitterSubmitButtonState();
        }

        function toggleSitterLocationFields() {
            const location = document.getElementById('sitterLocation').value;
            const additionalFields = document.getElementById('sitterAdditionalLocationFields');
            const districtField = document.getElementById('sitterDistrict');
            const districtRow = document.getElementById('sitterDistrictRow');
            const districtMsg = document.getElementById('sitterDistrictMsg');
            const mapDisplayField = document.getElementById('sitterLocationDisplay');
            const latField = document.getElementById('sitterLocationLat');
            const lngField = document.getElementById('sitterLocationLng');
            
            const showFields = ['my-home', 'park', 'other'].includes(location);
            
            if (showFields) {
                additionalFields.style.display = 'block';
                if (mapDisplayField) mapDisplayField.required = true;
                if (districtField) districtField.required = false;
                if (districtRow) districtRow.style.display = 'none';
                if (districtMsg) districtMsg.style.display = 'none';
                if (districtField) {
                    districtField.style.pointerEvents = 'none';
                    districtField.style.backgroundColor = '#f5f5f5';
                }
                initSitterLocationMap();
            } else {
                additionalFields.style.display = 'none';
                if (mapDisplayField) mapDisplayField.required = false;
                if (districtField) districtField.required = false;
                if (districtField) districtField.value = '';
                if (mapDisplayField) mapDisplayField.value = '';
                if (latField) latField.value = '';
                if (lngField) lngField.value = '';
                if (districtRow) districtRow.style.display = 'none';
                if (districtMsg) districtMsg.style.display = 'none';
                if (districtField) {
                    districtField.style.pointerEvents = 'none';
                    districtField.style.backgroundColor = '#f5f5f5';
                }
            }

            updateSitterSubmitButtonState();
        }

        // Sitter map state
        let sitterMap = null;
        let sitterMarker = null;
        let sitterLastMapInteractionAt = 0;

        function initSitterLocationMap() {
            const container = document.getElementById('sitterLeafletMapContainer');
            if (!container) return;
            if (typeof L === 'undefined') {
                console.error('Leaflet is not loaded (L is undefined)');
                return;
            }

            if (!sitterMap) {
                const latInput = document.getElementById('sitterLocationLat');
                const lngInput = document.getElementById('sitterLocationLng');
                const displayInput = document.getElementById('sitterLocationDisplay');
                const districtRow = document.getElementById('sitterDistrictRow');
                const districtSelect = document.getElementById('sitterDistrict');
                const districtMsg = document.getElementById('sitterDistrictMsg');

                const centerLat = parseFloat(latInput?.value) || SL_DEFAULT_LAT;
                const centerLng = parseFloat(lngInput?.value) || SL_DEFAULT_LNG;

                sitterMap = L.map('sitterLeafletMapContainer').setView([centerLat, centerLng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 19
                }).addTo(sitterMap);

                const markInteraction = () => { sitterLastMapInteractionAt = Date.now(); };
                sitterMap.on('dragstart', markInteraction);
                sitterMap.on('dragend', markInteraction);
                sitterMap.on('zoomstart', markInteraction);
                sitterMap.on('zoomend', markInteraction);
                sitterMap.on('movestart', markInteraction);
                sitterMap.on('moveend', markInteraction);

                async function applySitterLocation(lat, lng, shouldCenter = true) {
                    if (!latInput || !lngInput || !displayInput) return;
                    latInput.value = Number(lat).toFixed(6);
                    lngInput.value = Number(lng).toFixed(6);

                    if (sitterMarker) {
                        sitterMarker.setLatLng([lat, lng]);
                    } else {
                        sitterMarker = L.marker([lat, lng]).addTo(sitterMap);
                    }

                    if (shouldCenter) {
                        sitterMap.setView([lat, lng], Math.max(sitterMap.getZoom() || 13, 15));
                    }

                    const geo = await reverseGeocodeForLocation(lat, lng);
                    displayInput.value = (geo && geo.location) ? geo.location : `Lat: ${Number(lat).toFixed(6)}, Lng: ${Number(lng).toFixed(6)}`;
                    setDistrictAuto(districtSelect, districtRow, districtMsg, geo ? geo.district : null);

                    updateSitterSubmitButtonState();
                }

                sitterMap.on('click', async function(e) {
                    if (Date.now() - sitterLastMapInteractionAt < 450) return;
                    e.originalEvent.preventDefault();
                    e.originalEvent.stopPropagation();
                    await applySitterLocation(e.latlng.lat, e.latlng.lng, false);
                });

                const useBtn = document.getElementById('sitterUseMyLocationBtn');
                if (useBtn) {
                    useBtn.addEventListener('click', (ev) => {
                        ev.preventDefault();
                        ev.stopPropagation();

                        if (!navigator.geolocation) {
                            alert('Geolocation is not supported by your browser.');
                            return;
                        }

                        useBtn.disabled = true;
                        const oldText = useBtn.textContent;
                        useBtn.textContent = 'Getting location...';

                        navigator.geolocation.getCurrentPosition(
                            async (pos) => {
                                const lat = pos.coords.latitude;
                                const lng = pos.coords.longitude;
                                await applySitterLocation(lat, lng, true);
                                useBtn.disabled = false;
                                useBtn.textContent = oldText;
                            },
                            (err) => {
                                console.error('Geolocation error:', err);
                                alert('Unable to get your current location. Please click on the map to select.');
                                useBtn.disabled = false;
                                useBtn.textContent = oldText;
                            },
                            { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
                        );
                    });
                }
            }

            setTimeout(() => {
                if (sitterMap) sitterMap.invalidateSize();
            }, 120);
        }

        async function submitSitterBooking(event) {
            event.preventDefault();

            const form = document.getElementById('sitterBookingForm');
            if (form && !form.checkValidity()) {
                form.reportValidity();
                updateSitterSubmitButtonState();
                return;
            }

            // Final sitter availability check (like trainer) for the selected start date/time
            try {
                const date = document.getElementById('sitterAppointmentDate')?.value || '';
                const time = document.getElementById('sitterAppointmentTime')?.value || '';

                if (date && time && currentSitter && currentSitter.id) {
                    let url = `/PETVET/api/check-sitter-availability.php?sitter_id=${currentSitter.id}&date=${encodeURIComponent(date)}&time=${encodeURIComponent(time)}`;
                    const response = await fetch(url);
                    const data = await response.json();
                    if (data && data.success && !data.available) {
                        alert((data.message || 'Sitter is unavailable for the selected date/time.') + '\n\nPlease select a different date or time.');
                        updateSitterSubmitButtonState();
                        return;
                    }
                }
            } catch (error) {
                console.error('Final sitter availability check failed:', error);
            }
            
            const durationType = document.getElementById('durationType').value;
            
            pendingSitterBookingData = {
                sitterId: currentSitter.id,
                sitterName: currentSitter.name,
                sitterBusiness: currentSitter.business_name,
                petName: document.getElementById('sitterPetName').value,
                petType: document.getElementById('petType').value,
                petBreed: document.getElementById('petBreed').value,
                serviceType: document.getElementById('serviceType').value,
                durationType: durationType,
                location: document.getElementById('sitterLocation').value,
                notes: document.getElementById('sitterNotes').value
            };
            
            if (durationType === 'single') {
                const d = document.getElementById('sitterAppointmentDate')?.value || '';
                const t = document.getElementById('sitterAppointmentTime')?.value || '';
                pendingSitterBookingData.date = d;
                pendingSitterBookingData.startTime = t;
                // Single-day no longer has an end-time selector; keep confirmation stable
                pendingSitterBookingData.endTime = t;
            } else if (durationType === 'multiple') {
                const startDate = document.getElementById('sitterAppointmentDate')?.value || '';
                const startTime = document.getElementById('sitterAppointmentTime')?.value || '';
                const days = parseInt(document.getElementById('sitterNumberOfDays')?.value || '0', 10);

                pendingSitterBookingData.startDate = startDate;
                pendingSitterBookingData.startTime = startTime;
                pendingSitterBookingData.numberOfDays = days;

                // Compute an end date for confirmation/details (inclusive range)
                if (startDate && days > 0) {
                    const d = new Date(startDate + 'T00:00:00');
                    d.setDate(d.getDate() + Math.max(days - 1, 0));
                    const mm = String(d.getMonth() + 1).padStart(2, '0');
                    const dd = String(d.getDate()).padStart(2, '0');
                    pendingSitterBookingData.endDate = `${d.getFullYear()}-${mm}-${dd}`;
                } else {
                    pendingSitterBookingData.endDate = '';
                }
                // No separate end time selection; keep same time
                pendingSitterBookingData.endTime = startTime;
            }
            
            if (['my-home', 'park', 'other'].includes(pendingSitterBookingData.location)) {
                pendingSitterBookingData.district = document.getElementById('sitterDistrict').value;
                pendingSitterBookingData.mapLocation = document.getElementById('sitterLocationDisplay').value;
                pendingSitterBookingData.mapLat = document.getElementById('sitterLocationLat').value;
                pendingSitterBookingData.mapLng = document.getElementById('sitterLocationLng').value;
            }
            
            showSitterConfirmation();
        }

        function showSitterConfirmation() {
            const confirmDialog = document.getElementById('sitterConfirmationDialog');
            const detailsDiv = document.getElementById('sitterConfirmationDetails');
            
            const serviceTypeMap = {
                'dog-walking': 'Dog Walking',
                'pet-sitting': 'Pet Sitting (Daily Visits)',
                'overnight-care': 'Overnight Care',
                'house-sitting': 'House Sitting with Pets',
                'daycare': 'Pet Daycare'
            };
            
            const locationMap = {
                'my-home': 'At My Home',
                'sitter-home': "At Sitter's Home",
                'park': 'At Nearby Park',
                'other': 'Other Location'
            };
            
            let dateTimeInfo = '';
            if (pendingSitterBookingData.durationType === 'single') {
                dateTimeInfo = `${formatDate(pendingSitterBookingData.date)} from ${formatTime(pendingSitterBookingData.startTime)} to ${formatTime(pendingSitterBookingData.endTime)}`;
            } else {
                const startDateStr = pendingSitterBookingData.startDate || '';
                const startTimeStr = pendingSitterBookingData.startTime || '';
                const endTimeStr = pendingSitterBookingData.endTime || startTimeStr;
                const requestedDays = parseInt(pendingSitterBookingData.numberOfDays || '0', 10);

                // If endDate was not populated (or days changed), derive it from startDate + (days-1)
                let endDateStr = pendingSitterBookingData.endDate || '';
                if (startDateStr && requestedDays > 1) {
                    try {
                        const d = new Date(startDateStr + 'T00:00:00');
                        d.setDate(d.getDate() + Math.max(requestedDays - 1, 0));
                        const mm = String(d.getMonth() + 1).padStart(2, '0');
                        const dd = String(d.getDate()).padStart(2, '0');
                        endDateStr = `${d.getFullYear()}-${mm}-${dd}`;
                    } catch (e) {
                        // leave endDateStr as-is
                    }
                }

                const daySuffix = (requestedDays > 1) ? ` (${requestedDays} days)` : '';
                dateTimeInfo = `${formatDate(startDateStr)} ${formatTime(startTimeStr)} to ${formatDate(endDateStr || startDateStr)} ${formatTime(endTimeStr)}${daySuffix}`;
            }
            
            let detailsHTML = `
                <div class="detail-item">
                    <span class="detail-label">Sitter:</span>
                    <span class="detail-value">${pendingSitterBookingData.sitterBusiness || pendingSitterBookingData.sitterName}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Pet Name:</span>
                    <span class="detail-value">${pendingSitterBookingData.petName}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Pet Type:</span>
                    <span class="detail-value">${pendingSitterBookingData.petType.charAt(0).toUpperCase() + pendingSitterBookingData.petType.slice(1)}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Breed:</span>
                    <span class="detail-value">${pendingSitterBookingData.petBreed}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Service:</span>
                    <span class="detail-value">${serviceTypeMap[pendingSitterBookingData.serviceType]}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Duration:</span>
                    <span class="detail-value">${dateTimeInfo}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Location:</span>
                    <span class="detail-value">${locationMap[pendingSitterBookingData.location]}</span>
                </div>
            `;
            
            if (pendingSitterBookingData.district) {
                detailsHTML += `
                    <div class="detail-item">
                        <span class="detail-label">District:</span>
                        <span class="detail-value">${pendingSitterBookingData.district}</span>
                    </div>
                `;
            }

            if (pendingSitterBookingData.mapLocation) {
                detailsHTML += `
                    <div class="detail-item">
                        <span class="detail-label">Map Location:</span>
                        <span class="detail-value">${pendingSitterBookingData.mapLocation}</span>
                    </div>
                `;
            }
            
            detailsDiv.innerHTML = detailsHTML;
            confirmDialog.classList.add('active');
        }

        function closeSitterConfirmation() {
            const confirmDialog = document.getElementById('sitterConfirmationDialog');
            confirmDialog.classList.remove('active');
            pendingSitterBookingData = null;
        }

        function confirmSitterBooking() {
            const payload = pendingSitterBookingData;
            if (!payload) return;

            const bookedSitterId = Number(payload.sitterId);

            fetch('/PETVET/api/pet-owner/create-sitter-request.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(async (res) => {
                const data = await res.json().catch(() => null);
                if (!res.ok || !data || data.success !== true) {
                    const msg = (data && data.message) ? data.message : 'Failed to submit booking. Please try again.';
                    throw new Error(msg);
                }
                return data;
            })
            .then(() => {
                closeSitterConfirmation();
                closeSitterBooking();

                // Update active booking ids so card button greys out immediately
                if (!window.__activeBookingProviderIds) {
                    window.__activeBookingProviderIds = { trainers: [], sitters: [], breeders: [] };
                }
                const sList = Array.isArray(window.__activeBookingProviderIds.sitters) ? window.__activeBookingProviderIds.sitters : [];
                if (Number.isFinite(bookedSitterId) && bookedSitterId > 0 && !sList.map(Number).includes(bookedSitterId)) {
                    sList.push(bookedSitterId);
                }
                window.__activeBookingProviderIds.sitters = sList;
                updateSitterCardButtons();

                alert('Booking request submitted successfully!\n\nThe sitter will review it under Pending bookings.');
                pendingSitterBookingData = null;
            })
            .catch((err) => {
                console.error('Sitter booking submit failed:', err);
                alert((err && err.message) ? err.message : 'Failed to submit booking. Please try again.');
            });
        }

        // Breeder Booking Modal Functions
        let currentBreeder = null;
        let pendingBreedingData = null;
        let isBreederAlreadyBooked = false;

        // Breeder map state (same behavior as trainer)
        let breederMap = null;
        let breederMarker = null;
        let breederLastMapInteractionAt = 0;

        function initBreederLocationMap() {
            const container = document.getElementById('breederMapContainer');
            if (!container) return;
            if (typeof L === 'undefined') {
                console.error('Leaflet is not loaded (L is undefined)');
                return;
            }

            if (!breederMap) {
                const latInput = document.getElementById('breederLocationLat');
                const lngInput = document.getElementById('breederLocationLng');
                const displayInput = document.getElementById('breederLocationDisplay');
                const districtRow = document.getElementById('breederDistrictRow');
                const districtSelect = document.getElementById('breederLocationDistrict');
                const districtMsg = document.getElementById('breederDistrictMsg');

                const centerLat = parseFloat(latInput?.value) || SL_DEFAULT_LAT;
                const centerLng = parseFloat(lngInput?.value) || SL_DEFAULT_LNG;

                breederMap = L.map('breederMapContainer').setView([centerLat, centerLng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 19
                }).addTo(breederMap);

                const markInteraction = () => { breederLastMapInteractionAt = Date.now(); };
                breederMap.on('dragstart', markInteraction);
                breederMap.on('dragend', markInteraction);
                breederMap.on('zoomstart', markInteraction);
                breederMap.on('zoomend', markInteraction);
                breederMap.on('movestart', markInteraction);
                breederMap.on('moveend', markInteraction);

                async function applyBreederLocation(lat, lng, shouldCenter = true) {
                    if (!latInput || !lngInput || !displayInput) return;
                    latInput.value = Number(lat).toFixed(6);
                    lngInput.value = Number(lng).toFixed(6);

                    if (breederMarker) {
                        breederMarker.setLatLng([lat, lng]);
                    } else {
                        breederMarker = L.marker([lat, lng]).addTo(breederMap);
                    }

                    if (shouldCenter) {
                        breederMap.setView([lat, lng], Math.max(breederMap.getZoom() || 13, 15));
                    }

                    const geo = await reverseGeocodeForLocation(lat, lng);
                    displayInput.value = (geo && geo.location) ? geo.location : `Lat: ${Number(lat).toFixed(6)}, Lng: ${Number(lng).toFixed(6)}`;
                    setDistrictAuto(districtSelect, districtRow, districtMsg, geo ? geo.district : null);
                    updateBreederSubmitButtonState();
                }

                breederMap.on('click', async function(e) {
                    if (Date.now() - breederLastMapInteractionAt < 450) return;
                    e.originalEvent.preventDefault();
                    e.originalEvent.stopPropagation();
                    await applyBreederLocation(e.latlng.lat, e.latlng.lng, false);
                });

                const useBtn = document.getElementById('breederUseMyLocationBtn');
                if (useBtn) {
                    useBtn.addEventListener('click', (ev) => {
                        ev.preventDefault();
                        ev.stopPropagation();

                        if (!navigator.geolocation) {
                            alert('Geolocation is not supported by your browser.');
                            return;
                        }

                        useBtn.disabled = true;
                        const oldText = useBtn.textContent;
                        useBtn.textContent = 'Getting location...';

                        navigator.geolocation.getCurrentPosition(
                            async (pos) => {
                                const lat = pos.coords.latitude;
                                const lng = pos.coords.longitude;
                                await applyBreederLocation(lat, lng, true);
                                useBtn.disabled = false;
                                useBtn.textContent = oldText;
                            },
                            (err) => {
                                console.error('Geolocation error:', err);
                                alert('Unable to get your current location. Please click on the map to select.');
                                useBtn.disabled = false;
                                useBtn.textContent = oldText;
                            },
                            { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
                        );
                    });
                }
            }

            setTimeout(() => {
                if (breederMap) breederMap.invalidateSize();
            }, 120);
        }

        function toggleBreederLocationFields() {
            const locationSelect = document.getElementById('breedingLocation');
            const additionalFields = document.getElementById('breederAdditionalLocationFields');
            const districtField = document.getElementById('breederLocationDistrict');
            const districtRow = document.getElementById('breederDistrictRow');
            const districtMsg = document.getElementById('breederDistrictMsg');
            const mapDisplayField = document.getElementById('breederLocationDisplay');
            const latField = document.getElementById('breederLocationLat');
            const lngField = document.getElementById('breederLocationLng');

            if (!locationSelect || !additionalFields) {
                updateBreederSubmitButtonState();
                return;
            }

            const showFields = ['home', 'park', 'other'].includes(locationSelect.value);
            if (showFields) {
                additionalFields.style.display = 'block';

                if (mapDisplayField) mapDisplayField.required = true;
                if (districtField) districtField.required = false;

                if (districtRow) districtRow.style.display = 'none';
                if (districtMsg) districtMsg.style.display = 'none';
                if (districtField) {
                    districtField.style.pointerEvents = 'none';
                    districtField.style.backgroundColor = '#f5f5f5';
                }

                initBreederLocationMap();
            } else {
                additionalFields.style.display = 'none';
                if (mapDisplayField) mapDisplayField.required = false;
                if (districtField) districtField.required = false;

                if (districtField) districtField.value = '';
                if (mapDisplayField) mapDisplayField.value = '';
                if (latField) latField.value = '';
                if (lngField) lngField.value = '';
                if (districtRow) districtRow.style.display = 'none';
                if (districtMsg) districtMsg.style.display = 'none';
                if (districtField) {
                    districtField.style.pointerEvents = 'none';
                    districtField.style.backgroundColor = '#f5f5f5';
                }
            }

            updateBreederSubmitButtonState();
        }

        function openBreederDetails(breederData, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            currentBreeder = breederData;
            const modal = document.getElementById('breederDetailsModal');

            isBreederAlreadyBooked = isProviderAlreadyBooked('breeders', breederData.id);
            
            // Populate breeder info
            document.getElementById('breederModalAvatar').src = breederData.avatar;
            document.getElementById('breederModalName').textContent = breederData.name;
            document.getElementById('breederModalBusiness').textContent = breederData.business_name || '';
            document.getElementById('breederModalCity').textContent = breederData.city;
            document.getElementById('breederModalExperience').textContent = breederData.experience_years + ' years';
            document.getElementById('breederModalPhone').textContent = breederData.phone || 'N/A';
            const breederDescSection = document.getElementById('breederDescriptionSection');
            const breederDescEl = document.getElementById('breederModalDescription');
            if (breederData.description) {
                breederDescEl.textContent = breederData.description;
                breederDescSection.style.display = 'block';
            } else {
                breederDescEl.textContent = '';
                breederDescSection.style.display = 'none';
            }
            
            // Reset form
            document.getElementById('breedingBookingForm').reset();

            // Gate location until a valid date is selected (trainer-like flow)
            const breederLocationField = document.getElementById('breedingLocation');
            if (breederLocationField) {
                breederLocationField.disabled = true;
                breederLocationField.value = '';
            }

            // Reset availability state + field validity/messages
            isBreederDateTimeValid = false;
            const breederDateMsg = document.getElementById('breederDateAvailabilityMsg');
            const breederTimeMsg = document.getElementById('breederTimeAvailabilityMsg');
            if (breederDateMsg) breederDateMsg.style.display = 'none';
            if (breederTimeMsg) breederTimeMsg.style.display = 'none';
            const breederDateField = document.getElementById('breedingDate');
            const breederTimeField = document.getElementById('breedingTime');
            if (breederDateField) breederDateField.setCustomValidity('');
            if (breederTimeField) breederTimeField.setCustomValidity('');

            // Hide additional location fields by default
            const addLoc = document.getElementById('breederAdditionalLocationFields');
            if (addLoc) addLoc.style.display = 'none';
            const mapDisplayField = document.getElementById('breederLocationDisplay');
            if (mapDisplayField) {
                mapDisplayField.value = '';
                // Important: this input is marked required in HTML, but the section starts hidden.
                // Keep it non-required until a location that needs a map is selected.
                mapDisplayField.required = false;
            }
            const latField = document.getElementById('breederLocationLat');
            const lngField = document.getElementById('breederLocationLng');
            if (latField) latField.value = '';
            if (lngField) lngField.value = '';

            const districtRow = document.getElementById('breederDistrictRow');
            const districtMsg = document.getElementById('breederDistrictMsg');
            const districtField = document.getElementById('breederLocationDistrict');
            if (districtRow) districtRow.style.display = 'none';
            if (districtMsg) districtMsg.style.display = 'none';
            if (districtField) {
                districtField.value = '';
                districtField.required = false;
                districtField.style.pointerEvents = 'none';
                districtField.style.backgroundColor = '#f5f5f5';
            }

            // Add input listeners for submit button state
            const breederFormFields = document.querySelectorAll('#breedingBookingForm input, #breedingBookingForm select, #breedingBookingForm textarea');
            breederFormFields.forEach(field => {
                field.removeEventListener('input', updateBreederSubmitButtonState);
                field.removeEventListener('change', updateBreederSubmitButtonState);
                field.addEventListener('input', updateBreederSubmitButtonState);
                field.addEventListener('change', updateBreederSubmitButtonState);
            });

            // Enable location only after a date is selected
            const breederDateField2 = document.getElementById('breedingDate');
            if (breederDateField2) {
                breederDateField2.removeEventListener('change', handleBreederDateChange);
                breederDateField2.removeEventListener('input', handleBreederDateChange);
                breederDateField2.addEventListener('change', handleBreederDateChange);
                breederDateField2.addEventListener('input', handleBreederDateChange);
            }

            // Appointment time (trainer-like): enabled only after date is picked
            const breederTimeField2 = document.getElementById('breedingTime');
            if (breederTimeField2) {
                breederTimeField2.value = '';
                breederTimeField2.disabled = true;
                breederTimeField2.removeEventListener('change', handleBreederDateChange);
                breederTimeField2.removeEventListener('input', handleBreederDateChange);
                breederTimeField2.addEventListener('change', handleBreederDateChange);
                breederTimeField2.addEventListener('input', handleBreederDateChange);
            }

            const breederSubmitBtn = document.getElementById('breederSubmitBtn');
            if (breederSubmitBtn) breederSubmitBtn.disabled = true;

            // Load owner name and pets (same source as trainer/sitter)
            loadOwnerAndPets();
            updateBreederSubmitButtonState();
            
            // Lock scroll and save position
            savedScrollPosition = window.pageYOffset || document.documentElement.scrollTop;
            document.documentElement.style.overflow = 'hidden';
            document.body.style.overflow = 'hidden';
            document.body.style.position = 'relative';
            modal.classList.add('active');
            document.body.classList.add('modal-open');
        }

        function handleBreederDateChange() {
            const breederDateField = document.getElementById('breedingDate');
            const breederTimeField = document.getElementById('breedingTime');
            const breederLocationField = document.getElementById('breedingLocation');
            if (!breederDateField || !breederLocationField) {
                updateBreederSubmitButtonState();
                return;
            }

            const date = breederDateField.value;

            // If date is cleared, reset downstream fields.
            if (!date) {
                if (breederTimeField) {
                    breederTimeField.value = '';
                    breederTimeField.disabled = true;
                    breederTimeField.setCustomValidity('');
                }

                breederLocationField.disabled = true;
                breederLocationField.value = '';
                toggleBreederLocationFields();
                isBreederDateTimeValid = false;
                updateBreederSubmitButtonState();
                return;
            }

            // Let availability check decide whether time/location are enabled.
            if (typeof checkBreederAvailability === 'function') {
                checkBreederAvailability();
            }
            updateBreederSubmitButtonState();
        }

        function closeBreederDetails() {
            const modal = document.getElementById('breederDetailsModal');
            modal.classList.remove('active');
            document.body.classList.remove('modal-open');
            
            // Restore scroll and position
            document.documentElement.style.overflow = '';
            document.body.style.overflow = '';
            document.body.style.position = '';
            window.scrollTo(0, savedScrollPosition);
            currentBreeder = null;
            isBreederAlreadyBooked = false;
        }

        function submitBreedingBooking(event) {
            event.preventDefault();

            const form = document.getElementById('breedingBookingForm');
            if (form && !form.checkValidity()) {
                form.reportValidity();
                updateBreederSubmitButtonState();
                return;
            }

            if (isBreederAlreadyBooked) {
                alert('You already have an active booking with this breeder.');
                return;
            }
            
            pendingBreedingData = {
                breederId: currentBreeder.id,
                breederName: currentBreeder.name,
                breederBusiness: currentBreeder.business_name,
                petName: document.getElementById('breedingPetName').value,
                petGender: document.getElementById('breedingPetGender').value,
                petBreed: document.getElementById('breedingPetBreed').value,
                breedingDate: document.getElementById('breedingDate').value,
                breedingTime: document.getElementById('breedingTime') ? document.getElementById('breedingTime').value : '',
                notes: document.getElementById('breedingNotes').value,
                breedingLocation: document.getElementById('breedingLocation') ? document.getElementById('breedingLocation').value : ''
            };

            if (['home', 'park', 'other'].includes(pendingBreedingData.breedingLocation)) {
                pendingBreedingData.locationDistrict = document.getElementById('breederLocationDistrict') ? document.getElementById('breederLocationDistrict').value : '';
                pendingBreedingData.mapLocation = document.getElementById('breederLocationDisplay') ? document.getElementById('breederLocationDisplay').value : '';
                pendingBreedingData.mapLat = document.getElementById('breederLocationLat') ? document.getElementById('breederLocationLat').value : '';
                pendingBreedingData.mapLng = document.getElementById('breederLocationLng') ? document.getElementById('breederLocationLng').value : '';
            }
            
            showBreederConfirmation();
        }

        function showBreederConfirmation() {
            const confirmDialog = document.getElementById('breederConfirmationDialog');
            const detailsDiv = document.getElementById('breederConfirmationDetails');
            
            const locationMap = {
                'home': 'At My Home',
                'breeder': "At Breeder's Location",
                'park': 'At Nearby Park',
                'other': 'Other Location'
            };

            let detailsHTML = `
                <div class="detail-item">
                    <span class="detail-label">Breeder:</span>
                    <span class="detail-value">${pendingBreedingData.breederBusiness || pendingBreedingData.breederName}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Pet Name:</span>
                    <span class="detail-value">${pendingBreedingData.petName}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Breed:</span>
                    <span class="detail-value">${pendingBreedingData.petBreed}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Gender:</span>
                    <span class="detail-value">${pendingBreedingData.petGender}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Date & Time:</span>
                    <span class="detail-value">${formatDate(pendingBreedingData.breedingDate)}${pendingBreedingData.breedingTime ? (' at ' + formatTime(pendingBreedingData.breedingTime)) : ''}</span>
                </div>`;

            if (pendingBreedingData.breedingLocation) {
                detailsHTML += `
                    <div class="detail-item">
                        <span class="detail-label">Location:</span>
                        <span class="detail-value">${locationMap[pendingBreedingData.breedingLocation] || pendingBreedingData.breedingLocation}</span>
                    </div>`;
            }

            if (pendingBreedingData.locationDistrict) {
                detailsHTML += `
                    <div class="detail-item">
                        <span class="detail-label">District:</span>
                        <span class="detail-value">${pendingBreedingData.locationDistrict}</span>
                    </div>`;
            }

            if (pendingBreedingData.mapLocation) {
                detailsHTML += `
                    <div class="detail-item">
                        <span class="detail-label">Map Location:</span>
                        <span class="detail-value">${pendingBreedingData.mapLocation}</span>
                    </div>`;
            }

            if (pendingBreedingData.notes) {
                detailsHTML += `
                    <div class="detail-item">
                        <span class="detail-label">Notes:</span>
                        <span class="detail-value">${pendingBreedingData.notes}</span>
                    </div>`;
            }
            
            detailsDiv.innerHTML = detailsHTML;
            confirmDialog.classList.add('active');
        }

        function closeBreederConfirmation() {
            const confirmDialog = document.getElementById('breederConfirmationDialog');
            confirmDialog.classList.remove('active');
            pendingBreedingData = null;
        }

        function confirmBreedingBooking() {
            const payload = pendingBreedingData;
            if (!payload) return;

            const bookedBreederId = Number(payload.breederId);

            fetch('/PETVET/api/pet-owner/create-breeding-request.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(async (res) => {
                const data = await res.json().catch(() => null);
                if (!res.ok || !data || data.success !== true) {
                    const msg = (data && data.message) ? data.message : 'Failed to submit booking. Please try again.';
                    throw new Error(msg);
                }
                return data;
            })
            .then(() => {
                closeBreederConfirmation();
                closeBreederDetails();

                // Update active booking ids so buttons can grey out immediately
                if (!window.__activeBookingProviderIds) {
                    window.__activeBookingProviderIds = { trainers: [], sitters: [], breeders: [] };
                }
                const list = Array.isArray(window.__activeBookingProviderIds.breeders) ? window.__activeBookingProviderIds.breeders : [];
                if (Number.isFinite(bookedBreederId) && bookedBreederId > 0 && !list.map(Number).includes(bookedBreederId)) {
                    list.push(bookedBreederId);
                }
                window.__activeBookingProviderIds.breeders = list;
                updateSitterCardButtons();

                alert('Breeding request submitted successfully!\n\nThe breeder will review it under Pending requests.');
                pendingBreedingData = null;
            })
            .catch((err) => {
                console.error('Breeding booking submit failed:', err);
                alert((err && err.message) ? err.message : 'Failed to submit booking. Please try again.');
            });
        }

        // Auto-submit on filter change (optional)
        document.getElementById('filterForm').addEventListener('change', function(e) {
            if (e.target.tagName === 'SELECT') {
                // Optionally auto-submit on select change
                // this.submit();
            }
        });
    </script>

    <!-- Trainer Details Modal -->
    <div id="trainerDetailsModal" class="modal-overlay">
        <div class="modal-content trainer-modal-content">
            <button class="modal-close" onclick="closeTrainerDetails()">×</button>
            
            <div class="trainer-modal-header">
                <img id="trainerModalAvatar" src="" alt="Trainer" class="trainer-modal-avatar">
                <div class="trainer-modal-info">
                    <h3 id="trainerModalName" class="trainer-modal-name"></h3>
                    <p id="trainerModalBusiness" class="trainer-modal-business"></p>
                    <div class="trainer-modal-meta">
                        <span class="trainer-modal-spec">🎯 <span id="trainerModalSpecialization"></span></span>
                        <span class="trainer-modal-exp">⭐ <span id="trainerModalExperience"></span></span>
                    </div>
                    <div class="trainer-modal-areas" id="trainerModalAreas"></div>
                </div>
            </div>

            <div class="trainer-modal-body">
                <h4 class="modal-section-title">Select Training Type to Book Appointment</h4>
                
                <div class="training-rate-options">
                    <label class="rate-option">
                        <input type="radio" name="trainingType" value="Basic" onchange="selectTrainingType()">
                        <div class="rate-option-content">
                            <div class="rate-option-header">
                                <span class="rate-option-icon">🔰</span>
                                <span class="rate-option-name">Basic Training</span>
                            </div>
                            <div class="rate-option-price" id="basicRate">LKR 1,500.00/hr</div>
                        </div>
                    </label>

                    <label class="rate-option">
                        <input type="radio" name="trainingType" value="Intermediate" onchange="selectTrainingType()">
                        <div class="rate-option-content">
                            <div class="rate-option-header">
                                <span class="rate-option-icon">⚡</span>
                                <span class="rate-option-name">Intermediate Training</span>
                            </div>
                            <div class="rate-option-price" id="intermediateRate">LKR 2,250.00/hr</div>
                        </div>
                    </label>

                    <label class="rate-option">
                        <input type="radio" name="trainingType" value="Advanced" onchange="selectTrainingType()">
                        <div class="rate-option-content">
                            <div class="rate-option-header">
                                <span class="rate-option-icon">🏆</span>
                                <span class="rate-option-name">Advanced Training</span>
                            </div>
                            <div class="rate-option-price" id="advancedRate">LKR 3,000.00/hr</div>
                        </div>
                    </label>
                </div>

                <!-- Appointment Form (Initially Hidden) -->
                <div id="appointmentForm" class="appointment-form" style="display: none;">
                    <div class="appointment-form-header">
                        <h4 class="modal-section-title">Appointment Details</h4>
                        <span class="selected-type-badge">
                            Selected: <span id="selectedTrainingType" class="training-type-badge"></span>
                        </span>
                    </div>

                    <form id="bookingForm" onsubmit="submitTrainerAppointment(event)">
                        <div class="form-group">
                            <label for="ownerName">Owner Name *</label>
                            <input type="text" 
                                   id="ownerName" 
                                   name="ownerName" 
                                   required 
                                   readonly 
                                   style="background-color: #f5f5f5; cursor: not-allowed;" 
                                   placeholder="Loading...">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="petName">Pet Name *</label>
                                <input type="text" 
                                       id="petName" 
                                       name="petName" 
                                       list="petNamesList" 
                                       required 
                                       placeholder="Select or type pet name" 
                                       autocomplete="off">
                                <datalist id="petNamesList">
                                    <!-- Pet names will be loaded dynamically -->
                                </datalist>
                            </div>

                            <div class="form-group">
                                <label for="dogBreed">Dog Breed *</label>
                                <input type="text" id="dogBreed" name="dogBreed" required placeholder="e.g., Labrador, German Shepherd">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="appointmentDate">Appointment Date *</label>
                                <input type="date" 
                                       id="appointmentDate" 
                                       name="appointmentDate" 
                                       required 
                                       min="<?= date('Y-m-d') ?>"
                                       onchange="checkTrainerAvailability()">
                                <small id="dateAvailabilityMsg" style="display:none; color: #e74c3c; margin-top: 4px; font-size: 12px;"></small>
                            </div>

                            <div class="form-group">
                                <label for="appointmentTime">Appointment Time *</label>
                                <input type="time" 
                                       id="appointmentTime" 
                                       name="appointmentTime" 
                                       required
                                       disabled
                                       onchange="checkTrainerAvailability()">
                                <small id="timeAvailabilityMsg" style="display:none; color: #e74c3c; margin-top: 4px; font-size: 12px;"></small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="trainingLocation">Training Location *</label>
                            <select id="trainingLocation" name="trainingLocation" required onchange="toggleLocationFields()">
                                <option value="">Select location</option>
                                <option value="home">At My Home</option>
                                <option value="trainer">At Trainer's Location</option>
                                <option value="park">At Nearby Park</option>
                                <option value="other">Other (Specify in notes)</option>
                            </select>
                        </div>

                        <!-- Additional Location Fields (Shown conditionally) -->
                        <div id="additionalLocationFields" class="additional-location-fields" style="display: none;">
                            <div class="form-group">
                                <label for="trainerLocationDisplay">Map Location *</label>
                                <div style="display:flex; gap:10px; align-items:center; margin: 6px 0 10px;">
                                    <button type="button" class="btn outline" id="trainerUseMyLocationBtn">Use My Current Location</button>
                                    <small style="color: #6b7280;">Or click on the map to pick a point</small>
                                </div>

                                <div id="trainerMapContainer" style="height: 220px; width: 100%; border-radius: 10px; overflow: hidden; border: 1px solid #e5e7eb;"></div>

                                <input type="text"
                                       id="trainerLocationDisplay"
                                       name="trainerLocationDisplay"
                                       required
                                       readonly
                                       style="background-color: #f5f5f5; cursor: not-allowed; margin-top: 10px;"
                                       placeholder="Select a location on the map or use your current location">
                                <input type="hidden" id="trainerLocationLat" name="trainerLocationLat">
                                <input type="hidden" id="trainerLocationLng" name="trainerLocationLng">
                            </div>

                            <div class="form-row" id="trainerDistrictRow" style="display:none;">
                                <div class="form-group">
                                    <label for="locationDistrict">District *</label>
                                    <select id="locationDistrict" name="locationDistrict" style="pointer-events: none; background-color: #f5f5f5;">
                                        <option value="">Select district</option>
                                        <option value="Ampara">Ampara</option>
                                        <option value="Anuradhapura">Anuradhapura</option>
                                        <option value="Badulla">Badulla</option>
                                        <option value="Batticaloa">Batticaloa</option>
                                        <option value="Colombo">Colombo</option>
                                        <option value="Galle">Galle</option>
                                        <option value="Gampaha">Gampaha</option>
                                        <option value="Hambantota">Hambantota</option>
                                        <option value="Jaffna">Jaffna</option>
                                        <option value="Kalutara">Kalutara</option>
                                        <option value="Kandy">Kandy</option>
                                        <option value="Kegalle">Kegalle</option>
                                        <option value="Kilinochchi">Kilinochchi</option>
                                        <option value="Kurunegala">Kurunegala</option>
                                        <option value="Mannar">Mannar</option>
                                        <option value="Matale">Matale</option>
                                        <option value="Matara">Matara</option>
                                        <option value="Monaragala">Monaragala</option>
                                        <option value="Mullaitivu">Mullaitivu</option>
                                        <option value="Nuwara Eliya">Nuwara Eliya</option>
                                        <option value="Polonnaruwa">Polonnaruwa</option>
                                        <option value="Puttalam">Puttalam</option>
                                        <option value="Ratnapura">Ratnapura</option>
                                        <option value="Trincomalee">Trincomalee</option>
                                        <option value="Vavuniya">Vavuniya</option>
                                    </select>
                                    <small id="trainerDistrictMsg" style="display:none; color:#6b7280; margin-top: 4px; font-size: 12px;"></small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="additionalNotes">Additional Notes / Special Requirements</label>
                            <textarea id="additionalNotes" 
                                      name="additionalNotes" 
                                      rows="3" 
                                      placeholder="Any special instructions, behavioral concerns, or additional information for the trainer..."></textarea>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn outline" onclick="closeTrainerDetails()">Cancel</button>
                            <button type="submit" id="trainerSubmitBtn" class="btn primary" disabled>Book Appointment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Dialog -->
    <div id="confirmationDialog" class="modal-overlay confirmation-overlay">
        <div class="modal-content confirmation-dialog">
            <div class="confirmation-header">
                <div class="confirmation-icon">⚠️</div>
                <h3 class="confirmation-title">Confirm Appointment Booking</h3>
            </div>
            <div class="confirmation-body">
                <p class="confirmation-message">Are you sure you want to book this training appointment?</p>
                <div class="confirmation-details" id="confirmationDetails"></div>
            </div>
            <div class="confirmation-actions">
                <button type="button" class="btn outline" onclick="closeConfirmation()">Cancel</button>
                <button type="button" class="btn primary" onclick="confirmBooking()">Yes, Book Appointment</button>
            </div>
        </div>
    </div>

    <!-- Sitter Booking Modal -->
    <div id="sitterBookingModal" class="modal-overlay">
        <div class="modal-content trainer-modal-content">
            <button class="modal-close" onclick="closeSitterBooking()">×</button>
            
            <div class="trainer-modal-header">
                <img id="sitterModalAvatar" src="" alt="Sitter" class="trainer-modal-avatar">
                <div class="trainer-modal-info">
                    <h3 id="sitterModalName" class="trainer-modal-name"></h3>
                    <p id="sitterModalBusiness" class="trainer-modal-business"></p>
                    <div class="trainer-modal-meta">
                        <span class="trainer-modal-spec">🏠 <span id="sitterModalHomeType"></span></span>
                        <span class="trainer-modal-location">📍 <span id="sitterModalCity"></span></span>
                        <span class="trainer-modal-exp">⭐ <span id="sitterModalExperience"></span></span>
                    </div>
                </div>
            </div>

            <div class="trainer-modal-body">
                <form id="sitterBookingForm" onsubmit="submitSitterBooking(event)">
                    <div class="form-group">
                        <label for="sitterOwnerName">Owner Name *</label>
                        <input type="text"
                               id="sitterOwnerName"
                               name="sitterOwnerName"
                               required
                               readonly
                               style="background-color: #f5f5f5; cursor: not-allowed;"
                               placeholder="Loading...">
                    </div>

                    <h4 class="modal-section-title">Pet Details</h4>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="sitterPetName">Pet Name *</label>
                            <input type="text"
                                   id="sitterPetName"
                                   name="petName"
                                   list="sitterPetNamesList"
                                   required
                                   placeholder="Select or type pet name"
                                   autocomplete="off">
                            <datalist id="sitterPetNamesList">
                                <!-- Pet names will be loaded dynamically -->
                            </datalist>
                        </div>
                        <div class="form-group">
                            <label for="petType">Pet Type *</label>
                            <select id="petType" name="petType" required onchange="updateBreedOptions()">
                                <option value="">Select pet type</option>
                                <option value="dog">Dog</option>
                                <option value="cat">Cat</option>
                                <option value="bird">Bird</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="petBreed">Pet Breed *</label>
                        <select id="petBreed" name="petBreed" required>
                            <option value="">Select pet type first</option>
                        </select>
                    </div>

                    <h4 class="modal-section-title">Service Details</h4>
                    
                    <div class="form-group">
                        <label for="serviceType">Service Type *</label>
                        <select id="serviceType" name="serviceType" required>
                            <option value="">Select service type</option>
                            <option value="dog-walking">Dog Walking</option>
                            <option value="pet-sitting">Pet Sitting (Daily Visits)</option>
                            <option value="overnight-care">Overnight Care</option>
                            <option value="house-sitting">House Sitting with Pets</option>
                            <option value="daycare">Pet Daycare</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="durationType">Duration *</label>
                        <select id="durationType" name="durationType" required onchange="toggleDurationFields()">
                            <option value="">Select duration type</option>
                            <option value="single">Single Day</option>
                            <option value="multiple">Multiple Days</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="sitterAppointmentDate">Appointment Date *</label>
                            <input type="date"
                                   id="sitterAppointmentDate"
                                   name="sitterAppointmentDate"
                                   required
                                   min="<?= date('Y-m-d') ?>"
                                   onchange="checkSitterAvailability()">
                            <small id="sitterApptDateAvailabilityMsg" style="display:none; color: #e74c3c; margin-top: 4px; font-size: 12px;"></small>
                        </div>
                        <div class="form-group">
                            <label for="sitterAppointmentTime">Appointment Time *</label>
                            <input type="time"
                                   id="sitterAppointmentTime"
                                   name="sitterAppointmentTime"
                                   required
                                   disabled
                                   onchange="checkSitterAvailability()">
                            <small id="sitterApptTimeAvailabilityMsg" style="display:none; color: #e74c3c; margin-top: 4px; font-size: 12px;"></small>
                        </div>
                    </div>

                    <!-- Multiple Days Fields -->
                    <div id="multipleDaysFields" class="duration-fields" style="display: none;">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="sitterNumberOfDays">Number of Days</label>
                                <input type="number" id="sitterNumberOfDays" name="sitterNumberOfDays" min="1" max="60" placeholder="e.g., 3">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="sitterLocation">Service Location *</label>
                        <select id="sitterLocation" name="sitterLocation" required onchange="toggleSitterLocationFields()">
                            <option value="">Select location</option>
                            <option value="my-home">At My Home</option>
                            <option value="sitter-home">At Sitter's Home</option>
                            <option value="park">At Nearby Park</option>
                            <option value="other">Other Location</option>
                        </select>
                    </div>

                    <!-- Additional Location Fields -->
                    <div id="sitterAdditionalLocationFields" class="additional-location-fields" style="display: none;">
                        <div class="form-group">
                            <label for="sitterLocationDisplay">Map Location *</label>
                            <div style="display:flex; gap:10px; align-items:center; margin: 6px 0 10px;">
                                <button type="button" class="btn outline" id="sitterUseMyLocationBtn">Use My Current Location</button>
                                <small style="color: #6b7280;">Or click on the map to pick a point</small>
                            </div>

                            <div id="sitterLeafletMapContainer" style="height: 220px; width: 100%; border-radius: 10px; overflow: hidden; border: 1px solid #e5e7eb;"></div>

                            <input type="text"
                                   id="sitterLocationDisplay"
                                   name="sitterLocationDisplay"
                                   required
                                   readonly
                                   style="background-color: #f5f5f5; cursor: not-allowed; margin-top: 10px;"
                                   placeholder="Select a location on the map or use your current location">
                            <input type="hidden" id="sitterLocationLat" name="sitterLocationLat">
                            <input type="hidden" id="sitterLocationLng" name="sitterLocationLng">
                        </div>

                        <div class="form-row" id="sitterDistrictRow" style="display:none;">
                            <div class="form-group">
                                <label for="sitterDistrict">District *</label>
                                <select id="sitterDistrict" name="sitterDistrict" style="pointer-events: none; background-color: #f5f5f5;">
                                    <option value="">Select district</option>
                                    <option value="Ampara">Ampara</option>
                                    <option value="Anuradhapura">Anuradhapura</option>
                                    <option value="Badulla">Badulla</option>
                                    <option value="Batticaloa">Batticaloa</option>
                                    <option value="Colombo">Colombo</option>
                                    <option value="Galle">Galle</option>
                                    <option value="Gampaha">Gampaha</option>
                                    <option value="Hambantota">Hambantota</option>
                                    <option value="Jaffna">Jaffna</option>
                                    <option value="Kalutara">Kalutara</option>
                                    <option value="Kandy">Kandy</option>
                                    <option value="Kegalle">Kegalle</option>
                                    <option value="Kilinochchi">Kilinochchi</option>
                                    <option value="Kurunegala">Kurunegala</option>
                                    <option value="Mannar">Mannar</option>
                                    <option value="Matale">Matale</option>
                                    <option value="Matara">Matara</option>
                                    <option value="Monaragala">Monaragala</option>
                                    <option value="Mullaitivu">Mullaitivu</option>
                                    <option value="Nuwara Eliya">Nuwara Eliya</option>
                                    <option value="Polonnaruwa">Polonnaruwa</option>
                                    <option value="Puttalam">Puttalam</option>
                                    <option value="Ratnapura">Ratnapura</option>
                                    <option value="Trincomalee">Trincomalee</option>
                                    <option value="Vavuniya">Vavuniya</option>
                                </select>
                                <small id="sitterDistrictMsg" style="display:none; color:#6b7280; margin-top: 4px; font-size: 12px;"></small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="sitterNotes">Additional Notes / Special Instructions</label>
                        <textarea id="sitterNotes" 
                                  name="sitterNotes" 
                                  rows="3" 
                                  placeholder="Pet feeding schedule, medication needs, behavioral notes, emergency contacts, etc..."></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn outline" onclick="closeSitterBooking()">Cancel</button>
                        <button type="submit" id="sitterSubmitBtn" class="btn primary" disabled>Book Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sitter Confirmation Dialog -->
    <div id="sitterConfirmationDialog" class="modal-overlay confirmation-overlay">
        <div class="modal-content confirmation-dialog">
            <div class="confirmation-header">
                <div class="confirmation-icon">⚠️</div>
                <h3 class="confirmation-title">Confirm Pet Sitting Booking</h3>
            </div>
            <div class="confirmation-body">
                <p class="confirmation-message">Are you sure you want to book this pet sitting service?</p>
                <div class="confirmation-details" id="sitterConfirmationDetails"></div>
            </div>
            <div class="confirmation-actions">
                <button type="button" class="btn outline" onclick="closeSitterConfirmation()">Cancel</button>
                <button type="button" class="btn primary" onclick="confirmSitterBooking()">Yes, Book Service</button>
            </div>
        </div>
    </div>

    <!-- Breeder Details Modal -->
    <div id="breederDetailsModal" class="modal-overlay">
        <div class="modal-content trainer-modal-content">
            <div class="modal-header">
                <div class="header-content">
                    <div class="provider-avatar">
                        <img id="breederModalAvatar" src="" alt="Breeder Photo">
                    </div>
                    <div class="provider-info">
                        <h3 id="breederModalName"></h3>
                        <div id="breederModalBusiness" class="business-name"></div>
                        <div class="provider-meta">
                            <span class="meta-badge">
                                📍 <span id="breederModalCity"></span>
                            </span>
                            <span class="meta-badge">
                                ⭐ <span id="breederModalExperience"></span>
                            </span>
                            <span class="meta-badge">
                                📞 <span id="breederModalPhone"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <button class="modal-close" onclick="closeBreederDetails()">&times;</button>
            </div>
            
            <div class="modal-body">
                <div id="breederDescriptionSection" class="modal-section" style="display:none;">
                    <h4 class="modal-section-title">Breeding Services &amp; Pricing</h4>
                    <p id="breederModalDescription" class="modal-description"></p>
                </div>

                <form id="breedingBookingForm" onsubmit="submitBreedingBooking(event)">
                    <h4 class="modal-section-title">Book Breeding Service</h4>

                    <div class="form-group">
                        <label for="breederOwnerName">Owner Name *</label>
                        <input type="text"
                               id="breederOwnerName"
                               name="breederOwnerName"
                               required
                               readonly
                               style="background-color: #f5f5f5; cursor: not-allowed;"
                               placeholder="Loading...">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="breedingPetName">Pet Name *</label>
                            <input type="text"
                                   id="breedingPetName"
                                   name="petName"
                                   list="breederPetNamesList"
                                   required
                                   placeholder="Select or type pet name"
                                   autocomplete="off">
                            <datalist id="breederPetNamesList"></datalist>
                        </div>
                        <div class="form-group">
                            <label for="breedingPetGender">Gender *</label>
                            <select id="breedingPetGender" name="petGender" required>
                                <option value="">Select gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="breedingPetBreed">Breed *</label>
                        <input type="text" id="breedingPetBreed" name="petBreed" required placeholder="Enter pet breed">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="breedingDate">Appointment Date *</label>
                            <input type="date" id="breedingDate" name="breedingDate" required min="<?= date('Y-m-d') ?>" onchange="checkBreederAvailability()">
                            <small id="breederDateAvailabilityMsg" style="display:none; color: #e74c3c; margin-top: 4px; font-size: 12px;"></small>
                        </div>

                        <div class="form-group">
                            <label for="breedingTime">Appointment Time *</label>
                            <input type="time"
                                   id="breedingTime"
                                   name="breedingTime"
                                   required
                                   disabled
                                   onchange="checkBreederAvailability()">
                            <small id="breederTimeAvailabilityMsg" style="display:none; color: #e74c3c; margin-top: 4px; font-size: 12px;"></small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="breedingLocation">Service Location *</label>
                        <select id="breedingLocation" name="breedingLocation" required disabled onchange="toggleBreederLocationFields()">
                            <option value="">Select location</option>
                            <option value="home">At My Home</option>
                            <option value="breeder">At Breeder's Location</option>
                            <option value="park">At Nearby Park</option>
                            <option value="other">Other Location</option>
                        </select>
                    </div>

                    <!-- Additional Location Fields (Shown conditionally) -->
                    <div id="breederAdditionalLocationFields" class="additional-location-fields" style="display: none;">
                        <div class="form-group">
                            <label for="breederLocationDisplay">Map Location *</label>
                            <div style="display:flex; gap:10px; align-items:center; margin: 6px 0 10px;">
                                <button type="button" class="btn outline" id="breederUseMyLocationBtn">Use My Current Location</button>
                                <small style="color: #6b7280;">Or click on the map to pick a point</small>
                            </div>

                            <div id="breederMapContainer" style="height: 220px; width: 100%; border-radius: 10px; overflow: hidden; border: 1px solid #e5e7eb;"></div>

                            <input type="text"
                                   id="breederLocationDisplay"
                                   name="breederLocationDisplay"
                                   required
                                   readonly
                                   style="background-color: #f5f5f5; cursor: not-allowed; margin-top: 10px;"
                                   placeholder="Select a location on the map or use your current location">
                            <input type="hidden" id="breederLocationLat" name="breederLocationLat">
                            <input type="hidden" id="breederLocationLng" name="breederLocationLng">
                        </div>

                        <div class="form-row" id="breederDistrictRow" style="display:none;">
                            <div class="form-group">
                                <label for="breederLocationDistrict">District *</label>
                                <select id="breederLocationDistrict" name="breederLocationDistrict" style="pointer-events: none; background-color: #f5f5f5;">
                                    <option value="">Select district</option>
                                    <option value="Ampara">Ampara</option>
                                    <option value="Anuradhapura">Anuradhapura</option>
                                    <option value="Badulla">Badulla</option>
                                    <option value="Batticaloa">Batticaloa</option>
                                    <option value="Colombo">Colombo</option>
                                    <option value="Galle">Galle</option>
                                    <option value="Gampaha">Gampaha</option>
                                    <option value="Hambantota">Hambantota</option>
                                    <option value="Jaffna">Jaffna</option>
                                    <option value="Kalutara">Kalutara</option>
                                    <option value="Kandy">Kandy</option>
                                    <option value="Kegalle">Kegalle</option>
                                    <option value="Kilinochchi">Kilinochchi</option>
                                    <option value="Kurunegala">Kurunegala</option>
                                    <option value="Mannar">Mannar</option>
                                    <option value="Matale">Matale</option>
                                    <option value="Matara">Matara</option>
                                    <option value="Monaragala">Monaragala</option>
                                    <option value="Mullaitivu">Mullaitivu</option>
                                    <option value="Nuwara Eliya">Nuwara Eliya</option>
                                    <option value="Polonnaruwa">Polonnaruwa</option>
                                    <option value="Puttalam">Puttalam</option>
                                    <option value="Ratnapura">Ratnapura</option>
                                    <option value="Trincomalee">Trincomalee</option>
                                    <option value="Vavuniya">Vavuniya</option>
                                </select>
                                <small id="breederDistrictMsg" style="display:none; color:#6b7280; margin-top: 4px; font-size: 12px;"></small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="breedingNotes">Additional Notes / Special Requirements</label>
                        <textarea id="breedingNotes" 
                                name="breedingNotes" 
                                rows="3" 
                                placeholder="Any specific requirements, health conditions, or preferences..."></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn outline" onclick="closeBreederDetails()">Cancel</button>
                        <button type="submit" id="breederSubmitBtn" class="btn primary" disabled>Book Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Breeder Confirmation Dialog -->
    <div id="breederConfirmationDialog" class="modal-overlay confirmation-overlay">
        <div class="modal-content confirmation-dialog">
            <div class="confirmation-header">
                <div class="confirmation-icon">⚠️</div>
                <h3 class="confirmation-title">Confirm Breeding Service Booking</h3>
            </div>
            <div class="confirmation-body">
                <p class="confirmation-message">Are you sure you want to book this breeding service?</p>
                <div class="confirmation-details" id="breederConfirmationDetails"></div>
            </div>
            <div class="confirmation-actions">
                <button type="button" class="btn outline" onclick="closeBreederConfirmation()">Cancel</button>
                <button type="button" class="btn primary" onclick="confirmBreedingBooking()">Yes, Book Service</button>
            </div>
        </div>
    </div>

    <script src="/PETVET/public/js/pet-owner/groomer-distance.js"></script>
    
    <script>
        // Apply default sort on page load for non-trainers
        document.addEventListener('DOMContentLoaded', function() {
            const serviceTypeInput = document.getElementById('serviceTypeInput');
            const sortFilter = document.getElementById('sortFilter');
            
            if (serviceTypeInput && sortFilter) {
                const serviceType = serviceTypeInput.value;
                // If service type is not trainers and sort is not set, trigger nearest sort
                if (serviceType !== 'trainers' && (!sortFilter.value || sortFilter.value === 'az')) {
                    sortFilter.value = 'nearest';
                    sortFilter.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        });
    </script>
</body>
</html>
