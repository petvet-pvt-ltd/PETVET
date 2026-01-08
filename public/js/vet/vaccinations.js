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
            <th>Vaccine</th>
            <th>Next Due</th>
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
            return `
              <tr>
                <td>${v.date || v.created_at || ""}</td>
                <td>${v.pet_name || v.petName || ""}</td>
                <td>${v.owner_name || v.ownerName || ""}</td>
                <td>${v.vaccine || ""}</td>
                <td>${v.next_due || "-"}</td>
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
      formData.append('vaccine', form.elements["vaccine"].value.trim());
      formData.append('nextDue', form.elements["nextDue"].value);

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
