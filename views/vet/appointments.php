<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'vet'; // Set the module for sidebar detection
$GLOBALS['currentPage'] = 'appointments.php'; // Set current page for sidebar active state
$GLOBALS['module'] = 'vet'; // Set global module for sidebar
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Appointments | Veterinarian</title>
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
          <h1 class="page-title">Appointments</h1>
          <p class="page-subtitle">Manage your patient appointments</p>
        </div>
      </div>

      <section>
        <h3>Ongoing Appointments</h3>
        <input id="searchBar" data-target="ongoingTableContainer" placeholder="Search by pet, owner, or details...">
        <div id="ongoingTableContainer" class="table-wrap"></div>
      </section>

      <section>
        <h3>Upcoming Appointments</h3>
        <input id="searchBar" data-target="upcomingTableContainer" placeholder="Search by pet, owner, or details...">
        <div id="upcomingTableContainer" class="table-wrap"></div>
      </section>

      <section>
        <h3>Completed Appointments</h3>
        <input id="searchBar" data-target="completedTableContainer" placeholder="Search by pet, owner, or details...">
        <div id="completedTableContainer" class="table-wrap"></div>
      </section>

      <section>
        <h3>Cancelled Appointments</h3>
        <input id="searchBar" data-target="cancelledTableContainer" placeholder="Search by pet, owner, or details...">
        <div id="cancelledTableContainer" class="table-wrap"></div>
      </section>
    </div>
  </div>

  <script>
    window.PETVET_INITIAL_DATA = <?php echo json_encode([
      'appointments' => $appointments,
      'medicalRecords' => $medicalRecords,
      'prescriptions' => $prescriptions,
      'vaccinations' => $vaccinations
    ]); ?>;
    window.PETVET_TODAY='2025-10-12';
  </script>
  <script src="/PETVET/public/js/vet/appointments.js"></script>
</body>
</html>