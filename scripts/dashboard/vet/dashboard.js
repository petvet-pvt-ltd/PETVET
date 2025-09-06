document.addEventListener("DOMContentLoaded", () => {

  // Collect all table rows
  let appointmentRows = Array.from(document.querySelectorAll("#appointmentsTable tr"));

  const ongoingSection = document.querySelector(".appointment.ongoing");

  // Render ongoing appointment in the top section
  function renderOngoing(row) {
    if (!row) {
      ongoingSection.innerHTML = `
        <h3>Ongoing Appointment</h3>
        <p style="color:gray;">No more appointments today ✅</p>
      `;
      return;
    }

    const cells = row.querySelectorAll("td");
    const id = cells[0].textContent; // <-- actual ID
    const time = cells[1].textContent;
    const pet = cells[2].textContent;
    const owner = cells[3].textContent;
    const reason = cells[4].textContent;

    ongoingSection.innerHTML = `
      <h3>Ongoing Appointment</h3>
      <br/>
      <p><b>ID:</b> ${id}</p>
      <p><b>Time:</b> ${time}</p>
      <p><b>Pet:</b> ${pet}</p>
      <p><b>Owner:</b> ${owner}</p>
      <p><b>Reason:</b> ${reason}</p>
      <div class="actions">
        <button id="recordBtn" class="btn navy">Record</button>
        <button id="prescriptionBtn" class="btn navy">Prescription</button>
        <button id="completeBtn" class="btn navy">Complete</button>
        <button id="cancelBtn" class="btn red">Cancel</button>
      </div>
    `;

    attachOngoingHandlers(row);
  }

  // Attach actions for ongoing appointment buttons
  function attachOngoingHandlers(row) {
    const recordBtn = document.getElementById("recordBtn");
    const prescriptionBtn = document.getElementById("prescriptionBtn");
    const completeBtn = document.getElementById("completeBtn");
    const cancelBtn = document.getElementById("cancelBtn");

    if (recordBtn) recordBtn.addEventListener("click", () => {
      const appointmentId = row.querySelector("td:first-child").textContent; // actual ID
      window.location.href = `medical-records.php?appointment_id=${appointmentId}`;
    });

    if (prescriptionBtn) prescriptionBtn.addEventListener("click", () => {
      const appointmentId = row.querySelector("td:first-child").textContent; // actual ID
      window.location.href = `prescriptions.php?appointment_id=${appointmentId}`;
    });

    if (completeBtn) {
      completeBtn.addEventListener("click", () => {
        if (confirm("Are you sure you want to mark this appointment as COMPLETE?")) {
          row.remove();
          updateAppointments();
          renderOngoing(appointmentRows.shift());
        }
      });
    }

    if (cancelBtn) {
      cancelBtn.addEventListener("click", () => {
        if (confirm("Are you sure you want to CANCEL this appointment?")) {
          row.remove();
          updateAppointments();
          renderOngoing(appointmentRows.shift());
        }
      });
    }
  }

  // Attach cancel buttons for table rows
  function attachTableHandlers() {
    document.querySelectorAll(".cancel-btn").forEach(btn => {
      btn.addEventListener("click", function () {
        const row = this.closest("tr");
        if (!row) return;

        if (confirm("Are you sure you want to CANCEL this appointment?")) {
          row.remove();
          updateAppointments();
        }
      });
    });
  }

  // Update appointmentRows array after table changes
  function updateAppointments() {
    appointmentRows = Array.from(document.querySelectorAll("#appointmentsTable tr"));
    appointmentRows.forEach((row, i) => row.dataset.index = i); // update dataset.index
  }

  // Search bar filter
  const searchBar = document.getElementById("searchBar");
  if (searchBar) {
    searchBar.addEventListener("keyup", function () {
      const filter = this.value.toLowerCase();
      document.querySelectorAll("#appointmentsTable tr").forEach(row => {
        const cells = row.getElementsByTagName("td");
        const match = Array.from(cells).some(cell =>
          cell.textContent.toLowerCase().includes(filter)
        );
        row.style.display = match ? "" : "none";
      });
    });
  }

  // === Initial setup ===
  appointmentRows.forEach((row, i) => row.dataset.index = i); // set dataset.index
  renderOngoing(appointmentRows.shift()); // first appointment becomes ongoing
  attachTableHandlers(); // attach table cancel buttons
});
