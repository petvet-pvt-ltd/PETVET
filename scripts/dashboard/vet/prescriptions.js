// same pattern as medical_records.js but for prescriptions
const STORAGE_KEY = 'petvet_v1';
function getData(){ return JSON.parse(localStorage.getItem(STORAGE_KEY)); }
function setData(d){ localStorage.setItem(STORAGE_KEY, JSON.stringify(d)); }
function initIfNeeded(){ if(!localStorage.getItem(STORAGE_KEY) && window.PETVET_INITIAL_DATA) setData(window.PETVET_INITIAL_DATA); }

function showForm(prefill=false, apptId=null){
  const sec = document.getElementById('prescriptionFormSection');
  if(prefill && apptId){
    sec.style.display='block';
    const form = document.getElementById('prescriptionForm');
    const d = getData();
    const appt = d.appointments.find(a=>a.id===apptId);
    if(appt){
      form.elements['appointmentId'].value = appt.id;
      form.elements['petName'].value = appt.petName;
      form.elements['ownerName'].value = appt.ownerName;
    }
  } else sec.style.display='none';
}

function renderPrescriptions(list){
  const d = getData();
  const arr = list || d.prescriptions;
  const container = document.getElementById('prescriptionsContainer');
  if(!arr || arr.length===0){ container.innerHTML='<p>No prescriptions.</p>'; return; }
  let html = '<table><thead><tr><th>ID</th><th>Date</th><th>Pet</th><th>Owner</th><th>Medication</th><th>Dosage</th><th>Notes</th></tr></thead><tbody>';
  arr.forEach(r=> html += `<tr><td>${r.id}</td><td>${r.date}</td><td>${r.petName}</td><td>${r.ownerName}</td><td>${r.medication}</td><td>${r.dosage}</td><td>${r.notes}</td></tr>`);
  html += '</tbody></table>';
  container.innerHTML = html;
}

document.addEventListener('DOMContentLoaded', ()=>{
  initIfNeeded();
  const url = new URL(window.location.href);
  const from = url.searchParams.get('from');
  const apptId = url.searchParams.get('appt');

  if(from==='ongoing' && apptId){ showForm(true, apptId); renderPrescriptions(getData().prescriptions.filter(p=>p.appointmentId===apptId)); }
  else if(from==='completed' && apptId){ showForm(false); renderPrescriptions(getData().prescriptions.filter(p=>p.appointmentId===apptId)); }
  else { showForm(false); renderPrescriptions(); }

  const search = document.getElementById('searchBar');
  if(search) search.addEventListener('input', ()=> {
    const q = search.value.toLowerCase();
    const d = getData();
    renderPrescriptions(d.prescriptions.filter(p=> JSON.stringify(p).toLowerCase().includes(q)));
  });

  const form = document.getElementById('prescriptionForm');
  if(form) form.addEventListener('submit', e=>{
    e.preventDefault();
    const d = getData();
    const id = 'P' + Math.floor(Math.random()*9000+1000);
    const n = {
      id: id,
      appointmentId: form.elements['appointmentId'].value,
      petName: form.elements['petName'].value,
      ownerName: form.elements['ownerName'].value,
      date: new Date().toISOString().slice(0,10),
      medication: form.elements['medication'].value,
      dosage: form.elements['dosage'].value,
      notes: form.elements['notes'].value
    };
    d.prescriptions.push(n); setData(d); alert('Prescription added'); renderPrescriptions(d.prescriptions.filter(p=>p.appointmentId===n.appointmentId));
    form.reset(); form.elements['appointmentId'].value=n.appointmentId; form.elements['petName'].value=n.petName; form.elements['ownerName'].value=n.ownerName;
  });
});
