// Show or hide medical record form and prefill with appointment data if provided
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

// Build and render HTML table of medical records with file viewer links
function renderRecords(list){
  const container = document.getElementById('recordsContainer');
  if(!container) return;

  if(!list || list.length === 0){
    container.innerHTML = '<p>No records found.</p>';
    return;
  }

  // Build table HTML with dynamic record rows
  let html = `
    <div class="simple-mobile-table">
    <table>
      <thead>
        <tr>
          <th>Date</th>
          <th>Pet</th>
          <th>Owner</th>
          <th>Veterinarian</th>
          <th>Symptoms</th>
          <th>Diagnosis</th>
          <th>Treatment</th>
          <th>Reports</th>
        </tr>
      </thead>
      <tbody>
  `;

  list.forEach(r => {
    // Parse and create file gallery button if reports exist
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
        <td>${r.vet_name || '-'}</td>
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

// Initialize page, load data, setup filters, and event listeners
document.addEventListener('DOMContentLoaded', () => {
  const d = window.PETVET_INITIAL_DATA;
  if(!d) return;
  const currentVetId = Number(window.PETVET_CURRENT_VET_ID || 0);

  const url = new URL(window.location.href);
  const from = url.searchParams.get('from');
  let apptId = url.searchParams.get('appointment');
  apptId = apptId ? Number(apptId) : null;

  const search = document.getElementById('searchBar');
  let baseRecords = d.medicalRecords || [];
  // Track if showing only current vet's records
  let onlyMyRecords = false;

  // Apply search and vet filter then re-render table
  const applyFiltersAndRender = () => {
    const q = (search?.value || '').toLowerCase();

    const filtered = baseRecords.filter(r => {
      if (onlyMyRecords && currentVetId && Number(r.vet_id || 0) !== currentVetId) {
        return false;
      }

      if (!q) return true;
      return Object.values(r).some(v => String(v).toLowerCase().includes(q));
    });

    renderRecords(filtered);
  };

  // Create toggle checkbox to filter records by current veterinarian
  const renderMyRecordsToggle = () => {
    console.log('renderMyRecordsToggle called: from=' + from + ', apptId=' + apptId + ', search=' + !!search);
    if (from !== 'ongoing' || !apptId) {
      console.log('Toggle skipped: from !== ongoing or no apptId');
      return;
    }
    if (!search || !search.parentNode) {
      console.log('Toggle skipped: search not found or no parentNode');
      return;
    }

    console.log('Creating toggle...');
    const wrap = document.createElement('div');
    wrap.style.margin = '10px 0 0';
    wrap.style.padding = '10px';
    wrap.style.backgroundColor = '#f5f5f5';
    wrap.style.borderRadius = '4px';
    wrap.style.border = '1px solid #ddd';
    wrap.innerHTML = `
      <label style="display:inline-flex;align-items:center;gap:8px;cursor:pointer;font-weight:500;">
        <input type="checkbox" id="myVetOnlyToggle" style="cursor:pointer;">
        <span>Show only my records</span>
      </label>
    `;

    search.parentNode.insertBefore(wrap, search.nextSibling);
    console.log('Toggle inserted successfully');

    // Attach change listener to toggle checkbox
    const toggle = document.getElementById('myVetOnlyToggle');
    if (toggle) {
      toggle.addEventListener('change', () => {
        onlyMyRecords = toggle.checked;
        applyFiltersAndRender();
      });
    }
  };

  // Get all medical records for a specific pet by name
  function filterRecordsByPet(petName){
    return d.medicalRecords.filter(r =>
      (r.pet_name || r.petName) === petName
    );
  }

  // Get medical records linked to a specific appointment
  function filterRecordsByAppointment(){
    return d.medicalRecords.filter(r =>
      Number(r.appointment_id || r.appointmentId) === apptId
    );
  }

  // Determine context and load appropriate records and form state
  if(from === 'ongoing' && apptId){
    showForm(true, apptId);
    // Show all records for this pet
    const appt = d.appointments.find(a => Number(a.id) === apptId);
    if(appt){
      const petName = appt.pet_name || appt.petName;
      baseRecords = filterRecordsByPet(petName);
    } else {
      baseRecords = d.medicalRecords || [];
    }
  } else if(from === 'completed' && apptId){
    showForm(false);
    baseRecords = filterRecordsByAppointment();
  } else {
    showForm(false);
    baseRecords = d.medicalRecords || [];
  }

  renderMyRecordsToggle();
  applyFiltersAndRender();

  // Attach search input listener for real-time filtering
  if(search){
    search.addEventListener('input', () => {
      applyFiltersAndRender();
    });
  }

  // Handle form submission with file upload to server
  const form = document.getElementById('medicalRecordForm');
  if(form){
    // Display preview of selected files before upload
    const fileInput = form.querySelector('input[type="file"]');
    const filePreview = document.getElementById('filePreview');
    
    // Update preview when files are selected
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

    // Submit form data and files to API endpoint
    form.addEventListener('submit', async e => {
      e.preventDefault();

      const formData = new FormData();
      formData.append('appointment_id', form.elements['appointmentId'].value);
      formData.append('symptoms', form.elements['symptoms'].value);
      formData.append('diagnosis', form.elements['diagnosis'].value);
      formData.append('treatment', form.elements['treatment'].value);

      // Add file attachments to request
      const files = form.elements['reports[]'].files;
      for (let i = 0; i < files.length; i++) {
        formData.append('reports[]', files[i]);
      }

      try {
        // Send medical record and files to API
        const res = await fetch('/PETVET/api/vet/medical-records/add.php', {
          method: 'POST',
          body: formData
        });

        const json = await res.json();
        if(!json.success){
          alert(json.error || 'Failed to save medical record.');
          return;
        }

        // Reload page to display updated records
        alert('Medical record saved successfully.');
        location.reload();
      } catch(err){
        console.error(err);
        alert('Server error while saving medical record.');
      }
    });
  }
});
