// Explore Pets - Enhanced with carousel, contact modal, and improvements
document.addEventListener('DOMContentLoaded', () => {
	const qs = (s, el=document) => el.querySelector(s);
	const qsa = (s, el=document) => Array.from(el.querySelectorAll(s));
	const money = n => 'Rs ' + Number(n).toLocaleString();

	// Modal helpers (updated to work with both old and new modal structures)
	function openModal(id){ 
		const m=qs('#'+id); 
		if(!m) return; 
		m.setAttribute('aria-hidden','false'); 
		m.classList.add('show');
		// For modal-overlay with hidden attribute
		if(m.hasAttribute('hidden')) {
			m.removeAttribute('hidden');
		}
	}
	function closeModal(id){ 
		const m=qs('#'+id); 
		if(!m) return; 
		m.setAttribute('aria-hidden','true'); 
		m.classList.remove('show');
		// For modal-overlay with hidden attribute
		if(m.classList.contains('modal-overlay')) {
			m.setAttribute('hidden', '');
		}
	}
	qsa('[data-close]').forEach(btn=>btn.addEventListener('click',e=>closeModal(e.currentTarget.getAttribute('data-close'))));
	qsa('.modal, .modal-overlay').forEach(m=>m.addEventListener('click',e=>{ if(e.target===m) closeModal(m.id); }));
	document.addEventListener('keydown', e=>{ if(e.key==='Escape'){ qsa('.modal.show, .modal-overlay[aria-hidden="false"]').forEach(m=>closeModal(m.id)); }});

	// Load and render My Listings from database
	async function fetchMyListings() {
		try {
			const response = await fetch('/PETVET/api/sell-pet-listings/get-my-listings.php');
			const data = await response.json();
			
			if (data.success) {
				renderMyListings(data.listings);
				return data.listings;
			} else {
				console.error('Failed to fetch listings:', data.message);
				renderMyListings([]);
				return [];
			}
		} catch (error) {
			console.error('Error fetching listings:', error);
			renderMyListings([]);
			return [];
		}
	}
	
	function loadMyListings() {
		fetchMyListings();
	}

	function renderMyListings(listings) {
		const container = qs('#myListingsContent');
		if (!container) return;
		
		if (listings.length === 0) {
			container.innerHTML = `
				<div class="empty-state">
					<h4>No listings yet</h4>
					<p>Click <strong>Sell a Pet</strong> to create your first listing.</p>
				</div>
			`;
			return;
		}
		
		container.innerHTML = listings.map(listing => {
			const mainImage = listing.images && listing.images.length > 0 ? listing.images[0] : '/PETVET/public/images/placeholder-pet.jpg';
			const statusBadge = listing.status === 'pending' ? '<span class="status-badge pending">Pending Approval</span>' :
			                     listing.status === 'approved' ? '<span class="status-badge approved">Approved</span>' :
			                     listing.status === 'rejected' ? '<span class="status-badge rejected">Rejected</span>' :
			                     '<span class="status-badge sold">Sold</span>';
			return `
				<div class="listing-item" data-id="${listing.id}">
					<img src="${mainImage}" alt="${listing.name}" class="listing-thumb">
					<div class="listing-details">
						<h4 class="listing-title">${listing.name}</h4>
						<div class="listing-meta">${listing.species} • ${listing.breed}</div>
						<div class="listing-meta small">${listing.age} • ${listing.gender}</div>
						<span class="listing-badge price">Rs ${Number(listing.price).toLocaleString()}</span>
						${statusBadge}
					</div>
					<div class="listing-actions">
						<button class="btn outline edit-listing-btn" 
							data-id="${listing.id}"
							data-listing='${JSON.stringify(listing).replace(/'/g, "&apos;")}'>Edit</button>
						<button class="btn danger delete-listing-btn" 
							data-id="${listing.id}"
							data-name="${listing.name}">Delete</button>
					</div>
				</div>
			`;
		}).join('');
	}

	// Buttons
	qs('#btnSellPet')?.addEventListener('click',()=>openModal('sellModal'));
	qs('#btnMyListings')?.addEventListener('click', async ()=>{
		await fetchMyListings();
		openModal('myListingsModal');
	});
	
	// Cancel buttons for new modal structure
	qs('#cancelSell')?.addEventListener('click', () => closeModal('sellModal'));
	qs('#closeMyListings')?.addEventListener('click', () => closeModal('myListingsModal'));
	qs('#cancelEdit')?.addEventListener('click', () => closeModal('editListingModal'));
	qs('#closeEditModal')?.addEventListener('click', () => closeModal('editListingModal'));

	// Contact Modal
	const contactModal = qs('#contactModal');
	const contactContent = qs('#contactContent');
	const closeContactBtn = qs('#closeContact');
	
	closeContactBtn?.addEventListener('click', () => { contactModal.style.display = 'none'; });
	contactModal?.addEventListener('click', e => { if(e.target === contactModal) contactModal.style.display = 'none'; });

	// Confirm Dialog
	const confirmDialog = qs('#confirmDialog');
	const confirmPetName = qs('#confirmPetName');
	const cancelConfirmBtn = qs('#cancelConfirm');
	const confirmDeleteBtn = qs('#confirmDelete');
	let currentDeleteId = null;

	cancelConfirmBtn?.addEventListener('click', () => { confirmDialog.style.display = 'none'; });
	confirmDialog?.addEventListener('click', e => { if(e.target === confirmDialog) confirmDialog.style.display = 'none'; });
	
	confirmDeleteBtn?.addEventListener('click', async () => {
		if(currentDeleteId !== null){
			const submitBtn = confirmDeleteBtn;
			const originalText = submitBtn.textContent;
			submitBtn.textContent = 'Deleting...';
			submitBtn.disabled = true;
			
			try {
				const fd = new FormData();
				fd.append('id', currentDeleteId);
				
				const response = await fetch('/PETVET/api/sell-pet-listings/delete.php', {
					method: 'POST',
					body: fd
				});
				
				const data = await response.json();
				
				if (data.success) {
					alert(data.message || 'Listing deleted successfully');
					confirmDialog.style.display = 'none';
					currentDeleteId = null;
					await fetchMyListings(); // Refresh the list
				} else {
					alert(data.message || 'Failed to delete listing');
				}
			} catch (error) {
				console.error('Error deleting listing:', error);
				alert('An error occurred while deleting the listing');
			} finally {
				submitBtn.textContent = originalText;
				submitBtn.disabled = false;
			}
		}
	});

	// Image Carousel functionality
	function setupCarousels() {
		const cards = qsa('.card');
		cards.forEach(card => {
			const media = card.querySelector('.media');
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

	// Contact Seller functionality
	function setupContactButtons() {
		const contactBtns = qsa('.contact-seller-btn');
		contactBtns.forEach(btn => {
			btn.addEventListener('click', (e) => {
				e.preventDefault();
				e.stopPropagation();
				
				const name = btn.getAttribute('data-name');
				const phone = btn.getAttribute('data-phone');
				const phone2 = btn.getAttribute('data-phone2');
				const email = btn.getAttribute('data-email');

				contactContent.innerHTML = '';

				// Phone 1
				if (phone && phone.trim()) {
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
				if (email && email.trim()) {
					const item = document.createElement('div');
					item.className = 'contact-item';
					item.innerHTML = `
						<div class="contact-info">
							<div class="contact-label">Email Address</div>
							<div class="contact-value">${email}</div>
						</div>
						<a href="mailto:${email}?subject=Regarding ${name} pet listing" class="btn outline">Email</a>
					`;
					contactContent.appendChild(item);
				}

				// If no contact info
				if (!contactContent.innerHTML) {
					contactContent.innerHTML = '<p style="color:var(--muted);text-align:center;padding:20px;">No contact information available</p>';
				}

				contactModal.style.display = 'flex';
			});
		});
	}

	// Filters
	const searchInput = qs('#searchInput');
	const speciesFilter = qs('#speciesFilter');
	const sortBy = qs('#sortBy');
	const grid = qs('#listingsGrid');

	function applyFilters(){
		if(!grid) return;
		const term = (searchInput?.value||'').trim().toLowerCase();
		const species = speciesFilter?.value || '';
		let cards = qsa('.card', grid);
		cards.forEach(c => {
			const text = c.innerText.toLowerCase();
			const matchTerm = !term || text.includes(term);
			const matchSpecies = !species || c.dataset.species === species;
			c.style.display = (matchTerm && matchSpecies) ? '' : 'none';
		});
		cards = qsa('.card', grid).filter(c=>c.style.display!=='none');
		const mode = sortBy?.value;
		cards.sort((a,b)=>{
			const pa = +a.dataset.price, pb = +b.dataset.price;
			if(mode==='priceLow') return pa - pb;
			if(mode==='priceHigh') return pb - pa;
			if(mode==='age') return a.innerText.indexOf('1y')>-1 ? -1 : 1;
			return 0;
		});
		cards.forEach(c=>grid.appendChild(c));
		
		// Re-setup carousels and contact buttons after DOM changes
		setupCarousels();
		setupContactButtons();
	}
	[searchInput,speciesFilter,sortBy].forEach(el=> el?.addEventListener('input', applyFilters));

	// Grid events
	grid?.addEventListener('click', e => {
		const card = e.target.closest('.card');
		if(!card) return;
		if(e.target.classList.contains('view')){
			showDetails(card);
		}
	});

	function showDetails(card){
		const title = card.querySelector('h3')?.textContent || 'Pet';
		const price = card.dataset.price || 0;
		const img = card.querySelector('img')?.src || '';
		const meta = card.querySelector('.meta')?.textContent || '';
		const desc = card.querySelector('.desc')?.textContent || '';
		const sellerName = card.querySelector('.seller-name strong')?.textContent || 'Seller';
		const sellerLoc = card.querySelector('.seller-loc')?.textContent || '';
		
		let contactDetails = '';
		if (sellerName === 'You' || sellerName === window.EXPLORE_CURRENT_USER_NAME) {
			contactDetails = '<div class="seller-lg"><strong>Contact:</strong> 077-123-4567<br>Email: you@example.com</div>';
		} else if (sellerName === 'Kasun Perera') {
			contactDetails = '<div class="seller-lg"><strong>Contact:</strong> 077-987-6543<br>Email: kasun.perera@petvet.lk</div>';
		} else if (sellerName === 'Nirmala') {
			contactDetails = '<div class="seller-lg"><strong>Contact:</strong> 076-555-1212<br>Email: nirmala@example.com</div>';
		} else {
			contactDetails = '<div class="seller-lg"><strong>Contact:</strong> Not available</div>';
		}
		
		qs('#detailsTitle').textContent = title;
		qs('#detailsBody').innerHTML = `
			<div class="details details-modal-custom">
				<div class="details-img-wrap"><img src="${img}" alt="${title}"></div>
				<div class="details-info">
					<div class="price-lg highlight">${money(price)}</div>
					<div class="meta-lg"><span>${meta}</span></div>
					<p class="desc-lg">${desc}</p>
					<div class="seller-lg">
						<div class="seller-row"><span class="seller-label">Posted by</span><span class="seller-name"><strong>${sellerName}</strong></span><span class="seller-loc">${sellerLoc}</span></div>
						<div class="contact-row"><span class="contact-label">Contact</span><span class="contact-details">${contactDetails}</span></div>
					</div>
				</div>
			</div>`;
		openModal('detailsModal');
	}

	// Sell form
	const sellForm = qs('#sellForm');
	const sellImagesInput = qs('#sellImages');
	const sellImagePreviews = qs('#sellImagePreviews');
	const sellImageFallback = qs('#sellImageFallback');

	if (sellImagesInput && sellImagePreviews) {
		sellImagesInput.addEventListener('change', () => {
			sellImagePreviews.innerHTML = '';
			let files = Array.from(sellImagesInput.files || []);
			
			const maxFiles = parseInt(sellImagesInput.getAttribute('data-max-files')) || 3;
			if (files.length > maxFiles) {
				alert(`You can only upload up to ${maxFiles} images. Only the first ${maxFiles} will be used.`);
				files = files.slice(0, maxFiles);
			}
			
			if (files.length === 0) { sellImagePreviews.setAttribute('aria-hidden','true'); sellImageFallback.value = ''; return; }
			sellImagePreviews.removeAttribute('aria-hidden');
			files.forEach((f, idx) => {
				const url = URL.createObjectURL(f);
				const img = document.createElement('img');
				img.src = url; img.alt = f.name; img.style.width = '84px'; img.style.height = '84px'; img.style.objectFit = 'cover'; img.style.borderRadius = '10px'; img.style.marginRight = '8px';
				sellImagePreviews.appendChild(img);
				img.onload = () => URL.revokeObjectURL(url);
			});
			if (files[0]) {
				const firstUrl = URL.createObjectURL(files[0]);
				sellImageFallback.value = firstUrl;
				setTimeout(() => URL.revokeObjectURL(firstUrl), 2000);
			}
		});
	}

	sellForm?.addEventListener('submit', async e => {
		e.preventDefault();
		const fd = new FormData(sellForm);
		
		// Show loading state
		const submitBtn = sellForm.querySelector('button[type="submit"]');
		const originalText = submitBtn.textContent;
		submitBtn.textContent = 'Publishing...';
		submitBtn.disabled = true;
		
		try {
			const response = await fetch('/PETVET/api/sell-pet-listings/add.php', {
				method: 'POST',
				body: fd
			});
			
			const data = await response.json();
			
			if (data.success) {
				alert(data.message || 'Listing published successfully! It will be visible after admin approval.');
				closeModal('sellModal');
				sellForm.reset();
				sellImagePreviews.innerHTML = '';
				sellImagePreviews.setAttribute('aria-hidden','true');
				
				// Reload my listings
				await fetchMyListings();
			} else {
				// Show the actual error message from the server
				const errorMsg = data.message || 'Failed to publish listing';
				const debugInfo = data.error ? `\n\nDebug: ${data.error}` : '';
				alert(errorMsg + debugInfo);
				console.error('Server error:', data);
			}
		} catch (error) {
			console.error('Error publishing listing:', error);
			alert('An error occurred while publishing the listing. Please check the console for details.');
		} finally {
			submitBtn.textContent = originalText;
			submitBtn.disabled = false;
		}
	});
	
	function addListingToDisplay(fd, badges, species, price, phone, phone2, email, imageUrls) {
		const card = document.createElement('article');
		card.className='card'; card.dataset.species=species; card.dataset.price=price;
		
		// Build carousel HTML if multiple images
		let mediaHTML = '';
		if(imageUrls.length > 1){
			mediaHTML = `<div class="media">`;
			imageUrls.forEach((url, idx) => {
				mediaHTML += `<img src="${url}" alt="Photo ${idx+1}" class="carousel-image" ${idx > 0 ? 'style="display:none;"' : ''} data-index="${idx}">`;
			});
			mediaHTML += `
				<button class="carousel-nav prev" data-direction="prev"></button>
				<button class="carousel-nav next" data-direction="next"></button>
				<div class="carousel-indicators">`;
			imageUrls.forEach((url, idx) => {
				mediaHTML += `<button class="carousel-indicator ${idx === 0 ? 'active' : ''}" data-index="${idx}"></button>`;
			});
			mediaHTML += `</div><span class="price">${money(price)}</span></div>`;
		} else {
			mediaHTML = `<div class="media"><img src="${imageUrls[0] || ''}" alt="${fd.get('name')}" class="carousel-image"><span class="price">${money(price)}</span></div>`;
		}
		
		card.innerHTML = mediaHTML + `
			<div class="body"><div class="line1"><h3>${fd.get('name')}</h3><span class="meta">${species} • ${fd.get('breed')} • ${fd.get('age')}</span></div>
				<p class="desc">${fd.get('desc')}</p>
				<div class="badges">${badges.map(b=>`<span class="badge">${b}</span>`).join('')}</div>
				<div class="seller"><span class="seller-name">Posted by <strong>${window.EXPLORE_CURRENT_USER_NAME||'You'}</strong></span><span class="seller-loc">Colombo</span></div>
			</div>
			<div class="actions-row">
				<button class="btn ghost view">View Details</button>
				<button class="btn buy contact-seller-btn" 
					data-name="${window.EXPLORE_CURRENT_USER_NAME||'You'}"
					data-phone="${phone}"
					data-phone2="${phone2}"
					data-email="${email}">
					Contact Seller
				</button>
			</div>`;
		
		grid?.prepend(card);
		closeModal('sellModal');
		sellForm.reset();
		sellImagePreviews.innerHTML = '';
		sellImagePreviews.setAttribute('aria-hidden','true');
		
		// Setup carousel and contact for new card
		setupCarousels();
		setupContactButtons();
		
		alert('Listing published successfully!');
	}

	// My Listings event delegation (edit/delete buttons)
	const myListingsContent = qs('#myListingsContent');
	myListingsContent?.addEventListener('click', e => {
		// Handle Delete button
		if(e.target.classList.contains('delete-listing-btn')){
			const id = e.target.dataset.id;
			const name = e.target.dataset.name;
			
			currentDeleteId = id;
			confirmPetName.textContent = name;
			confirmDialog.style.display = 'flex';
		}
		
		// Handle Edit button
		if(e.target.classList.contains('edit-listing-btn')){
			const btn = e.target;
			const listing = JSON.parse(btn.dataset.listing.replace(/&apos;/g, "'"));
			
			const editForm = qs('#editForm');
			if(!editForm) return;
			
			// Fill form fields
			editForm.querySelector('[name="id"]').value = listing.id;
			editForm.querySelector('[name="name"]').value = listing.name;
			editForm.querySelector('[name="species"]').value = listing.species;
			editForm.querySelector('[name="breed"]').value = listing.breed;
			editForm.querySelector('[name="age"]').value = listing.age;
			editForm.querySelector('[name="gender"]').value = listing.gender;
			editForm.querySelector('[name="price"]').value = listing.price;
			editForm.querySelector('[name="desc"]').value = listing.desc || '';
			editForm.querySelector('[name="location"]').value = listing.location;
			editForm.querySelector('[name="phone"]').value = listing.phone || '';
			editForm.querySelector('[name="phone2"]').value = listing.phone2 || '';
			editForm.querySelector('[name="email"]').value = listing.email || '';
			
			// Show existing photos
			const existingPhotosDiv = qs('#editExistingPhotos');
			const existingImagesInput = qs('#existingImages');
			
			if (existingPhotosDiv && listing.images && listing.images.length > 0) {
				existingPhotosDiv.innerHTML = listing.images.map((img, idx) => `
					<div class="photo-preview-item" style="position:relative;display:inline-block;margin:4px;">
						<img src="${img}" alt="Photo ${idx+1}" style="width:100px;height:100px;object-fit:cover;border-radius:8px;border:2px solid var(--primary);">
						<button type="button" class="remove-photo-btn" data-index="${idx}">×</button>
					</div>
				`).join('');
				
				existingImagesInput.value = JSON.stringify(listing.images);
				
				// Handle photo removal
				existingPhotosDiv.querySelectorAll('.remove-photo-btn').forEach(btn => {
					btn.addEventListener('click', () => {
						const index = parseInt(btn.dataset.index);
						const currentImages = JSON.parse(existingImagesInput.value);
						currentImages.splice(index, 1);
						existingImagesInput.value = JSON.stringify(currentImages);
						btn.closest('.photo-preview-item').remove();
					});
				});
			} else if (existingPhotosDiv) {
				existingPhotosDiv.innerHTML = '<p style="color:var(--muted);">No photos yet</p>';
				existingImagesInput.value = '[]';
			}
			
			// Clear new photo previews
			qs('#editImagePreviews').innerHTML = '';
			qs('#editImages').value = '';
			
			closeModal('myListingsModal');
			openModal('editListingModal');
		}
	});

	// Edit form submission
	const editForm = qs('#editForm');
	editForm?.addEventListener('submit', async e => {
		e.preventDefault();
		const fd = new FormData(editForm);
		
		// Show loading state
		const submitBtn = editForm.querySelector('button[type="submit"]');
		const originalText = submitBtn.textContent;
		submitBtn.textContent = 'Saving...';
		submitBtn.disabled = true;
		
		try {
			const response = await fetch('/PETVET/api/sell-pet-listings/update.php', {
				method: 'POST',
				body: fd
			});
			
			const data = await response.json();
			
			if (data.success) {
				alert(data.message || 'Listing updated successfully!');
				closeModal('editListingModal');
				openModal('myListingsModal');
				await fetchMyListings(); // Refresh the display
			} else {
				alert(data.message || 'Failed to update listing');
			}
		} catch (error) {
			console.error('Error updating listing:', error);
			alert('An error occurred while updating the listing');
		} finally {
			submitBtn.textContent = originalText;
			submitBtn.disabled = false;
		}
	});

	// Initialize carousels and contact buttons on page load
	setupCarousels();
	setupContactButtons();
	
	// Load My Listings data on page load (prepares localStorage)
	loadMyListings();
});
