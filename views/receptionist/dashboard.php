<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | Receptionist</title>
  <link rel="stylesheet" href="/PETVET/public/css/clinic-manager/enhanced-global.css">
  <style>
    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 24px;
      margin-bottom: 32px;
    }
    
    .stat-card {
      background: white;
      border-radius: var(--border-radius-lg);
      padding: 24px;
      box-shadow: var(--shadow-sm);
      border-left: 4px solid var(--primary);
    }
    
    .stat-number {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--primary);
      margin-bottom: 8px;
    }
    
    .stat-label {
      color: var(--gray-600);
      font-weight: 600;
      margin-bottom: 4px;
    }
    
    .stat-description {
      color: var(--gray-500);
      font-size: 14px;
    }
    
    .appointments-today {
      background: white;
      border-radius: var(--border-radius-lg);
      padding: 24px;
      box-shadow: var(--shadow-sm);
    }
    
    .section-title {
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--gray-900);
      margin-bottom: 16px;
    }
    
    .appointment-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px 0;
      border-bottom: 1px solid var(--gray-100);
    }
    
    .appointment-item:last-child {
      border-bottom: none;
    }
    
    .appointment-info {
      flex: 1;
    }
    
    .appointment-time {
      font-weight: 600;
      color: var(--primary);
      font-size: 14px;
    }
    
    .appointment-details {
      color: var(--gray-600);
      font-size: 14px;
      margin-top: 2px;
    }
    
    .appointment-vet {
      color: var(--gray-500);
      font-size: 12px;
      margin-top: 2px;
    }
    
    .quick-actions {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;
      margin-top: 32px;
    }
    
    .action-card {
      background: white;
      border-radius: var(--border-radius-lg);
      padding: 20px;
      box-shadow: var(--shadow-sm);
      text-align: center;
      cursor: pointer;
      transition: var(--transition);
      border: 2px solid transparent;
    }
    
    .action-card:hover {
      border-color: var(--primary);
      transform: translateY(-2px);
    }
    
    .action-icon {
      font-size: 2rem;
      margin-bottom: 12px;
    }
    
    .action-title {
      font-weight: 600;
      color: var(--gray-900);
      margin-bottom: 4px;
    }
    
    .action-description {
      color: var(--gray-600);
      font-size: 14px;
    }
    
    .no-appointments {
      text-align: center;
      color: var(--gray-500);
      font-style: italic;
      padding: 40px;
    }
  </style>
</head>
<body>

<div class="main-content">
  <div class="page-header">
    <div>
      <h1 class="page-title">Receptionist Dashboard</h1>
      <p class="page-subtitle">Welcome back! Here's what's happening today.</p>
    </div>
  </div>

  <!-- Stats Grid -->
  <div class="dashboard-grid">
    <div class="stat-card">
      <div class="stat-number"><?= $totalToday ?></div>
      <div class="stat-label">Today's Appointments</div>
      <div class="stat-description">Scheduled for <?= date('F j, Y') ?></div>
    </div>
    
    <div class="stat-card">
      <div class="stat-number"><?= $upcomingWeek ?></div>
      <div class="stat-label">This Week</div>
      <div class="stat-description">Total appointments next 7 days</div>
    </div>
    
    <div class="stat-card">
      <div class="stat-number"><?= count(array_filter($todayAppointments, function($appt) { 
        return strtotime($appt['time']) > time(); 
      })) ?></div>
      <div class="stat-label">Remaining Today</div>
      <div class="stat-description">Appointments still to come</div>
    </div>
  </div>

  <!-- Today's Appointments -->
  <div class="appointments-today">
    <h2 class="section-title">Today's Schedule</h2>
    
    <?php if (empty($todayAppointments)): ?>
      <div class="no-appointments">
        No appointments scheduled for today.
      </div>
    <?php else: ?>
      <?php foreach($todayAppointments as $appt): ?>
        <div class="appointment-item">
          <div class="appointment-info">
            <div class="appointment-time"><?= date('g:i A', strtotime($appt['time'])) ?></div>
            <div class="appointment-details"><?= htmlspecialchars($appt['client']) ?> - <?= htmlspecialchars($appt['pet']) ?></div>
            <div class="appointment-vet">with <?= htmlspecialchars($appt['vet']) ?></div>
          </div>
          <div>
            <button class="btn btn-sm btn-primary" onclick="viewAppointment('<?= htmlspecialchars($appt['pet']) ?>', '<?= htmlspecialchars($appt['client']) ?>', '<?= htmlspecialchars($appt['vet']) ?>', '<?= date('Y-m-d') ?>', '<?= $appt['time'] ?>')">
              View Details
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- Quick Actions -->
  <div class="quick-actions">
    <div class="action-card" onclick="openAddModal()">
      <div class="action-icon">📅</div>
      <div class="action-title">New Appointment</div>
      <div class="action-description">Schedule a new patient visit</div>
    </div>
    
    <div class="action-card" onclick="window.location.href='?module=receptionist&page=appointments'">
      <div class="action-icon">📋</div>
      <div class="action-title">View Calendar</div>
      <div class="action-description">See all appointments</div>
    </div>
    
    <div class="action-card" onclick="searchAppointments()">
      <div class="action-icon">🔍</div>
      <div class="action-title">Search</div>
      <div class="action-description">Find specific appointments</div>
    </div>
    
    <div class="action-card" onclick="emergencyMode()">
      <div class="action-icon">🚨</div>
      <div class="action-title">Emergency</div>
      <div class="action-description">Handle urgent cases</div>
    </div>
  </div>

</div>

<!-- Include appointment modals -->
<?php include __DIR__ . '/../shared/appointments/appointment-modals.php'; ?>

<script>
function viewAppointment(pet, client, vet, date, time) {
  openDetailsModal(pet, client, vet, date, time);
}

function searchAppointments() {
  // Simple search functionality
  const query = prompt('Enter client name, pet name, or veterinarian to search:');
  if (query) {
    // In a real app, this would search the database
    alert('Search functionality would be implemented here for: ' + query);
  }
}

function emergencyMode() {
  // Emergency appointment scheduling
  if (confirm('Schedule emergency appointment? This will take priority over existing appointments.')) {
    openAddModal();
    // Pre-fill form with emergency defaults
    document.getElementById('appointmentReason').value = 'Emergency';
    document.getElementById('appointmentDate').value = new Date().toISOString().split('T')[0];
  }
}
</script>

</body>
</html>