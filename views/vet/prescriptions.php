<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'vet'; // Set the module for sidebar detection
$GLOBALS['currentPage'] = 'prescriptions.php'; // Set current page for sidebar active state
$GLOBALS['module'] = 'vet'; // Set global module for sidebar
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Prescriptions</title>
  <link rel="stylesheet" href="/PETVET/public/css/vet/dashboard.css">
</head>
<body>
  <?php include 'views/shared/sidebar/sidebar.php'; ?>
  
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
    window.PETVET_INITIAL_DATA = <?php echo json_encode([
      'appointments' => $appointments,
      'prescriptions' => $prescriptions
    ]); ?>;
  </script>
  <script src="/PETVET/public/js/vet/prescriptions.js"></script>
</body>
</html>