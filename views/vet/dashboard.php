<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'vet'; // Set the module for sidebar detection
$GLOBALS['currentPage'] = 'dashboard.php'; // Set current page for sidebar active state
$GLOBALS['module'] = 'vet'; // Set global module for sidebar

$data = $dashboardData; // This comes from the controller

// server-side: expose data to JS for initialization
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PetVet — Overview</title>
  <link rel="stylesheet" href="/PETVET/public/css/vet/dashboard.css">
</head>
<body>
  <?php include 'views/shared/sidebar/sidebar.php'; ?>
  
  <div class="main-content">
    <header class="dashboard-header">
      <h2>Welcome, Dr. <?php echo htmlspecialchars(isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Smith'); ?></h2>
      <div class="date"><?php echo date("l, F j, Y", strtotime('2025-10-12')); ?></div>
    </header>

    <div class="cards">
      <div class="card">
        <h3 id="kpi-today">—</h3>
        <p>Appointments Today</p>
      </div>
      <div class="card">
        <h3 id="kpi-total">—</h3>
        <p>Total Appointments</p>
      </div>
    </div>

    <section id="ongoing-section">
      <h3>Ongoing Appointment</h3>
      <div id="ongoing-container"><!-- filled by JS --></div>
    </section>

    <section>
      <h3>Today's Upcoming Appointments</h3>
      <input id="searchBar" placeholder="Search by pet, owner, reason...">
      <div class="simple-mobile-table" style="overflow:auto">
        <table id="upcomingTable"><thead><tr><th>ID</th><th>Time</th><th>Pet</th><th>Owner</th><th>Reason</th><th>Action</th></tr></thead>
        <tbody></tbody></table>
      </div>
    </section>
  </div>

  <script>window.PETVET_INITIAL_DATA = <?php echo json_encode($data, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>; window.PETVET_TODAY='2025-10-12';</script>
  <script src="/PETVET/public/js/vet/dashboard.js"></script>
</body>
</html>