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
          <th>Date</th>
          <th>Pet</th>
          <th>Owner</th>
          <th>Symptoms</th>
          <th>Diagnosis</th>
          <th>Treatment</th>
          <th>Reports</th>
        </tr>
      </thead>
      <tbody>
  `;

  list.forEach(r => {
    let reportsHtml = '';
    if (r.reports) {
      try {
        const files = JSON.parse(r.reports);
        if (files && files.length > 0) {
          const filesJson = JSON.stringify(files).replace(/'/g, "&apos;");
          const fileCount = files.length;
          const label = fileCount === 1 ? '1 document' : `${fileCount} documents`;
          reportsHtml = `<button class="btn-view-files" onclick='openFilesGallery(${filesJson})'>${label}</button>`;
        }
      } catch(e) {
        reportsHtml = '';
      }
    }

    html += `
      <tr>
        <td>${r.date || r.created_at || ''}</td>
        <td>${r.pet_name || r.petName || ''}</td>
        <td>${r.owner_name || r.ownerName || ''}</td>
        <td>${r.symptoms || ''}</td>
        <td>${r.diagnosis || ''}</td>
        <td>${r.treatment || ''}</td>
        <td>${reportsHtml || '-'}</td>
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

  function filterRecordsByPet(petName){
    return d.medicalRecords.filter(r =>
      (r.pet_name || r.petName) === petName
    );
  }

  function filterRecordsByAppointment(){
    return d.medicalRecords.filter(r =>
      Number(r.appointment_id || r.appointmentId) === apptId
    );
  }

  if(from === 'ongoing' && apptId){
    showForm(true, apptId);
    // Show all records for this pet
    const appt = d.appointments.find(a => Number(a.id) === apptId);
    if(appt){
      const petName = appt.pet_name || appt.petName;
      renderRecords(filterRecordsByPet(petName));
    } else {
      renderRecords(d.medicalRecords);
    }
  } else if(from === 'completed' && apptId){
    showForm(false);
    renderRecords(filterRecordsByAppointment());
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

  // ✅ REAL SAVE (DB) with file upload
  const form = document.getElementById('medicalRecordForm');
  if(form){
    // File preview
    const fileInput = form.querySelector('input[type="file"]');
    const filePreview = document.getElementById('filePreview');
    
    if (fileInput && filePreview) {
      fileInput.addEventListener('change', (e) => {
        filePreview.innerHTML = '';
        const files = Array.from(e.target.files);
        
        if (files.length > 0) {
          filePreview.innerHTML = '<h4>Selected Files:</h4>';
          files.forEach((file, idx) => {
            const div = document.createElement('div');
            div.className = 'file-item';
            div.innerHTML = `
              <span>${idx + 1}. ${file.name} (${(file.size / 1024).toFixed(2)} KB)</span>
            `;
            filePreview.appendChild(div);
          });
        }
      });
    }

    form.addEventListener('submit', async e => {
      e.preventDefault();

      const formData = new FormData();
      formData.append('appointment_id', form.elements['appointmentId'].value);
      formData.append('symptoms', form.elements['symptoms'].value);
      formData.append('diagnosis', form.elements['diagnosis'].value);
      formData.append('treatment', form.elements['treatment'].value);

      // Add files
      const files = form.elements['reports[]'].files;
      for (let i = 0; i < files.length; i++) {
        formData.append('reports[]', files[i]);
      }

      try {
        const res = await fetch('/PETVET/api/vet/medical-records/add.php', {
          method: 'POST',
          body: formData
        });

        const json = await res.json();
        if(!json.success){
          alert(json.error || 'Failed to save medical record.');
          return;
        }

        alert('Medical record saved successfully.');
        location.reload();
      } catch(err){
        console.error(err);
        alert('Server error while saving medical record.');
      }
    });
  }
});
