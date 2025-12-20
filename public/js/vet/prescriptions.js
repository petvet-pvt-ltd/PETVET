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
            <th>ID</th>
            <th>Date</th>
            <th>Pet</th>
            <th>Owner</th>
            <th>Medication</th>
            <th>Dosage</th>
            <th>Notes</th>
          </tr>
        </thead>
        <tbody>
  `;

  arr.forEach(r => {
    html += `
      <tr>
        <td>${r.id}</td>
        <td>${r.date || r.created_at || ''}</td>
        <td>${r.petName || r.pet_name || ''}</td>
        <td>${r.ownerName || r.owner_name || ''}</td>
        <td>${r.medication || ''}</td>
        <td>${r.dosage || ''}</td>
        <td>${r.notes || ''}</td>
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
    renderPrescriptions(
      d.prescriptions.filter(p => String(p.appointment_id) === String(apptId))
    );
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
    form.addEventListener('submit', async e => {
      e.preventDefault();

      const payload = {
        appointment_id: form.elements['appointmentId'].value,
        medication: form.elements['medication'].value,
        dosage: form.elements['dosage'].value,
        notes: form.elements['notes'].value
      };

      try {
        const res = await fetch('/PETVET/api/vet/prescriptions/add.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
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
