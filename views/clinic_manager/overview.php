<?php
$currentPage = basename($_SERVER['PHP_SELF']);
date_default_timezone_set('Asia/Colombo');
$today = date('l, F j, Y');


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Clinic Manager Dashboard</title>
  <link rel="stylesheet" href="/PETVET/public/css/clinic-manager/overview.css">
  <style>
    .main-content {
    margin-left: 240px;
    padding: 24px;
  }

  @media (max-width: 768px) {
    .main-content {
      margin-left: 0;
      width: 100%;
    }
  }
  </style>
</head>
<body>
  <main class="main-content">
    <div class="page-frame">
      <!-- Header -->
      <header class="cm-header">
        <h1>Welcome, Clinic Manager</h1>
        <p>Today is <?php echo htmlspecialchars($today); ?></p>
      </header>

      <!-- Two-column layout -->
      <section class="cm-layout">
        <!-- LEFT: KPIs + Appointments -->
        <div class="left-col">
          <!-- KPI Cards -->
          <section class="cm-kpis" role="region" aria-label="Key Performance Indicators">
            <?php if (empty($kpis)): ?>
              <p>No KPI data available.</p>
            <?php else: ?>
              <?php foreach ($kpis as $kpi): ?>
                <article class="kpi-card" role="article" aria-label="<?php echo htmlspecialchars($kpi['label']); ?>">
                  <div class="kpi-number"><?php echo $kpi['value']; ?></div>
                  <div class="kpi-label"><?php echo htmlspecialchars($kpi['label']); ?></div>
                </article>
              <?php endforeach; ?>
            <?php endif; ?>
          </section>

          <!-- Ongoing Appointments -->
          <section class="card table-card">
            <div class="card-head">
              <h2>Ongoing Appointments</h2>
              <a class="link-muted" href="/PETVET/index.php?module=clinic-manager&page=appointments" aria-label="View all appointments">View all</a>
            </div>
            <div class="table-wrap" role="region" aria-label="Ongoing Appointments">
              <?php if (empty($ongoingAppointments)): ?>
                <p>No vets are in an appointment right now.</p>
              <?php else: ?>
                <table class="cm-table">
                  <thead>
                    <tr>
                      <th scope="col">Vet</th>
                      <th scope="col">Animal</th>
                      <th scope="col">Client</th>
                      <th scope="col">Type</th>
                      <th scope="col">Time</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($ongoingAppointments as $row): ?>
                      <?php if (!$row['hasAppointment']): ?>
                        <tr>
                          <td><?php echo htmlspecialchars($row['vet']); ?></td>
                          <td colspan="4" style="color:#64748b; font-style:italic;">No current appointment</td>
                        </tr>
                      <?php else: ?>
                        <tr>
                          <td><?php echo htmlspecialchars($row['vet']); ?></td>
                          <td><?php echo htmlspecialchars($row['animal']); ?></td>
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
        </div>

        <!-- RIGHT: Staff on Duty -->
        <aside class="right-col">
          <section class="card staff-card">
            <h2>Staff on Duty Today</h2>
            <div class="staff-body">
              <?php if (empty($staff)): ?>
                <p>No staff scheduled today.</p>
              <?php else: ?>
                <?php foreach ($staff as $role => $people): ?>
                  <div class="staff-block">
                    <h3><?php echo htmlspecialchars($role); ?></h3>
                    <ul class="staff-list">
                      <?php foreach ($people as $person): ?>
                        <li class="pill-row">
                          <span class="dot <?php echo $person['status']; ?>" aria-label="<?php echo $person['status']; ?> status"></span>
                          <span class="pill-name"><?php echo htmlspecialchars($person['name']); ?></span>
                          <span class="pill-time"><?php echo $person['time']; ?></span>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
            <button type="button" class="btn-primary btn-full" aria-label="Add new staff">+ Add Staff</button>
          </section>
        </aside>
      </section>
    </div>
  </main>
</body>
</html>