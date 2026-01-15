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
        
        // Service type labels
        $serviceLabels = [
            'trainers' => 'Trainers',
            'sitters' => 'Pet Sitters',
            'breeders' => 'Breeders',
            'groomers' => 'Groomers'
        ];
        ?>
        
        <header class="page-header">
            <div class="title-wrap">
                <h2>Discover Pet Services</h2>
                <p class="subtitle">Find professionals for your pet care needs</p>
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
                <div class="service-label">Pet Sitters</div>
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
                                $sortValue = ($serviceType === 'groomers') ? 'nearest' : 'az';
                            }
                        ?>
                        <select id="sortFilter" name="sort">
                            <option value="nearest" <?= $sortValue === 'nearest' ? 'selected' : '' ?>>Nearest</option>
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
                            <article class="provider-card <?= $serviceType === 'trainers' ? 'trainer-card' : '' ?>" data-groomer-id="<?= $serviceType === 'groomers' ? (int)$provider['id'] : 0 ?>" data-sort-name="<?= htmlspecialchars($providerSortName) ?>">
                                <div class="provider-header">
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

                                <?php if ($serviceType === 'groomers'): ?>
                                    <span class="clinic-distance groomer-distance" data-groomer-id="<?= (int)$provider['id'] ?>">
                                        <span class="distance-loader">⏳ Calculating...</span>
                                    </span>
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
                                        <button class="btn primary" onclick='openSitterBooking(<?= json_encode([
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
                                            "phone" => $provider["phone"] ?? "",
                                            "experience_years" => $provider["experience_years"] ?? 0,
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
        });

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
            
            const trainerModal = document.getElementById('trainerDetailsModal');
            if (event.target === trainerModal) {
                closeTrainerDetails();
            }

            const breederModal = document.getElementById('breederDetailsModal');
            if (event.target === breederModal) {
                closeBreederDetails();
            }
        });

        // Trainer Details Modal Functions
        let currentTrainer = null;
        let pendingAppointmentData = null;

        function openTrainerDetails(trainerData, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            currentTrainer = trainerData;
            const modal = document.getElementById('trainerDetailsModal');
            
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
            
            // Lock scroll and save position
            savedScrollPosition = window.pageYOffset || document.documentElement.scrollTop;
            document.documentElement.style.overflow = 'hidden';
            document.body.style.overflow = 'hidden';
            document.body.style.position = 'relative';
            modal.classList.add('active');
            document.body.classList.add('modal-open');
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
        }

        function selectTrainingType() {
            const selectedType = document.querySelector('input[name="trainingType"]:checked');
            if (selectedType) {
                document.getElementById('appointmentForm').style.display = 'block';
                document.getElementById('selectedTrainingType').textContent = selectedType.value;
            }
        }

        function toggleLocationFields() {
            const locationSelect = document.getElementById('trainingLocation');
            const additionalFields = document.getElementById('additionalLocationFields');
            const districtField = document.getElementById('locationDistrict');
            const mapField = document.getElementById('googleMapLocation');
            
            // Show additional fields for: home, park, or other
            // Hide for: trainer's location
            const showFields = ['home', 'park', 'other'].includes(locationSelect.value);
            
            if (showFields) {
                additionalFields.style.display = 'block';
                // Make fields required
                districtField.required = true;
                mapField.required = true;
            } else {
                additionalFields.style.display = 'none';
                // Remove required attribute
                districtField.required = false;
                mapField.required = false;
                // Clear values
                districtField.value = '';
                mapField.value = '';
            }
        }

        function submitTrainerAppointment(event) {
            event.preventDefault();
            
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
                pendingAppointmentData.googleMapLocation = document.getElementById('googleMapLocation').value;
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
            // Here you would send the data to the server
            console.log('Confirmed Appointment Data:', pendingAppointmentData);
            
            // Close both modals
            closeConfirmation();
            closeTrainerDetails();
            
            // Show success message (you can replace this with a better notification)
            alert('Appointment booked successfully!\n\nYou will receive a confirmation email shortly.');
            
            pendingAppointmentData = null;
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
        }

        function openSitterBooking(sitterData, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            currentSitter = sitterData;
            const modal = document.getElementById('sitterBookingModal');
            
            // Populate sitter info
            document.getElementById('sitterModalAvatar').src = sitterData.avatar;
            document.getElementById('sitterModalName').textContent = sitterData.name;
            document.getElementById('sitterModalBusiness').textContent = sitterData.business_name || '';
            document.getElementById('sitterModalHomeType').textContent = sitterData.home_type;
            document.getElementById('sitterModalCity').textContent = sitterData.city;
            document.getElementById('sitterModalExperience').textContent = sitterData.experience_years + ' years';
            
            // Reset form
            document.getElementById('sitterBookingForm').reset();
            document.getElementById('singleDayFields').style.display = 'none';
            document.getElementById('multipleDaysFields').style.display = 'none';
            document.getElementById('sitterAdditionalLocationFields').style.display = 'none';
            document.getElementById('petBreed').innerHTML = '<option value="">Select pet type first</option>';
            document.getElementById('petBreed').required = true;
            document.querySelector('[for="petBreed"]').parentElement.style.display = 'block';
            
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
        }

        function toggleDurationFields() {
            const durationType = document.getElementById('durationType').value;
            const singleFields = document.getElementById('singleDayFields');
            const multiFields = document.getElementById('multipleDaysFields');
            
            // Reset all fields first
            singleFields.style.display = 'none';
            multiFields.style.display = 'none';
            
            // Clear required attributes
            document.getElementById('singleDate').required = false;
            document.getElementById('startTime').required = false;
            document.getElementById('endTime').required = false;
            document.getElementById('startDate').required = false;
            document.getElementById('startTimeMulti').required = false;
            document.getElementById('endDate').required = false;
            document.getElementById('endTimeMulti').required = false;
            
            if (durationType === 'single') {
                singleFields.style.display = 'block';
                document.getElementById('singleDate').required = true;
                document.getElementById('startTime').required = true;
                document.getElementById('endTime').required = true;
            } else if (durationType === 'multiple') {
                multiFields.style.display = 'block';
                document.getElementById('startDate').required = true;
                document.getElementById('startTimeMulti').required = true;
                document.getElementById('endDate').required = true;
                document.getElementById('endTimeMulti').required = true;
            }
        }

        function toggleSitterLocationFields() {
            const location = document.getElementById('sitterLocation').value;
            const additionalFields = document.getElementById('sitterAdditionalLocationFields');
            const districtField = document.getElementById('sitterDistrict');
            const mapField = document.getElementById('sitterMapLocation');
            
            const showFields = ['my-home', 'park', 'other'].includes(location);
            
            if (showFields) {
                additionalFields.style.display = 'block';
                districtField.required = true;
                mapField.required = true;
            } else {
                additionalFields.style.display = 'none';
                districtField.required = false;
                mapField.required = false;
                districtField.value = '';
                mapField.value = '';
            }
        }

        function submitSitterBooking(event) {
            event.preventDefault();
            
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
                pendingSitterBookingData.date = document.getElementById('singleDate').value;
                pendingSitterBookingData.startTime = document.getElementById('startTime').value;
                pendingSitterBookingData.endTime = document.getElementById('endTime').value;
            } else if (durationType === 'multiple') {
                pendingSitterBookingData.startDate = document.getElementById('startDate').value;
                pendingSitterBookingData.startTime = document.getElementById('startTimeMulti').value;
                pendingSitterBookingData.endDate = document.getElementById('endDate').value;
                pendingSitterBookingData.endTime = document.getElementById('endTimeMulti').value;
            }
            
            if (['my-home', 'park', 'other'].includes(pendingSitterBookingData.location)) {
                pendingSitterBookingData.district = document.getElementById('sitterDistrict').value;
                pendingSitterBookingData.mapLocation = document.getElementById('sitterMapLocation').value;
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
                dateTimeInfo = `${formatDate(pendingSitterBookingData.startDate)} ${formatTime(pendingSitterBookingData.startTime)} to ${formatDate(pendingSitterBookingData.endDate)} ${formatTime(pendingSitterBookingData.endTime)}`;
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
            
            detailsDiv.innerHTML = detailsHTML;
            confirmDialog.classList.add('active');
        }

        function closeSitterConfirmation() {
            const confirmDialog = document.getElementById('sitterConfirmationDialog');
            confirmDialog.classList.remove('active');
            pendingSitterBookingData = null;
        }

        function confirmSitterBooking() {
            console.log('Confirmed Sitter Booking Data:', pendingSitterBookingData);
            
            closeSitterConfirmation();
            closeSitterBooking();
            
            alert('Pet sitting service booked successfully!\n\nYou will receive a confirmation email shortly.');
            
            pendingSitterBookingData = null;
        }

        // Breeder Booking Modal Functions
        let currentBreeder = null;
        let pendingBreedingData = null;

        function openBreederDetails(breederData, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            currentBreeder = breederData;
            const modal = document.getElementById('breederDetailsModal');
            
            // Populate breeder info
            document.getElementById('breederModalAvatar').src = breederData.avatar;
            document.getElementById('breederModalName').textContent = breederData.name;
            document.getElementById('breederModalBusiness').textContent = breederData.business_name || '';
            document.getElementById('breederModalCity').textContent = breederData.city;
            document.getElementById('breederModalExperience').textContent = breederData.experience_years + ' years';
            document.getElementById('breederModalPhone').textContent = breederData.phone || 'N/A';
            
            // Reset form
            document.getElementById('breedingBookingForm').reset();
            
            // Lock scroll and save position
            savedScrollPosition = window.pageYOffset || document.documentElement.scrollTop;
            document.documentElement.style.overflow = 'hidden';
            document.body.style.overflow = 'hidden';
            document.body.style.position = 'relative';
            modal.classList.add('active');
            document.body.classList.add('modal-open');
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
        }

        function submitBreedingBooking(event) {
            event.preventDefault();
            
            pendingBreedingData = {
                breederId: currentBreeder.id,
                breederName: currentBreeder.name,
                breederBusiness: currentBreeder.business_name,
                petName: document.getElementById('breedingPetName').value,
                petGender: document.getElementById('breedingPetGender').value,
                petBreed: document.getElementById('breedingPetBreed').value,
                breedingDate: document.getElementById('breedingDate').value,
                notes: document.getElementById('breedingNotes').value
            };
            
            showBreederConfirmation();
        }

        function showBreederConfirmation() {
            const confirmDialog = document.getElementById('breederConfirmationDialog');
            const detailsDiv = document.getElementById('breederConfirmationDetails');
            
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
                    <span class="detail-label">Preferred Date:</span>
                    <span class="detail-value">${formatDate(pendingBreedingData.breedingDate)}</span>
                </div>`;

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
            console.log('Confirmed Breeding Service Data:', pendingBreedingData);
            
            closeBreederConfirmation();
            closeBreederDetails();
            
            alert('Breeding service booked successfully!\n\nYou will receive a confirmation email shortly.');
            
            pendingBreedingData = null;
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
                        <div class="form-row">
                            <div class="form-group">
                                <label for="petName">Pet Name *</label>
                                <input type="text" id="petName" name="petName" required placeholder="Enter your pet's name">
                            </div>

                            <div class="form-group">
                                <label for="dogBreed">Dog Breed *</label>
                                <input type="text" id="dogBreed" name="dogBreed" required placeholder="e.g., Labrador, German Shepherd">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="appointmentDate">Appointment Date *</label>
                                <input type="date" id="appointmentDate" name="appointmentDate" required min="<?= date('Y-m-d') ?>">
                            </div>

                            <div class="form-group">
                                <label for="appointmentTime">Appointment Time *</label>
                                <input type="time" id="appointmentTime" name="appointmentTime" required>
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
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="locationDistrict">District *</label>
                                    <select id="locationDistrict" name="locationDistrict">
                                        <option value="">Select district</option>
                                        <option value="Colombo">Colombo</option>
                                        <option value="Gampaha">Gampaha</option>
                                        <option value="Kalutara">Kalutara</option>
                                        <option value="Kandy">Kandy</option>
                                        <option value="Matale">Matale</option>
                                        <option value="Nuwara Eliya">Nuwara Eliya</option>
                                        <option value="Galle">Galle</option>
                                        <option value="Matara">Matara</option>
                                        <option value="Hambantota">Hambantota</option>
                                        <option value="Jaffna">Jaffna</option>
                                        <option value="Kilinochchi">Kilinochchi</option>
                                        <option value="Mannar">Mannar</option>
                                        <option value="Vavuniya">Vavuniya</option>
                                        <option value="Mullaitivu">Mullaitivu</option>
                                        <option value="Batticaloa">Batticaloa</option>
                                        <option value="Ampara">Ampara</option>
                                        <option value="Trincomalee">Trincomalee</option>
                                        <option value="Kurunegala">Kurunegala</option>
                                        <option value="Puttalam">Puttalam</option>
                                        <option value="Anuradhapura">Anuradhapura</option>
                                        <option value="Polonnaruwa">Polonnaruwa</option>
                                        <option value="Badulla">Badulla</option>
                                        <option value="Moneragala">Moneragala</option>
                                        <option value="Ratnapura">Ratnapura</option>
                                        <option value="Kegalle">Kegalle</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="googleMapLocation">Google Map Location *</label>
                                    <input type="url" 
                                           id="googleMapLocation" 
                                           name="googleMapLocation" 
                                           placeholder="Paste Google Maps link here"
                                           title="Please provide a valid Google Maps URL">
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
                            <button type="submit" class="btn primary">Book Appointment</button>
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
                    <h4 class="modal-section-title">Pet Details</h4>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="sitterPetName">Pet Name *</label>
                            <input type="text" id="sitterPetName" name="petName" required placeholder="Enter your pet's name">
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

                    <!-- Single Day Fields -->
                    <div id="singleDayFields" class="duration-fields" style="display: none;">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="singleDate">Date *</label>
                                <input type="date" id="singleDate" name="singleDate" min="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="startTime">Start Time *</label>
                                <input type="time" id="startTime" name="startTime">
                            </div>
                            <div class="form-group">
                                <label for="endTime">End Time *</label>
                                <input type="time" id="endTime" name="endTime">
                            </div>
                        </div>
                    </div>

                    <!-- Multiple Days Fields -->
                    <div id="multipleDaysFields" class="duration-fields" style="display: none;">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="startDate">Start Date *</label>
                                <input type="date" id="startDate" name="startDate" min="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="form-group">
                                <label for="startTimeMulti">Start Time *</label>
                                <input type="time" id="startTimeMulti" name="startTimeMulti">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="endDate">End Date *</label>
                                <input type="date" id="endDate" name="endDate" min="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="form-group">
                                <label for="endTimeMulti">End Time *</label>
                                <input type="time" id="endTimeMulti" name="endTimeMulti">
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
                        <div class="form-row">
                            <div class="form-group">
                                <label for="sitterDistrict">District *</label>
                                <select id="sitterDistrict" name="sitterDistrict">
                                    <option value="">Select district</option>
                                    <option value="Colombo">Colombo</option>
                                    <option value="Gampaha">Gampaha</option>
                                    <option value="Kalutara">Kalutara</option>
                                    <option value="Kandy">Kandy</option>
                                    <option value="Galle">Galle</option>
                                    <option value="Matara">Matara</option>
                                    <option value="Negombo">Negombo</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="sitterMapLocation">Google Map Location *</label>
                                <input type="url" 
                                       id="sitterMapLocation" 
                                       name="sitterMapLocation" 
                                       placeholder="Paste Google Maps link here">
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
                        <button type="submit" class="btn primary">Book Service</button>
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
                <form id="breedingBookingForm" onsubmit="submitBreedingBooking(event)">
                    <h4 class="modal-section-title">Book Breeding Service</h4>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="breedingPetName">Pet Name *</label>
                            <input type="text" id="breedingPetName" name="petName" required placeholder="Enter your pet's name">
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

                    <div class="form-row">
                        <div class="form-group">
                            <label for="breedingPetBreed">Breed *</label>
                            <input type="text" id="breedingPetBreed" name="petBreed" required placeholder="Enter pet breed">
                        </div>
                        <div class="form-group">
                            <label for="breedingDate">Preferred Date *</label>
                            <input type="date" id="breedingDate" name="breedingDate" required min="<?= date('Y-m-d') ?>">
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
                        <button type="submit" class="btn primary">Book Service</button>
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
</body>
</html>
