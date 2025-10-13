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
  <title>Appointments</title>
  <link rel="stylesheet" href="/PETVET/public/css/vet/dashboard.css">
</head>
<body>
  <?php include 'views/shared/sidebar/sidebar.php'; ?>
  
  <div class="main-content">
    <header class="dashboard-header">
      <h2>Appointments</h2>
    </header>

    <section>
      <h3>Ongoing Appointments</h3>
      <input id="searchBar" data-target="ongoingTableContainer" placeholder="Search ...">
      <div id="ongoingTableContainer"></div>
    </section>

    <section>
      <h3>Upcoming Appointments</h3>
      <input id="searchBar" data-target="upcomingTableContainer" placeholder="Search ...">
      <div id="upcomingTableContainer"></div>
    </section>

    <section>
      <h3>Completed Appointments</h3>
      <input id="searchBar" data-target="completedTableContainer" placeholder="Search ...">
      <div id="completedTableContainer"></div>
    </section>

    <section>
      <h3>Cancelled Appointments</h3>
      <input id="searchBar" data-target="cancelledTableContainer" placeholder="Search ...">
      <div id="cancelledTableContainer"></div>
    </section>
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