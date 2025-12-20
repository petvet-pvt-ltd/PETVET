function renderKPIs(data){
  const today = window.PETVET_TODAY;
  const todayCount = data.appointments.filter(a=>a.appointment_date===today && a.status!=='cancelled').length;
  document.getElementById('kpi-today').textContent = todayCount;
  document.getElementById('kpi-total').textContent = data.appointments.length;
}

function renderOngoing(data){
  const container = document.getElementById('ongoing-container');
  if(!container) return;

  container.innerHTML = '';
  const ongoing = data.appointments.find(a=>a.status==='ongoing');
  if(!ongoing){ container.innerHTML = '<p>No ongoing appointment currently.</p>'; return; }

  container.innerHTML = `
    <p><strong>ID:</strong> ${ongoing.id}</p>
    <p><strong>Time:</strong> ${ongoing.appointment_time}</p>
    <p><strong>Pet:</strong> ${ongoing.pet_name}</p>
    <p><strong>Owner:</strong> ${ongoing.owner_name}</p>
    <p><strong>Reason:</strong> ${ongoing.appointment_type}</p>
    <div style="margin-top:10px">
      <button class="btn navy" onclick="goToForm('medical-records','${ongoing.id}')">Record</button>
      <button class="btn blue" onclick="goToForm('prescriptions','${ongoing.id}')">Prescription</button>
      <button class="btn green" onclick="goToForm('vaccinations','${ongoing.id}')">Vaccination</button>
      <button class="btn navy" onclick="updateStatus('${ongoing.id}','completed')">Complete</button>
      <button class="btn red" onclick="updateStatus('${ongoing.id}','cancelled')">Cancel</button>
    </div>
  `;
}

function renderUpcoming(data){
  const tbody = document.querySelector('#upcomingTable tbody');
  tbody.innerHTML = '';
  const today = window.PETVET_TODAY;
  const rows = data.appointments.filter(a=>a.appointment_date===today && a.status==='approved');

  rows.forEach(a=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${a.id}</td><td>${a.appointment_time}</td><td>${a.pet_name}</td><td>${a.owner_name}</td><td>${a.appointment_type}</td>
      <td>
        <button class="btn navy" onclick="startAppointment('${a.id}')">Start</button>
        <button class="btn red" onclick="updateStatus('${a.id}','cancelled')">Cancel</button>
      </td>`;
    tbody.appendChild(tr);
  });

  if(rows.length===0) tbody.innerHTML = '<tr><td colspan="6">No upcoming appointments for today.</td></tr>';
}

function goToForm(page, apptId){
  location.href = `/PETVET/?module=vet&page=${page}&from=ongoing&appointment=${apptId}`;
}

function updateStatus(id, status){
  if(status==='cancelled' && !confirm('Cancel this appointment?')) return;

  fetch('/PETVET/api/vet/appointments/update-status.php',{
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body: JSON.stringify({appointmentId:id,status:status})
  }).then(res=>res.json()).then(res=>{
    if(res.success) fetchDashboardData();
  });
}

function startAppointment(id){
  const ongoing = window.PETVET_INITIAL_DATA.appointments.find(a=>a.status==='ongoing');
  if(ongoing) updateStatus(ongoing.id,'completed');
  updateStatus(id,'ongoing');
}

function bindSearch(){
  const input = document.getElementById('searchBar');
  input?.addEventListener('input',()=>{
    const q = input.value.toLowerCase();
    document.querySelectorAll('#upcomingTable tbody tr').forEach(row=>{
      row.style.display = row.textContent.toLowerCase().includes(q)?'':'none';
    });
  });
}

function renderAll(){
  const d = window.PETVET_INITIAL_DATA;
  renderKPIs(d);
  renderOngoing(d);
  renderUpcoming(d);
}

function fetchDashboardData(){
  fetch('/PETVET/api/vet/dashboard-data.php')
  .then(res=>res.json())
  .then(data=>{
    window.PETVET_INITIAL_DATA = data;
    renderAll();
  });
}

document.addEventListener('DOMContentLoaded',()=>{
  renderAll();
  bindSearch();
});
