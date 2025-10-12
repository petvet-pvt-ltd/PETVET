<?php
session_start();
if (!isset($_SESSION['user_name'])) $_SESSION['user_name'] = 'Dr. Smith';
include '../sidebar.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Vaccinations</title>
  <link rel="stylesheet" href="../../styles/dashboard/vet/dashboard.css">
</head>
<body>
  <div class="main-content">
    <header class="dashboard-header"><h2>Vaccinations</h2></header>

    <!-- Vaccination Form -->
    <section id="vaccFormSection" style="display:none">
      <h3>Add Vaccination</h3>
      <form id="vaccinationForm">
        <div class="form-row">
          <label>Appointment ID<input name="appointmentId" readonly></label>
          <label>Pet Name<input name="petName" readonly></label>
          <label>Owner<input name="ownerName" readonly></label>
        </div>
        <div class="form-row">
          <label>Vaccine<input name="vaccine" required></label>
          <label>Next Due<input type="date" name="nextDue" required></label>
        </div>
        <button class="btn green" type="submit">Save Vaccination</button>
      </form>
    </section>

    <!-- Vaccinations Records Section -->
    <section>
      <h3>Vaccination Records</h3>
      <input id="searchBar" placeholder="Search vaccinations...">
      <div id="vaccinationsContainer"></div>
    </section>
  </div>

  <script>
  window.PETVET_INITIAL_DATA = {
    appointments: [
      { id: 1, pet: "Bella", owner: "John Doe", status: "Ongoing", date: "2025-10-10" },
      { id: 2, pet: "Max", owner: "Jane Smith", status: "Completed", date: "2025-09-28" },
      { id: 3, pet: "Luna", owner: "Chris Brown", status: "Cancelled", date: "2025-09-15" },
      { id: 4, pet: "Charlie", owner: "Emily Clark", status: "Upcoming", date: "2025-10-20" },
      { id: 5, pet: "Lucy", owner: "Michael Adams", status: "Completed", date: "2025-09-25" },
      { id: 6, pet: "Cooper", owner: "Olivia Harris", status: "Completed", date: "2025-09-22" },
      { id: 7, pet: "Daisy", owner: "William Moore", status: "Ongoing", date: "2025-10-11" },
      { id: 8, pet: "Milo", owner: "Sophia Lee", status: "Upcoming", date: "2025-10-18" },
      { id: 9, pet: "Rocky", owner: "James Scott", status: "Completed", date: "2025-09-10" },
      { id: 10, pet: "Molly", owner: "Ava Taylor", status: "Completed", date: "2025-09-05" }
    ],
    vaccinations: [
      { appointmentId: 2, pet: "Max", owner: "Jane Smith", vaccine: "Rabies", nextDue: "2026-09-28" },
      { appointmentId: 5, pet: "Lucy", owner: "Michael Adams", vaccine: "Parvovirus", nextDue: "2026-09-25" },
      { appointmentId: 6, pet: "Cooper", owner: "Olivia Harris", vaccine: "Distemper", nextDue: "2026-09-22" },
      { appointmentId: 9, pet: "Rocky", owner: "James Scott", vaccine: "Hepatitis", nextDue: "2026-09-10" },
      { appointmentId: 10, pet: "Molly", owner: "Ava Taylor", vaccine: "Leptospirosis", nextDue: "2026-09-05" }
    ]
  };
  </script>

  <script src="../../scripts/dashboard/vet/vaccinations.js"></script>
</body>
</html>
