// Admin Pet Listings Management
let currentListings = [];

// DOM Elements
const searchInput = document.getElementById('searchInput');
const statusFilter = document.getElementById('statusFilter');
const speciesFilter = document.getElementById('speciesFilter');
const listingsGrid = document.getElementById('listingsGrid');
const pendingCount = document.getElementById('pendingCount');
const approvedCount = document.getElementById('approvedCount');
const viewModal = document.getElementById('viewModal');
const confirmApproveModal = document.getElementById('confirmApproveModal');
const confirmDeclineModal = document.getElementById('confirmDeclineModal');

// Modal Elements
const mainImage = document.getElementById('mainImage');
const thumbnailsContainer = document.getElementById('thumbnails');
const detailName = document.getElementById('detailName');
const detailSpecies = document.getElementById('detailSpecies');
const detailBreed = document.getElementById('detailBreed');
const detailAge = document.getElementById('detailAge');
const detailGender = document.getElementById('detailGender');
const detailPrice = document.getElementById('detailPrice');
const detailLocation = document.getElementById('detailLocation');
const detailDescription = document.getElementById('detailDescription');
const detailPhone = document.getElementById('detailPhone');
const detailPhone2 = document.getElementById('detailPhone2');
const detailEmail = document.getElementById('detailEmail');
const detailBadges = document.getElementById('detailBadges');
const detailOwner = document.getElementById('detailOwner');
const detailOwnerEmail = document.getElementById('detailOwnerEmail');
const detailStatus = document.getElementById('detailStatus');

// Current listing ID for actions
let currentListingId = null;

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  fetchListings();
  setupEventListeners();
});

// Event Listeners
function setupEventListeners() {
  searchInput.addEventListener('input', filterListings);
  statusFilter.addEventListener('change', filterListings);
  speciesFilter.addEventListener('change', filterListings);
  
  // Close modals with X button
  document.querySelectorAll('.modal-close').forEach(btn => {
    btn.addEventListener('click', () => closeAllModals());
  });
  
  // Close modals with cancel buttons
  document.querySelectorAll('.btn-cancel, .btn.outline').forEach(btn => {
    if (btn.textContent.includes('Cancel') || btn.textContent.includes('Close')) {
      btn.addEventListener('click', () => closeAllModals());
    }
  });
  
  // Close modal on overlay click
  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) closeAllModals();
    });
  });
  
  // Approve confirmation
  const approveBtn = document.getElementById('confirmApproveBtn');
  if (approveBtn) approveBtn.addEventListener('click', approveListing);
  
  // Decline confirmation
  const declineBtn = document.getElementById('confirmDeclineBtn');
  if (declineBtn) declineBtn.addEventListener('click', declineListing);
}

// Fetch all listings
async function fetchListings() {
  listingsGrid.innerHTML = '<div class="loading-state"><div class="spinner"></div><p>Loading listings...</p></div>';
  
  try {
    const response = await fetch('/PETVET/api/sell-pet-listings/get-all-listings.php');
    const data = await response.json();
    
    console.log('API Response:', data); // Debug log
    if (data.data && data.data[0]) {
      console.log('First listing images:', data.data[0].images); // Debug images
    }
    
    if (data.success) {
      currentListings = data.data;
      updateStats();
      renderListings(currentListings);
    } else {
      console.error('API Error:', data);
      showError('Failed to load listings: ' + (data.message || 'Unknown error') + (data.error ? ' - ' + data.error : ''));
    }
  } catch (error) {
    console.error('Error fetching listings:', error);
    showError('Network error. Please try again. Details: ' + error.message);
  }
}

// Update statistics
function updateStats() {
  const pending = currentListings.filter(l => l.status === 'pending').length;
  const approved = currentListings.filter(l => l.status === 'approved').length;
  
  pendingCount.textContent = `${pending} Pending`;
  approvedCount.textContent = `${approved} Approved`;
}

// Render listings
function renderListings(listings) {
  if (listings.length === 0) {
    listingsGrid.innerHTML = `
      <div class="empty-state">
        <h4>No listings found</h4>
        <p>Try adjusting your filters</p>
      </div>
    `;
    return;
  }
  
  listingsGrid.innerHTML = listings.map(listing => {
    // Handle images - could be string or object
    let mainImg = '/PETVET/public/images/sample-xray.jpg'; // Use existing image as fallback
    if (listing.images && listing.images.length > 0) {
      const firstImg = listing.images[0];
      // Image URL already contains /PETVET/ prefix, don't add it again
      mainImg = typeof firstImg === 'string' ? firstImg : firstImg.image_url;
    }
    
    const badgesHtml = listing.badges && listing.badges.length > 0
      ? listing.badges.map(b => `<span class="badge">${typeof b === 'string' ? b : b.badge}</span>`).join('')
      : '';
    
    return `
      <div class="listing-card" data-id="${listing.id}">
        <div class="listing-image">
          <img src="${mainImg}" alt="${listing.name}">
          <span class="status-badge-overlay ${listing.status}">${listing.status}</span>
        </div>
        <div class="listing-body">
          <h3 class="listing-title">${listing.name}</h3>
          <div class="listing-meta">
            <span class="meta-item">${listing.species}</span>
            <span class="meta-item">•</span>
            <span class="meta-item">${listing.breed}</span>
            <span class="meta-item">•</span>
            <span class="meta-item">${listing.age} ${listing.age === '1' ? 'year' : 'years'}</span>
          </div>
          <div class="listing-price">LKR ${parseFloat(listing.price).toLocaleString()}</div>
          <div class="listing-owner">
            <strong>Owner:</strong> ${listing.username || 'N/A'} (${listing.user_email || 'N/A'})
          </div>
          <div class="listing-actions">
            <button class="btn btn-icon outline btn-view" data-id="${listing.id}">
              👁️ View
            </button>
            ${listing.status === 'pending' ? `
              <button class="btn success btn-approve" data-id="${listing.id}" data-name="${listing.name.replace(/"/g, '&quot;')}">
                ✓ Approve
              </button>
              <button class="btn danger btn-decline" data-id="${listing.id}" data-name="${listing.name.replace(/"/g, '&quot;')}">
                ✗ Decline
              </button>
            ` : ''}
          </div>
        </div>
      </div>
    `;
  }).join('');
  
  // Add event listeners using delegation
  const viewButtons = listingsGrid.querySelectorAll('.btn-view');
  console.log('View buttons found:', viewButtons.length); // Debug
  
  viewButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      console.log('View button clicked, ID:', btn.dataset.id); // Debug
      viewDetails(parseInt(btn.dataset.id));
    });
  });
  
  listingsGrid.querySelectorAll('.btn-approve').forEach(btn => {
    btn.addEventListener('click', () => showApproveConfirm(parseInt(btn.dataset.id), btn.dataset.name));
  });
  
  listingsGrid.querySelectorAll('.btn-decline').forEach(btn => {
    btn.addEventListener('click', () => showDeclineConfirm(parseInt(btn.dataset.id), btn.dataset.name));
  });
}

// Filter listings
function filterListings() {
  const searchTerm = searchInput.value.toLowerCase();
  const statusValue = statusFilter.value;
  const speciesValue = speciesFilter.value;
  
  let filtered = [...currentListings];
  
  // Search filter
  if (searchTerm) {
    filtered = filtered.filter(l => 
      l.name.toLowerCase().includes(searchTerm) ||
      l.breed.toLowerCase().includes(searchTerm) ||
      l.location.toLowerCase().includes(searchTerm) ||
      l.username?.toLowerCase().includes(searchTerm)
    );
  }
  
  // Status filter
  if (statusValue && statusValue !== 'all') {
    filtered = filtered.filter(l => l.status === statusValue);
  }
  
  // Species filter
  if (speciesValue && speciesValue !== 'all') {
    filtered = filtered.filter(l => l.species === speciesValue);
  }
  
  renderListings(filtered);
}

// View details
function viewDetails(id) {
  console.log('viewDetails called with ID:', id);
  console.log('currentListings:', currentListings);
  
  // Convert id to string for comparison since DB IDs come as strings
  const listing = currentListings.find(l => l.id == id); // Use == instead of ===
  console.log('Found listing:', listing);
  
  if (!listing) {
    console.error('Listing not found!');
    return;
  }
  
  currentListingId = id;
  
  console.log('About to process images...');
  
  // Images
  if (listing.images && listing.images.length > 0) {
    const firstImg = listing.images[0];
    // Image URL already contains /PETVET/ prefix
    const firstImgUrl = typeof firstImg === 'string' ? firstImg : firstImg.image_url;
    mainImage.src = firstImgUrl;
    mainImage.style.display = 'block';
    
    // Add carousel navigation if multiple images
    const detailImagesDiv = document.querySelector('.detail-images');
    if (listing.images.length > 1) {
      // Remove old nav buttons if they exist
      detailImagesDiv.querySelectorAll('.carousel-nav').forEach(btn => btn.remove());
      
      // Add navigation buttons
      const prevBtn = document.createElement('button');
      prevBtn.className = 'carousel-nav prev';
      prevBtn.addEventListener('click', () => navigateImage(-1, listing.images));
      
      const nextBtn = document.createElement('button');
      nextBtn.className = 'carousel-nav next';
      nextBtn.addEventListener('click', () => navigateImage(1, listing.images));
      
      detailImagesDiv.insertBefore(prevBtn, mainImage);
      detailImagesDiv.insertBefore(nextBtn, mainImage.nextSibling);
    }
    
    thumbnailsContainer.innerHTML = listing.images.map((img, idx) => {
      const imgUrl = typeof img === 'string' ? img : img.image_url;
      return `
        <img src="${imgUrl}" 
             alt="${listing.name}" 
             class="detail-thumbnail ${idx === 0 ? 'active' : ''}"
             data-index="${idx}"
             data-src="${imgUrl}">
      `;
    }).join('');
    
    // Add click handlers to thumbnails
    thumbnailsContainer.querySelectorAll('.detail-thumbnail').forEach(thumb => {
      thumb.addEventListener('click', () => changeMainImage(thumb));
    });
  } else {
    mainImage.style.display = 'none';
    thumbnailsContainer.innerHTML = '<p style="color: var(--muted);">No images uploaded</p>';
  }
  
  // Details
  detailName.textContent = listing.name;
  detailSpecies.textContent = listing.species;
  detailBreed.textContent = listing.breed;
  detailAge.textContent = `${listing.age} ${listing.age === '1' ? 'year' : 'years'}`;
  detailGender.textContent = listing.gender;
  detailPrice.textContent = `LKR ${parseFloat(listing.price).toLocaleString()}`;
  detailLocation.textContent = listing.location;
  detailDescription.textContent = listing.description || 'No description provided';
  detailPhone.textContent = listing.phone || 'N/A';
  detailPhone2.textContent = listing.phone2 || 'N/A';
  detailEmail.textContent = listing.email || 'N/A';
  detailOwner.textContent = listing.username || 'N/A';
  detailOwnerEmail.textContent = listing.email || 'N/A';
  detailStatus.textContent = listing.status;
  detailStatus.className = `status-badge-overlay ${listing.status}`;
  
  // Badges
  if (listing.badges && listing.badges.length > 0) {
    detailBadges.innerHTML = listing.badges.map(b => {
      const badgeText = typeof b === 'string' ? b : b.badge;
      return `<span class="badge">${badgeText}</span>`;
    }).join('');
  } else {
    detailBadges.innerHTML = '<span style="color: var(--muted);">No badges</span>';
  }
  
  console.log('About to show modal, viewModal element:', viewModal);
  viewModal.style.display = 'flex';
  console.log('Modal display set to flex');
}

// Change main image
function changeMainImage(thumb) {
  mainImage.src = thumb.src;
  document.querySelectorAll('.detail-thumbnail').forEach(t => t.classList.remove('active'));
  thumb.classList.add('active');
}

// Navigate carousel images
function navigateImage(direction, images) {
  const activeThumbnail = document.querySelector('.detail-thumbnail.active');
  if (!activeThumbnail) return;
  
  const currentIndex = parseInt(activeThumbnail.dataset.index);
  let newIndex = currentIndex + direction;
  
  // Loop around
  if (newIndex < 0) newIndex = images.length - 1;
  if (newIndex >= images.length) newIndex = 0;
  
  const newThumbnail = document.querySelector(`.detail-thumbnail[data-index="${newIndex}"]`);
  if (newThumbnail) {
    changeMainImage(newThumbnail);
  }
}

// Show approve confirmation
function showApproveConfirm(id, name) {
  currentListingId = id;
  document.getElementById('approvePetName').textContent = name;
  confirmApproveModal.style.display = 'flex';
}

// Show decline confirmation
function showDeclineConfirm(id, name) {
  currentListingId = id;
  document.getElementById('declinePetName').textContent = name;
  confirmDeclineModal.style.display = 'flex';
}

// Approve listing
async function approveListing() {
  if (!currentListingId) return;
  
  const btn = document.getElementById('confirmApproveBtn');
  btn.disabled = true;
  btn.textContent = 'Approving...';
  
  try {
    const formData = new FormData();
    formData.append('id', currentListingId);
    
    const response = await fetch('/PETVET/api/sell-pet-listings/approve.php', {
      method: 'POST',
      body: formData
    });
    
    const data = await response.json();
    
    if (data.success) {
      showSuccess('Listing approved successfully!');
      closeAllModals();
      await fetchListings(); // Refresh
    } else {
      showError('Failed to approve: ' + data.message);
    }
  } catch (error) {
    console.error('Error approving listing:', error);
    showError('Network error. Please try again.');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Yes, Approve';
  }
}

// Decline listing
async function declineListing() {
  if (!currentListingId) return;
  
  const btn = document.getElementById('confirmDeclineBtn');
  btn.disabled = true;
  btn.textContent = 'Declining...';
  
  try {
    const formData = new FormData();
    formData.append('id', currentListingId);
    
    const response = await fetch('/PETVET/api/sell-pet-listings/decline.php', {
      method: 'POST',
      body: formData
    });
    
    const data = await response.json();
    
    if (data.success) {
      showSuccess('Listing declined and deleted successfully!');
      closeAllModals();
      await fetchListings(); // Refresh
    } else {
      showError('Failed to decline: ' + data.message);
    }
  } catch (error) {
    console.error('Error declining listing:', error);
    showError('Network error. Please try again.');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Yes, Decline';
  }
}

// Close all modals
function closeAllModals() {
  viewModal.style.display = 'none';
  confirmApproveModal.style.display = 'none';
  confirmDeclineModal.style.display = 'none';
  currentListingId = null;
}

// Show success message
function showSuccess(message) {
  alert('✅ ' + message); // Replace with your preferred notification system
}

// Show error message
function showError(message) {
  alert('❌ ' + message); // Replace with your preferred notification system
}
