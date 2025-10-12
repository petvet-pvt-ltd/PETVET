const STORAGE_KEY = 'petvet_v1';

function getData(){
  return JSON.parse(localStorage.getItem(STORAGE_KEY)) || null;
}
function setData(d){ localStorage.setItem(STORAGE_KEY, JSON.stringify(d)); }

function initIfNeeded(){
  if(!localStorage.getItem(STORAGE_KEY) && window.PETVET_INITIAL_DATA){
    setData(window.PETVET_INITIAL_DATA);
  }
}

function renderKPIs(data){
  const today = window.PETVET_TODAY;
  const todayCount = data.appointments.filter(a=>a.date===today && a.status!=='cancelled').length;
  document.getElementById('kpi-today').textContent = todayCount;
  document.getElementById('kpi-total').textContent = data.appointments.length;
}

function renderOngoing(data){
  const container = document.getElementById('ongoing-container');
  container.innerHTML = '';
  const ongoing = data.appointments.find(a=>a.status==='ongoing');
  if(!ongoing){
    container.innerHTML = '<p>No ongoing appointment currently.</p>';
    return;
  }
  const html = `
    <p><strong>ID:</strong> ${ongoing.id}</p>
    <p><strong>Time:</strong> ${ongoing.time}</p>
    <p><strong>Pet:</strong> ${ongoing.petName}</p>
    <p><strong>Owner:</strong> ${ongoing.ownerName}</p>
    <p><strong>Reason:</strong> ${ongoing.reason}</p>
    <div style="margin-top:10px">
      <button class="btn navy" onclick="goToForm('medical-records.php', '${ongoing.id}')">Record</button>
      <button class="btn blue" onclick="goToForm('prescriptions.php', '${ongoing.id}')">Prescription</button>
      <button class="btn green" onclick="goToForm('vaccinations.php', '${ongoing.id}')">Vaccination</button>
      <button class="btn navy" onclick="completeAppointment('${ongoing.id}')">Complete</button>
      <button class="btn red" onclick="cancelAppointment('${ongoing.id}')">Cancel</button>
    </div>
  `;
  container.innerHTML = html;
}

function renderUpcoming(data){
  const tbody = document.querySelector('#upcomingTable tbody');
  tbody.innerHTML = '';
  const today = window.PETVET_TODAY;
  const rows = data.appointments.filter(a=>a.date===today && a.status==='scheduled');
  rows.forEach(a=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${a.id}</td><td>${a.time}</td><td>${a.petName}</td><td>${a.ownerName}</td><td>${a.reason}</td>
    <td><button class="btn navy" onclick="startAppointment('${a.id}')">Start</button>
    <button class="btn red" onclick="cancelAppointment('${a.id}')">Cancel</button></td>`;
    tbody.appendChild(tr);
  });
  if(rows.length===0) tbody.innerHTML = '<tr><td colspan="6">No upcoming appointments for today.</td></tr>';
}

function goToForm(page, apptId){
  location.href = `${page}?from=ongoing&appt=${encodeURIComponent(apptId)}`;
}

function startAppointment(id){
  initIfNeeded();
  const d = getData();
  // mark any ongoing to completed 
  d.appointments.forEach(a=>{ if(a.status==='ongoing') a.status='completed'; });
  const ap = d.appointments.find(a=>a.id===id);
  if(ap) ap.status='ongoing';
  setData(d);
  renderAll();
}

function completeAppointment(id){
  initIfNeeded();
  const d = getData();
  const ap = d.appointments.find(a=>a.id===id);
  if(ap) ap.status='completed';
  setData(d);
  renderAll();
}

function cancelAppointment(id){
  if(!confirm('Are you sure you want to cancel this appointment?')) return;
  initIfNeeded();
  const d = getData();
  const ap = d.appointments.find(a=>a.id===id);
  if(ap) ap.status='cancelled';
  setData(d);
  renderAll();
}

function bindSearch(){
  const input = document.getElementById('searchBar');
  if(!input) return;
  input.addEventListener('input', ()=>{
    const q = input.value.toLowerCase();
    document.querySelectorAll('#upcomingTable tbody tr').forEach(row=>{
      row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  });
}

function renderAll(){
  initIfNeeded();
  const d = getData();
  renderKPIs(d);
  renderOngoing(d);
  renderUpcoming(d);
}

document.addEventListener('DOMContentLoaded', ()=>{
  initIfNeeded();
  renderAll();
  bindSearch();
});
