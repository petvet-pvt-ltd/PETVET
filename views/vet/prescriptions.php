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
  <title>Prescriptions | Veterinarian</title>
  <link rel="stylesheet" href="/PETVET/public/css/vet/enhanced-vet.css">
</head>
<body>
  <?php include 'views/shared/sidebar/sidebar.php'; ?>
  
  <div class="main-content">
    <?php 
    // Include user welcome header
    include __DIR__ . '/../shared/components/user-welcome-header.php'; 
    ?>
    
    <div class="page-frame">
      <div class="page-header">
        <div>
          <h1 class="page-title">Prescriptions</h1>
          <p class="page-subtitle">Manage patient medications and prescriptions</p>
        </div>
      </div>

      <!-- Prescription Form -->
      <section id="prescriptionFormSection" class="form-section" style="display:none">
        <h3>Add Prescription</h3>
        <form id="prescriptionForm">
          <div class="form-row">
            <label>
              Appointment ID
              <input name="appointmentId" readonly>
            </label>
            <label>
              Pet Name
              <input name="petName" readonly>
            </label>
            <label>
              Owner
              <input name="ownerName" readonly>
            </label>
          </div>
          <div class="form-row">
            <label>
              Medication
              <input name="medication" required placeholder="Enter medication name">
            </label>
            <label>
              Dosage
              <input name="dosage" required placeholder="e.g., 10mg twice daily">
            </label>
          </div>
          <div class="form-row">
            <label>
              Notes
              <textarea name="notes" rows="3" placeholder="Additional instructions or notes..."></textarea>
            </label>
          </div>
          <button class="btn secondary" type="submit">💊 Save Prescription</button>
        </form>
      </section>

      <!-- Prescription Records Section -->
      <section>
        <h3>Prescription Records</h3>
        <input id="searchBar" placeholder="Search prescriptions by pet, medication, or owner...">
        <div id="prescriptionsContainer" class="table-wrap"></div>
      </section>
    </div>
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