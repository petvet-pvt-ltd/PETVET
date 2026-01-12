<?php
// Extract data passed from controller
$pet = $pet ?? [];
$clinic_visits = $clinic_visits ?? [];
$medical_records = $medical_records ?? [];
$vaccinations = $vaccinations ?? [];
$prescriptions = $prescriptions ?? [];

// Pet info with fallbacks
$petName = htmlspecialchars($pet['name'] ?? 'Unknown');
$species = htmlspecialchars($pet['species'] ?? '');
$breed = htmlspecialchars($pet['breed'] ?? '');
$age = htmlspecialchars($pet['age'] ?? 'N/A');
$microchip = !empty($pet['microchip_id']);
$vaccinated = count($vaccinations) > 0;

// Get latest vaccination date
$lastVaccination = 'N/A';
if (!empty($vaccinations)) {
    $latest = $vaccinations[0];
    $lastVaccination = htmlspecialchars($latest['date'] ?? 'N/A');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Medical Records - <?php echo $petName; ?></title>
  <link rel="stylesheet" href="/PETVET/public/css/pet-owner/medical-records.css"/>
</head>
<body>
  <main class="main-content">

  <!-- Header -->
  <header class="top-card">
    <div>
      <h1 id="petName"><?php echo $petName; ?></h1>
      <p id="petInfo"><?php echo $species . ($breed ? ' • ' . $breed : '') . ' • ' . $age . 'y'; ?></p>
      <?php if ($microchip): ?><span class="badge microchip">Microchipped</span><?php endif; ?>
      <?php if ($vaccinated): ?><span class="badge vaccine">Vaccinated</span><?php endif; ?>
    </div>
  </header>

  <!-- Overview -->
  <div class="section">
    <h2>Overview</h2>
    <p><b>Last Vaccination:</b> <?php echo $lastVaccination; ?></p>
    <p><b>Medical Records:</b> <?php echo count($medical_records); ?> records on file</p>
    <p><b>Prescriptions:</b> <?php echo count($prescriptions); ?> prescriptions</p>
    <p><b>Clinic Visits:</b> <?php echo count($clinic_visits); ?> visits</p>
  </div>

  <!-- Clinic Visits & Appointments -->
  <div class="section">
    <h2>Clinic Visits & Appointments</h2>
    <?php if (empty($clinic_visits)): ?>
      <p class="empty-state">No clinic visits or appointments recorded yet.</p>
    <?php else: ?>
      <?php
      // Separate appointments by status and date
      $upcoming = [];
      $ongoing = [];
      $completed = [];
      $today = date('Y-m-d');
      
      foreach ($clinic_visits as $visit) {
        $status = strtolower($visit['status'] ?? '');
        $appointmentDate = $visit['appointment_date'] ?? '';
        
        // Upcoming: pending/approved AND date is today or in the future
        if (in_array($status, ['pending', 'approved']) && $appointmentDate >= $today) {
          $upcoming[] = $visit;
        } 
        // Ongoing
        elseif ($status === 'ongoing') {
          $ongoing[] = $visit;
        } 
        // Completed
        elseif ($status === 'completed') {
          $completed[] = $visit;
        }
        // Old approved appointments (date passed) are treated as completed
        elseif (in_array($status, ['approved']) && $appointmentDate < $today) {
          // Don't show old approved appointments that never happened
        }
      }
      ?>

      <!-- Upcoming Appointments -->
      <?php if (!empty($upcoming)): ?>
        <h3 style="margin-top: 20px; color: #2563eb;">📅 Upcoming Appointments</h3>
        <div class="simple-mobile-table">
          <table>
            <thead>
              <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Type</th>
                <th>Vet</th>
                <th>Status</th>
                <th>Symptoms</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($upcoming as $visit): ?>
              <tr>
                <td><?php echo htmlspecialchars($visit['appointment_date'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($visit['appointment_time'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($visit['appointment_type'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($visit['vet_name'] ?? 'N/A'); ?></td>
                <td><span class="status-<?php echo strtolower($visit['status'] ?? ''); ?>"><?php echo htmlspecialchars($visit['status'] ?? ''); ?></span></td>
                <td><?php echo htmlspecialchars($visit['symptoms'] ?? '-'); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <!-- Ongoing Appointments -->
      <?php if (!empty($ongoing)): ?>
        <h3 style="margin-top: 20px; color: #f59e0b;">🏥 Ongoing Appointments</h3>
        <div class="simple-mobile-table">
          <table>
            <thead>
              <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Type</th>
                <th>Vet</th>
                <th>Status</th>
                <th>Diagnosis</th>
                <th>Treatment</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($ongoing as $visit): ?>
              <tr>
                <td><?php echo htmlspecialchars($visit['appointment_date'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($visit['appointment_time'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($visit['appointment_type'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($visit['vet_name'] ?? 'N/A'); ?></td>
                <td><span class="status-<?php echo strtolower($visit['status'] ?? ''); ?>"><?php echo htmlspecialchars($visit['status'] ?? ''); ?></span></td>
                <td><?php echo htmlspecialchars($visit['diagnosis'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($visit['treatment'] ?? '-'); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <!-- Completed Appointments -->
      <?php if (!empty($completed)): ?>
        <h3 style="margin-top: 20px; color: #10b981;">✅ Completed Appointments</h3>
        <div class="simple-mobile-table">
          <table>
            <thead>
              <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Type</th>
                <th>Vet</th>
                <th>Diagnosis</th>
                <th>Treatment</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($completed as $visit): ?>
              <tr>
                <td><?php echo htmlspecialchars($visit['appointment_date'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($visit['appointment_time'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($visit['appointment_type'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($visit['vet_name'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($visit['diagnosis'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($visit['treatment'] ?? '-'); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>

  <!-- Medical Records -->
  <div class="section">
    <h2>Medical Records</h2>
    <?php if (empty($medical_records)): ?>
      <p class="empty-state">No medical records available.</p>
    <?php else: ?>
      <div class="simple-mobile-table">
        <table>
          <thead>
            <tr>
              <th>Date</th>
              <th>Symptoms</th>
              <th>Diagnosis</th>
              <th>Treatment</th>
              <th>Vet</th>
              <th>Reports</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($medical_records as $record): ?>
            <tr>
              <td><?php echo htmlspecialchars($record['date'] ?? ''); ?></td>
              <td><?php echo htmlspecialchars($record['symptoms'] ?? ''); ?></td>
              <td><?php echo htmlspecialchars($record['diagnosis'] ?? ''); ?></td>
              <td><?php echo htmlspecialchars($record['treatment'] ?? ''); ?></td>
              <td><?php echo htmlspecialchars($record['vet_name'] ?? 'N/A'); ?></td>
              <td>
                <?php
                if (!empty($record['reports'])) {
                  $files = json_decode($record['reports'], true);
                  if ($files && is_array($files)) {
                    foreach ($files as $file) {
                      $filename = basename($file);
                      $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                      $icon = in_array($ext, ['jpg','jpeg','png','gif','webp']) ? '🖼️' : '📄';
                      echo '<a href="/PETVET/' . htmlspecialchars($file) . '" target="_blank" title="' . htmlspecialchars($filename) . '">' . $icon . '</a> ';
                    }
                  }
                } else {
                  echo '-';
                }
                ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <!-- Vaccinations -->
  <div class="section">
    <h2>Vaccinations</h2>
    <?php if (empty($vaccinations)): ?>
      <p class="empty-state">No vaccination records available.</p>
    <?php else: ?>
      <div class="simple-mobile-table">
        <table>
          <thead>
            <tr>
              <th>Date</th>
              <th>Vaccines</th>
              <th>Vet</th>
              <th>Reports</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($vaccinations as $vax): ?>
            <tr>
              <td><?php echo htmlspecialchars($vax['date'] ?? ''); ?></td>
              <td>
                <?php 
                if (!empty($vax['vaccines']) && is_array($vax['vaccines'])) {
                  foreach ($vax['vaccines'] as $v) {
                    $nextDue = !empty($v['next_due']) ? ' (Next: ' . htmlspecialchars($v['next_due']) . ')' : '';
                    echo '<div><strong>' . htmlspecialchars($v['vaccine'] ?? '') . '</strong>' . $nextDue . '</div>';
                  }
                } else {
                  echo '-';
                }
                ?>
              </td>
              <td><?php echo htmlspecialchars($vax['vet_name'] ?? 'N/A'); ?></td>
              <td>
                <?php
                if (!empty($vax['reports'])) {
                  $files = json_decode($vax['reports'], true);
                  if ($files && is_array($files)) {
                    foreach ($files as $file) {
                      $filename = basename($file);
                      $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                      $icon = in_array($ext, ['jpg','jpeg','png','gif','webp']) ? '🖼️' : '📄';
                      echo '<a href="/PETVET/' . htmlspecialchars($file) . '" target="_blank" title="' . htmlspecialchars($filename) . '">' . $icon . '</a> ';
                    }
                  }
                } else {
                  echo '-';
                }
                ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <!-- Prescriptions -->
  <div class="section">
    <h2>Prescriptions</h2>
    <?php if (empty($prescriptions)): ?>
      <p class="empty-state">No prescription records available.</p>
    <?php else: ?>
      <div class="simple-mobile-table">
        <table>
          <thead>
            <tr>
              <th>Date</th>
              <th>Medications</th>
              <th>Notes</th>
              <th>Vet</th>
              <th>Reports</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($prescriptions as $rx): ?>
            <tr>
              <td><?php echo htmlspecialchars($rx['date'] ?? ''); ?></td>
              <td>
                <?php 
                if (!empty($rx['medications']) && is_array($rx['medications'])) {
                  foreach ($rx['medications'] as $med) {
                    echo '<div><strong>' . htmlspecialchars($med['medication'] ?? '') . '</strong>: ' . htmlspecialchars($med['dosage'] ?? '') . '</div>';
                  }
                } else {
                  echo '-';
                }
                ?>
              </td>
              <td><?php echo htmlspecialchars($rx['notes'] ?? ''); ?></td>
              <td><?php echo htmlspecialchars($rx['vet_name'] ?? 'N/A'); ?></td>
              <td>
                <?php
                if (!empty($rx['reports'])) {
                  $files = json_decode($rx['reports'], true);
                  if ($files && is_array($files)) {
                    foreach ($files as $file) {
                      $filename = basename($file);
                      $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                      $icon = in_array($ext, ['jpg','jpeg','png','gif','webp']) ? '🖼️' : '📄';
                      echo '<a href="/PETVET/' . htmlspecialchars($file) . '" target="_blank" title="' . htmlspecialchars($filename) . '">' . $icon . '</a> ';
                    }
                  }
                } else {
                  echo '-';
                }
                ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  </main>
</body>
</html>
