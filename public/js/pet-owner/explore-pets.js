// Explore Pets interactions (prototype migration)
document.addEventListener('DOMContentLoaded', () => {
	const qs = (s, el=document) => el.querySelector(s);
	const qsa = (s, el=document) => Array.from(el.querySelectorAll(s));
	const money = n => 'Rs ' + Number(n).toLocaleString();

	// Modal helpers
	function openModal(id){ const m=qs('#'+id); if(!m) return; m.setAttribute('aria-hidden','false'); m.classList.add('show'); }
	function closeModal(id){ const m=qs('#'+id); if(!m) return; m.setAttribute('aria-hidden','true'); m.classList.remove('show'); }
	qsa('[data-close]').forEach(btn=>btn.addEventListener('click',e=>closeModal(e.currentTarget.getAttribute('data-close'))));
	qsa('.modal').forEach(m=>m.addEventListener('click',e=>{ if(e.target===m) closeModal(m.id); }));
	document.addEventListener('keydown', e=>{ if(e.key==='Escape'){ qsa('.modal.show').forEach(m=>closeModal(m.id)); }});

	// Buttons
	qs('#btnSellPet')?.addEventListener('click',()=>openModal('sellModal'));
	qs('#btnMyListings')?.addEventListener('click',()=>openModal('myListingsModal'));

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
			if(mode==='age') return a.innerText.indexOf('1y')>-1 ? -1 : 1; // light demo heuristic
			return 0; // newest retains DOM order
		});
		cards.forEach(c=>grid.appendChild(c));
	}
	[searchInput,speciesFilter,sortBy].forEach(el=> el?.addEventListener('input', applyFilters));

	// Grid events
	grid?.addEventListener('click', e => {
		const card = e.target.closest('.card');
		if(!card) return;
		if(e.target.classList.contains('call')){
			const sellerName = card.querySelector('.seller-name strong')?.textContent || 'Seller';
			const sellerLoc = card.querySelector('.seller-loc')?.textContent || '';
			alert('Call seller: ' + sellerName + ' (' + sellerLoc + ')');
		}
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

	// Render previews for selected images (demo uses object URLs)
	if (sellImagesInput && sellImagePreviews) {
		sellImagesInput.addEventListener('change', () => {
			// clear
			sellImagePreviews.innerHTML = '';
			const files = Array.from(sellImagesInput.files || []);
			if (files.length === 0) { sellImagePreviews.setAttribute('aria-hidden','true'); sellImageFallback.value = ''; return; }
			sellImagePreviews.removeAttribute('aria-hidden');
			files.forEach((f, idx) => {
				const url = URL.createObjectURL(f);
				const img = document.createElement('img');
				img.src = url; img.alt = f.name; img.style.width = '84px'; img.style.height = '84px'; img.style.objectFit = 'cover'; img.style.borderRadius = '10px'; img.style.marginRight = '8px';
				sellImagePreviews.appendChild(img);
				// revoke object URL after image loads to free memory
				img.onload = () => URL.revokeObjectURL(url);
			});
			// populate fallback with first file's object URL (demo-only)
			if (files[0]) {
				const firstUrl = URL.createObjectURL(files[0]);
				sellImageFallback.value = firstUrl;
				// revoke after short delay (we still use it immediately to create the card)
				setTimeout(() => URL.revokeObjectURL(firstUrl), 2000);
			}
		});
	}
		sellForm?.addEventListener('submit', e => {
		e.preventDefault();
		const fd = new FormData(sellForm);
		const badges = fd.getAll('badges[]');
		const species = fd.get('species');
		const price = fd.get('price');
			// Build image list (demo uses object URLs for local preview & card image)
			let imageUrls = [];
			const files = Array.from(sellImagesInput?.files || []);
			if (files.length) {
				imageUrls = files.map(f => URL.createObjectURL(f));
			} else if (fd.get('image')) {
				imageUrls = [fd.get('image')];
			}

			const card = document.createElement('article');
		card.className='card'; card.dataset.species=species; card.dataset.price=price;
			card.innerHTML = `
				<div class="media"><img src="${imageUrls[0] || ''}" alt="${fd.get('name')}"><span class="price">${money(price)}</span></div>
			<div class="body"><div class="line1"><h3>${fd.get('name')}</h3><span class="meta">${species} • ${fd.get('breed')} • ${fd.get('age')}</span></div>
				<p class="desc">${fd.get('desc')}</p>
				<div class="badges">${badges.map(b=>`<span class="badge">${b}</span>`).join('')}</div>
				<div class="seller"><span class="seller-name">Posted by <strong>${window.EXPLORE_CURRENT_USER_NAME||'You'}</strong></span><span class="seller-loc">Colombo</span></div>
			</div>
			<div class="actions-row"><button class="btn ghost view">View Details</button><button class="btn buy call">Call</button></div>`;

			// attach images array to card element for potential later use (demo only)
			try { card.dataset.images = JSON.stringify(imageUrls); } catch(e) { card.dataset.images = '[]'; }
		grid?.prepend(card);
			closeModal('sellModal'); sellForm.reset(); if (sellImagePreviews) sellImagePreviews.innerHTML = ''; applyFilters(); alert('Listing published (demo only).');
	});

	// My listings actions
	const myWrap = qs('#myListingsWrap');
	// My Listings -> Edit & Remove handlers (Edit opens editListingModal and pre-fills the form)
	const editForm = qs('#editForm');
	const myModal = qs('#myListingsModal');

	if (myModal && editForm) {
		myModal.addEventListener('click', (e) => {
			const editBtn = e.target.closest('.edit');
			if (editBtn) {
				const ds = editBtn.dataset;
				editForm.elements['id'].value = ds.id || '';
				editForm.elements['name'].value = ds.name || '';
				editForm.elements['species'].value = ds.species || 'Dog';
				editForm.elements['breed'].value = ds.breed || '';
				editForm.elements['age'].value = ds.age || '';
				editForm.elements['gender'].value = ds.gender || 'Male';
				editForm.elements['price'].value = ds.price || 0;
				editForm.elements['desc'].value = ds.desc || '';
				editForm.elements['location'].value = ds.location || '';
				// Prefill images: read dataset.images (JSON) or fallback to single image src from the listing row
				const row = editBtn.closest('.listing-row');
				const existingInput = qs('#existingImages');
				const editPreviews = qs('#editImagePreviews');
				if (existingInput && editPreviews) {
					let imgs = [];
					try {
						imgs = JSON.parse(editBtn.dataset.images || '[]');
					} catch (e) { imgs = []; }
					// If not present in dataset, try reading the row image
					if (!imgs.length && row) {
						const imgEl = row.querySelector('img');
						if (imgEl && imgEl.src) imgs = [imgEl.src];
					}
					existingInput.value = JSON.stringify(imgs);
					// render previews
					editPreviews.innerHTML = '';
					if (imgs.length) {
						editPreviews.removeAttribute('aria-hidden');
						imgs.forEach((u, i) => {
							const wrap = document.createElement('div');
							wrap.className = 'preview-item';
							wrap.innerHTML = `<img src="${u}" alt="photo-${i}"><button class="btn sm outline remove-photo" data-index="${i}" title="Remove">Remove</button>`;
							editPreviews.appendChild(wrap);
						});
					} else {
						editPreviews.setAttribute('aria-hidden','true');
					}
				}
				openModal('editListingModal');
				return;
			}

			const removeBtn = e.target.closest('.remove');
			if (removeBtn) {
				const row = removeBtn.closest('.listing-row'); if (row) row.remove();
			}
		});

		// Save changes in the Edit form into the My Listings table (UI only)
		editForm.addEventListener('submit', (ev) => {
			ev.preventDefault();
			const id = editForm.elements['id'].value;
			const name = editForm.elements['name'].value.trim();
			const species = editForm.elements['species'].value.trim();
			const breed = editForm.elements['breed'].value.trim();
			const age = editForm.elements['age'].value.trim();
			const gender = editForm.elements['gender'].value.trim();
			const price = Number(editForm.elements['price'].value || 0);
			const desc = editForm.elements['desc'].value.trim();
			const location = editForm.elements['location'].value.trim();
			// Images handling: merge existingImages (JSON) and any newly selected files from #editImages
			const existingInput = qs('#existingImages');
			const editImagesInput = qs('#editImages');
			let finalImgs = [];
			try { finalImgs = existingInput && existingInput.value ? JSON.parse(existingInput.value) : []; } catch(e){ finalImgs = []; }
			if (editImagesInput && editImagesInput.files && editImagesInput.files.length) {
				const files = Array.from(editImagesInput.files);
				// use object URLs for demo preview and storage in dataset
				const urls = files.map(f => URL.createObjectURL(f));
				finalImgs = finalImgs.concat(urls);
			}

			const row = myModal.querySelector(`.listing-row[data-id="${CSS.escape(id)}"]`);
			if (row) {
				const infoTitle = qs('h4', row);
				const infoLine = qs('p', row);
				const infoMuted = qs('.muted', row);
				const editBtn = qs('.listing-actions .edit', row);
				const thumb = row.querySelector('img');

				if (infoTitle) infoTitle.textContent = name || infoTitle.textContent;
				if (infoLine) infoLine.textContent = `${species} • Rs ${price.toLocaleString('en-LK')}`;
				if (infoMuted) infoMuted.textContent = `${breed} • ${age} • ${gender} • ${location}`;

				if (editBtn) {
					editBtn.dataset.name = name;
					editBtn.dataset.species = species;
					editBtn.dataset.breed = breed;
					editBtn.dataset.age = age;
					editBtn.dataset.gender = gender;
					editBtn.dataset.price = String(price);
					editBtn.dataset.desc = desc;
					editBtn.dataset.location = location;
					// attach images back to the edit button so future edits can prefill
					try { editBtn.dataset.images = JSON.stringify(finalImgs); } catch(e){ editBtn.dataset.images = '[]'; }
					// update thumbnail in the listing row (use first image)
					if (thumb) thumb.src = finalImgs[0] || thumb.src || '';
				}
			}

			closeModal('editListingModal');
		});
	}

	applyFilters();
});
