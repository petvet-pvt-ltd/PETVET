// Explore Pets - Guest version (no sell/my listings features)
document.addEventListener('DOMContentLoaded', () => {
	const qs = (s, el=document) => el.querySelector(s);
	const qsa = (s, el=document) => Array.from(el.querySelectorAll(s));
	const money = n => 'Rs ' + Number(n).toLocaleString();

	// Modal helpers
	function openModal(id){ 
		const m=qs('#'+id); 
		if(!m) return; 
		m.setAttribute('aria-hidden','false'); 
		m.classList.add('show');
	}
	function closeModal(id){ 
		const m=qs('#'+id); 
		if(!m) return; 
		m.setAttribute('aria-hidden','true'); 
		m.classList.remove('show');
	}
	qsa('[data-close]').forEach(btn=>btn.addEventListener('click',e=>closeModal(e.currentTarget.getAttribute('data-close'))));
	qsa('.modal').forEach(m=>m.addEventListener('click',e=>{ if(e.target===m) closeModal(m.id); }));
	document.addEventListener('keydown', e=>{ if(e.key==='Escape'){ qsa('.modal.show').forEach(m=>closeModal(m.id)); }});

	// Contact Modal
	const contactModal = qs('#contactModal');
	const contactContent = qs('#contactContent');
	const closeContactBtn = qs('#closeContact');
	
	closeContactBtn?.addEventListener('click', () => { contactModal.style.display = 'none'; });
	contactModal?.addEventListener('click', e => { if(e.target === contactModal) contactModal.style.display = 'none'; });

	// Contact Seller buttons
	qsa('.contact-seller-btn').forEach(btn => {
		btn.addEventListener('click', () => {
			const name = btn.getAttribute('data-name');
			const phone = btn.getAttribute('data-phone');
			const phone2 = btn.getAttribute('data-phone2');
			const email = btn.getAttribute('data-email');
			
			let html = `<div class="contact-info-list">`;
			html += `<div class="contact-item"><strong>Seller:</strong> ${name}</div>`;
			if(phone) html += `<div class="contact-item"><strong>Primary Phone:</strong> <a href="tel:${phone}">${phone}</a></div>`;
			if(phone2) html += `<div class="contact-item"><strong>Secondary Phone:</strong> <a href="tel:${phone2}">${phone2}</a></div>`;
			if(email) html += `<div class="contact-item"><strong>Email:</strong> <a href="mailto:${email}">${email}</a></div>`;
			html += `</div>`;
			
			contactContent.innerHTML = html;
			contactModal.style.display = 'flex';
		});
	});

	// View Details buttons - match pet-owner details modal structure/styles
	qsa('.view').forEach(btn => {
		btn.addEventListener('click', e => {
			const card = e.currentTarget.closest('.card');
			const title = card.querySelector('h3')?.textContent || 'Pet';
			const price = card.getAttribute('data-price') || 0;
			const img = card.querySelector('img')?.src || '';
			const meta = card.querySelector('.meta')?.textContent || '';
			const desc = card.querySelector('.desc')?.textContent || '';
			const sellerName = card.querySelector('.seller-name strong')?.textContent || '';
			const sellerLoc = card.querySelector('.seller-loc')?.textContent || '';

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
						</div>
					</div>
				</div>`;
			openModal('detailsModal');
		});
	});

	// Image carousel in cards
	qsa('.card').forEach(card => {
		const images = qsa('.carousel-image', card);
		const indicators = qsa('.carousel-indicator', card);
		const prevBtn = qs('.carousel-nav.prev', card);
		const nextBtn = qs('.carousel-nav.next', card);
		
		if(images.length <= 1) return;
		
		let currentIndex = 0;
		
		function showImage(index) {
			images.forEach((img, i) => {
				img.style.display = i === index ? 'block' : 'none';
			});
			indicators.forEach((ind, i) => {
				ind.classList.toggle('active', i === index);
			});
			currentIndex = index;
		}
		
		prevBtn?.addEventListener('click', e => {
			e.stopPropagation();
			const newIndex = (currentIndex - 1 + images.length) % images.length;
			showImage(newIndex);
		});
		
		nextBtn?.addEventListener('click', e => {
			e.stopPropagation();
			const newIndex = (currentIndex + 1) % images.length;
			showImage(newIndex);
		});
		
		indicators.forEach((ind, i) => {
			ind.addEventListener('click', e => {
				e.stopPropagation();
				showImage(i);
			});
		});
	});

	// Search and filters
	const searchInput = qs('#searchInput');
	const speciesFilter = qs('#speciesFilter');
	const sortBy = qs('#sortBy');
	const grid = qs('#listingsGrid');
	
	function filterAndSort() {
		const cards = qsa('.card', grid);
		const searchTerm = searchInput.value.toLowerCase();
		const species = speciesFilter.value;
		const sort = sortBy.value;
		
		// Filter
		let visibleCards = cards.filter(card => {
			const matchSearch = !searchTerm || 
				card.textContent.toLowerCase().includes(searchTerm);
			const matchSpecies = !species || 
				card.getAttribute('data-species') === species;
			return matchSearch && matchSpecies;
		});
		
		// Sort
		visibleCards.sort((a, b) => {
			const priceA = parseInt(a.getAttribute('data-price')) || 0;
			const priceB = parseInt(b.getAttribute('data-price')) || 0;
			
			switch(sort) {
				case 'priceLow': return priceA - priceB;
				case 'priceHigh': return priceB - priceA;
				case 'age': return 0; // Could implement if needed
				default: return 0; // newest (already in order)
			}
		});
		
		// Hide all first
		cards.forEach(card => card.style.display = 'none');
		
		// Show and reorder visible cards
		visibleCards.forEach(card => {
			card.style.display = 'block';
			grid.appendChild(card);
		});
	}
	
	searchInput?.addEventListener('input', filterAndSort);
	speciesFilter?.addEventListener('change', filterAndSort);
	sortBy?.addEventListener('change', filterAndSort);
});
