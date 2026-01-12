document.addEventListener("DOMContentLoaded", () => {
  const data = window.PETVET_INITIAL_DATA ?? {};
  const appointments = data.appointments || [];
  let vaccinations = data.vaccinations || [];

  const container = document.getElementById("vaccinationsContainer");
  const searchBar = document.getElementById("searchBar");
  const formSection = document.getElementById("vaccFormSection");
  const form = document.getElementById("vaccinationForm");

  function renderVaccinations(list) {
    if (!container) return;

    if (!list || !list.length) {
      container.innerHTML = `<p class="empty-text">No vaccination records found.</p>`;
      return;
    }

    container.innerHTML = `
      <div class="simple-mobile-table">
      <table class="data-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Pet</th>
            <th>Owner</th>
            <th>Vaccines</th>
            <th>Reports</th>
          </tr>
        </thead>
        <tbody>
          ${list.map(v => {
            let reportsHtml = '';
            if (v.reports) {
              try {
                const files = JSON.parse(v.reports);
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

            // Display multiple vaccines
            let vaccinesHtml = '';
            if (v.vaccines && v.vaccines.length > 0) {
              vaccinesHtml = v.vaccines.map(vac => 
                `<div><strong>${vac.vaccine}</strong>${vac.next_due ? ` (Next: ${vac.next_due})` : ''}</div>`
              ).join('');
            } else {
              vaccinesHtml = '-';
            }

            return `
              <tr>
                <td>${v.date || v.created_at || ""}</td>
                <td>${v.pet_name || v.petName || ""}</td>
                <td>${v.owner_name || v.ownerName || ""}</td>
                <td>${vaccinesHtml}</td>
                <td>${reportsHtml || '-'}</td>
              </tr>
            `;
          }).join("")}
        </tbody>
      </table>
      </div>
    `;
  }

  function showForm(prefill, apptId) {
    if (!formSection || !form) return;

    if (!prefill) {
      formSection.style.display = "none";
      return;
    }

    const appt = appointments.find(a => String(a.id) === String(apptId));
    if (!appt) {
      alert("Appointment not found");
      formSection.style.display = "none";
      return;
    }

    formSection.style.display = "block";
    form.elements["appointmentId"].value = appt.id;
    form.elements["petName"].value = appt.pet_name || "";
    form.elements["ownerName"].value = appt.owner_name || "";
  }

  // ✅ URL MODE
  const url = new URL(window.location.href);
  const from = url.searchParams.get("from");
  const apptId = url.searchParams.get("appointment");

  if (from === "ongoing" && apptId) {
    showForm(true, apptId);
    // Show all vaccinations for this pet
    const appt = appointments.find(a => String(a.id) === String(apptId));
    if(appt){
      const petName = appt.pet_name || appt.petName;
      const filtered = vaccinations.filter(v => (v.pet_name || v.petName) === petName);
      renderVaccinations(filtered);
    } else {
      renderVaccinations(vaccinations);
    }
  } else if (from === "completed" && apptId) {
    showForm(false);
    const filtered = vaccinations.filter(v => String(v.appointment_id) === String(apptId));
    renderVaccinations(filtered);
  } else {
    showForm(false);
    renderVaccinations(vaccinations);
  }

  // Search
  if (searchBar) {
    searchBar.addEventListener("input", (e) => {
      const q = e.target.value.toLowerCase();
      const filtered = vaccinations.filter(v =>
        String(v.pet_name || "").toLowerCase().includes(q) ||
        String(v.owner_name || "").toLowerCase().includes(q) ||
        String(v.vaccine || "").toLowerCase().includes(q)
      );
      renderVaccinations(filtered);
    });
  }

  // ✅ Dynamic vaccine rows
  let vaccineRowCounter = 1;

  window.addVaccineRow = function() {
    const container = document.getElementById('vaccinesContainer');
    const newRow = document.createElement('div');
    newRow.className = 'vaccine-row';
    newRow.setAttribute('data-row', vaccineRowCounter);
    newRow.innerHTML = `
      <div class="form-row">
        <label style="flex: 2;">
          Vaccine
          <input type="text" name="vaccines[${vaccineRowCounter}][vaccine]" required placeholder="Enter vaccine name">
        </label>
        <label style="flex: 2;">
          Next Due Date
          <input type="date" name="vaccines[${vaccineRowCounter}][nextDue]">
        </label>
        <button type="button" class="btn-remove" onclick="removeVaccineRow(${vaccineRowCounter})">Remove</button>
      </div>
    `;
    container.appendChild(newRow);
    vaccineRowCounter++;
  };

  window.removeVaccineRow = function(rowId) {
    const rows = document.querySelectorAll('.vaccine-row');
    if (rows.length > 1) {
      const row = document.querySelector(`.vaccine-row[data-row="${rowId}"]`);
      if (row) row.remove();
    } else {
      alert('At least one vaccine is required.');
    }
  };

  // Save
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

    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      const formData = new FormData();
      formData.append('appointmentId', form.elements["appointmentId"].value);

      // Collect all vaccines
      const vaccines = [];
      document.querySelectorAll('.vaccine-row').forEach(row => {
        const vacInput = row.querySelector('input[name*="[vaccine]"]');
        const dueInput = row.querySelector('input[name*="[nextDue]"]');
        if (vacInput && vacInput.value) {
          vaccines.push({
            vaccine: vacInput.value,
            nextDue: dueInput ? dueInput.value : ''
          });
        }
      });
      formData.append('vaccines', JSON.stringify(vaccines));

      // Add files
      const files = form.elements['reports[]'].files;
      for (let i = 0; i < files.length; i++) {
        formData.append('reports[]', files[i]);
      }

      try {
        const res = await fetch(`/PETVET/api/vet/vaccinations/add.php`, {
          method: "POST",
          body: formData
        });

        const json = await res.json();

        if (!json.success) {
          alert(json.message || json.error || "Error saving vaccination");
          return;
        }

        alert("Vaccination successfully saved!");
        location.reload();
      } catch (err) {
        console.error(err);
        alert("A server error occurred");
      }
    });
  }
});
