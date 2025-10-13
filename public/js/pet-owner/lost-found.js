// Lost & Found interactions (migrated prototype UI-only logic)
document.addEventListener('DOMContentLoaded', () => {
	const qs = s => document.querySelector(s);
	const qsa = s => document.querySelectorAll(s);

	const segBtns = qsa('.seg-btn');
	const lostList  = qs('#lostList');
	const foundList = qs('#foundList');
	const searchInput = qs('#q');
	const speciesSel  = qs('#species');
	const sortSel     = qs('#sortBy');

	const reportModal = qs('#reportModal');
	const openReportBtns = ['#openReport','#emptyReportLost','#emptyReportFound']
		.map(sel=>qs(sel)).filter(Boolean);
	const cancelReportBtn = qs('#cancelReport');
	const reportForm = qs('#reportForm');

	let currentView = 'lost';
	const params = new URLSearchParams(location.search);
	currentView = params.get('view') || localStorage.getItem('lfView') || 'lost';
	setView(currentView);

	segBtns.forEach(btn=> btn.addEventListener('click', () => {
		const v = btn.getAttribute('data-view');
		setView(v);
	}));

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

	// Modal behavior
	openReportBtns.forEach(btn => btn.addEventListener('click', () => { reportModal.hidden=false; }));
	cancelReportBtn && cancelReportBtn.addEventListener('click', () => { closeModal(); });
	reportModal && reportModal.addEventListener('mousedown', e=>{ if(e.target===reportModal) closeModal(); });
	document.addEventListener('keydown', e=>{ if(e.key==='Escape' && !reportModal.hidden) closeModal(); });
	reportForm && reportForm.addEventListener('submit', e=>{ e.preventDefault(); alert('Demo only: This would create a new report.'); closeModal(); });

	function closeModal(){ reportModal.hidden=true; reportForm && reportForm.reset(); }

	applyFilters();
});
