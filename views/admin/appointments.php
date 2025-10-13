<link rel="stylesheet" href="/PETVET/public/css/admin/appointments.css" />
<div class="main-content">
  <header class="topbar">
    <input type="text" placeholder="Search..." class="search-bar" />
    <div class="actions">
      <button class="btn">⬇ Export Schedule</button>
      <button class="btn primary">📅 New Appointment</button>
      <div class="profile">
        <div class="circle">AJ</div>
        <span>Admin User</span>
      </div>
    </div>
  </header>

  <section class="overview">
    <h1>Appointments</h1>
    <p>Manage and schedule patient appointments</p>

    <div class="cards">
      <div class="card">
        <h3>Today's Appointments</h3>
        <p class="number"><?php echo $stats['todayTotal']; ?></p>
        <small><?php echo $stats['todayCompleted']; ?> completed, <?php echo $stats['todayInProgress']; ?> in progress, <?php echo $stats['todayUpcoming']; ?> upcoming</small>
      </div>
      <div class="card">
        <h3>This Week</h3>
        <p class="number"><?php echo $stats['weekTotal']; ?></p>
        <small><?php echo htmlspecialchars($stats['weekGrowth']); ?></small>
      </div>
      <div class="card">
        <h3>Cancellations</h3>
        <p class="number"><?php echo $stats['cancellations']; ?></p>
        <small><?php echo htmlspecialchars($stats['cancellationRate']); ?></small>
      </div>
      <div class="card">
        <h3>Avg. Duration</h3>
        <p class="number"><?php echo htmlspecialchars($stats['avgDuration']); ?></p>
        <small><?php echo htmlspecialchars($stats['durationChange']); ?></small>
      </div>
    </div>

    <div class="search-filter">
      <input type="text" placeholder="🔍 Search appointments..." class="search-filter-input" />
      <select><option>All Dates</option></select>
      <select><option>All Status</option></select>
    </div>

    <table>
      <thead>
        <tr>
          <th>Appointment</th>
          <th>Pet/Owner</th>
          <th>Service</th>
          <th>Date & Time</th>
          <th>Veterinarian</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($appointments as $appointment): ?>
        <tr>
          <td><?php echo htmlspecialchars($appointment['id']); ?></td>
          <td><?php echo htmlspecialchars($appointment['petName']); ?><br><small>Owner: <?php echo htmlspecialchars($appointment['ownerName']); ?></small></td>
          <td><?php echo htmlspecialchars($appointment['reason']); ?></td>
          <td><?php echo htmlspecialchars($appointment['time']); ?></td>
          <td><?php echo htmlspecialchars($appointment['vet']); ?></td>
          <td>
            <span class="badge <?php echo $appointment['status'] === 'Completed' ? 'green' : ($appointment['status'] === 'Pending' ? 'blue' : 'orange'); ?>">
              <?php echo htmlspecialchars($appointment['status']); ?>
            </span>
          </td>
          <td>👁 ✏️ 🗑</td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</div>
