<?php
$GLOBALS['currentPage'] = 'vaccinations.php';
$GLOBALS['module'] = 'vet';

$data = [
    'appointments'  => $appointments ?? [],
    'vaccinations'  => $vaccinations ?? []
];
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
  <div class="page-frame">
    <div class="page-header">
      <div>
        <h1 class="page-title">Vaccinations</h1>
        <p class="page-subtitle">Manage patient vaccination records and schedules</p>
      </div>
    </div>

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
            <input type="date" name="nextDue">
          </label>
        </div>

        <button class="btn success" type="submit">💉 Save Vaccination</button>
      </form>
    </section>

    <section>
      <h3>Vaccination Records</h3>
      <input id="searchBar" placeholder="Search vaccinations by pet, vaccine, or owner...">
      <div id="vaccinationsContainer" class="table-wrap"></div>
    </section>
  </div>
</div>

<script>
window.PETVET_INITIAL_DATA = <?php echo json_encode($data, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
</script>
<script src="/PETVET/public/js/vet/vaccinations.js"></script>
</body>
</html>
