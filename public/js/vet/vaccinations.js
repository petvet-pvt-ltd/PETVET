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
            <th>Appointment ID</th>
            <th>Pet</th>
            <th>Owner</th>
            <th>Vaccine</th>
            <th>Next Due</th>
            <th>Created</th>
          </tr>
        </thead>
        <tbody>
          ${list.map(v => `
            <tr>
              <td>${v.appointment_id || v.appointmentId}</td>
              <td>${v.pet_name || v.petName || ""}</td>
              <td>${v.owner_name || v.ownerName || ""}</td>
              <td>${v.vaccine || ""}</td>
              <td>${v.next_due || "-"}</td>
              <td>${v.created_at || v.date || ""}</td>
            </tr>
          `).join("")}
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

  if ((from === "ongoing" || from === "completed") && apptId) {
    // Only show form for ongoing
    showForm(from === "ongoing", apptId);

    // Filter list by appointment
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
    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      const payload = {
        appointmentId: form.elements["appointmentId"].value,
        vaccine: form.elements["vaccine"].value.trim(),
        nextDue: form.elements["nextDue"].value
      };

      try {
        const res = await fetch(`/PETVET/api/vet/vaccinations/add.php`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload)
        });

        const json = await res.json();

        if (!json.success) {
          alert(json.message || "Error saving vaccination");
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
