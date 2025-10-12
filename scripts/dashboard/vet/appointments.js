const STORAGE_KEY = 'petvet_v1';

function getData(){ 
    return JSON.parse(localStorage.getItem(STORAGE_KEY)) || null; 
}
function setData(d){ 
    localStorage.setItem(STORAGE_KEY, JSON.stringify(d)); 
}
function initIfNeeded(){
    if(!localStorage.getItem(STORAGE_KEY) && window.PETVET_INITIAL_DATA) setData(window.PETVET_INITIAL_DATA);
}

// Adds Date column
function buildTableHTML(rows, includeActions=false){
    if(rows.length===0) 
        return '<table><thead><tr><th>ID</th><th>Date</th><th>Time</th><th>Pet</th><th>Owner</th><th>Reason</th>' + 
               (includeActions? '<th>Actions</th>':'') + 
               '</tr></thead><tbody><tr><td colspan="'+ (includeActions?7:6) +'">No records</td></tr></tbody></table>';
    
    let html = '<table><thead><tr><th>ID</th><th>Date</th><th>Time</th><th>Pet</th><th>Owner</th><th>Reason</th>' + 
               (includeActions? '<th>Actions</th>':'') + '</tr></thead><tbody>';
    
    rows.forEach(r=>{
        html += `<tr><td>${r.id}</td><td>${r.date}</td><td>${r.time}</td><td>${r.petName}</td><td>${r.ownerName}</td><td>${r.reason}</td>`;
        
        if(includeActions){
            const d = getData();
            const hasRec = d.medicalRecords.some(m=>m.appointmentId===r.id);
            const hasPres = d.prescriptions.some(p=>p.appointmentId===r.id);
            const hasVacc = d.vaccinations.some(v=>v.appointmentId===r.id);
            let actions = '';
            if(hasRec) actions += `<button class="btn navy" onclick="goView('medical-records.php','${r.id}')">View Record</button>`;
            if(hasPres) actions += `<button class="btn blue" onclick="goView('prescriptions.php','${r.id}')">View Prescription</button>`;
            if(hasVacc) actions += `<button class="btn green" onclick="goView('vaccinations.php','${r.id}')">View Vaccination</button>`;
            html += `<td>${actions}</td>`;
        }

        html += `</tr>`;
    });

    html += '</tbody></table>';
    return html;
}

function goView(page, apptId){
    location.href = `${page}?from=completed&appt=${encodeURIComponent(apptId)}`;
}

function renderSection(containerId, data, includeActions=false){
    document.getElementById(containerId).innerHTML = buildTableHTML(data, includeActions);
}

function renderAll(){
    initIfNeeded();
    const d = getData();

    // Keep all data sets separately for filtering later
    window.PETVET_TABLES = {
        ongoing: d.appointments.filter(a=>a.status==='ongoing'),
        upcoming: d.appointments.filter(a=>a.status==='scheduled'),
        completed: d.appointments.filter(a=>a.status==='completed'),
        cancelled: d.appointments.filter(a=>a.status==='cancelled')
    };

    renderSection('ongoingTableContainer', window.PETVET_TABLES.ongoing, false);
    renderSection('upcomingTableContainer', window.PETVET_TABLES.upcoming, false);
    renderSection('completedTableContainer', window.PETVET_TABLES.completed, true);
    renderSection('cancelledTableContainer', window.PETVET_TABLES.cancelled, false);
}

// 🔍 Add search functionality
function setupSearch(){
    const inputs = document.querySelectorAll('.searchBar');
    inputs.forEach(input=>{
        input.addEventListener('input', e=>{
            const term = e.target.value.toLowerCase();
            const target = e.target.getAttribute('data-target');

            // Map container to dataset
            let dataset;
            if(target.includes('ongoing')) dataset = window.PETVET_TABLES.ongoing;
            else if(target.includes('upcoming')) dataset = window.PETVET_TABLES.upcoming;
            else if(target.includes('completed')) dataset = window.PETVET_TABLES.completed;
            else if(target.includes('cancelled')) dataset = window.PETVET_TABLES.cancelled;

            const filtered = dataset.filter(r => 
                Object.values(r).some(val => String(val).toLowerCase().includes(term))
            );

            const includeActions = target.includes('completed');
            document.getElementById(target).innerHTML = buildTableHTML(filtered, includeActions);
        });
    });
}

document.addEventListener('DOMContentLoaded', ()=>{
    renderAll();
    setupSearch();
});
