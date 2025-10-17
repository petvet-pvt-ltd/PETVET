<?php
// Pet Listings Management - Admin view with demo data
// Sample data for demonstration
$totalPets = 278;
$petsForAdoption = 145;
$petsForSale = 98;
$pendingRequests = 12;
$approvedListings = 240;
$rejectedListings = 6;

$pets = [
  ['id' => 1, 'name' => 'Milo', 'species' => 'Dog', 'breed' => 'Domestic Shorthair', 'type' => 'Adoption', 'listedBy' => 'John Doe', 'contact' => 'john.doe@example.com', 'status' => 'Approved', 'date' => '2023-10-26', 'image' => '/PETVET/views/shared/images/cat2.png'],
  ['id' => 2, 'name' => 'Buddy', 'species' => 'Dog', 'breed' => 'Golden Retriever', 'type' => 'Sale', 'listedBy' => 'Guest User', 'contact' => '+1234567890', 'status' => 'Pending', 'date' => '2023-10-25', 'image' => '/PETVET/views/shared/images/dog3.png', 'badge' => 'Guest'],
  ['id' => 3, 'name' => 'Kiwi', 'species' => 'Bird', 'breed' => 'Parrot', 'type' => 'Adoption', 'listedBy' => 'Jane Smith', 'contact' => 'jane.smith@example.com', 'status' => 'Rejected', 'date' => '2023-10-24', 'image' => '/PETVET/views/shared/images/cat5.png'],
];

$pendingRequestsData = [
  ['name' => 'Charlie - Beagle', 'type' => 'Adoption', 'by' => 'Mark Johnson'],
  ['name' => 'Luna - Siamese', 'type' => 'Sale', 'by' => 'Emily White', 'badge' => 'Guest'],
];
?>
<link rel="stylesheet" href="/PETVET/public/css/admin/pet_listings.css" />
<div class="main-content">
  <!-- Header Section -->
  <header class="page-header">
    <div class="header-content">
      <h1>Pet Listings Management</h1>
    </div>
    <div class="header-actions">
  <button type="button" class="btn pending-requests" id="openListingDrawer" aria-expanded="false" title="View pending listing requests">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="vertical-align:middle;margin-right:6px">
          <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Pending Requests
        <span class="pulse-badge"><?php echo htmlspecialchars(count($pendingRequestsData)); ?></span>
      </button>
      <button class="btn-add-new" id="addNewListing">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        Add New Listing
      </button>
    </div>
  </header>

  <!-- Search and Filters Section -->
  <section class="filters-section">
    <div class="search-box">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="search-icon">
        <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
        <path d="m21 21-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
      <input type="text" id="searchPets" placeholder="Search pets by name, breed, or owner" />
    </div>
    <div class="filters-row">
      <select class="filter-select" id="listingTypeFilter">
        <option value="">Listing Type: All</option>
        <option value="adoption">Adoption</option>
        <option value="sale">Sale</option>
      </select>
      <select class="filter-select" id="statusFilter">
        <option value="">Status: All</option>
        <option value="approved">Approved</option>
        <option value="pending">Pending</option>
        <option value="rejected">Rejected</option>
      </select>
      <select class="filter-select" id="speciesFilter">
        <option value="">Species: All</option>
        <option value="dog">Dog</option>
        <option value="cat">Cat</option>
        <option value="bird">Bird</option>
        <option value="other">Other</option>
      </select>
    </div>
  </section>

  <!-- Stats Cards -->
  <section class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon pets-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="12" cy="8" r="3" stroke="currentColor" stroke-width="2"/>
          <path d="M4 18c0-2.5 4-4 8-4s8 1.5 8 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </div>
      <div class="stat-content">
        <div class="stat-label">Total Pets Listed</div>
        <div id="totalPetsValue" class="stat-value animated-number" data-target="<?php echo $totalPets; ?>">0</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon adoption-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z" stroke="currentColor" stroke-width="2"/>
        </svg>
      </div>
      <div class="stat-content">
        <div class="stat-label">Pets for Adoption</div>
        <div id="adoptionCountValue" class="stat-value animated-number" data-target="<?php echo $petsForAdoption; ?>">0</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon sale-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="9" cy="21" r="1" stroke="currentColor" stroke-width="2"/>
          <circle cx="20" cy="21" r="1" stroke="currentColor" stroke-width="2"/>
          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </div>
      <div class="stat-content">
        <div class="stat-label">Pets for Sale</div>
        <div id="saleCountValue" class="stat-value animated-number" data-target="<?php echo $petsForSale; ?>">0</div>
      </div>
    </div>

    <div class="stat-card pending-card">
      <div class="stat-icon pending-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
          <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </div>
      <div class="stat-content">
        <div class="stat-label">Pending Requests</div>
        <div id="pendingCountValue" class="stat-value animated-number" data-target="<?php echo count($pendingRequestsData); ?>">0</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon approved-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          <path d="M22 4 12 14.01l-3-3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </div>
      <div class="stat-content">
        <div class="stat-label">Approved Listings</div>
        <div id="approvedListingsValue" class="stat-value animated-number" data-target="<?php echo $approvedListings; ?>">0</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon rejected-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
          <path d="m15 9-6 6M9 9l6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </div>
      <div class="stat-content">
        <div class="stat-label">Rejected Listings</div>
        <div id="rejectedListingsValue" class="stat-value animated-number" data-target="<?php echo $rejectedListings; ?>">0</div>
      </div>
    </div>
  </section>

  <!-- Main Content Grid -->
  <div class="content-grid">
    <!-- Pets Table -->
    <section class="table-section">
      <table class="pets-table">
        <thead>
          <tr>
            <th>Pet</th>
            <th>Type</th>
            <th>Listed By</th>
            <th>Contact Info</th>
            <th>Status</th>
            <th>Date Added</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($pets as $pet): ?>
      <tr data-pet-id="<?php echo $pet['id']; ?>"
        data-type="<?php echo strtolower($pet['type']); ?>"
        data-status="<?php echo strtolower($pet['status']); ?>"
        data-date="<?php echo date('Y-m-d', strtotime($pet['date'])); ?>"
        data-species="<?php echo strtolower($pet['species'] ?? 'other'); ?>">
            <td class="pet-info-cell">
              <div class="pet-avatar-wrapper">
                <img src="<?php echo htmlspecialchars($pet['image']); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>" class="pet-avatar">
              </div>
              <div class="pet-details">
                <div class="pet-name"><?php echo htmlspecialchars($pet['name']); ?></div>
                <div class="pet-breed"><?php echo htmlspecialchars($pet['breed']); ?></div>
              </div>
            </td>
            <td>
              <span class="type-badge <?php echo strtolower($pet['type']); ?>-badge">
                <?php echo htmlspecialchars($pet['type']); ?>
              </span>
            </td>
            <td>
              <div class="listed-by-cell">
                <div class="listed-by-name"><?php echo htmlspecialchars($pet['listedBy']); ?></div>
                <?php if(isset($pet['badge'])): ?>
                  <span class="user-badge"><?php echo htmlspecialchars($pet['badge']); ?></span>
                <?php endif; ?>
              </div>
            </td>
            <td class="contact-cell"><?php echo htmlspecialchars($pet['contact']); ?></td>
            <td>
              <span class="status-pill status-<?php echo strtolower($pet['status']); ?>">
                <?php echo htmlspecialchars($pet['status']); ?>
              </span>
            </td>
            <td class="date-cell"><?php echo date('Y-m-d', strtotime($pet['date'])); ?></td>
            <td class="actions-cell">
              <button class="action-btn view-btn" title="View Details" data-pet-id="<?php echo $pet['id']; ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/>
                  <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                </svg>
              </button>
              <button class="action-btn approve-btn" title="Approve" data-pet-id="<?php echo $pet['id']; ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
              </button>
              <button class="action-btn reject-btn" title="Reject" data-pet-id="<?php echo $pet['id']; ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="m18 6-12 12M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
              </button>
              <button class="action-btn delete-btn" title="Delete" data-pet-id="<?php echo $pet['id']; ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
              </button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <!-- Charts Section -->
      <div class="charts-row">
        <div class="chart-card">
          <h3>Adoption vs Sale Overview</h3>
          <div class="chart-wrap">
            <svg id="donutChart" width="260" height="260" viewBox="0 0 260 260" aria-label="Adoption vs Sale donut"></svg>
            <div id="donutCenter" class="donut-center">
              <div class="big">0</div>
              <div class="muted">Total</div>
            </div>
          </div>
          <div class="chart-legend" id="donutLegend"></div>
        </div>

        <div class="chart-card">
          <h3>Monthly Listings Trend</h3>
          <svg id="monthlyChart" width="100%" height="220" aria-label="Monthly listings line chart"></svg>
          <div class="chart-legend" id="monthlyLegend"></div>
          <div class="mini-status">
            <div class="mini-title">Status distribution</div>
            <div class="stack-bar">
              <span class="seg approved" style="width:33%"></span>
              <span class="seg pending" style="width:33%"></span>
              <span class="seg rejected" style="width:34%"></span>
            </div>
            <div class="stack-labels">
              <span class="lab approved">Approved</span>
              <span class="lab pending">Pending</span>
              <span class="lab rejected">Rejected</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Drawer Markup (like manage-users) -->
    <aside id="listingDrawer" class="drawer" aria-hidden="true" style="display:none;">
      <div class="drawer-header">
        <h2>Pending Requests (<?php echo count($pendingRequestsData); ?>)</h2>
        <button id="closeListingDrawer" class="icon-btn" aria-label="Close">✕</button>
      </div>
      <div class="drawer-body">
        <?php if (!empty($pendingRequestsData)): foreach ($pendingRequestsData as $request): ?>
          <div class="pending-item">
            <div class="pending-info">
              <div class="pending-title"><?php echo htmlspecialchars($request['name']); ?></div>
              <div class="pending-meta">
                <span class="pending-type">Type: <?php echo htmlspecialchars($request['type']); ?></span>
                <span class="pending-by">
                  By: <?php echo htmlspecialchars($request['by']); ?>
                  <?php if(isset($request['badge'])): ?>
                    <span class="guest-badge">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                        <path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                      </svg>
                      <?php echo htmlspecialchars($request['badge']); ?>
                    </span>
                  <?php endif; ?>
                </span>
              </div>
            </div>
            <div class="pending-actions">
              <button class="btn-approve">✓ Approve</button>
              <button class="btn-reject">✕ Reject</button>
            </div>
          </div>
        <?php endforeach; else: ?>
          <div class="empty">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="12" cy="12" r="10" stroke="#cbd5e1" stroke-width="2"/>
              <path d="M9 12h6M12 9v6" stroke="#cbd5e1" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <div style="font-size:16px;font-weight:600;margin-bottom:4px;color:#64748b">All clear!</div>
            <div style="font-size:14px;color:#94a3b8">No pending requests at the moment</div>
          </div>
        <?php endif; ?>
      </div>
    </aside>
    <div id="listingBackdrop" class="backdrop"></div>
  </div>
</div>

<!-- Add New Listing Modal -->
<div id="addListingModal" class="pl-modal" aria-hidden="true" style="display:none;">
  <div class="pl-modal-backdrop"></div>
  <div class="pl-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="addListingTitle">
    <div class="pl-modal-content">
      <div class="pl-modal-header">
        <h3 id="addListingTitle">Add New Pet Listing</h3>
        <button id="addListingClose" class="icon-btn" aria-label="Close">✕</button>
      </div>
      <form id="addListingForm" class="pl-form">
        <div class="pl-form-grid">
          <div class="pl-span-2"><h4 class="pl-section-title">Pet Information</h4></div>
          <div class="pl-field">
            <label>Pet Name</label>
            <input type="text" id="plName" placeholder="Enter pet's name" required />
          </div>
          <div class="pl-field">
            <label>Species</label>
            <select id="plSpecies" required>
              <option value="" disabled selected>Select species</option>
              <option value="Dog">Dog</option>
              <option value="Cat">Cat</option>
              <option value="Bird">Bird</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div class="pl-field">
            <label>Breed</label>
            <input type="text" id="plBreed" placeholder="Enter pet's breed" />
          </div>
          <div class="pl-field">
            <label>Gender</label>
            <select id="plGender">
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Unknown">Unknown</option>
            </select>
          </div>
          <div class="pl-field">
            <label>Date of Birth</label>
            <input type="date" id="plDob" />
          </div>
          <div class="pl-field">
            <label>Age</label>
            <input type="number" id="plAge" min="0" step="0.1" placeholder="Enter age in years" />
          </div>
          <div class="pl-field pl-span-2">
            <label>Location</label>
            <input type="text" id="plLocation" placeholder="Enter city or area" />
          </div>

          <div class="pl-span-2"><h4 class="pl-section-title">Listing Details</h4></div>
          <div class="pl-field">
            <label>Purpose</label>
            <div class="pl-purpose">
              <button type="button" class="pill active" data-value="Adoption">Adoption</button>
              <button type="button" class="pill" data-value="Sale">Sale</button>
            </div>
            <select id="plType" style="display:none;">
              <option value="Adoption" selected>Adoption</option>
              <option value="Sale">Sale</option>
            </select>
          </div>
          <div class="pl-field" id="plPriceField" style="display:none;">
            <label>Price (for Sale)</label>
            <input type="number" id="plPrice" min="0" step="0.01" placeholder="0.00" />
          </div>
          <div class="pl-field">
            <label>Status</label>
            <select id="plStatus">
              <option value="Pending" selected>Pending</option>
              <option value="Approved">Approved</option>
              <option value="Rejected">Rejected</option>
            </select>
          </div>
          <div class="pl-field">
            <label>Listed By</label>
            <input type="text" id="plListedBy" placeholder="e.g., John Doe" value="Admin" />
          </div>
          <div class="pl-field">
            <label>Contact</label>
            <input type="text" id="plContact" placeholder="Email or Phone Number" />
          </div>

          <div class="pl-span-2"><h4 class="pl-section-title">Media</h4></div>
          <div class="pl-field pl-span-2">
            <div id="plDropzone" class="pl-dropzone">
              <input id="plMediaInput" type="file" accept="image/*" multiple style="display:none;" />
              <div class="dz-icon">📤</div>
              <div class="dz-text">Click to upload or drag and drop<br/><small>SVG, PNG, JPG or GIF (max 5 files)</small></div>
            </div>
            <div id="plMediaPreviews" class="pl-media-previews"></div>
          </div>

          <div class="pl-span-2"><h4 class="pl-section-title">Additional Information</h4></div>
          <div class="pl-field pl-span-2">
            <label>Description</label>
            <textarea id="plDesc" rows="4" placeholder="Tell us about the pet..."></textarea>
          </div>
          <div class="pl-field pl-span-2">
            <label>Health Flags</label>
            <div class="pl-flag-group">
              <label><input type="checkbox" id="plVaccinated" /> Vaccinated</label>
              <label><input type="checkbox" id="plNeutered" /> Neutered/Spayed</label>
              <label><input type="checkbox" id="plMicrochipped" /> Microchipped</label>
            </div>
          </div>
        </div>
        <div class="pl-modal-footer">
          <button type="button" class="btn" id="addListingCancel">Cancel</button>
          <button type="submit" class="btn primary" id="addListingSave">Submit Listing</button>
        </div>
      </form>
    </div>
  </div>
  </div>

<script>
(function() {
  'use strict';

  // ============================================
  // ANIMATED NUMBER COUNTERS
  // ============================================
  function animateNumbers() {
    document.querySelectorAll('.animated-number').forEach(elem => {
      const target = parseInt(elem.dataset.target) || 0;
      const duration = 1500;
      const startTime = performance.now();
      
      function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const easeProgress = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
        const current = Math.floor(easeProgress * target);
        
        elem.textContent = current.toLocaleString();
        
        if (progress < 1) {
          requestAnimationFrame(update);
        }
      }
      
      requestAnimationFrame(update);
    });
  }

  // ============================================
  // SEARCH AND FILTER FUNCTIONALITY
  // ============================================
  const searchInput = document.getElementById('searchPets');
  const listingTypeFilter = document.getElementById('listingTypeFilter');
  const statusFilter = document.getElementById('statusFilter');
  const speciesFilter = document.getElementById('speciesFilter');

  function filterTable() {
    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    const listingType = listingTypeFilter ? listingTypeFilter.value.toLowerCase() : '';
    const status = statusFilter ? statusFilter.value.toLowerCase() : '';
    const species = speciesFilter ? speciesFilter.value.toLowerCase() : '';

    const rows = document.querySelectorAll('.pets-table tbody tr');
    let visibleCount = 0;

    rows.forEach(row => {
      const petName = row.querySelector('.pet-name')?.textContent.toLowerCase() || '';
      const petBreed = row.querySelector('.pet-breed')?.textContent.toLowerCase() || '';
      const listedBy = row.querySelector('.listed-by-name')?.textContent.toLowerCase() || '';
      const rowType = row.querySelector('.type-badge')?.textContent.toLowerCase() || '';
      const rowStatus = row.querySelector('.status-pill')?.textContent.toLowerCase() || '';

      const matchesSearch = !searchTerm || 
        petName.includes(searchTerm) || 
        petBreed.includes(searchTerm) || 
        listedBy.includes(searchTerm);
      
      const matchesListingType = !listingType || rowType.includes(listingType);
      const matchesStatus = !status || rowStatus.includes(status);

      if (matchesSearch && matchesListingType && matchesStatus) {
        row.style.display = '';
        visibleCount++;
        
        // Add highlight animation
        row.classList.add('row-highlight');
        setTimeout(() => row.classList.remove('row-highlight'), 1500);
      } else {
        row.style.display = 'none';
      }
    });

    // Show/hide no results message
    updateTableMessage(visibleCount);
    // Update charts based on current visibility
    updateCharts();
  }

  function updateTableMessage(count) {
    let messageRow = document.querySelector('.no-results-row');
    
    if (count === 0) {
      if (!messageRow) {
        const tbody = document.querySelector('.pets-table tbody');
        messageRow = document.createElement('tr');
        messageRow.className = 'no-results-row';
        messageRow.innerHTML = `
          <td colspan="7" style="text-align:center;padding:40px;color:#64748b;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="opacity:0.3;margin:0 auto 12px;display:block">
              <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
              <path d="m21 21-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <div style="font-size:16px;font-weight:600;margin-bottom:4px">No pets found</div>
            <div style="font-size:14px">Try adjusting your search or filters</div>
          </td>
        `;
        tbody.appendChild(messageRow);
      }
      messageRow.style.display = '';
    } else if (messageRow) {
      messageRow.style.display = 'none';
    }
  }

  if (searchInput) searchInput.addEventListener('input', filterTable);
  if (listingTypeFilter) listingTypeFilter.addEventListener('change', filterTable);
  if (statusFilter) statusFilter.addEventListener('change', filterTable);
  if (speciesFilter) speciesFilter.addEventListener('change', filterTable);

  // Ensure Add New Listing button opens modal (direct binding)
  const addNewBtn = document.getElementById('addNewListing');
  if(addNewBtn){
    addNewBtn.addEventListener('click', function(e){ e.preventDefault(); openAddListingModal(); });
  }

  // ============================================
  // ACTION BUTTONS
  // ============================================
  document.addEventListener('click', function(e) {
    const target = e.target.closest('button');
    if (!target) return;

    // View button
    if (target.classList.contains('view-btn')) {
      const petId = target.dataset.petId;
      showNotification('Opening pet details...', 'info');
      console.log('View pet:', petId);
    }

    // Approve button
    if (target.classList.contains('approve-btn')) {
      const petId = target.dataset.petId;
      const row = target.closest('tr');
      const statusPill = row.querySelector('.status-pill');
      
      if (statusPill) {
        statusPill.className = 'status-pill status-approved';
        statusPill.textContent = 'Approved';
      }
      
      showNotification('Pet listing approved!', 'success');
      console.log('Approve pet:', petId);
      updateCharts();
    }

    // Reject button
    if (target.classList.contains('reject-btn')) {
      const petId = target.dataset.petId;
      const row = target.closest('tr');
      const statusPill = row.querySelector('.status-pill');
      
      if (statusPill) {
        statusPill.className = 'status-pill status-rejected';
        statusPill.textContent = 'Rejected';
      }
      
      showNotification('Pet listing rejected', 'warning');
      console.log('Reject pet:', petId);
      updateCharts();
    }

    // Delete button
    if (target.classList.contains('delete-btn')) {
      const petId = target.dataset.petId;
      if (confirm('Are you sure you want to delete this pet listing?')) {
        const row = target.closest('tr');
        row.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => row.remove(), 300);
        showNotification('Pet listing deleted', 'info');
        console.log('Delete pet:', petId);
        updateCharts();
      }
    }

    // Add new listing button
    if (target.id === 'addNewListing') {
      openAddListingModal();
    }

    // Sidebar approve/reject buttons
    if (target.classList.contains('btn-approve')) {
      const pendingItem = target.closest('.pending-item');
      pendingItem.style.animation = 'slideOut 0.3s ease';
      setTimeout(() => pendingItem.remove(), 300);
      showNotification('Request approved!', 'success');
      updatePendingCount(-1);
    }

    if (target.classList.contains('btn-reject')) {
      const pendingItem = target.closest('.pending-item');
      pendingItem.style.animation = 'slideOut 0.3s ease';
      setTimeout(() => pendingItem.remove(), 300);
      showNotification('Request rejected', 'warning');
      updatePendingCount(-1);
    }
  });

  // ============================================
  // NOTIFICATION SYSTEM
  // ============================================
  function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
      position: fixed;
      top: 80px;
      right: 20px;
      padding: 14px 20px;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 600;
      z-index: 10000;
      animation: slideInRight 0.3s ease;
      box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    `;
    
    if (type === 'success') {
      notification.style.background = 'linear-gradient(135deg, #10b981, #059669)';
      notification.style.color = 'white';
    } else if (type === 'info') {
      notification.style.background = 'linear-gradient(135deg, #3b82f6, #2563eb)';
      notification.style.color = 'white';
    } else if (type === 'warning') {
      notification.style.background = 'linear-gradient(135deg, #f59e0b, #d97706)';
      notification.style.color = 'white';
    }
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
      notification.style.animation = 'slideOutRight 0.3s ease';
      setTimeout(() => notification.remove(), 300);
    }, 3000);
  }

  // ============================================
  // TOOLTIPS
  // ============================================
  document.querySelectorAll('.action-btn').forEach(btn => {
    btn.addEventListener('mouseenter', function(e) {
      const title = this.getAttribute('title');
      if (!title) return;
      
      const tooltip = document.createElement('div');
      tooltip.className = 'tooltip';
      tooltip.textContent = title;
      tooltip.style.cssText = `
        position: absolute;
        background: #0f172a;
        color: white;
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 12px;
        white-space: nowrap;
        z-index: 1000;
        pointer-events: none;
        transform: translateY(-100%);
        margin-top: -8px;
      `;
      
      this.style.position = 'relative';
      this.appendChild(tooltip);
      
      const rect = this.getBoundingClientRect();
      tooltip.style.left = '50%';
      tooltip.style.transform = 'translateX(-50%) translateY(-100%)';
    });
    
    btn.addEventListener('mouseleave', function() {
      const tooltip = this.querySelector('.tooltip');
      if (tooltip) tooltip.remove();
    });
  });

  // ============================================
  // ENTRANCE ANIMATIONS
  // ============================================
  window.addEventListener('load', function() {
    // Animate numbers
    setTimeout(() => animateNumbers(), 100);

    // Stagger stat cards
    document.querySelectorAll('.stat-card').forEach((card, index) => {
      card.style.opacity = '0';
      card.style.transform = 'translateY(20px)';
      
      setTimeout(() => {
        card.style.transition = 'all 0.4s ease';
        card.style.opacity = '1';
        card.style.transform = 'translateY(0)';
      }, index * 80);
    });

    // Stagger table rows
    document.querySelectorAll('.pets-table tbody tr').forEach((row, index) => {
      row.style.opacity = '0';
      row.style.transform = 'translateX(-20px)';
      
      setTimeout(() => {
        row.style.transition = 'all 0.3s ease';
        row.style.opacity = '1';
        row.style.transform = 'translateX(0)';
      }, 400 + index * 50);
    });

    // Initial chart render
    updateCharts();
  });

  // ============================================
  // ANIMATION STYLES
  // ============================================
  const style = document.createElement('style');
  style.textContent = `
    @keyframes slideInRight {
      from { transform: translateX(400px); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
      from { transform: translateX(0); opacity: 1; }
      to { transform: translateX(400px); opacity: 0; }
    }
    @keyframes slideOut {
      from { transform: translateX(0); opacity: 1; }
      to { transform: translateX(-100px); opacity: 0; }
    }
    @keyframes rowHighlight {
      0% { background: #dbeafe; }
      100% { background: transparent; }
    }
    .row-highlight {
      animation: rowHighlight 1.5s ease;
    }
  `;
  document.head.appendChild(style);

  // ============================================
  // CHARTS: Donut + Monthly Line + Status stack
  // ============================================
  const chartTooltip = document.createElement('div');
  chartTooltip.className = 'chart-tooltip';
  document.body.appendChild(chartTooltip);

  function getVisibleData() {
    const rows = Array.from(document.querySelectorAll('.pets-table tbody tr'))
      .filter(r => r.style.display !== 'none');
    return rows.map(r => ({
      type: (r.dataset.type||'').toLowerCase(),
      status: (r.dataset.status||'').toLowerCase(),
      date: r.dataset.date,
      species: (r.dataset.species||'').toLowerCase()
    }));
  }

  function updateCharts(){
    const items = getVisibleData();
    renderDonut(items);
    renderMonthly(items);
    renderStatusStack(items);
  }

  function renderDonut(items){
    const svg = document.getElementById('donutChart');
    const center = document.getElementById('donutCenter');
    const legend = document.getElementById('donutLegend');
    if(!svg) return;
    svg.innerHTML = '';
    const adoption = items.filter(i=>i.type.includes('adoption')).length;
    const sale = items.filter(i=>i.type.includes('sale')).length;
    const total = adoption + sale;
    const cx = 130, cy = 130, r = 85, stroke = 30;
    const circumference = 2*Math.PI*r;
    // background ring
    const bg = document.createElementNS('http://www.w3.org/2000/svg','circle');
    bg.setAttribute('cx', cx); bg.setAttribute('cy', cy); bg.setAttribute('r', r);
    bg.setAttribute('fill','none'); bg.setAttribute('stroke','#e5e7eb'); bg.setAttribute('stroke-width', stroke);
    svg.appendChild(bg);
    // segments
    const segs = [
      {label:'Adoption', value: adoption, color:'#60a5fa'},
      {label:'Sale', value: sale, color:'#ef4444'}
    ];
    let offset = 0;
    segs.forEach(s => {
      const v = s.value, frac = total ? v/total : 0;
      if(frac <= 0) return;
      const arc = document.createElementNS('http://www.w3.org/2000/svg','circle');
      arc.setAttribute('cx', cx); arc.setAttribute('cy', cy); arc.setAttribute('r', r);
      arc.setAttribute('fill','none'); arc.setAttribute('stroke', s.color); arc.setAttribute('stroke-width', stroke);
      arc.setAttribute('stroke-dasharray', `${frac*circumference} ${circumference}`);
      arc.setAttribute('stroke-dashoffset', `${-offset*circumference}`);
      arc.style.transform = 'rotate(-90deg)';
      arc.style.transformOrigin = `${cx}px ${cy}px`;
      svg.appendChild(arc);
      offset += frac;
    });
    if(center){
      center.querySelector('.big').textContent = (total||0).toLocaleString();
    }
    if(legend){
      const pct = v => total ? Math.round(v/total*100) : 0;
      legend.innerHTML = `
        <div class="legend-item"><span class="legend-dot" style="background:#60a5fa"></span><span>Adoption (${pct(adoption)}%)</span></div>
        <div class="legend-item"><span class="legend-dot" style="background:#ef4444"></span><span>Sale (${pct(sale)}%)</span></div>
      `;
    }
  }

  function monthKey(dateStr){
    const d = new Date(dateStr);
    if(isNaN(d)) return null;
    return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}`;
  }

  function renderMonthly(items){
    const svg = document.getElementById('monthlyChart');
    const legend = document.getElementById('monthlyLegend');
    if(!svg) return;
    const width = svg.clientWidth || svg.parentElement.clientWidth || 300;
    const height = 220; svg.setAttribute('viewBox', `0 0 ${width} ${height}`);
    svg.innerHTML = '';
    const now = new Date();
    const keys=[]; for(let n=5; n>=0; n--){ const d=new Date(now.getFullYear(), now.getMonth()-n, 1); keys.push(`${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}`); }
    const counts = new Map(keys.map(k=>[k,0]));
    items.forEach(i=>{ const k=monthKey(i.date); if(k && counts.has(k)) counts.set(k, counts.get(k)+1); });
    const values = keys.map(k=> counts.get(k)||0);
    const maxV = Math.max(1, ...values);
    const padding = {l:36, r:12, t:16, b:28};
    const innerW = width - padding.l - padding.r;
    const innerH = height - padding.t - padding.b;
    // grid
    for(let g=0; g<=4; g++){
      const y = padding.t + innerH * g/4;
      const line = document.createElementNS('http://www.w3.org/2000/svg','line');
      line.setAttribute('x1', padding.l); line.setAttribute('x2', width - padding.r); line.setAttribute('y1', y); line.setAttribute('y2', y);
      line.setAttribute('stroke', '#e5e7eb'); line.setAttribute('stroke-width','1');
      svg.appendChild(line);
    }
    // x labels
    keys.forEach((k, idx)=>{
      const x = padding.l + innerW * idx/(keys.length-1);
      const text = document.createElementNS('http://www.w3.org/2000/svg','text');
      text.setAttribute('x', x); text.setAttribute('y', height - 6); text.setAttribute('text-anchor','middle');
      text.setAttribute('fill', '#64748b'); text.setAttribute('font-size','10');
      const [yyyy, mm] = k.split('-');
      text.textContent = `${mm}/${String(yyyy).slice(2)}`;
      svg.appendChild(text);
    });
    // path
    const pts = values.map((v, idx)=>{
      const x = padding.l + innerW * idx/(keys.length-1);
      const y = padding.t + innerH * (1 - (v/maxV));
      return {x,y,v,label:keys[idx]};
    });
    const path = document.createElementNS('http://www.w3.org/2000/svg','path');
    path.setAttribute('d', pts.map((p,i)=> (i?'L':'M')+p.x+','+p.y).join(' '));
    path.setAttribute('fill','none'); path.setAttribute('stroke','#3b82f6'); path.setAttribute('stroke-width','2');
    svg.appendChild(path);
    // points
    pts.forEach(p=>{
      const dot = document.createElementNS('http://www.w3.org/2000/svg','circle');
      dot.setAttribute('cx', p.x); dot.setAttribute('cy', p.y); dot.setAttribute('r', 3.5);
      dot.setAttribute('fill', '#3b82f6'); dot.style.cursor='pointer';
      dot.addEventListener('mouseenter', (ev)=> showTip(ev, `${p.label}: ${p.v} listings`));
      dot.addEventListener('mouseleave', hideTip);
      svg.appendChild(dot);
    });
    if(legend){ legend.innerHTML = `<div class="legend-item"><span class="legend-dot" style="background:#3b82f6"></span><span>New listings (last 6 months)</span></div>`; }
  }

  function renderStatusStack(items){
    const total = items.length || 1;
    const approved = items.filter(i=> i.status.includes('approved')).length;
    const pending = items.filter(i=> i.status.includes('pending')).length;
    const rejected = items.filter(i=> i.status.includes('rejected')).length;
    const a = Math.round(approved/total*100), p = Math.round(pending/total*100), r = 100 - a - p;
    const bar = document.querySelector('.mini-status .stack-bar');
    if(bar){
      const segs = bar.querySelectorAll('.seg');
      if(segs[0]) segs[0].style.width = `${a}%`;
      if(segs[1]) segs[1].style.width = `${p}%`;
      if(segs[2]) segs[2].style.width = `${r}%`;
    }
    const labs = document.querySelector('.mini-status .stack-labels');
    if(labs){
      labs.innerHTML = `<span class=\"lab approved\">Approved (${a}%)</span><span class=\"lab pending\">Pending (${p}%)</span><span class=\"lab rejected\">Rejected (${r}%)</span>`;
    }
  }

  function showTip(ev, text){
    chartTooltip.textContent = text;
    chartTooltip.style.opacity = '1';
    chartTooltip.style.left = (ev.clientX + 12) + 'px';
    chartTooltip.style.top = (ev.clientY + 12) + 'px';
  }
  function hideTip(){ chartTooltip.style.opacity = '0'; }

  // keep charts responsive
  window.addEventListener('resize', ()=>{
    updateCharts();
  });


  // ============================================
  // DRAWER OPEN/CLOSE & COUNT SYNC
  // ============================================
  const drawer = document.getElementById('listingDrawer');
  const backdrop = document.getElementById('listingBackdrop');
  function openDrawer(btn){
    if(!drawer || !backdrop) return;
    drawer.style.display = '';
    setTimeout(()=>{ drawer.classList.add('open'); }, 10);
    backdrop.classList.add('show');
    if(btn) btn.setAttribute('aria-expanded','true');
  }
  function closeDrawer(){
    if(!drawer || !backdrop) return;
    drawer.classList.remove('open');
    backdrop.classList.remove('show');
    const btn = document.getElementById('openListingDrawer');
    if(btn) btn.setAttribute('aria-expanded','false');
    setTimeout(()=>{ drawer.style.display = 'none'; }, 200);
  }

  document.addEventListener('click', function(e){
    const openBtn = e.target.closest('#openListingDrawer');
    if(openBtn){ e.preventDefault(); openDrawer(openBtn); return; }
    if(e.target.closest('#closeListingDrawer') || e.target.closest('#listingBackdrop')){ e.preventDefault(); closeDrawer(); return; }
  });
  document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeDrawer(); });

  function updatePendingCount(delta){
    // Update header badge
    const badge = document.querySelector('#openListingDrawer .pulse-badge');
    if(badge){
      const current = parseInt(badge.textContent) || 0;
      const next = Math.max(0, current + delta);
      badge.textContent = next;
    }
    // Update stat card number
    const stat = document.getElementById('pendingCountValue');
    if(stat){
      const current = parseInt((stat.textContent||'0').replace(/,/g,'')) || 0;
      const next = Math.max(0, current + delta);
      stat.textContent = next.toLocaleString();
    }
  }

  // ============================================
  // ADD LISTING MODAL BEHAVIOR
  // ============================================
  const addModal = document.getElementById('addListingModal');
  const addClose = document.getElementById('addListingClose');
  const addCancel = document.getElementById('addListingCancel');
  const addForm = document.getElementById('addListingForm');
  const plType = document.getElementById('plType');
  const plPriceField = document.getElementById('plPriceField');

  function openAddListingModal(){
    if(!addModal) return;
    addModal.style.display = 'block';
    setTimeout(()=> addModal.classList.add('show'), 10);
    document.body.classList.add('modal-open');
  }
  function closeAddListingModal(){
    if(!addModal) return;
    addModal.classList.remove('show');
    setTimeout(()=> { addModal.style.display = 'none'; }, 200);
    document.body.classList.remove('modal-open');
  }
  if(addClose) addClose.addEventListener('click', closeAddListingModal);
  if(addCancel) addCancel.addEventListener('click', closeAddListingModal);
  const addBackdrop = addModal ? addModal.querySelector('.pl-modal-backdrop') : null;
  if(addBackdrop) addBackdrop.addEventListener('click', closeAddListingModal);
  document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') closeAddListingModal(); });

  if(plType){
    plType.addEventListener('change', ()=>{
      if(plType.value === 'Sale'){ plPriceField.style.display = ''; }
      else { plPriceField.style.display = 'none'; }
    });
  }

  // Purpose pills interaction
  const purposeWrap = document.querySelector('.pl-purpose');
  if(purposeWrap){
    purposeWrap.addEventListener('click', (e)=>{
      const pill = e.target.closest('.pill');
      if(!pill) return;
      purposeWrap.querySelectorAll('.pill').forEach(p=>p.classList.remove('active'));
      pill.classList.add('active');
      const val = pill.getAttribute('data-value');
      const sel = document.getElementById('plType');
      if(sel){ sel.value = val; sel.dispatchEvent(new Event('change')); }
    });
  }

  // Simple dropzone
  const dz = document.getElementById('plDropzone');
  const dzInput = document.getElementById('plMediaInput');
  const dzPreviews = document.getElementById('plMediaPreviews');
  if(dz && dzInput){
    dz.addEventListener('click', ()=> dzInput.click());
    dz.addEventListener('dragover', (e)=>{ e.preventDefault(); dz.classList.add('drag'); });
    dz.addEventListener('dragleave', ()=> dz.classList.remove('drag'));
    dz.addEventListener('drop', (e)=>{
      e.preventDefault(); dz.classList.remove('drag');
      if(e.dataTransfer && e.dataTransfer.files) handleFiles(e.dataTransfer.files);
    });
    dzInput.addEventListener('change', ()=> handleFiles(dzInput.files));
  }
  function handleFiles(fileList){
    if(!dzPreviews) return;
    const files = Array.from(fileList).slice(0,5);
    files.forEach(file=>{
      const reader = new FileReader();
      reader.onload = function(evt){
        const div = document.createElement('div');
        div.className = 'thumb';
        const img = document.createElement('img');
        img.src = evt.target.result;
        div.appendChild(img);
        dzPreviews.appendChild(div);
      };
      reader.readAsDataURL(file);
    });
  }

  function incTextInt(el, delta){ if(!el) return; const n=parseInt((el.textContent||'0').replace(/,/g,''))||0; el.textContent = (Math.max(0, n+delta)).toLocaleString(); }

  if(addForm){
    addForm.addEventListener('submit', function(e){
      e.preventDefault();
      const name = document.getElementById('plName').value.trim();
      const species = document.getElementById('plSpecies').value;
      const breed = document.getElementById('plBreed').value.trim();
      const gender = document.getElementById('plGender').value;
      const age = document.getElementById('plAge').value;
      const type = document.getElementById('plType').value;
      const price = document.getElementById('plPrice').value;
      const status = document.getElementById('plStatus').value;
      const listedBy = document.getElementById('plListedBy').value.trim() || 'Admin';
      const contact = document.getElementById('plContact').value.trim();
      const image = document.getElementById('plImage').value.trim() || '/PETVET/views/shared/images/sidebar/pets.png';
      const desc = document.getElementById('plDesc').value.trim();
      const vaccinated = document.getElementById('plVaccinated').checked;
      const neutered = document.getElementById('plNeutered').checked;
      const microchipped = document.getElementById('plMicrochipped').checked;
      if(!name){ showNotification('Please enter a pet name', 'warning'); return; }

      const tbody = document.querySelector('.pets-table tbody');
      const tr = document.createElement('tr');
      const petId = Date.now();
      const today = new Date();
      const dateStr = today.toISOString().slice(0,10);
      tr.setAttribute('data-pet-id', petId);
      tr.setAttribute('data-type', type.toLowerCase());
      tr.setAttribute('data-status', status.toLowerCase());
      tr.setAttribute('data-date', dateStr);
      tr.setAttribute('data-species', species.toLowerCase());
      tr.innerHTML = `
        <td class="pet-info-cell">
          <div class="pet-avatar-wrapper">
            <img src="${image}" alt="${name}" class="pet-avatar">
          </div>
          <div class="pet-details">
            <div class="pet-name">${name}</div>
            <div class="pet-breed">${breed || species}</div>
          </div>
        </td>
        <td>
          <span class="type-badge ${type.toLowerCase()}-badge">${type}</span>
        </td>
        <td>
          <div class="listed-by-cell">
            <div class="listed-by-name">${listedBy}</div>
          </div>
        </td>
        <td class="contact-cell">${contact}</td>
        <td>
          <span class="status-pill status-${status.toLowerCase()}">${status}</span>
        </td>
        <td class="date-cell">${dateStr}</td>
        <td class="actions-cell">
          <button class="action-btn view-btn" title="View Details" data-pet-id="${petId}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/>
              <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
            </svg>
          </button>
          <button class="action-btn approve-btn" title="Approve" data-pet-id="${petId}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </button>
          <button class="action-btn reject-btn" title="Reject" data-pet-id="${petId}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="m18 6-12 12M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </button>
          <button class="action-btn delete-btn" title="Delete" data-pet-id="${petId}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </button>
        </td>`;
      tbody.prepend(tr);
      closeAddListingModal();
      addForm.reset();
      if(plPriceField) plPriceField.style.display = 'none';
      // update stats
      const totalEl = document.getElementById('totalPetsValue');
      if(totalEl) { const t=parseInt((totalEl.textContent||'0').replace(/,/g,''))||0; totalEl.textContent=(t+1).toLocaleString(); }
      if(type === 'Adoption'){ const el=document.getElementById('adoptionCountValue'); if(el){ const n=parseInt((el.textContent||'0').replace(/,/g,''))||0; el.textContent=(n+1).toLocaleString(); } }
      if(type === 'Sale'){ const el=document.getElementById('saleCountValue'); if(el){ const n=parseInt((el.textContent||'0').replace(/,/g,''))||0; el.textContent=(n+1).toLocaleString(); } }
      if(status === 'Approved'){ const el=document.getElementById('approvedListingsValue'); if(el){ const n=parseInt((el.textContent||'0').replace(/,/g,''))||0; el.textContent=(n+1).toLocaleString(); } }
      if(status === 'Rejected'){ const el=document.getElementById('rejectedListingsValue'); if(el){ const n=parseInt((el.textContent||'0').replace(/,/g,''))||0; el.textContent=(n+1).toLocaleString(); } }
      showNotification('New pet listing added', 'success');
      updateCharts();
    });
  }

  // keep charts responsive
  window.addEventListener('resize', ()=>{ updateCharts(); });

})();
</script>