<?php
$GLOBALS['currentPage'] = 'appointments.php';
$GLOBALS['module'] = 'vet';

// IMPORTANT FIX: ongoing may be a single associative row → wrap to array
$ongoingArr = [];
if (!empty($ongoing)) {
  $ongoingArr = is_array($ongoing) && isset($ongoing[0]) ? $ongoing : [$ongoing];
}

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
  <link rel="stylesheet" href="/PETVET/public/css/vet/enhanced-vet.css">
</head>
<body>

<?php include 'views/shared/sidebar/sidebar.php'; ?>

<div class="main-content">
  <div class="page-frame">
    <div class="page-header">
      <div>
        <h1 class="page-title">Appointments</h1>
        <p class="page-subtitle">Manage your patient appointments</p>
      </div>
    </div>

    <section>
      <h3>Ongoing Appointments</h3>
      <input id="searchBarOngoing" placeholder="Search...">
      <div id="ongoingTableContainer" class="table-wrap"></div>
    </section>

    <section>
      <h3>Upcoming Appointments</h3>
      <input id="searchBarUpcoming" placeholder="Search...">
      <div id="upcomingTableContainer" class="table-wrap"></div>
    </section>

    <section>
      <h3>Completed Appointments</h3>
      <input id="searchBarCompleted" placeholder="Search...">
      <div id="completedTableContainer" class="table-wrap"></div>
    </section>

    <section>
      <h3>Cancelled Appointments</h3>
      <input id="searchBarCancelled" placeholder="Search...">
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
