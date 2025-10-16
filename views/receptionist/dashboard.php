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
    <h1>Welcome, Receptionist!</h1>
    <p>Today is <?php echo date('l, F j, Y'); ?></p>
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

</body>
</html>