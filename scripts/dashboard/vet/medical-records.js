const STORAGE_KEY = 'petvet_v1';
function getData(){ return JSON.parse(localStorage.getItem(STORAGE_KEY)); }
function setData(d){ localStorage.setItem(STORAGE_KEY, JSON.stringify(d)); }
function initIfNeeded(){ if(!localStorage.getItem(STORAGE_KEY) && window.PETVET_INITIAL_DATA) setData(window.PETVET_INITIAL_DATA); }

function showForm(prefill=false, apptId=null){
  const fs = document.getElementById('formSection');
  if(prefill && apptId){
    fs.style.display='block';
    const d = getData();
    const appt = d.appointments.find(a=>a.id===apptId);
    if(appt){
      const form = document.getElementById('medicalRecordForm');
      form.elements['appointmentId'].value = appt.id;
      form.elements['petName'].value = appt.petName;
      form.elements['ownerName'].value = appt.ownerName;
    }
  } else {
    fs.style.display='none';
  }
}

function renderRecords(filtered=null){
  const d = getData();
  const list = filtered || d.medicalRecords;
  const container = document.getElementById('recordsContainer');
  if(!list || list.length===0){ container.innerHTML = '<p>No records found.</p>'; return; }
  let html = '<table><thead><tr><th>ID</th><th>Date</th><th>Pet</th><th>Owner</th><th>Symptoms</th><th>Diagnosis</th><th>Treatment</th></tr></thead><tbody>';
  list.forEach(r=> html += `<tr><td>${r.id}</td><td>${r.date}</td><td>${r.petName}</td><td>${r.ownerName}</td><td>${r.symptoms}</td><td>${r.diagnosis}</td><td>${r.treatment}</td></tr>`);
  html += '</tbody></table>';
  container.innerHTML = html;
}

document.addEventListener('DOMContentLoaded', ()=>{
  initIfNeeded();
  const url = new URL(window.location.href);
  const from = url.searchParams.get('from'); // 'ongoing' or 'completed' or null
  const apptId = url.searchParams.get('appt');

  if(from==='ongoing' && apptId){
    // show form and render records filtered by apptId
    showForm(true, apptId);
    const d = getData();
    const records = d.medicalRecords.filter(r=>r.appointmentId===apptId);
    renderRecords(records);
  } else if(from === 'completed' && apptId){
    // no form, only show records for that appointment
    showForm(false);
    const d = getData();
    const records = d.medicalRecords.filter(r=>r.appointmentId===apptId);
    renderRecords(records);
  } else {
    // via sidebar: show all records, no form
    showForm(false);
    renderRecords();
  }

  // search
  const search = document.getElementById('searchBar');
  if(search){
    search.addEventListener('input', ()=>{
      const q = search.value.toLowerCase();
      const d = getData();
      const filtered = d.medicalRecords.filter(r=> JSON.stringify(r).toLowerCase().includes(q) );
      renderRecords(filtered);
    });
  }

  // form submit
  const form = document.getElementById('medicalRecordForm');
  if(form){
    form.addEventListener('submit', (e)=>{
      e.preventDefault();
      const d = getData();
      const fid = 'M' + Math.floor(Math.random()*9000+1000);
      const newRec = {
        id: fid,
        appointmentId: form.elements['appointmentId'].value,
        petName: form.elements['petName'].value,
        ownerName: form.elements['ownerName'].value,
        date: new Date().toISOString().slice(0,10),
        symptoms: form.elements['symptoms'].value,
        diagnosis: form.elements['diagnosis'].value,
        treatment: form.elements['treatment'].value
      };
      d.medicalRecords.push(newRec);
      setData(d);
      alert('Medical record saved.');
      // re-render filtered list
      const apptId = newRec.appointmentId;
      renderRecords(d.medicalRecords.filter(r=>r.appointmentId===apptId));
      form.reset();
      // keep appointment/pet fields (refill)
      form.elements['appointmentId'].value = newRec.appointmentId;
      form.elements['petName'].value = newRec.petName;
      form.elements['ownerName'].value = newRec.ownerName;
    });
  }
});
