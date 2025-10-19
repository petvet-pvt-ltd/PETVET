<?php
$currentPage = basename($_SERVER['PHP_SELF']);

// Set user role for shared components
$userRole = 'receptionist';

// Include the shared appointments model
require_once __DIR__ . '/../../models/SharedAppointmentsModel.php';

// Initialize the model
$appointmentsModel = new SharedAppointmentsModel();

// Get filter parameters
$selectedVet = $_GET['vet'] ?? 'all';
$view = $_GET['view'] ?? 'today';

// Get data from model
$appointments = $appointmentsModel->getAppointments($selectedVet);
$pendingAppointments = $appointmentsModel->getPendingAppointments();
$vetNames = $appointmentsModel->getVetNames();
$weekDays = $appointmentsModel->getWeekDates();
$monthDays = $appointmentsModel->getMonthDates();
$moduleName = $appointmentsModel->getModuleName($userRole);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Appointments | Receptionist</title>
  <link rel="stylesheet" href="/PETVET/public/css/clinic-manager/enhanced-global.css">
  <link rel="stylesheet" href="/PETVET/public/css/clinic-manager/appointments.css">
  <link rel="stylesheet" href="/PETVET/public/css/shared/appointments.css">
  <link rel="stylesheet" href="/PETVET/public/css/receptionist/appointments-receptionist.css">
</head>
<body>

<div class="main-content">
  <div class="page-header">
    <div>
      <h1 class="page-title">Appointments</h1>
      <p class="page-subtitle">Manage and schedule patient appointments</p>
    </div>
    <div>
      <button class="btn btn-primary" onclick="openAddModal()">
        ➕ New Appointment
      </button>
    </div>
  </div>

  <!-- Two Column Layout -->
  <div class="appointments-layout">
    <!-- Left: Pending Appointments -->
    <aside class="pending-appointments-sidebar">
      <div class="pending-header">
        <h3>Pending Requests</h3>
        <span class="pending-count"><?= count($pendingAppointments) ?></span>
      </div>
      
      <div class="pending-list">
        <?php if (empty($pendingAppointments)): ?>
          <div class="empty-state">
            <div class="empty-icon">📭</div>
            <p>No pending requests</p>
          </div>
        <?php else: ?>
          <?php foreach ($pendingAppointments as $pending): ?>
            <div class="pending-card" data-id="<?= $pending['id'] ?>">
              <div class="pending-card-header">
                <div class="pending-pet-info">
                  <span class="pet-name"><?= htmlspecialchars($pending['pet']) ?></span>
                  <span class="pet-type"><?= htmlspecialchars($pending['pet_type']) ?></span>
                </div>
                <span class="pending-time-ago"><?= timeAgo($pending['requested_at']) ?></span>
              </div>
              
              <div class="pending-card-body">
                <div class="pending-detail">
                  <span class="label">Owner:</span>
                  <span class="value"><?= htmlspecialchars($pending['owner']) ?></span>
                </div>
                <div class="pending-detail">
                  <span class="label">Type:</span>
                  <span class="value"><?= htmlspecialchars($pending['appointment_type']) ?></span>
                </div>
                <div class="pending-detail">
                  <span class="label">Date:</span>
                  <span class="value"><?= date('M j, Y', strtotime($pending['requested_date'])) ?> at <?= date('g:i A', strtotime($pending['requested_time'])) ?></span>
                </div>
                <div class="pending-detail">
                  <span class="label">Vet:</span>
                  <span class="value"><?= htmlspecialchars($pending['requested_vet']) ?></span>
                </div>
              </div>
              
              <div class="pending-card-actions">
                <button class="btn-accept" onclick="acceptAppointment(<?= $pending['id'] ?>)">
                  ✓ Accept
                </button>
                <button class="btn-decline" onclick="declineAppointment(<?= $pending['id'] ?>)">
                  ✕ Decline
                </button>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </aside>

    <!-- Right: Calendar View -->
    <main class="calendar-section">
      <?php
        // Include complete shared appointments component
        include __DIR__ . '/../shared/appointments/appointments-complete.php';
      ?>
    </main>
  </div>
</div>

<script src="/PETVET/public/js/shared/appointments.js"></script>
<script src="/PETVET/public/js/receptionist/appointments-receptionist.js"></script>
</body>
</html>

<?php
// Helper function for time ago
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    return floor($diff / 86400) . 'd ago';
}
?>