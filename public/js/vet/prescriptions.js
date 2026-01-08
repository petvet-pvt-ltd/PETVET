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
            <th>Medication</th>
            <th>Dosage</th>
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
          reportsHtml = files.map(f => {
            const filename = f.split('/').pop();
            const ext = filename.split('.').pop().toLowerCase();
            const icon = ['jpg','jpeg','png','gif','webp'].includes(ext) ? '🖼️' : '📄';
            return `<a href="/PETVET/${f}" target="_blank" title="${filename}">${icon}</a>`;
          }).join(' ');
        }
      } catch(e) {
        reportsHtml = '';
      }
    }

    html += `
      <tr>
        <td>${r.date || r.created_at || ''}</td>
        <td>${r.petName || r.pet_name || ''}</td>
        <td>${r.ownerName || r.owner_name || ''}</td>
        <td>${r.medication || ''}</td>
        <td>${r.dosage || ''}</td>
        <td>${r.notes || ''}</td>
        <td>${reportsHtml || '-'}</td>
      </tr>
    `;
  });

  html += '</tbody></table></div>';
  container.innerHTML = html;
}

document.addEventListener('DOMContentLoaded', () => {
  const d = window.PETVET_INITIAL_DATA;
  if (!d) return;

  const url = new URL(window.location.href);
  const from = url.searchParams.get('from');
  const apptId = url.searchParams.get('appointment');

  if (from === 'ongoing' && apptId) {
    showForm(true, apptId);
    // Show all prescriptions for this pet
    const appt = d.appointments.find(a => String(a.id) === String(apptId));
    if(appt){
      const petName = appt.pet_name || appt.petName;
      renderPrescriptions(
        d.prescriptions.filter(p => (p.petName || p.pet_name) === petName)
      );
    } else {
      renderPrescriptions();
    }
  }
  else if (from === 'completed' && apptId) {
    showForm(false);
    renderPrescriptions(
      d.prescriptions.filter(p => String(p.appointment_id) === String(apptId))
    );
  }
  else {
    showForm(false);
    renderPrescriptions();
  }

  const search = document.getElementById('searchBar');
  if (search) {
    search.addEventListener('input', () => {
      const q = search.value.toLowerCase();
      const filtered = d.prescriptions.filter(p =>
        Object.values(p).some(v => String(v).toLowerCase().includes(q))
      );
      renderPrescriptions(filtered);
    });
  }

  const form = document.getElementById('prescriptionForm');
  if (form) {
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
      formData.append('medication', form.elements['medication'].value);
      formData.append('dosage', form.elements['dosage'].value);
      formData.append('notes', form.elements['notes'].value);

      // Add files
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
