<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'vet'; // Set the module for sidebar detection
$GLOBALS['currentPage'] = 'vaccinations.php'; // Set current page for sidebar active state
$GLOBALS['module'] = 'vet'; // Set global module for sidebar
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vaccinations | Veterinarian</title>
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
          <h1 class="page-title">Vaccinations</h1>
          <p class="page-subtitle">Manage patient vaccination records and schedules</p>
        </div>
      </div>

      <!-- Vaccination Form -->
      <section id="vaccFormSection" class="form-section" style="display:none">
        <h3>Add Vaccination</h3>
        <form id="vaccinationForm">
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
              Vaccine
              <input name="vaccine" required placeholder="Enter vaccine name">
            </label>
            <label>
              Next Due Date
              <input type="date" name="nextDue" required>
            </label>
          </div>
          <button class="btn success" type="submit">💉 Save Vaccination</button>
        </form>
      </section>

      <!-- Vaccinations Records Section -->
      <section>
        <h3>Vaccination Records</h3>
        <input id="searchBar" placeholder="Search vaccinations by pet, vaccine, or owner...">
        <div id="vaccinationsContainer" class="table-wrap"></div>
      </section>
    </div>
  </div>

  <script>
    window.PETVET_INITIAL_DATA = <?php echo json_encode([
      'appointments' => $appointments,
      'vaccinations' => $vaccinations
    ]); ?>;
  </script>
  <script src="/PETVET/public/js/vet/vaccinations.js"></script>
</body>
</html>