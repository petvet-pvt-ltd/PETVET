// ===========================
// Appointments JS (DB-driven)
// ===========================

function buildTableHTML(rows, includeActions=false){
    if(rows.length===0) 
        return '<div class="simple-mobile-table"><table><thead><tr><th>ID</th><th>Date</th><th>Time</th><th>Pet</th><th>Owner</th><th>Reason</th>' + 
               (includeActions? '<th>Actions</th>':'') + 
               '</tr></thead><tbody><tr><td colspan="'+ (includeActions?7:6) +'">No records</td></tr></tbody></table></div>';
    
    let html = '<div class="simple-mobile-table"><table><thead><tr><th>ID</th><th>Date</th><th>Time</th><th>Pet</th><th>Owner</th><th>Reason</th>' + 
               (includeActions? '<th>Actions</th>':'') + '</tr></thead><tbody>';
    
    rows.forEach(r=>{
        html += `<tr><td>${r.id}</td><td>${r.appointment_date||r.date}</td><td>${r.appointment_time||r.time}</td><td>${r.pet_name||r.petName}</td><td>${r.owner_name||r.ownerName}</td><td>${r.appointment_type||r.reason}</td>`;
        
        if(includeActions){
            const hasRec = window.PETVET_INITIAL_DATA.medicalRecords.some(m =>m.appointmentId == r.id || m.appointment_id == r.id);
            const hasPres = window.PETVET_INITIAL_DATA.prescriptions.some(p =>p.appointmentId == r.id || p.appointment_id == r.id);
            const hasVacc = window.PETVET_INITIAL_DATA.vaccinations.some(v =>v.appointmentId == r.id || v.appointment_id == r.id);

            let actions = '';
            if(hasRec) actions += `<button class="btn navy" onclick="goView('medical-records','${r.id}')">View Record</button>`;
            if(hasPres) actions += `<button class="btn blue" onclick="goView('prescriptions','${r.id}')">View Prescription</button>`;
            if(hasVacc) actions += `<button class="btn green" onclick="goView('vaccinations','${r.id}')">View Vaccination</button>`;
            html += `<td>${actions}</td>`;
        }

        html += `</tr>`;
    });

    html += '</tbody></table></div>';
    return html;
}

function goView(page, apptId){
    location.href = `/PETVET/?module=vet&page=${page}&from=completed&appointment=${encodeURIComponent(apptId)}`;
}

function renderAll(){
    const d = window.PETVET_INITIAL_DATA;
    if(!d) return;

    renderSection('ongoingTableContainer', d.ongoing, false);
    renderSection('upcomingTableContainer', d.upcoming, false);
    renderSection('completedTableContainer', d.completed, true);
    renderSection('cancelledTableContainer', d.cancelled, false);
}

function renderSection(containerId, data, includeActions=false){
    const container = document.getElementById(containerId);
    if(container){
        container.innerHTML = buildTableHTML(data, includeActions);
    }
}

function setupSearch(){
    const inputs = document.querySelectorAll('input[id^="searchBar"]');
    inputs.forEach(input=>{
        input.addEventListener('input', e=>{
            const term = e.target.value.toLowerCase();
            let containerId = '';
            if(input.parentElement.querySelector('div')) containerId = input.parentElement.querySelector('div').id;

            if(!containerId || !window.PETVET_INITIAL_DATA) return;

            let dataset = [];
            if(containerId==='ongoingTableContainer') dataset = window.PETVET_INITIAL_DATA.ongoing;
            else if(containerId==='upcomingTableContainer') dataset = window.PETVET_INITIAL_DATA.upcoming;
            else if(containerId==='completedTableContainer') dataset = window.PETVET_INITIAL_DATA.completed;
            else if(containerId==='cancelledTableContainer') dataset = window.PETVET_INITIAL_DATA.cancelled;

            const includeActions = containerId==='completedTableContainer';
            const filtered = dataset.filter(r => Object.values(r).some(v=>String(v).toLowerCase().includes(term)));
            renderSection(containerId, filtered, includeActions);
        });
    });
}

document.addEventListener('DOMContentLoaded', ()=>{
    renderAll();
    setupSearch();
});
