// Lost & Found interactions - Enhanced version
// Global distance tracking variables
let userLocation = null;
let reportsWithDistance = new Map(); // Map of report_id => distance data

// Helper function for querySelector (global scope for use in async functions)
const qs = s => document.querySelector(s);
const qsa = s => document.querySelectorAll(s);

// Calculate relative time (e.g., "2 hours ago", "30 minutes ago")
function getRelativeTime(dateStr, timeStr) {
	if (!dateStr) return '';
	
	// Combine date and time
	const dateTimeStr = timeStr ? `${dateStr} ${timeStr}` : dateStr;
	const reportDate = new Date(dateTimeStr);
	const now = new Date();
	
	if (isNaN(reportDate.getTime())) return '';
	
	const diffMs = now - reportDate;
	const diffSec = Math.floor(diffMs / 1000);
	const diffMin = Math.floor(diffSec / 60);
	const diffHour = Math.floor(diffMin / 60);
	const diffDay = Math.floor(diffHour / 24);
	const diffWeek = Math.floor(diffDay / 7);
	const diffMonth = Math.floor(diffDay / 30);
	const diffYear = Math.floor(diffDay / 365);
	
	if (diffSec < 60) return 'Just now';
	if (diffMin < 60) return `${diffMin} minute${diffMin !== 1 ? 's' : ''} ago`;
	if (diffHour < 24) return `${diffHour} hour${diffHour !== 1 ? 's' : ''} ago`;
	if (diffDay < 7) return `${diffDay} day${diffDay !== 1 ? 's' : ''} ago`;
	if (diffWeek < 4) return `${diffWeek} week${diffWeek !== 1 ? 's' : ''} ago`;
	if (diffMonth < 12) return `${diffMonth} month${diffMonth !== 1 ? 's' : ''} ago`;
	return `${diffYear} year${diffYear !== 1 ? 's' : ''} ago`;
}

document.addEventListener('DOMContentLoaded', () => {
	const segBtns = qsa('.seg-btn');
	const lostList  = qs('#lostList');
	const foundList = qs('#foundList');
	const searchInput = qs('#q');
	const speciesSel  = qs('#species');
	const sortSel     = qs('#sortBy');

	// Leaflet map instances
	let reportMap = null;
	let reportMarker = null;
	let editMap = null;
	let editMarker = null;

	// Report Pet Modal
	const reportModal = qs('#reportModal');
	const openReportBtns = ['#openReport','#emptyReportLost','#emptyReportFound']
		.map(sel=>qs(sel)).filter(Boolean);
	const cancelReportBtn = qs('#cancelReport');
	const reportForm = qs('#reportForm');
	const photoInput = qs('#rPhoto');
	const photoPreview = qs('#photoPreview');

	// Contact Modal
	const contactModal = qs('#contactModal');
	const contactContent = qs('#contactContent');
	const closeContactBtn = qs('#closeContact');

	// My Listings Modal
	const myListingsModal = qs('#myListingsModal');
	const myListingsBtn = qs('#myListingsBtn');
	const myListingsContent = qs('#myListingsContent');
	const closeMyListingsBtn = qs('#closeMyListings');

	// Edit Listing Modal
	const editListingModal = qs('#editListingModal');
	const editListingForm = qs('#editListingForm');
	const cancelEditListingBtn = qs('#cancelEditListing');

	// Confirm Dialog
	const confirmDialog = qs('#confirmDialog');
	const confirmMessage = qs('#confirmMessage');
	const confirmPetName = qs('#confirmPetName');
	const cancelConfirmBtn = qs('#cancelConfirm');
	const confirmDeleteBtn = qs('#confirmDelete');

	let currentView = 'lost';
	let currentDeleteId = null;
	let myListings = []; // Demo data - in production, fetch from server

	// Initialize Leaflet map for report modal
	function initReportMap() {
		if (reportMap) {
			reportMap.remove();
		}

		// Default center - Sri Lanka (Colombo)
		const defaultLat = 6.9271;
		const defaultLng = 79.8612;

		reportMap = L.map('mapContainer').setView([defaultLat, defaultLng], 13);

		// Add OpenStreetMap tiles
		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
			maxZoom: 19
		}).addTo(reportMap);

		// Add click handler
		reportMap.on('click', function(e) {
			const { lat, lng } = e.latlng;
			
			// Update hidden fields
			qs('#rLatitude').value = lat.toFixed(6);
			qs('#rLongitude').value = lng.toFixed(6);
			
			// Update marker
			if (reportMarker) {
				reportMarker.setLatLng([lat, lng]);
			} else {
				reportMarker = L.marker([lat, lng]).addTo(reportMap);
			}

			// Reverse geocode to get address
			fetchAddress(lat, lng, 'rLocation');
		});

		// Try to get user's current location
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(
				(position) => {
					const { latitude, longitude } = position.coords;
					reportMap.setView([latitude, longitude], 15);
				},
				(error) => {
					console.log('Geolocation error:', error);
				}
			);
		}
	}

	// Initialize Leaflet map for edit modal
	function initEditMap(lat, lng) {
		if (editMap) {
			editMap.remove();
		}

		// Use provided coordinates or default
		const centerLat = lat || 6.9271;
		const centerLng = lng || 79.8612;

		editMap = L.map('editMapContainer').setView([centerLat, centerLng], 13);

		// Add OpenStreetMap tiles
		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
			maxZoom: 19
		}).addTo(editMap);

		// Add marker if coordinates provided
		if (lat && lng) {
			editMarker = L.marker([lat, lng]).addTo(editMap);
		}

		// Add click handler
		editMap.on('click', function(e) {
			const { lat, lng } = e.latlng;
			
			// Update hidden fields
			qs('#editLatitude').value = lat.toFixed(6);
			qs('#editLongitude').value = lng.toFixed(6);
			
			// Update marker
			if (editMarker) {
				editMarker.setLatLng([lat, lng]);
			} else {
				editMarker = L.marker([lat, lng]).addTo(editMap);
			}

			// Reverse geocode to get address
			fetchAddress(lat, lng, 'editLocation');
		});
	}

	// Reverse geocode using Nominatim API via PHP proxy (to avoid CORS issues)
	async function fetchAddress(lat, lng, targetInputId) {
		const inputElement = qs('#' + targetInputId);
		if (!inputElement) {
			console.error('Input element not found:', targetInputId);
			return;
		}
		
		inputElement.value = 'Getting location...';
		
		try {
			// Use PHP proxy to avoid CORS issues
			const response = await fetch(
				`/PETVET/api/pet-owner/reverse-geocode.php?lat=${lat}&lng=${lng}`
			);
			
			if (!response.ok) {
				throw new Error(`HTTP error! status: ${response.status}`);
			}
			
			const data = await response.json();
			
			if (data.success && data.location) {
				inputElement.value = data.location;
			} else if (data.fallback) {
				inputElement.value = data.fallback;
			} else {
				// Fallback to coordinates
				inputElement.value = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
			}
		} catch (error) {
			console.error('Geocoding error:', error);
			// On error, show coordinates
			inputElement.value = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
		}
	}

	const params = new URLSearchParams(location.search);
	currentView = params.get('view') || localStorage.getItem('lfView') || 'lost';
	setView(currentView);

	segBtns.forEach(btn=> btn.addEventListener('click', () => {
		const v = btn.getAttribute('data-view');
		setView(v);
	}));

	// Image Carousel functionality
	function setupCarousels() {
		const cards = qsa('.card');
		cards.forEach(card => {
			const media = card.querySelector('.card-media');
			if (!media) return;

			const images = Array.from(media.querySelectorAll('.carousel-image'));
			if (images.length <= 1) return;

			let currentIndex = 0;
			const prevBtn = media.querySelector('.carousel-nav.prev');
			const nextBtn = media.querySelector('.carousel-nav.next');
			const indicators = Array.from(media.querySelectorAll('.carousel-indicator'));

			function updateCarousel(newIndex) {
				currentIndex = (newIndex + images.length) % images.length;
				
				// Hide all images and show only the current one
				images.forEach((img, idx) => {
					img.style.display = idx === currentIndex ? 'block' : 'none';
				});
				
				// Update indicators
				indicators.forEach((ind, idx) => {
					ind.classList.toggle('active', idx === currentIndex);
				});
			}

			if (prevBtn) {
				prevBtn.addEventListener('click', (e) => {
					e.preventDefault();
					e.stopPropagation();
					updateCarousel(currentIndex - 1);
				});
			}

			if (nextBtn) {
				nextBtn.addEventListener('click', (e) => {
					e.preventDefault();
					e.stopPropagation();
					updateCarousel(currentIndex + 1);
				});
			}

			indicators.forEach((indicator, idx) => {
				indicator.addEventListener('click', (e) => {
					e.preventDefault();
					e.stopPropagation();
					updateCarousel(idx);
				});
			});
		});
	}

	// Contact button functionality
	function setupContactButtons() {
		const contactBtns = qsa('.contact-owner-btn');
		contactBtns.forEach(btn => {
			btn.addEventListener('click', () => {
				const name = btn.getAttribute('data-name');
				const phone = btn.getAttribute('data-phone');
				const phone2 = btn.getAttribute('data-phone2');
				const email = btn.getAttribute('data-email');

				contactContent.innerHTML = '';

				// Phone 1
				if (phone) {
					const item = document.createElement('div');
					item.className = 'contact-item';
					item.innerHTML = `
						<div class="contact-info">
							<div class="contact-label">Primary Phone</div>
							<div class="contact-value">${phone}</div>
						</div>
						<a href="tel:${phone}" class="btn primary">Call</a>
					`;
					contactContent.appendChild(item);
				}

				// Phone 2
				if (phone2 && phone2.trim()) {
					const item = document.createElement('div');
					item.className = 'contact-item';
					item.innerHTML = `
						<div class="contact-info">
							<div class="contact-label">Secondary Phone</div>
							<div class="contact-value">${phone2}</div>
						</div>
						<a href="tel:${phone2}" class="btn primary">Call</a>
					`;
					contactContent.appendChild(item);
				}

				// Email
				if (email) {
					const item = document.createElement('div');
					item.className = 'contact-item';
					item.innerHTML = `
						<div class="contact-info">
							<div class="contact-label">Email Address</div>
							<div class="contact-value">${email}</div>
						</div>
						<a href="mailto:${email}?subject=Regarding ${name}" class="btn outline">Email</a>
					`;
					contactContent.appendChild(item);
				}

				contactModal.hidden = false;
			});
		});
	}

	// My Listings functionality
	async function loadMyListings() {
		try {
			const response = await fetch('/PETVET/api/pet-owner/get-my-reports.php');
			const result = await response.json();
			
			if (result.success) {
				// Transform API data to match existing format
				myListings = result.reports.map(report => ({
					id: report.id,
					type: report.type,
					species: report.species,
					name: report.name || 'Unknown',
					color: report.color,
					location: report.location,
					date: report.date,
					time: report.time || '',
					notes: report.notes,
					phone: report.contact.phone,
					phone2: report.contact.phone2,
					email: report.contact.email,
					photos: report.photos || [],
					latitude: report.latitude,
					longitude: report.longitude
				}));
			} else {
				console.error('Failed to load listings:', result.message);
				myListings = [];
			}
		} catch (error) {
			console.error('Error loading listings:', error);
			myListings = [];
		}
	}

	function renderMyListings() {
		if (myListings.length === 0) {
			myListingsContent.innerHTML = `
				<div class="empty" style="border:none;padding:32px;">
					<h3>No listings yet</h3>
					<p>You haven't reported any lost or found pets.</p>
				</div>
			`;
			return;
		}

		myListingsContent.innerHTML = '';
		myListings.forEach((listing) => {
			const item = document.createElement('div');
			item.className = 'listing-item';
			
			const photoHtml = listing.photos && listing.photos[0] 
				? `<img src="${listing.photos[0]}" class="listing-thumb" alt="${listing.name}">`
				: `<div class="listing-thumb-fallback">${listing.species.charAt(0).toUpperCase()}</div>`;

			item.innerHTML = `
				${photoHtml}
				<div class="listing-details">
					<h4 class="listing-title">${listing.name || 'Unknown Name'}</h4>
					<div class="listing-meta">${listing.species} • ${listing.location} • ${listing.date}</div>
					<span class="listing-badge ${listing.type}">${listing.type.toUpperCase()}</span>
				</div>
				<div class="listing-actions">
					<button class="btn outline edit-listing-btn" data-id="${listing.id}">Edit</button>
					<button class="btn danger delete-listing-btn" data-id="${listing.id}" data-name="${listing.name || 'this pet'}">Delete</button>
				</div>
			`;
			myListingsContent.appendChild(item);
		});

		setupListingButtons();
	}

	function setupListingButtons() {
		// Edit buttons
		qsa('.edit-listing-btn').forEach(btn => {
			btn.addEventListener('click', () => {
				const id = parseInt(btn.getAttribute('data-id'));
				openEditListing(id);
			});
		});

		// Delete buttons
		qsa('.delete-listing-btn').forEach(btn => {
			btn.addEventListener('click', () => {
				const id = parseInt(btn.getAttribute('data-id'));
				const name = btn.getAttribute('data-name');
				openConfirmDialog(id, name);
			});
		});
	}

	function openEditListing(id) {
		const listing = myListings.find(l => l.id == id);
		if (!listing) return;

		qs('#editId').value = id;
		qs('#editType').value = listing.type;
		qs('#editSpecies').value = listing.species;
		qs('#editName').value = listing.name || '';
		qs('#editColor').value = listing.color || '';
		qs('#editLocation').value = listing.location;
		qs('#editDate').value = listing.date;
		qs('#editTime').value = listing.time || '';
		qs('#editNotes').value = listing.notes || '';
		qs('#editPhone').value = listing.phone || '';
		qs('#editPhone2').value = listing.phone2 || '';
		qs('#editEmail').value = listing.email || '';
		
		// Set coordinates if available
		if (listing.latitude && listing.longitude) {
			qs('#editLatitude').value = listing.latitude;
			qs('#editLongitude').value = listing.longitude;
		}

		// Display photos
		const photoPreview = qs('#editPhotoPreview');
		photoPreview.innerHTML = '';
		if (listing.photos && listing.photos.length > 0) {
			listing.photos.forEach((photo, idx) => {
				const img = document.createElement('img');
				img.src = photo;
				img.alt = `Photo ${idx + 1}`;
				img.style.width = '100px';
				img.style.height = '100px';
				img.style.objectFit = 'cover';
				img.style.borderRadius = '8px';
				img.style.border = '2px solid var(--line)';
				photoPreview.appendChild(img);
			});
		} else {
			photoPreview.innerHTML = '<p style="color:var(--gray);font-size:13px;">No photos</p>';
		}

		myListingsModal.hidden = true;
		editListingModal.hidden = false;
		
		// Initialize edit map with existing coordinates
		setTimeout(() => {
			initEditMap(listing.latitude, listing.longitude);
		}, 100);
	}

	function openConfirmDialog(id, name) {
		currentDeleteId = id;
		confirmPetName.textContent = name;
		myListingsModal.hidden = true;
		confirmDialog.hidden = false;
	}

	async function deleteListing(id) {
		try {
			const formData = new FormData();
			formData.append('report_id', id);
			
			const response = await fetch('/PETVET/api/pet-owner/delete-report.php', {
				method: 'POST',
				body: formData
			});
			
			const result = await response.json();
			
			if (result.success) {
				// Remove from local array
				myListings = myListings.filter(l => l.id != id);
				renderMyListings();
				confirmDialog.hidden = true;
				myListingsModal.hidden = false;
				alert('Report deleted successfully');
				
				// Reload page to update main listing
				setTimeout(() => window.location.reload(), 1000);
			} else {
				alert('Error: ' + (result.message || 'Failed to delete report'));
				confirmDialog.hidden = true;
				myListingsModal.hidden = false;
			}
		} catch (error) {
			console.error('Error deleting report:', error);
			alert('An error occurred while deleting the report');
			confirmDialog.hidden = true;
			myListingsModal.hidden = false;
		}
	}

	// Handle photo preview
	if (photoInput && photoPreview) {
		photoInput.addEventListener('change', function() {
			photoPreview.innerHTML = '';
			let files = Array.from(this.files || []);
			
			const maxFiles = parseInt(this.getAttribute('data-max-files')) || 3;
			if (files.length > maxFiles) {
				alert(`You can only upload up to ${maxFiles} photos. Only the first ${maxFiles} will be used.`);
				files = files.slice(0, maxFiles);
			}
			
			if (files.length > 0) {
				photoPreview.style.display = 'flex';
				files.forEach((file, idx) => {
					const reader = new FileReader();
					reader.onload = function(e) {
						const img = document.createElement('img');
						img.src = e.target.result;
						img.alt = `Photo preview ${idx + 1}`;
						photoPreview.appendChild(img);
					};
					reader.readAsDataURL(file);
				});
			} else {
				photoPreview.style.display = 'none';
			}
		});
	}

	function setView(v){
		currentView = (v==='found') ? 'found':'lost';
		segBtns.forEach(b => {
			const isActive = b.getAttribute('data-view') === currentView;
			b.classList.toggle('is-active', isActive);
			b.setAttribute('aria-selected', isActive ? 'true':'false');
		});
		lostList.classList.toggle('hidden', currentView !== 'lost');
		foundList.classList.toggle('hidden', currentView !== 'found');
		const url = new URL(location.href);
		url.searchParams.set('view', currentView);
		history.replaceState({}, '', url);
		localStorage.setItem('lfView', currentView);
		applyFilters();
	}

	function applyFilters(){
		const q = (searchInput?.value || '').toLowerCase();
		const sp = speciesSel?.value || '';
		const sort = sortSel?.value || 'new';
		const container = currentView === 'lost' ? lostList : foundList;
		if(!container) return;
		const cards = container.querySelectorAll('.card');
		const list = Array.from(cards).map(card => {
			const hay = (card.textContent || '').toLowerCase();
			const species = card.getAttribute('data-species');
			const date = card.getAttribute('data-date') || '1970-01-01';
			const time = card.getAttribute('data-time') || '';
			const reportId = card.getAttribute('data-report-id');
			const matchQ = q === '' || hay.includes(q);
			const matchS = sp === '' || species === sp;
			const visible = matchQ && matchS;
			
			// Get distance for nearby sorting
			const view = currentView;
			const key = `${view}-${reportId}`;
			const distanceData = reportsWithDistance.get(key);
			const distance = distanceData ? distanceData.distance_km : 999999;
			
			// Create datetime for sorting
			const dateTimeStr = time ? `${date} ${time}` : date;
			const datetime = new Date(dateTimeStr).getTime() || 0;
			
			return { card, visible, date, distance, datetime };
		});
		list.forEach(({card, visible}) => { card.style.display = visible ? '' : 'none'; });
		const body = container;
		const vis = list.filter(x=>x.visible);
		
		// Sort based on selection
		if (sort === 'nearby') {
			// Sort by distance (nearest first)
			vis.sort((a,b) => a.distance - b.distance);
		} else if (sort === 'latest') {
			// Sort by datetime (latest first)
			vis.sort((a,b) => b.datetime - a.datetime);
		} else {
			// Sort by date only
			vis.sort((a,b)=>{ 
				const ta=Date.parse(a.date)||0; 
				const tb=Date.parse(b.date)||0; 
				return (sort==='old') ? (ta-tb):(tb-ta); 
			});
		}
		
		vis.forEach(x=> body.appendChild(x.card));
	}

	[searchInput,speciesSel,sortSel].forEach(ctrl => {
		ctrl && ctrl.addEventListener('input', applyFilters);
		ctrl && ctrl.addEventListener('change', applyFilters);
	});

	// Modal events
	openReportBtns.forEach(btn => btn.addEventListener('click', () => { 
		reportModal.hidden=false; 
		// Initialize map after modal is visible
		setTimeout(() => {
			initReportMap();
		}, 100);
	}));
	cancelReportBtn && cancelReportBtn.addEventListener('click', () => { closeReportModal(); });
	reportModal && reportModal.addEventListener('mousedown', e=>{ if(e.target===reportModal) closeReportModal(); });

	closeContactBtn && closeContactBtn.addEventListener('click', () => { contactModal.hidden = true; });
	contactModal && contactModal.addEventListener('mousedown', e=>{ if(e.target===contactModal) contactModal.hidden = true; });

	myListingsBtn && myListingsBtn.addEventListener('click', async () => { 
		myListingsModal.hidden = false;
		myListingsContent.innerHTML = '<p style="text-align:center;padding:32px;color:var(--gray);">Loading...</p>';
		await loadMyListings();
		renderMyListings();
	});
	closeMyListingsBtn && closeMyListingsBtn.addEventListener('click', () => { myListingsModal.hidden = true; });
	myListingsModal && myListingsModal.addEventListener('mousedown', e=>{ if(e.target===myListingsModal) myListingsModal.hidden = true; });

	cancelEditListingBtn && cancelEditListingBtn.addEventListener('click', () => { 
		editListingModal.hidden = true;
		myListingsModal.hidden = false;
	});
	editListingModal && editListingModal.addEventListener('mousedown', e=>{ 
		if(e.target===editListingModal) {
			editListingModal.hidden = true;
			myListingsModal.hidden = false;
		}
	});

	cancelConfirmBtn && cancelConfirmBtn.addEventListener('click', () => { 
		confirmDialog.hidden = true;
		myListingsModal.hidden = false;
	});
	confirmDeleteBtn && confirmDeleteBtn.addEventListener('click', () => { 
		if (currentDeleteId !== null) {
			deleteListing(currentDeleteId);
			currentDeleteId = null;
		}
	});
	confirmDialog && confirmDialog.addEventListener('mousedown', e=>{ 
		if(e.target===confirmDialog) {
			confirmDialog.hidden = true;
			myListingsModal.hidden = false;
		}
	});

	// Form submissions
	reportForm && reportForm.addEventListener('submit', async (e) => { 
		e.preventDefault();
		
		// Validate coordinates are selected
		const latitude = qs('#rLatitude').value;
		const longitude = qs('#rLongitude').value;
		
		if (!latitude || !longitude) {
			alert('Please select a location on the map.');
			return;
		}
		
		// Create FormData from form
		const formData = new FormData();
		formData.append('type', qs('#rType').value);
		formData.append('species', qs('#rSpecies').value);
		formData.append('name', qs('#rName').value || '');
		formData.append('color', qs('#rColor').value || '');
		formData.append('location', qs('#rLocation').value);
		formData.append('latitude', latitude);
		formData.append('longitude', longitude);
		formData.append('date', qs('#rDate').value);
		formData.append('time', qs('#rTime').value || '');
		formData.append('notes', qs('#rNotes').value || '');
		formData.append('phone', qs('#rPhone').value || '');
		formData.append('phone2', qs('#rPhone2').value || '');
		formData.append('email', qs('#rEmail').value || '');
		
		// Append multiple photos
		if (photoInput.files.length > 0) {
			Array.from(photoInput.files).forEach((file, index) => {
				formData.append('photos[]', file);
			});
		}
		
		try {
			// Show loading indicator
			const submitBtn = reportForm.querySelector('button[type="submit"]');
			const originalText = submitBtn.textContent;
			submitBtn.disabled = true;
			submitBtn.textContent = 'Submitting...';
			
			// Submit to API
			const response = await fetch('/PETVET/api/pet-owner/submit-report.php', {
				method: 'POST',
				body: formData
			});
			
			const result = await response.json();
			
			// Restore button
			submitBtn.disabled = false;
			submitBtn.textContent = originalText;
			
			if (result.success) {
				alert('Report submitted successfully! Thank you for helping the community.');
				closeReportModal();
				
				// Reload page to show new report
				window.location.reload();
			} else {
				alert('Error: ' + (result.message || 'Failed to submit report'));
			}
		} catch (error) {
			console.error('Error submitting report:', error);
			alert('An error occurred while submitting your report. Please try again.');
			
			// Re-enable button
			const submitBtn = reportForm.querySelector('button[type="submit"]');
			submitBtn.disabled = false;
			submitBtn.textContent = 'Submit Report';
		}
	});

	editListingForm && editListingForm.addEventListener('submit', async (e) => { 
		e.preventDefault();
		
		// Validate coordinates are selected
		const latitude = qs('#editLatitude').value;
		const longitude = qs('#editLongitude').value;
		
		if (!latitude || !longitude) {
			alert('Please select a location on the map.');
			return;
		}
		
		try {
			const reportId = qs('#editId').value;
			
			// Create FormData
			const formData = new FormData();
			formData.append('report_id', reportId);
			formData.append('type', qs('#editType').value);
			formData.append('species', qs('#editSpecies').value);
			formData.append('name', qs('#editName').value);
			formData.append('color', qs('#editColor').value);
			formData.append('location', qs('#editLocation').value);
			formData.append('latitude', latitude);
			formData.append('longitude', longitude);
			formData.append('date', qs('#editDate').value);
			formData.append('time', qs('#editTime').value || '');
			formData.append('notes', qs('#editNotes').value);
			formData.append('phone', qs('#editPhone').value);
			formData.append('phone2', qs('#editPhone2').value);
			formData.append('email', qs('#editEmail').value);
			
			// Check if new photos uploaded
			const photoInput = qs('#editPhoto');
			if (photoInput && photoInput.files.length > 0) {
				Array.from(photoInput.files).forEach((file) => {
					formData.append('photos[]', file);
				});
			}
			
			// Show loading
			const submitBtn = editListingForm.querySelector('button[type="submit"]');
			const originalText = submitBtn.textContent;
			submitBtn.disabled = true;
			submitBtn.textContent = 'Updating...';
			
			// Submit to API
			const response = await fetch('/PETVET/api/pet-owner/update-report.php', {
				method: 'POST',
				body: formData
			});
			
			const result = await response.json();
			
			// Restore button
			submitBtn.disabled = false;
			submitBtn.textContent = originalText;
			
			if (result.success) {
				alert('Listing updated successfully!');
				editListingModal.hidden = true;
				myListingsModal.hidden = false;
				
				// Reload listings and page
				await loadMyListings();
				renderMyListings();
				setTimeout(() => window.location.reload(), 1000);
			} else {
				alert('Error: ' + (result.message || 'Failed to update report'));
			}
		} catch (error) {
			console.error('Error updating report:', error);
			alert('An error occurred while updating the report');
			
			// Re-enable button
			const submitBtn = editListingForm.querySelector('button[type="submit"]');
			submitBtn.disabled = false;
			submitBtn.textContent = 'Update Listing';
		}
	});

	// Escape key handling
	document.addEventListener('keydown', e=>{ 
		if(e.key==='Escape') {
			if (!reportModal.hidden) closeReportModal();
			else if (!contactModal.hidden) contactModal.hidden = true;
			else if (!myListingsModal.hidden) myListingsModal.hidden = true;
			else if (!editListingModal.hidden) {
				editListingModal.hidden = true;
				myListingsModal.hidden = false;
			}
			else if (!confirmDialog.hidden) {
				confirmDialog.hidden = true;
				myListingsModal.hidden = false;
			}
		}
	});

	function closeReportModal(){ 
		reportModal.hidden=true; 
		reportForm && reportForm.reset(); 
		if (photoPreview) {
			photoPreview.style.display = 'none';
			photoPreview.innerHTML = '';
		}
		// Clear map and marker
		if (reportMarker) {
			reportMarker = null;
		}
		if (reportMap) {
			reportMap.remove();
			reportMap = null;
		}
		// Clear coordinate fields
		qs('#rLatitude').value = '';
		qs('#rLongitude').value = '';
		qs('#rLocation').value = '';
	}

	// Initialize
	setupCarousels();
	setupContactButtons();
	applyFilters();
	
	// Initialize distance calculation
	initDistanceCalculation();
	
	// Initialize relative time display
	updateAllRelativeTimes();
});

// Distance calculation functionality
function initDistanceCalculation() {
	// Check if geolocation is supported
	if (!navigator.geolocation) {
		console.warn('Geolocation not supported');
		hideAllDistanceLoaders();
		return;
	}

	// Request user location
	const options = {
		enableHighAccuracy: true,
		timeout: 10000,
		maximumAge: 300000 // Cache for 5 minutes
	};

	navigator.geolocation.getCurrentPosition(
		onLocationSuccess,
		onLocationError,
		options
	);
}

async function onLocationSuccess(position) {
	userLocation = {
		latitude: position.coords.latitude,
		longitude: position.coords.longitude
	};

	// Load reports with distance from API
	await loadReportsWithDistance();
}

function onLocationError(error) {
	console.warn('Location error:', error.message);
	hideAllDistanceLoaders();
}

async function loadReportsWithDistance() {
	try {
		// Load lost reports
		const lostUrl = `/PETVET/api/pet-owner/get-reports-by-distance.php?latitude=${userLocation.latitude}&longitude=${userLocation.longitude}&type=lost`;
		const lostResponse = await fetch(lostUrl);
		const lostData = await lostResponse.json();

		if (lostData.success && lostData.reports) {
			lostData.reports.forEach(report => {
				reportsWithDistance.set(`lost-${report.id}`, {
					distance_km: report.distance_km,
					distance_formatted: report.distance_formatted,
					duration_formatted: report.duration_formatted
				});
			});
		}

		// Load found reports
		const foundUrl = `/PETVET/api/pet-owner/get-reports-by-distance.php?latitude=${userLocation.latitude}&longitude=${userLocation.longitude}&type=found`;
		const foundResponse = await fetch(foundUrl);
		const foundData = await foundResponse.json();

		if (foundData.success && foundData.reports) {
			foundData.reports.forEach(report => {
				reportsWithDistance.set(`found-${report.id}`, {
					distance_km: report.distance_km,
					distance_formatted: report.distance_formatted,
					duration_formatted: report.duration_formatted
				});
			});
		}

		// Update distance badges
		updateDistanceBadges();
		
		// Re-apply filters with distance data
		applyFilters();
	} catch (error) {
		console.error('Error loading reports by distance:', error);
		hideAllDistanceLoaders();
	}
}

function updateDistanceBadges() {
	document.querySelectorAll('.report-distance').forEach(el => {
		const reportId = el.getAttribute('data-report-id');
		const view = el.closest('#lostList') ? 'lost' : 'found';
		const key = `${view}-${reportId}`;
		
		const distanceData = reportsWithDistance.get(key);
		
		if (distanceData && distanceData.distance_formatted !== 'Unknown') {
			el.innerHTML = `
				<span style="color: var(--primary, #4a90e2); font-size: 13px; display: inline-flex; align-items: center; gap: 4px;">
					<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
						<circle cx="12" cy="10" r="3"></circle>
					</svg>
					${distanceData.distance_formatted} away
				</span>
			`;
		} else {
			el.innerHTML = ''; // Hide if no distance data
		}
	});
}

function hideAllDistanceLoaders() {
	document.querySelectorAll('.report-distance').forEach(el => {
		if (el.querySelector('.distance-loader')) {
			el.innerHTML = '';
		}
	});
}

// Update all relative time displays
function updateAllRelativeTimes() {
	document.querySelectorAll('.time-ago').forEach(el => {
		const date = el.getAttribute('data-date');
		const time = el.getAttribute('data-time');
		const relativeTime = getRelativeTime(date, time);
		
		if (relativeTime) {
			el.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 4px;">
				<circle cx="12" cy="12" r="10"></circle>
				<polyline points="12 6 12 12 16 14"></polyline>
			</svg>${relativeTime}`;
		}
	});
	
	// Update every minute
	setTimeout(updateAllRelativeTimes, 60000);
}
