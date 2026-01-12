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
  <?php include __DIR__ . '/../shared/components/user-welcome-header.php'; ?>

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
        <input type="hidden" name="appointmentId">
        <div class="form-row">
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
            Vaccines
          </label>
        </div>

        <div id="vaccinesContainer">
          <div class="vaccine-row" data-row="0">
            <div class="form-row">
              <label style="flex: 2;">
                Vaccine
                <input type="text" name="vaccines[0][vaccine]" required placeholder="Enter vaccine name">
              </label>
              <label style="flex: 2;">
                Next Due Date
                <input type="date" name="vaccines[0][nextDue]">
              </label>
              <button type="button" class="btn-remove" onclick="removeVaccineRow(0)">Remove</button>
            </div>
          </div>
        </div>

        <button type="button" class="btn secondary" onclick="addVaccineRow()" style="margin-bottom: 15px;">➕ Add Another Vaccine</button>

        <div class="form-row">
          <label>
            Reports & Documents
            <input type="file" name="reports[]" multiple accept="image/*,.pdf,.doc,.docx,.txt">
            <small>Upload vaccination certificates, batch information, medical images, etc.</small>
          </label>
        </div>

        <div id="filePreview" class="file-preview"></div>

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
<script src="/PETVET/public/js/vet/file-viewer-modal.js"></script>
<script src="/PETVET/public/js/vet/vaccinations.js"></script>
</body>
</html>
