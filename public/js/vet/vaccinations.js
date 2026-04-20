// Initialize page, load data, setup filters, and form handlers
document.addEventListener("DOMContentLoaded", () => {
  const data = window.PETVET_INITIAL_DATA ?? {};
  const currentVetId = Number(window.PETVET_CURRENT_VET_ID || 0);
  const appointments = data.appointments || [];
  let vaccinations = data.vaccinations || [];

  const container = document.getElementById("vaccinationsContainer");
  const searchBar = document.getElementById("searchBar");
  const formSection = document.getElementById("vaccFormSection");
  const form = document.getElementById("vaccinationForm");

  let baseVaccinations = vaccinations;
  let onlyMyRecords = false;

  // Build and render HTML table of vaccination records with file viewer links
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
            <th>Veterinarian</th>
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
                <td>${v.vet_name || '-'}</td>
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

  // Show or hide vaccination form and prefill with appointment data if provided
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

  // Determine context and load appropriate vaccinations and form state
  const url = new URL(window.location.href);
  const from = url.searchParams.get("from");
  const apptId = url.searchParams.get("appointment");

  // Apply search and vet filter then re-render table
  const applyFiltersAndRender = () => {
    const q = (searchBar?.value || '').toLowerCase();

    const filtered = baseVaccinations.filter(v => {
      if (onlyMyRecords && currentVetId && Number(v.vet_id || 0) !== currentVetId) {
        return false;
      }

      if (!q) return true;
      return Object.values(v).some(val => String(val).toLowerCase().includes(q));
    });

    renderVaccinations(filtered);
  };

  // Create toggle checkbox to filter records by current veterinarian
  const renderMyRecordsToggle = () => {
    console.log('renderMyRecordsToggle called: from=' + from + ', apptId=' + apptId + ', searchBar=' + !!searchBar);
    if (from !== 'ongoing' || !apptId) {
      console.log('Toggle skipped: from !== ongoing or no apptId');
      return;
    }
    if (!searchBar || !searchBar.parentNode) {
      console.log('Toggle skipped: searchBar not found or no parentNode');
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

    searchBar.parentNode.insertBefore(wrap, searchBar.nextSibling);
    console.log('Toggle inserted successfully');

    const toggle = document.getElementById('myVetOnlyToggle');
    if (toggle) {
      toggle.addEventListener('change', () => {
        onlyMyRecords = toggle.checked;
        applyFiltersAndRender();
      });
    }
  };

  if (from === "ongoing" && apptId) {
    showForm(true, apptId);
    // Show all vaccinations for this pet
    const appt = appointments.find(a => String(a.id) === String(apptId));
    if(appt){
      const petName = appt.pet_name || appt.petName;
      baseVaccinations = vaccinations.filter(v => (v.pet_name || v.petName) === petName);
    } else {
      baseVaccinations = vaccinations;
    }
  } else if (from === "completed" && apptId) {
    showForm(false);
    baseVaccinations = vaccinations.filter(v => String(v.appointment_id) === String(apptId));
  } else {
    showForm(false);
    baseVaccinations = vaccinations;
  }

  renderMyRecordsToggle();
  applyFiltersAndRender();

  // Attach search input listener for real-time filtering
  if (searchBar) {
    searchBar.addEventListener("input", () => {
      applyFiltersAndRender();
    });
  }

  // Dynamic vaccine rows
  let vaccineRowCounter = 1;

  // Add new vaccine input row to form
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

  // Remove vaccine input row from form
  window.removeVaccineRow = function(rowId) {
    const rows = document.querySelectorAll('.vaccine-row');
    if (rows.length > 1) {
      const row = document.querySelector(`.vaccine-row[data-row="${rowId}"]`);
      if (row) row.remove();
    } else {
      alert('At least one vaccine is required.');
    }
  };

  // Handle form submission with vaccines collection and file upload
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

    // Submit form data with vaccines and files to API endpoint
    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      const formData = new FormData();
      formData.append('appointmentId', form.elements["appointmentId"].value);

      // Collect all vaccines from form rows
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

      // Add file attachments to request
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
