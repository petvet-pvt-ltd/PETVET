// Lost & Found - Guest version (Report pet available, no My Listings)
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

	let currentView = 'lost';

	const params = new URLSearchParams(location.search);
	currentView = params.get('view') || localStorage.getItem('lfView') || 'lost';
	setView(currentView);

	segBtns.forEach(btn=> btn.addEventListener('click', () => {
		const v = btn.getAttribute('data-view');
		setView(v);
	}));

	function setView(v) {
		currentView = v;
		localStorage.setItem('lfView', v);
		segBtns.forEach(b => {
			const matches = b.getAttribute('data-view')===v;
			b.classList.toggle('is-active', matches);
			b.setAttribute('aria-selected', matches ? 'true' : 'false');
		});
		if(v==='lost'){
			lostList.classList.remove('hidden');
			foundList.classList.add('hidden');
		} else {
			lostList.classList.add('hidden');
			foundList.classList.remove('hidden');
		}
		filterAndSort();
	}

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
				
				images.forEach((img, idx) => {
					img.style.display = idx === currentIndex ? 'block' : 'none';
				});
				
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
						<a href="mailto:${email}?subject=Regarding Lost/Found Pet" class="btn outline">Email</a>
					`;
					contactContent.appendChild(item);
				}

				contactModal.hidden = false;
			});
		});
	}

	// Filter and sort functionality
	function filterAndSort() {
		const list = currentView === 'lost' ? lostList : foundList;
		const cards = Array.from(qsa('.card', list));
		const q = searchInput.value.toLowerCase();
		const species = speciesSel.value.toLowerCase();
		const sort = sortSel.value;

		let visible = cards.filter(card => {
			const matchQ = !q || card.textContent.toLowerCase().includes(q);
			const matchSpecies = !species || card.getAttribute('data-species').toLowerCase() === species;
			return matchQ && matchSpecies;
		});

		// Sort
		if(sort === 'new') {
			visible.sort((a,b) => {
				const da = new Date(a.getAttribute('data-date'));
				const db = new Date(b.getAttribute('data-date'));
				return db - da;
			});
		} else if(sort === 'old') {
			visible.sort((a,b) => {
				const da = new Date(a.getAttribute('data-date'));
				const db = new Date(b.getAttribute('data-date'));
				return da - db;
			});
		}

		cards.forEach(c => c.style.display = 'none');
		visible.forEach(c => {
			c.style.display = 'block';
			list.appendChild(c);
		});

		if(visible.length === 0 && list.querySelector('.empty')) {
			list.querySelector('.empty').style.display = 'flex';
		} else if(list.querySelector('.empty')) {
			list.querySelector('.empty').style.display = 'none';
		}
	}

	searchInput?.addEventListener('input', filterAndSort);
	speciesSel?.addEventListener('change', filterAndSort);
	sortSel?.addEventListener('change', filterAndSort);

	// Report Pet Modal handlers
	openReportBtns.forEach(btn => {
		btn.addEventListener('click', () => {
			reportModal.hidden = false;
		});
	});

	cancelReportBtn?.addEventListener('click', () => {
		reportModal.hidden = true;
		reportForm.reset();
		photoPreview.innerHTML = '';
	});

	reportModal?.addEventListener('click', (e) => {
		if(e.target === reportModal) {
			reportModal.hidden = true;
			reportForm.reset();
			photoPreview.innerHTML = '';
		}
	});

	// Photo preview for report form
	photoInput?.addEventListener('change', (e) => {
		const files = Array.from(e.target.files);
		photoPreview.innerHTML = '';
		
		files.forEach(file => {
			const reader = new FileReader();
			reader.onload = (e) => {
				const img = document.createElement('img');
				img.src = e.target.result;
				img.style.width = '80px';
				img.style.height = '80px';
				img.style.objectFit = 'cover';
				img.style.borderRadius = '8px';
				photoPreview.appendChild(img);
			};
			reader.readAsDataURL(file);
		});
		
		photoPreview.style.display = files.length > 0 ? 'flex' : 'none';
	});

	// Report form submission
	reportForm?.addEventListener('submit', (e) => {
		e.preventDefault();
		
		// In production, this would submit to server
		alert('Thank you for reporting! Your submission has been received. (This is a demo - no actual submission)');
		
		reportModal.hidden = true;
		reportForm.reset();
		photoPreview.innerHTML = '';
	});

	// Contact Modal handlers
	closeContactBtn?.addEventListener('click', () => {
		contactModal.hidden = true;
	});

	contactModal?.addEventListener('click', (e) => {
		if(e.target === contactModal) {
			contactModal.hidden = true;
		}
	});

	// Initialize carousels and contact buttons
	setupCarousels();
	setupContactButtons();

	// ESC key to close modals
	document.addEventListener('keydown', (e) => {
		if(e.key === 'Escape') {
			if(!reportModal.hidden) reportModal.hidden = true;
			if(!contactModal.hidden) contactModal.hidden = true;
		}
	});
});
