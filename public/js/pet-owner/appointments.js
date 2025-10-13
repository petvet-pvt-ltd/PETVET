// Appointments interactions (migrated from prototype)
document.addEventListener("DOMContentLoaded", () => {
	const wrap = document.querySelector(".appointments-wrap");
	const resOverlay = document.getElementById("rescheduleModal");
	const resForm = document.getElementById("rescheduleForm");
	const resCancelBtn = document.getElementById("cancelReschedule");
	const newDateInput = document.getElementById("newDate");
	const newTimeInput = document.getElementById("newTime");
	const cancelOverlay = document.getElementById("cancelModal");
	const backCancelBtn = document.getElementById("backCancel");
	const confirmCancelBtn = document.getElementById("confirmCancel");

	let activeRow = null;
	let currentDateTime = null;
	const modalGuard = new WeakMap();

	function openModal(overlay){ overlay.hidden=false; modalGuard.set(overlay,false); setTimeout(()=>modalGuard.set(overlay,true),150); }
	function closeModal(overlay){ overlay.hidden=true; modalGuard.set(overlay,false); }
	function overlayCanClose(overlay){ return modalGuard.get(overlay)===true; }

	[resOverlay, cancelOverlay].forEach(ov=>{
		ov.addEventListener('mousedown', e=>{ if(e.target===ov && overlayCanClose(ov)) closeModal(ov); });
	});
	document.addEventListener('keydown', e=>{ if(e.key==='Escape'){ if(!resOverlay.hidden) closeModal(resOverlay); if(!cancelOverlay.hidden) closeModal(cancelOverlay);} });

	document.addEventListener('click', e=>{
		const resBtn = e.target.closest('.btn-blue[data-appt]');
		if(resBtn){
			activeRow = resBtn.closest('.appt-row');
			const dayCard = activeRow.closest('.day-card');
			const existingDate = dayCard.getAttribute('data-day');
			const timeEl = activeRow.querySelector('.appt-time');
			const time24 = timeEl.getAttribute('data-time-24') || convertTo24(timeEl.textContent.trim());
			currentDateTime = toDate(existingDate, time24);
			newDateInput.value = existingDate; newDateInput.min = existingDate; newTimeInput.value = time24;
			openModal(resOverlay); newDateInput.focus(); return;
		}
		const cancelBtn = e.target.closest('.btn-red[data-appt]');
		if(cancelBtn){ activeRow = cancelBtn.closest('.appt-row'); openModal(cancelOverlay); return; }
	});

	resCancelBtn.addEventListener('click', ()=> closeModal(resOverlay));
	resForm.addEventListener('submit', e=>{
		e.preventDefault(); if(!activeRow) return; const newDate=newDateInput.value; const newTime=newTimeInput.value; if(!newDate||!newTime) return;
		const newDateTime = toDate(newDate,newTime); if(newDateTime<=currentDateTime){ alert('Please choose a future date/time after the existing appointment.'); return; }
		const timeEl = activeRow.querySelector('.appt-time'); timeEl.textContent = format12(newTime); timeEl.setAttribute('data-time-24', newTime);
		const badge = activeRow.querySelector('.badge-status'); badge.textContent='Pending'; badge.className='badge badge-status badge-pending';
		let targetSection = document.querySelector(`[data-day='${newDate}']`);
		if(!targetSection){ targetSection=document.createElement('section'); targetSection.className='day-card'; targetSection.setAttribute('data-day',newDate); targetSection.innerHTML=`<header class="day-card__header"><span class="day-card__title">${formatHeader(newDate)}</span></header><div class="day-card__body"></div>`; wrap.appendChild(targetSection);} 
		targetSection.querySelector('.day-card__body').appendChild(activeRow);
		closeModal(resOverlay); resForm.reset(); activeRow=null; currentDateTime=null; });

	backCancelBtn.addEventListener('click', ()=>{ closeModal(cancelOverlay); activeRow=null; });
	confirmCancelBtn.addEventListener('click', ()=>{ if(!activeRow) return; const badge=activeRow.querySelector('.badge-status'); badge.textContent='Cancelled'; badge.className='badge badge-status badge-cancelled'; activeRow.classList.add('appt-row--cancelled'); closeModal(cancelOverlay); activeRow=null; });

	function convertTo24(time12h){ const s=time12h.replace(/\s+/g,' ').trim(); const m=s.match(/^(\d{1,2}):(\d{2})\s*([AP]M)$/i); if(!m) return '00:00'; let h=parseInt(m[1],10); const min=m[2]; const mer=m[3].toUpperCase(); if(h===12) h=0; if(mer==='PM') h+=12; return `${String(h).padStart(2,'0')}:${min}`; }
	function format12(hhmm24){ let [h,m]=hhmm24.split(':').map(n=>parseInt(n,10)); const mer=h>=12?'PM':'AM'; h=h%12; if(h===0) h=12; return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')} ${mer}`; }
	function toDate(ymd, hhmm){ const [Y,M,D]=ymd.split('-').map(n=>parseInt(n,10)); const [h,m]=hhmm.split(':').map(n=>parseInt(n,10)); return new Date(Y,M-1,D,h,m,0,0); }
	function formatHeader(ymd){ const d=new Date(ymd+'T00:00:00'); const month=d.toLocaleString(undefined,{month:'short'}); const day=d.getDate(); const wk=d.toLocaleString(undefined,{weekday:'short'}); return `${month} ${day} - ${wk}`; }
});
