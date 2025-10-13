<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'vet'; // Set the module for sidebar detection
$GLOBALS['currentPage'] = 'medical-records.php'; // Set current page for sidebar active state
$GLOBALS['module'] = 'vet'; // Set global module for sidebar
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Medical Records</title>
  <link rel="stylesheet" href="/PETVET/public/css/vet/dashboard.css">
</head>
<body>
  <?php include 'views/shared/sidebar/sidebar.php'; ?>
  
  <div class="main-content">
    <header class="dashboard-header"><h2>Medical Records</h2></header>

    <!-- Form (visible only when from=ongoing) -->
    <section id="formSection" style="display:none">
      <h3>Add Medical Record</h3>
      <form id="medicalRecordForm">
        <div class="form-row">
          <label>Appointment ID<input name="appointmentId" readonly></label>
          <label>Pet name<input name="petName" readonly></label>
          <label>Owner<input name="ownerName" readonly></label>
        </div>
        <div class="form-row">
          <label>Symptoms<textarea name="symptoms" rows="2" required></textarea></label>
          <label>Diagnosis<textarea name="diagnosis" rows="2" required></textarea></label>
        </div>
        <div class="form-row">
          <label>Treatment<textarea name="treatment" rows="2" required></textarea></label>
        </div>
        <button class="btn navy" type="submit">Save Record</button>
      </form>
    </section>

    <section>
      <h3>Records</h3>
      <input id="searchBar" placeholder="Search records...">
      <div id="recordsContainer"></div>
    </section>
  </div>

  <script>
    window.PETVET_INITIAL_DATA = <?php echo json_encode([
      'appointments' => $appointments,
      'medicalRecords' => $medicalRecords,
      'prescriptions' => $prescriptions,
      'vaccinations' => $vaccinations
    ]); ?>;
  </script>
  <script src="/PETVET/public/js/vet/medical-records.js"></script>
</body>
</html>