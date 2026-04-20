// Show or hide prescription form and prefill with appointment data if provided
function showForm(prefill = false, apptId = null) {
  const sec = document.getElementById('prescriptionFormSection');
  if (!sec) return;

  const d = window.PETVET_INITIAL_DATA;
  if (!d) return;

  if (prefill && apptId) {
    sec.style.display = 'block';

    const appt = d.appointments.find(a => String(a.id) === String(apptId));
    if (appt) {
      const form = document.getElementById('prescriptionForm');
      if (form) {
        form.elements['appointmentId'].value = appt.id;
        form.elements['petName'].value = appt.pet_name || appt.petName || '';
        form.elements['ownerName'].value = appt.owner_name || appt.ownerName || '';
      }
    }
  } else {
    sec.style.display = 'none';
  }
}

// Build and render HTML table of prescriptions with file viewer links
function renderPrescriptions(list) {
  const container = document.getElementById('prescriptionsContainer');
  if (!container) return;

  const arr = list || window.PETVET_INITIAL_DATA.prescriptions;
  if (!arr || arr.length === 0) {
    container.innerHTML = '<p>No prescriptions.</p>';
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
            <th>Veterinarian</th>
            <th>Medications</th>
            <th>Notes</th>
            <th>Reports</th>
          </tr>
        </thead>
        <tbody>
  `;

  arr.forEach(r => {
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

    // Display multiple medications
    let medicationsHtml = '';
    if (r.medications && r.medications.length > 0) {
      medicationsHtml = r.medications.map(med => 
        `<div><strong>${med.medication}</strong>: ${med.dosage}</div>`
      ).join('');
    } else {
      medicationsHtml = '-';
    }

    html += `
      <tr>
        <td>${r.date || r.created_at || ''}</td>
        <td>${r.petName || r.pet_name || ''}</td>
        <td>${r.ownerName || r.owner_name || ''}</td>
        <td>${r.vet_name || '-'}</td>
        <td>${medicationsHtml}</td>
        <td>${r.notes || ''}</td>
        <td>${reportsHtml || '-'}</td>
      </tr>
    `;
  });

  html += '</tbody></table></div>';
  container.innerHTML = html;
}

// Initialize page, load data, setup filters, and form handlers
document.addEventListener('DOMContentLoaded', () => {
  const d = window.PETVET_INITIAL_DATA;
  if (!d) return;
  const currentVetId = Number(window.PETVET_CURRENT_VET_ID || 0);

  const url = new URL(window.location.href);
  const from = url.searchParams.get('from');
  const apptId = url.searchParams.get('appointment');

  const search = document.getElementById('searchBar');
  let basePrescriptions = d.prescriptions || [];
  let onlyMyRecords = false;

  // Apply search and vet filter then re-render table
  const applyFiltersAndRender = () => {
    const q = (search?.value || '').toLowerCase();

    const filtered = basePrescriptions.filter(p => {
      if (onlyMyRecords && currentVetId && Number(p.vet_id || 0) !== currentVetId) {
        return false;
      }

      if (!q) return true;
      return Object.values(p).some(v => String(v).toLowerCase().includes(q));
    });

    renderPrescriptions(filtered);
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

    const toggle = document.getElementById('myVetOnlyToggle');
    if (toggle) {
      toggle.addEventListener('change', () => {
        onlyMyRecords = toggle.checked;
        applyFiltersAndRender();
      });
    }
  };

  // Determine context and load appropriate prescriptions and form state
  if (from === 'ongoing' && apptId) {
    showForm(true, apptId);
    // Show all prescriptions for this pet
    const appt = d.appointments.find(a => String(a.id) === String(apptId));
    if(appt){
      const petName = appt.pet_name || appt.petName;
      basePrescriptions = d.prescriptions.filter(p => (p.petName || p.pet_name) === petName);
    } else {
      basePrescriptions = d.prescriptions || [];
    }
  }
  else if (from === 'completed' && apptId) {
    showForm(false);
    basePrescriptions = d.prescriptions.filter(p => String(p.appointment_id) === String(apptId));
  }
  else {
    showForm(false);
    basePrescriptions = d.prescriptions || [];
  }

  renderMyRecordsToggle();
  applyFiltersAndRender();

  // Attach search input listener for real-time filtering
  if (search) {
    search.addEventListener('input', () => {
      applyFiltersAndRender();
    });
  }

  // Dynamic medication rows
  let medicationRowCounter = 1;

  // Add new medication input row to form
  window.addMedicationRow = function() {
    const container = document.getElementById('medicationsContainer');
    const newRow = document.createElement('div');
    newRow.className = 'medication-row';
    newRow.setAttribute('data-row', medicationRowCounter);
    newRow.innerHTML = `
      <div class="form-row">
        <label style="flex: 2;">
          Medication
          <input type="text" name="medications[${medicationRowCounter}][medication]" required placeholder="Enter medication name">
        </label>
        <label style="flex: 2;">
          Dosage
          <input type="text" name="medications[${medicationRowCounter}][dosage]" required placeholder="e.g., 10mg twice daily">
        </label>
        <button type="button" class="btn-remove" onclick="removeMedicationRow(${medicationRowCounter})">Remove</button>
      </div>
    `;
    container.appendChild(newRow);
    medicationRowCounter++;
  };

  // Remove medication input row from form
  window.removeMedicationRow = function(rowId) {
    const rows = document.querySelectorAll('.medication-row');
    if (rows.length > 1) {
      const row = document.querySelector(`.medication-row[data-row="${rowId}"]`);
      if (row) row.remove();
    } else {
      alert('At least one medication is required.');
    }
  };

  // Handle form submission with medications collection and file upload
  const form = document.getElementById('prescriptionForm');
  if (form) {
    // Display preview of selected files before upload
    const fileInput = form.querySelector('input[type="file"]');
    const filePreview = document.getElementById('filePreview');
    
    if (fileInput && filePreview) {
      // Update preview when files are selected
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

    // Submit form data with medications and files to API endpoint
    form.addEventListener('submit', async e => {
      e.preventDefault();

      const formData = new FormData();
      formData.append('appointment_id', form.elements['appointmentId'].value);
      formData.append('notes', form.elements['notes'].value);

      // Collect all medications from form rows
      const medications = [];
      document.querySelectorAll('.medication-row').forEach(row => {
        const medInput = row.querySelector('input[name*="[medication]"]');
        const dosInput = row.querySelector('input[name*="[dosage]"]');
        if (medInput && dosInput && medInput.value && dosInput.value) {
          medications.push({
            medication: medInput.value,
            dosage: dosInput.value
          });
        }
      });
      formData.append('medications', JSON.stringify(medications));

      // Add file attachments to request
      const files = form.elements['reports[]'].files;
      for (let i = 0; i < files.length; i++) {
        formData.append('reports[]', files[i]);
      }

      try {
        const res = await fetch('/PETVET/api/vet/prescriptions/add.php', {
          method: 'POST',
          body: formData
        });

        const json = await res.json();

        if (json.success) {
          alert('Prescription saved successfully.');
          location.reload();
        } else {
          alert(json.error || 'Failed to save prescription.');
        }
      } catch (err) {
        console.error(err);
        alert('Error saving prescription.');
      }
    });
  }
});
