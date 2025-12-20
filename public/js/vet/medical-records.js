function showForm(prefill=false, apptId=null){
  const fs = document.getElementById('formSection');
  if(!fs) return;

  const d = window.PETVET_INITIAL_DATA;
  if(!d) return;

  apptId = Number(apptId);

  if(prefill && apptId){
    fs.style.display = 'block';

    const appt = d.appointments.find(a => Number(a.id) === apptId);
    if(appt){
      const form = document.getElementById('medicalRecordForm');
      if(form){
        form.elements['appointmentId'].value = appt.id;
        form.elements['petName'].value = appt.pet_name || appt.petName || '';
        form.elements['ownerName'].value = appt.owner_name || appt.ownerName || '';
      }
    }
  } else {
    fs.style.display = 'none';
  }
}

function renderRecords(list){
  const container = document.getElementById('recordsContainer');
  if(!container) return;

  if(!list || list.length === 0){
    container.innerHTML = '<p>No records found.</p>';
    return;
  }

  let html = `
    <div class="simple-mobile-table">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Date</th>
          <th>Pet</th>
          <th>Owner</th>
          <th>Symptoms</th>
          <th>Diagnosis</th>
          <th>Treatment</th>
        </tr>
      </thead>
      <tbody>
  `;

  list.forEach(r => {
    html += `
      <tr>
        <td>${r.id}</td>
        <td>${r.date || r.created_at || ''}</td>
        <td>${r.pet_name || r.petName || ''}</td>
        <td>${r.owner_name || r.ownerName || ''}</td>
        <td>${r.symptoms || ''}</td>
        <td>${r.diagnosis || ''}</td>
        <td>${r.treatment || ''}</td>
      </tr>
    `;
  });

  html += '</tbody></table></div>';
  container.innerHTML = html;
}

document.addEventListener('DOMContentLoaded', () => {
  const d = window.PETVET_INITIAL_DATA;
  if(!d) return;

  const url = new URL(window.location.href);
  const from = url.searchParams.get('from');
  let apptId = url.searchParams.get('appointment');
  apptId = apptId ? Number(apptId) : null;

  function filterRecords(){
    return d.medicalRecords.filter(r =>
      Number(r.appointment_id || r.appointmentId) === apptId
    );
  }

  if(from === 'ongoing' && apptId){
    showForm(true, apptId);
    renderRecords(filterRecords());
  } else if(from === 'completed' && apptId){
    showForm(false);
    renderRecords(filterRecords());
  } else {
    showForm(false);
    renderRecords(d.medicalRecords);
  }

  // Search
  const search = document.getElementById('searchBar');
  if(search){
    search.addEventListener('input', () => {
      const q = search.value.toLowerCase();
      const filtered = d.medicalRecords.filter(r =>
        Object.values(r).some(v => String(v).toLowerCase().includes(q))
      );
      renderRecords(filtered);
    });
  }

  // ✅ REAL SAVE (DB)
  const form = document.getElementById('medicalRecordForm');
  if(form){
    form.addEventListener('submit', async e => {
      e.preventDefault();

      const payload = {
        appointment_id: form.elements['appointmentId'].value,
        symptoms: form.elements['symptoms'].value,
        diagnosis: form.elements['diagnosis'].value,
        treatment: form.elements['treatment'].value
      };

      try {
        const res = await fetch('/PETVET/api/vet/medical-records/add.php', {
          method: 'POST',
          headers: {'Content-Type':'application/json'},
          body: JSON.stringify(payload)
        });

        const json = await res.json();
        if(!json.success){
          alert(json.error || 'Failed to save medical record.');
          return;
        }

        alert('Medical record saved.');
        location.reload();
      } catch(err){
        console.error(err);
        alert('Server error while saving medical record.');
      }
    });
  }
});
