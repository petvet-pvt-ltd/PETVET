<?php
session_start();
if (!isset($_SESSION['user_name'])) $_SESSION['user_name'] = 'Dr. Smith';
include '../sidebar.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Prescriptions</title>
  <link rel="stylesheet" href="../../styles/dashboard/vet/dashboard.css">
</head>
<body>
  <div class="main-content">
    <header class="dashboard-header"><h2>Prescriptions</h2></header>

    <!-- Prescription Form -->
    <section id="prescriptionFormSection" style="display:none">
      <h3>Add Prescription</h3>
      <form id="prescriptionForm">
        <div class="form-row">
          <label>Appointment ID<input name="appointmentId" readonly></label>
          <label>Pet Name<input name="petName" readonly></label>
          <label>Owner<input name="ownerName" readonly></label>
        </div>
        <div class="form-row">
          <label>Medication<input name="medication" required></label>
          <label>Dosage<input name="dosage" required></label>
        </div>
        <div class="form-row">
          <label>Notes<textarea name="notes" rows="2"></textarea></label>
        </div>
        <button class="btn blue" type="submit">Save Prescription</button>
      </form>
    </section>

    <!-- Prescription Records Section -->
    <section>
      <h3>Prescriptions</h3>
      <input id="searchBar" placeholder="Search prescriptions...">
      <div id="prescriptionsContainer"></div>
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
    prescriptions: [
      { appointmentId: 2, pet: "Max", owner: "Jane Smith", medication: "Amoxicillin", dosage: "2x/day", notes: "7 days course" },
      { appointmentId: 5, pet: "Lucy", owner: "Michael Adams", medication: "Metronidazole", dosage: "1x/day", notes: "After meals" },
      { appointmentId: 6, pet: "Cooper", owner: "Olivia Harris", medication: "Prednisone", dosage: "1/2 tab/day", notes: "For inflammation" },
      { appointmentId: 9, pet: "Rocky", owner: "James Scott", medication: "Cefalexin", dosage: "2x/day", notes: "10 days" },
      { appointmentId: 10, pet: "Molly", owner: "Ava Taylor", medication: "Carprofen", dosage: "1x/day", notes: "Pain management" }
    ]
  };
  </script>

  <script src="../../scripts/dashboard/vet/prescriptions.js"></script>
</body>
</html>
