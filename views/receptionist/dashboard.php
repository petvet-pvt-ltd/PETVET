<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'receptionist';
$GLOBALS['currentPage'] = 'dashboard.php';
$GLOBALS['module'] = 'receptionist';
date_default_timezone_set('Asia/Colombo');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | Receptionist</title>
  <link rel="stylesheet" href="/PETVET/public/css/receptionist/dashboard.css">
</head>
<body>
<?php include __DIR__ . '/../shared/sidebar/sidebar.php'; ?>

<main class="main-content">
  <div class="dashboard-header">
    <div class="welcome-section">
      <h1 class="welcome-name">Welcome, <?php echo htmlspecialchars($userName ?: 'Receptionist'); ?>!</h1>
      <p class="welcome-date">Today is <?php echo date('l, F j, Y'); ?></p>
    </div>
    <?php if ($clinicName): ?>
      <div class="clinic-badge">
        <span class="clinic-icon">🏥</span>
        <span class="clinic-name"><?php echo htmlspecialchars($clinicName); ?></span>
      </div>
    <?php endif; ?>
  </div>

  <!-- Stats Grid - Two Cards in a Row -->
  <div class="stats-grid">
    <div class="stat-card stat-pending">
      <div class="stat-icon">⏳</div>
      <div class="stat-content">
        <div class="stat-number"><?php echo $pendingCount; ?></div>
        <div class="stat-label">Pending Appointments</div>
      </div>
    </div>
    
    <div class="stat-card stat-ongoing">
      <div class="stat-icon">👨‍⚕️</div>
      <div class="stat-content">
        <div class="stat-number"><?php echo $ongoingCount; ?></div>
        <div class="stat-label">Ongoing Appointments</div>
      </div>
    </div>
  </div>

  <!-- Two Column Layout: Ongoing Appointments + Upcoming Appointments -->
  <div class="appointments-grid">
    <!-- Ongoing Appointments Table -->
    <section class="appointments-section">
      <div class="section-header">
        <h2>Ongoing Appointments</h2>
      </div>
      
      <div class="table-container">
        <?php if (empty($ongoingAppointments)): ?>
          <p class="no-data">No vets are in an appointment right now.</p>
        <?php else: ?>
          <table class="appointments-table">
            <thead>
              <tr>
                <th>Vet</th>
                <th>Client</th>
                <th>Type</th>
                <th>Time</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($ongoingAppointments as $row): ?>
                <?php if (!$row['hasAppointment']): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($row['vet']); ?></td>
                    <td colspan="3" class="no-appointment">No current appointment</td>
                  </tr>
                <?php else: ?>
                  <tr>
                    <td><?php echo htmlspecialchars($row['vet']); ?></td>
                    <td><?php echo htmlspecialchars($row['client']); ?></td>
                    <td><?php echo htmlspecialchars($row['type']); ?></td>
                    <td><?php echo htmlspecialchars($row['time_range']); ?></td>
                  </tr>
                <?php endif; ?>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </section>

    <!-- Upcoming Appointments -->
    <section class="appointments-section">
      <div class="section-header">
        <h2>Today's Upcoming</h2>
        <a href="/PETVET/index.php?module=receptionist&page=appointments" class="link-view-all">View all</a>
      </div>
      
      <div class="upcoming-list">
        <?php if (empty($upcomingAppointments)): ?>
          <p class="no-data">No upcoming appointments.</p>
        <?php else: ?>
          <?php foreach ($upcomingAppointments as $appt): ?>
            <div class="upcoming-item">
              <div class="upcoming-date">
                <div class="date-day"><?php echo date('d', strtotime($appt['date'])); ?></div>
                <div class="date-month"><?php echo date('M', strtotime($appt['date'])); ?></div>
              </div>
              <div class="upcoming-details">
                <div class="upcoming-time"><?php echo date('g:i A', strtotime($appt['time'])); ?></div>
                <div class="upcoming-client"><?php echo htmlspecialchars($appt['client']); ?> - <?php echo htmlspecialchars($appt['pet']); ?></div>
                <div class="upcoming-vet"><?php echo htmlspecialchars($appt['vet']); ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>
  </div>

</main>

<script>
// Real-time dashboard stats refresh
let refreshInterval;

function updateDashboard() {
  console.log('[Dashboard Refresh] Fetching updated stats...');
  
  fetch('/PETVET/api/receptionist/get-dashboard-stats.php', {
    credentials: 'same-origin'
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log('[Dashboard Refresh] ✅ Stats updated:', data);
      
      // Update pending count
      const pendingElement = document.querySelector('.stat-pending .stat-number');
      if (pendingElement) {
        updateNumber(pendingElement, data.pendingCount);
      }
      
      // Update ongoing count
      const ongoingElement = document.querySelector('.stat-ongoing .stat-number');
      if (ongoingElement) {
        updateNumber(ongoingElement, data.ongoingCount);
      }
      
      // Update ongoing appointments table
      updateOngoingTable(data.ongoingAppointments);
      
      // Update upcoming appointments list
      updateUpcomingList(data.upcomingAppointments);
      
    } else {
      console.error('[Dashboard Refresh] ❌ Error:', data.error);
    }
  })
  .catch(error => {
    console.error('[Dashboard Refresh] ❌ Fetch error:', error);
  });
}

function updateNumber(element, newValue) {
  if (element.textContent !== newValue.toString()) {
    element.classList.add('updating');
    element.textContent = newValue;
    setTimeout(() => element.classList.remove('updating'), 500);
  }
}

function updateOngoingTable(appointments) {
  const tbody = document.querySelector('.appointments-table tbody');
  const noDataMsg = document.querySelector('.appointments-section .no-data');
  const tableContainer = document.querySelector('.appointments-section .table-container');
  
  if (!tableContainer) return;
  
  if (appointments.length === 0) {
    if (!noDataMsg) {
      tableContainer.innerHTML = '<p class="no-data">No vets are in an appointment right now.</p>';
    }
  } else {
    if (noDataMsg) {
      tableContainer.innerHTML = `
        <table class="appointments-table">
          <thead>
            <tr>
              <th>Vet</th>
              <th>Client</th>
              <th>Type</th>
              <th>Time</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      `;
    }
    
    const newTbody = document.querySelector('.appointments-table tbody');
    if (newTbody) {
      newTbody.innerHTML = appointments.map(appt => `
        <tr>
          <td>${escapeHtml(appt.vet)}</td>
          <td>${escapeHtml(appt.client)}</td>
          <td>${escapeHtml(appt.type)}</td>
          <td>${escapeHtml(appt.time_range)}</td>
        </tr>
      `).join('');
    }
  }
}

function updateUpcomingList(appointments) {
  const listContainer = document.querySelector('.upcoming-list');
  if (!listContainer) return;
  
  if (appointments.length === 0) {
    listContainer.innerHTML = '<p class="no-data">No upcoming appointments.</p>';
  } else {
    listContainer.innerHTML = appointments.map(appt => {
      const date = new Date(appt.date + ' ' + appt.time);
      const day = date.getDate().toString().padStart(2, '0');
      const month = date.toLocaleString('en-US', { month: 'short' });
      const time = date.toLocaleString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
      
      return `
        <div class="upcoming-item">
          <div class="upcoming-date">
            <div class="date-day">${day}</div>
            <div class="date-month">${month}</div>
          </div>
          <div class="upcoming-details">
            <div class="upcoming-time">${time}</div>
            <div class="upcoming-client">${escapeHtml(appt.client)} - ${escapeHtml(appt.pet)}</div>
            <div class="upcoming-vet">${escapeHtml(appt.vet)}</div>
          </div>
        </div>
      `;
    }).join('');
  }
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

// Start auto-refresh every 10 seconds
refreshInterval = setInterval(updateDashboard, 10000);
console.log('[Dashboard] ✅ Auto-refresh enabled (every 10 seconds)');

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
  if (refreshInterval) {
    clearInterval(refreshInterval);
  }
});
</script>

</body>
</html>