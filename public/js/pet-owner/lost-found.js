// Lost & Found interactions - Enhanced version
document.addEventListener('DOMContentLoaded', () => {
	const qs = s => document.querySelector(s);
	const qsa = s => document.querySelectorAll(s);

	const segBtns = qsa('.seg-btn');
	const lostList  = qs('#lostList');
	const foundList = qs('#foundList');
	const searchInput = qs('#q');
	const speciesSel  = qs('#species');
	const sortSel     = qs('#sortBy');

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
	function loadMyListings() {
		// Demo data - in production, fetch from server
		const stored = localStorage.getItem('myLostFoundListings');
		const version = localStorage.getItem('myLostFoundListingsVersion');
		
		// Clear old data if version doesn't match
		if (stored && version !== '3.0') {
			localStorage.removeItem('myLostFoundListings');
			localStorage.setItem('myLostFoundListingsVersion', '3.0');
		}
		
		if (stored && version === '3.0') {
			myListings = JSON.parse(stored);
		} else {
			// Initialize with two demo reports with multiple photos
			myListings = [
				{
					type: 'lost',
					species: 'dog',
					name: 'Max',
					color: 'Golden Brown',
					location: 'Central Park, Downtown',
					date: '2025-10-18',
					notes: 'Very friendly golden retriever. Has a blue collar with name tag. Last seen near the fountain.',
					phone: '+94 77 123 4567',
					phone2: '+94 77 123 4568',
					email: 'owner@example.com',
					photos: [
						'https://images.pexels.com/photos/356378/pexels-photo-356378.jpeg?auto=format%2Ccompress&cs=tinysrgb&dpr=1&w=500',
						'https://images.pexels.com/photos/1805164/pexels-photo-1805164.jpeg?auto=format%2Ccompress&cs=tinysrgb&dpr=1&w=500',
						'https://images.pexels.com/photos/2253275/pexels-photo-2253275.jpeg?auto=format%2Ccompress&cs=tinysrgb&dpr=1&w=500'
					]
				},
				{
					type: 'found',
					species: 'cat',
					name: 'Unknown',
					color: 'Gray and White',
					location: 'Maple Street, Suburb',
					date: '2025-10-19',
					notes: 'Found this cat wandering near my house. Well-groomed, seems to be someone\'s pet. Very calm and friendly.',
					phone: '+94 76 555 1212',
					phone2: '',
					email: 'finder@example.com',
					photos: [
						'https://images.unsplash.com/photo-1574158622682-e40e69881006?q=80&w=300&auto=format&fit=crop',
						'https://images.unsplash.com/photo-1573865526739-10c1dd7aa123?q=80&w=300&auto=format&fit=crop'
					]
				}
			];
			saveMyListings();
		}
	}

	function saveMyListings() {
		localStorage.setItem('myLostFoundListings', JSON.stringify(myListings));
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
		myListings.forEach((listing, idx) => {
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
					<button class="btn outline edit-listing-btn" data-id="${idx}">Edit</button>
					<button class="btn danger delete-listing-btn" data-id="${idx}" data-name="${listing.name || 'this pet'}">Delete</button>
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
		const listing = myListings[id];
		if (!listing) return;

		qs('#editId').value = id;
		qs('#editType').value = listing.type;
		qs('#editSpecies').value = listing.species;
		qs('#editName').value = listing.name || '';
		qs('#editColor').value = listing.color || '';
		qs('#editLocation').value = listing.location;
		qs('#editDate').value = listing.date;
		qs('#editNotes').value = listing.notes || '';
		qs('#editPhone').value = listing.phone || '';
		qs('#editPhone2').value = listing.phone2 || '';
		qs('#editEmail').value = listing.email || '';

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
	}

	function openConfirmDialog(id, name) {
		currentDeleteId = id;
		confirmPetName.textContent = name;
		myListingsModal.hidden = true;
		confirmDialog.hidden = false;
	}

	function deleteListing(id) {
		myListings.splice(id, 1);
		saveMyListings();
		renderMyListings();
		confirmDialog.hidden = true;
		myListingsModal.hidden = false;
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
			const matchQ = q === '' || hay.includes(q);
			const matchS = sp === '' || species === sp;
			const visible = matchQ && matchS;
			return { card, visible, date };
		});
		list.forEach(({card, visible}) => { card.style.display = visible ? '' : 'none'; });
		const body = container;
		const vis = list.filter(x=>x.visible);
		vis.sort((a,b)=>{ const ta=Date.parse(a.date)||0; const tb=Date.parse(b.date)||0; return (sort==='old') ? (ta-tb):(tb-ta); });
		vis.forEach(x=> body.appendChild(x.card));
	}

	[searchInput,speciesSel,sortSel].forEach(ctrl => {
		ctrl && ctrl.addEventListener('input', applyFilters);
		ctrl && ctrl.addEventListener('change', applyFilters);
	});

	// Modal events
	openReportBtns.forEach(btn => btn.addEventListener('click', () => { reportModal.hidden=false; }));
	cancelReportBtn && cancelReportBtn.addEventListener('click', () => { closeReportModal(); });
	reportModal && reportModal.addEventListener('mousedown', e=>{ if(e.target===reportModal) closeReportModal(); });

	closeContactBtn && closeContactBtn.addEventListener('click', () => { contactModal.hidden = true; });
	contactModal && contactModal.addEventListener('mousedown', e=>{ if(e.target===contactModal) contactModal.hidden = true; });

	myListingsBtn && myListingsBtn.addEventListener('click', () => { 
		loadMyListings();
		renderMyListings();
		myListingsModal.hidden = false;
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
	reportForm && reportForm.addEventListener('submit', e=>{ 
		e.preventDefault();
		const formData = new FormData(reportForm);
		const photos = [];
		if (photoInput.files.length > 0) {
			Array.from(photoInput.files).slice(0, 3).forEach(file => {
				photos.push(URL.createObjectURL(file));
			});
		}
		
		const newListing = {
			type: qs('#rType').value,
			species: qs('#rSpecies').value,
			name: qs('#rName').value,
			color: qs('#rColor').value,
			location: qs('#rLocation').value,
			date: qs('#rDate').value,
			notes: qs('#rNotes').value,
			phone: qs('#rPhone').value,
			phone2: qs('#rPhone2').value,
			email: qs('#rEmail').value,
			photos: photos
		};
		
		myListings.push(newListing);
		saveMyListings();
		
		alert('Report submitted successfully (demo mode)');
		closeReportModal();
	});

	editListingForm && editListingForm.addEventListener('submit', e=>{ 
		e.preventDefault();
		const id = parseInt(qs('#editId').value);
		
		myListings[id] = {
			...myListings[id],
			type: qs('#editType').value,
			species: qs('#editSpecies').value,
			name: qs('#editName').value,
			color: qs('#editColor').value,
			location: qs('#editLocation').value,
			date: qs('#editDate').value,
			notes: qs('#editNotes').value,
			phone: qs('#editPhone').value,
			phone2: qs('#editPhone2').value,
			email: qs('#editEmail').value
		};
		
		saveMyListings();
		renderMyListings();
		editListingModal.hidden = true;
		myListingsModal.hidden = false;
		alert('Listing updated successfully');
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
	}

	// Initialize
	setupCarousels();
	setupContactButtons();
	applyFilters();
});
