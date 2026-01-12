<?php
$GLOBALS['currentPage'] = 'prescriptions.php';
$GLOBALS['module'] = 'vet';

$data = [
    'appointments'   => $appointments ?? [],
    'prescriptions'  => $prescriptions ?? []
];
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
  <?php include __DIR__ . '/../shared/components/user-welcome-header.php'; ?>

  <div class="page-frame">
    <div class="page-header">
      <div>
        <h1 class="page-title">Prescriptions</h1>
        <p class="page-subtitle">Manage patient medications and prescriptions</p>
      </div>
    </div>

    <section id="prescriptionFormSection" class="form-section" style="display:none">
      <h3>Add Prescription</h3>
      <form id="prescriptionForm">
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
            Medications
          </label>
        </div>

        <div id="medicationsContainer">
          <div class="medication-row" data-row="0">
            <div class="form-row">
              <label style="flex: 2;">
                Medication
                <input type="text" name="medications[0][medication]" required placeholder="Enter medication name">
              </label>
              <label style="flex: 2;">
                Dosage
                <input type="text" name="medications[0][dosage]" required placeholder="e.g., 10mg twice daily">
              </label>
              <button type="button" class="btn-remove" onclick="removeMedicationRow(0)">Remove</button>
            </div>
          </div>
        </div>

        <button type="button" class="btn secondary" onclick="addMedicationRow()" style="margin-bottom: 15px;">➕ Add Another Medication</button>

        <div class="form-row">
          <label>
            Notes
            <textarea name="notes" rows="3" placeholder="Additional instructions or notes..."></textarea>
          </label>
        </div>

        <div class="form-row">
          <label>
            Reports & Documents
            <input type="file" name="reports[]" multiple accept="image/*,.pdf,.doc,.docx,.txt">
            <small>Upload prescription images, pharmacy documents, medication guides, etc.</small>
          </label>
        </div>

        <div id="filePreview" class="file-preview"></div>

        <button class="btn secondary" type="submit">💊 Save Prescription</button>
      </form>
    </section>

    <section>
      <h3>Prescription Records</h3>
      <input id="searchBar" placeholder="Search prescriptions by pet, medication, or owner...">
      <div id="prescriptionsContainer" class="table-wrap"></div>
    </section>
  </div>
</div>

<script>
window.PETVET_INITIAL_DATA = <?php echo json_encode($data, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
</script>
<script src="/PETVET/public/js/vet/file-viewer-modal.js"></script>
<script src="/PETVET/public/js/vet/prescriptions.js"></script>
</body>
</html>
