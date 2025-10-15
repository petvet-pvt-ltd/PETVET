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
		// Find any element with a data-appt attribute (buttons in the markup)
		const btn = e.target.closest('[data-appt]');
		if (!btn) return;
		// Reschedule button in the PHP uses classes: "btn primary"
		if (btn.classList.contains('primary')){
			activeRow = btn.closest('.appt-row');
			const dayCard = activeRow.closest('.day-card');
			const existingDate = dayCard.getAttribute('data-day');
			const timeEl = activeRow.querySelector('.appt-time');
			const time24 = timeEl.getAttribute('data-time-24') || convertTo24(timeEl.textContent.trim());
			currentDateTime = toDate(existingDate, time24);
			newDateInput.value = existingDate; newDateInput.min = existingDate; newTimeInput.value = time24;
			openModal(resOverlay); newDateInput.focus();
			return;
		}
		// Cancel button in the PHP uses classes: "btn danger"
		if (btn.classList.contains('danger')){
			activeRow = btn.closest('.appt-row');
			openModal(cancelOverlay);
			return;
		}
	});

	resCancelBtn.addEventListener('click', ()=> closeModal(resOverlay));
	resForm.addEventListener('submit', e=>{
		e.preventDefault();
		if(!activeRow) return;
		const newDate=newDateInput.value; const newTime=newTimeInput.value; if(!newDate||!newTime) return;
		const newDateTime = toDate(newDate,newTime);
		if(newDateTime<=currentDateTime){ alert('Please choose a future date/time after the existing appointment.'); return; }

		// Prepare payload (assumes each row has data-appt id on the button)
		const apptId = activeRow.querySelector('[data-appt]')?.getAttribute('data-appt');
		const payload = { id: apptId ? Number(apptId) : null, date: newDate, time: newTime };

		// Optimistic UI update function
		const applyUi = ()=>{
			const timeEl = activeRow.querySelector('.appt-time'); timeEl.textContent = format12(newTime); timeEl.setAttribute('data-time-24', newTime);
			const badge = activeRow.querySelector('.badge-status'); badge.textContent='Pending'; badge.className='badge badge-status badge-pending';

			const sourceSection = activeRow.closest('.day-card');
			let targetSection = document.querySelector(`[data-day='${newDate}']`);
			// If target doesn't exist, create it and insert in chronological order
			if(!targetSection){
				targetSection=document.createElement('section');
				targetSection.className='day-card';
				targetSection.setAttribute('data-day',newDate);
				targetSection.innerHTML=`<header class="day-card__header"><span class="day-card__title">${formatHeader(newDate)}</span></header><div class="day-card__body"></div>`;
				// find insertion point (first existing day > newDate)
				const existing = Array.from(wrap.querySelectorAll('.day-card'));
				const insertBeforeNode = existing.find(s => s.getAttribute('data-day') > newDate);
				if(insertBeforeNode){ wrap.insertBefore(targetSection, insertBeforeNode); } else { wrap.appendChild(targetSection); }
			}

			// If moving within same day, only update time/status; do not reappend
			if(sourceSection && sourceSection.getAttribute('data-day') === newDate){
				// nothing more to do (time and status already updated)
				return;
			}

			// Move the appointment row into the target day's body
			targetSection.querySelector('.day-card__body').appendChild(activeRow);

			// If the source day has no more appointments, remove it
			if(sourceSection){
				const body = sourceSection.querySelector('.day-card__body');
				if(!body || body.children.length === 0){ sourceSection.parentNode && sourceSection.parentNode.removeChild(sourceSection); }
			}
		};

		// Send to backend; if backend not present, still apply UI changes (graceful fallback)
		fetch('/PETVET/pet-owner/appointments/reschedule', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify(payload)
		}).then(r=>{
			if(!r.ok) throw new Error('Network response was not ok');
			return r.json().catch(()=>({ success: true }));
		}).then(data=>{
			if(data && data.success===false){ throw new Error(data.message || 'Could not reschedule'); }
			applyUi();
			closeModal(resOverlay); resForm.reset(); activeRow=null; currentDateTime=null;
		}).catch(err=>{
			// Fallback: still update UI but notify user
			applyUi();
			closeModal(resOverlay); resForm.reset(); activeRow=null; currentDateTime=null;
			console.warn('Reschedule request failed:', err);
			alert('Reschedule saved locally. Server update failed.');
		});
	});

	backCancelBtn.addEventListener('click', ()=>{ closeModal(cancelOverlay); activeRow=null; });
	confirmCancelBtn.addEventListener('click', ()=>{
		if(!activeRow) return;
		const apptId = activeRow.querySelector('[data-appt]')?.getAttribute('data-appt');
		const payload = { id: apptId ? Number(apptId) : null };
		fetch('/PETVET/pet-owner/appointments/cancel', {
			method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload)
		}).then(r=>{ if(!r.ok) throw new Error('Network response was not ok'); return r.json().catch(()=>({ success: true })); }).then(data=>{
			if(data && data.success===false){ throw new Error(data.message || 'Could not cancel'); }
			const badge=activeRow.querySelector('.badge-status'); badge.textContent='Cancelled'; badge.className='badge badge-status badge-cancelled'; activeRow.classList.add('appt-row--cancelled');
			closeModal(cancelOverlay); activeRow=null;
		}).catch(err=>{
			// Fallback: apply UI but notify user
			const badge=activeRow.querySelector('.badge-status'); badge.textContent='Cancelled'; badge.className='badge badge-status badge-cancelled'; activeRow.classList.add('appt-row--cancelled');
			closeModal(cancelOverlay); activeRow=null; console.warn('Cancel request failed:', err); alert('Cancellation saved locally. Server update failed.');
		});
	});

	function convertTo24(time12h){ const s=time12h.replace(/\s+/g,' ').trim(); const m=s.match(/^(\d{1,2}):(\d{2})\s*([AP]M)$/i); if(!m) return '00:00'; let h=parseInt(m[1],10); const min=m[2]; const mer=m[3].toUpperCase(); if(h===12) h=0; if(mer==='PM') h+=12; return `${String(h).padStart(2,'0')}:${min}`; }
	function format12(hhmm24){ let [h,m]=hhmm24.split(':').map(n=>parseInt(n,10)); const mer=h>=12?'PM':'AM'; h=h%12; if(h===0) h=12; return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')} ${mer}`; }
	function toDate(ymd, hhmm){ const [Y,M,D]=ymd.split('-').map(n=>parseInt(n,10)); const [h,m]=hhmm.split(':').map(n=>parseInt(n,10)); return new Date(Y,M-1,D,h,m,0,0); }
	function formatHeader(ymd){ const d=new Date(ymd+'T00:00:00'); const month=d.toLocaleString(undefined,{month:'short'}); const day=d.getDate(); const wk=d.toLocaleString(undefined,{weekday:'short'}); return `${month} ${day} - ${wk}`; }
});
