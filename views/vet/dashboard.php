<?php
// VetController passes: ['dashboardData' => ...]
$data = $dashboardData ?? [
  'appointments' => [],
  'medicalRecords' => [],
  'prescriptions' => [],
  'vaccinations' => []
];

$isSuspended = (bool)($isSuspended ?? false);

$GLOBALS['currentPage'] = 'dashboard.php';
$GLOBALS['module'] = 'vet';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PetVet — Overview</title>
<link rel="stylesheet" href="/PETVET/public/css/vet/enhanced-vet.css">
</head>
<body>

<?php include 'views/shared/sidebar/sidebar.php'; ?>

<div class="main-content">
  <?php include __DIR__ . '/../shared/components/user-welcome-header.php'; ?>

  <div class="page-frame<?= $isSuspended ? ' suspension-frame' : '' ?>">
    <?php if ($isSuspended): ?>
      <div class="suspension-wrap">
        <div class="suspension-hero" role="status" aria-live="polite">
          <div class="suspension-hero-left">
            <div class="suspension-icon" aria-hidden="true">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                <path d="M12 8v5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <path d="M12 16.5h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
              </svg>
            </div>
            <div style="min-width:0;">
              <h1 class="suspension-title">Account Suspended</h1>
              <p class="suspension-subtitle">Your clinic manager has temporarily disabled your vet access for this clinic.</p>
            </div>
          </div>
          <div class="suspension-badge">
            <span aria-hidden="true">●</span>
            Suspended
          </div>
        </div>

        <div class="suspension-card">
          <div class="suspension-card-inner">
            <div class="suspension-grid">
              <div>
                <h3>Access restricted</h3>
                <p>
                  Your vet tools are currently disabled by your clinic manager. While suspended, you can still review/update your account in <strong>Settings</strong>
                  and you may <strong>Logout</strong> anytime.
                </p>
                <p style="margin-top:12px;">
                  If you believe this is a mistake, please contact your clinic manager to request re-activation.
                </p>
              </div>

              <div class="suspension-side">
                <h4>Quick actions</h4>
                <div class="suspension-actions">
                  <a class="btn secondary" href="/PETVET/index.php?module=vet&page=settings">Go to Settings</a>
                  <a class="btn danger" href="/PETVET/logout.php">Logout</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php else: ?>
      <div class="page-header">
        <h1 class="page-title">Dashboard Overview</h1>
        <p class="page-subtitle">Monitor your appointments and patient care</p>
      </div>

      <div class="cards">
        <div class="card">
          <h3 id="kpi-today">—</h3>
          <p>Appointments Today</p>
        </div>
        <div class="card">
          <h3 id="kpi-total">—</h3>
          <p>This Week (Last 7 Days)</p>
        </div>
      </div>

      <section id="ongoing-section">
        <h3>Ongoing Appointment</h3>
        <div id="ongoing-container"></div>
      </section>

      <section>
        <h3>Today's Upcoming Appointments</h3>
        <input id="searchBar" placeholder="Search appointments...">
        <div class="simple-mobile-table">
          <table id="upcomingTable">
            <thead>
              <tr>
                <th>Time</th><th>Pet</th><th>Owner</th><th>Reason</th><th>Action</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </section>
    <?php endif; ?>
  </div>
</div>

<?php if (!$isSuspended): ?>
  <script>
  window.PETVET_INITIAL_DATA = <?php echo json_encode($data, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
  window.PETVET_TODAY = "<?php echo date('Y-m-d'); ?>";
  </script>
  <script src="/PETVET/public/js/vet/dashboard.js"></script>
<?php endif; ?>
</body>
</html>
