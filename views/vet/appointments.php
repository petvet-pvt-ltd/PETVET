<?php
// Initialize page context for vet appointments view
$GLOBALS['currentPage'] = 'appointments.php';
$GLOBALS['module'] = 'vet';

// Normalize ongoing appointment to array format for consistency
$ongoingArr = [];
if (!empty($ongoing)) {
  $ongoingArr = is_array($ongoing) && isset($ongoing[0]) ? $ongoing : [$ongoing];
}

// Prepare appointment data from controller for JavaScript
$data = [
    'ongoing'   => $ongoingArr,
    'upcoming'  => $upcoming ?? [],
    'completed' => $completed ?? [],
    'cancelled' => $cancelled ?? [],
    'medicalRecords' => $medicalRecords ?? [],
    'prescriptions'  => $prescriptions ?? [],
    'vaccinations'   => $vaccinations ?? [],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Appointments | Veterinarian</title>
  <!-- Appointment management styling -->
  <link rel="stylesheet" href="/PETVET/public/css/vet/enhanced-vet.css">
</head>
<body>

<?php include 'views/shared/sidebar/sidebar.php'; ?>

<div class="main-content">
  <?php include __DIR__ . '/../shared/components/user-welcome-header.php'; ?>

  <div class="page-frame">
    <div class="page-header">
      <div>
        <h1 class="page-title">Appointments</h1>
        <p class="page-subtitle">Manage your patient appointments</p>
      </div>
    </div>

    <section>
      <h3>Ongoing Appointments</h3>
      <input id="searchBarOngoing" placeholder="Search by pet, owner, or phone...">
      <div id="ongoingTableContainer" class="table-wrap"></div>
    </section>

    <section>
      <h3>Upcoming Appointments</h3>
      <input id="searchBarUpcoming" placeholder="Search by pet, owner, or phone...">
      <div id="upcomingTableContainer" class="table-wrap"></div>
    </section>

    <section>
      <h3>Completed Appointments</h3>
      <input id="searchBarCompleted" placeholder="Search by pet, owner, or phone...">
      <div id="completedTableContainer" class="table-wrap"></div>
    </section>

    <section>
      <h3>Cancelled Appointments</h3>
      <input id="searchBarCancelled" placeholder="Search by pet, owner, or phone...">
      <div id="cancelledTableContainer" class="table-wrap"></div>
    </section>
  </div>
</div>

<script>
window.PETVET_INITIAL_DATA = <?php echo json_encode($data, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
window.PETVET_TODAY = "<?php echo date('Y-m-d'); ?>";
</script>
<script src="/PETVET/public/js/vet/appointments.js"></script>
</body>
</html>
