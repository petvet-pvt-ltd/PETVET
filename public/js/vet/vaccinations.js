// same pattern as medical_records.js but for vaccinations
const STORAGE_KEY = 'petvet_v1';
function getData(){ return JSON.parse(localStorage.getItem(STORAGE_KEY)); }
function setData(d){ localStorage.setItem(STORAGE_KEY, JSON.stringify(d)); }
function initIfNeeded(){ if(!localStorage.getItem(STORAGE_KEY) && window.PETVET_INITIAL_DATA) setData(window.PETVET_INITIAL_DATA); }

function showForm(prefill=false, apptId=null){
  const sec = document.getElementById('vaccFormSection');
  if(!sec) return;
  
  if(prefill && apptId){
    sec.style.display='block';
    const form = document.getElementById('vaccinationForm');
    const d = getData();
    if(!d) return;
    
    const appt = d.appointments.find(a=>a.id===apptId);
    if(appt && form){ 
      if(form.elements['appointmentId']) form.elements['appointmentId'].value = appt.id; 
      if(form.elements['petName']) form.elements['petName'].value = appt.petName; 
      if(form.elements['ownerName']) form.elements['ownerName'].value = appt.ownerName; 
    }
  } else {
    sec.style.display='none';
  }
}

function renderVaccinations(list){
  const d = getData();
  if(!d) return;
  
  const arr = list || d.vaccinations;
  const c = document.getElementById('vaccinationsContainer');
  if(!c) return;
  
  if(!arr || arr.length===0){ 
    c.innerHTML = '<p>No vaccinations.</p>'; 
    return; 
  }
  
  let html = '<div class="simple-mobile-table"><table><thead><tr><th>ID</th><th>Date</th><th>Pet</th><th>Owner</th><th>Vaccine</th><th>Next Due</th></tr></thead><tbody>';
  arr.forEach(r=> html += `<tr><td>${r.id}</td><td>${r.date}</td><td>${r.petName}</td><td>${r.ownerName}</td><td>${r.vaccine}</td><td>${r.nextDue}</td></tr>`);
  html += '</tbody></table></div>';
  c.innerHTML = html;
}

document.addEventListener('DOMContentLoaded', ()=>{
  initIfNeeded();
  const url = new URL(window.location.href);
  const from = url.searchParams.get('from');
  const apptId = url.searchParams.get('appointment');

  if(from==='ongoing' && apptId){ 
    showForm(true, apptId); 
    const d = getData();
    if(d) renderVaccinations(d.vaccinations.filter(v=>v.appointmentId===apptId)); 
  }
  else if(from==='completed' && apptId){ 
    showForm(false); 
    const d = getData();
    if(d) renderVaccinations(d.vaccinations.filter(v=>v.appointmentId===apptId)); 
  }
  else { 
    showForm(false); 
    renderVaccinations(); 
  }

  const search = document.getElementById('searchBar');
  if(search) search.addEventListener('input', ()=> {
    const q = search.value.toLowerCase();
    const d = getData();
    if(!d) return;
    
    renderVaccinations(d.vaccinations.filter(v=> JSON.stringify(v).toLowerCase().includes(q)));
  });

  const form = document.getElementById('vaccinationForm');
  if(form) form.addEventListener('submit', e=>{
    e.preventDefault();
    const d = getData();
    if(!d) return;
    
    const id = 'V' + Math.floor(Math.random()*9000+1000);
    const n = {
      id:id,
      appointmentId: form.elements['appointmentId'].value,
      petName: form.elements['petName'].value,
      ownerName: form.elements['ownerName'].value,
      date: new Date().toISOString().slice(0,10),
      vaccine: form.elements['vaccine'].value,
      nextDue: form.elements['nextDue'].value
    };
    d.vaccinations.push(n);
    setData(d);
    alert('Vaccination saved.');
    renderVaccinations(d.vaccinations.filter(v=>v.appointmentId===n.appointmentId));
    
    form.reset();
    if(form.elements['appointmentId']) form.elements['appointmentId'].value=n.appointmentId; 
    if(form.elements['petName']) form.elements['petName'].value=n.petName; 
    if(form.elements['ownerName']) form.elements['ownerName'].value=n.ownerName;
  });
});