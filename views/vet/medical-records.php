<?php
// Initialize page context and prepare data for medical records view
$GLOBALS['currentPage'] = 'medical-records.php';
$GLOBALS['module'] = 'vet';

// Prepare data array with appointments, records, prescriptions, and vaccinations
$data = [
    'appointments'   => $appointments ?? [],
    'medicalRecords' => $medicalRecords ?? [],
    'prescriptions'  => $prescriptions ?? [],
    'vaccinations'   => $vaccinations ?? []
];

// Check if returning from ongoing appointments view
$showBackToOngoing = isset($_GET['from']) && $_GET['from'] === 'ongoing';
?>
<!-- Medical Records Management Interface -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Medical Records | Veterinarian</title>
  <!-- Medical records styling -->
  <link rel="stylesheet" href="/PETVET/public/css/vet/enhanced-vet.css">
</head>
<body>

<!-- Include sidebar navigation -->
<?php include 'views/shared/sidebar/sidebar.php'; ?>

<!-- Main content area -->
<div class="main-content">
  <?php include __DIR__ . '/../shared/components/user-welcome-header.php'; ?>

  <!-- Page header with title and description -->
  <div class="page-frame">
    <div class="page-header">
      <div>
        <h1 class="page-title">Medical Records</h1>
        <p class="page-subtitle">Manage patient medical history and records</p>
      </div>
    </div>

    <!-- Form section for adding new medical records (hidden by default) -->
    <section id="formSection" class="form-section" style="display:none">
      <h3>Add Medical Record</h3>
      <!-- Medical record details form -->
      <form id="medicalRecordForm">
        <!-- Hidden appointment ID reference -->
        <input type="hidden" name="appointmentId">
        <!-- Pet and owner information (read-only) -->
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

        <!-- Medical information inputs -->
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

        <!-- Treatment plan input -->
        <div class="form-row">
          <label>
            Treatment
            <textarea name="treatment" rows="3" required placeholder="Describe the treatment plan..."></textarea>
          </label>
        </div>

        <!-- Medical documents and reports upload -->
        <div class="form-row">
          <label>
            Reports & Documents
            <input type="file" name="reports[]" multiple accept="image/*,.pdf,.doc,.docx,.txt">
            <small>Upload medical reports, images, lab results, X-rays, etc. (Multiple files allowed)</small>
          </label>
        </div>

        <!-- File preview area for uploaded documents -->
        <div id="filePreview" class="file-preview"></div>

        <!-- Submit button for saving record -->
        <button class="btn primary" type="submit">💾 Save Record</button>
      </form>

      <?php if ($showBackToOngoing): ?>
        <!-- Back button to ongoing appointments -->
        <div style="margin-top: 12px;">
          <a class="btn navy" href="/PETVET/?module=vet&page=dashboard#ongoing-section">← Back to Ongoing Appointment</a>
        </div>
      <?php endif; ?>
    </section>

    <!-- Medical records list section with search -->
    <section>
      <h3>Medical Records</h3>
      <!-- Search bar for filtering records -->
      <input id="searchBar" placeholder="Search records by pet, owner, phone, or diagnosis...">
      <!-- Records table container -->
      <div id="recordsContainer" class="table-wrap"></div>
    </section>
  </div>
</div>

<!-- Pass data to JavaScript for client-side operations -->
<script>
window.PETVET_INITIAL_DATA = <?php echo json_encode($data, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
window.PETVET_CURRENT_VET_ID = <?php echo json_encode((int)($vet['id'] ?? 0)); ?>;
</script>
<!-- File viewer modal and medical records functionality -->
<script src="/PETVET/public/js/vet/file-viewer-modal.js"></script>
<script src="/PETVET/public/js/vet/medical-records.js"></script>
</body>
</html>
