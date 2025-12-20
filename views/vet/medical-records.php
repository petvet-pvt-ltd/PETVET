<?php
$GLOBALS['currentPage'] = 'medical-records.php';
$GLOBALS['module'] = 'vet';

$data = [
    'appointments'   => $appointments ?? [],
    'medicalRecords' => $medicalRecords ?? [],
    'prescriptions'  => $prescriptions ?? [],
    'vaccinations'   => $vaccinations ?? []
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Medical Records | Veterinarian</title>
  <link rel="stylesheet" href="/PETVET/public/css/vet/enhanced-vet.css">
</head>
<body>

<?php include 'views/shared/sidebar/sidebar.php'; ?>

<div class="main-content">
  <?php include __DIR__ . '/../shared/components/user-welcome-header.php'; ?>

  <div class="page-frame">
    <div class="page-header">
      <div>
        <h1 class="page-title">Medical Records</h1>
        <p class="page-subtitle">Manage patient medical history and records</p>
      </div>
    </div>

    <section id="formSection" class="form-section" style="display:none">
      <h3>Add Medical Record</h3>
      <form id="medicalRecordForm">
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
            Symptoms
            <textarea name="symptoms" rows="3" required placeholder="Describe the symptoms observed..."></textarea>
          </label>
          <label>
            Diagnosis
            <textarea name="diagnosis" rows="3" required placeholder="Enter your diagnosis..."></textarea>
          </label>
        </div>

        <div class="form-row">
          <label>
            Treatment
            <textarea name="treatment" rows="3" required placeholder="Describe the treatment plan..."></textarea>
          </label>
        </div>

        <button class="btn primary" type="submit">💾 Save Record</button>
      </form>
    </section>

    <section>
      <h3>Medical Records</h3>
      <input id="searchBar" placeholder="Search records by pet, owner, or diagnosis...">
      <div id="recordsContainer" class="table-wrap"></div>
    </section>
  </div>
</div>

<script>
window.PETVET_INITIAL_DATA = <?php echo json_encode($data, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
</script>
<script src="/PETVET/public/js/vet/medical-records.js"></script>
</body>
</html>
