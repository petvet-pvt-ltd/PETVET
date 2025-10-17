<?php
// Admin Dashboard - simplified layout matching the design: top stat cards, large growth chart and role distribution donut.
// Safe defaults for variables to avoid notices during early testing.
$totalUsers = isset($totalUsers) ? $totalUsers : 1234;
$usersGrowth = isset($usersGrowth) ? $usersGrowth : '+12%';
$activeUsersToday = isset($activeUsersToday) ? $activeUsersToday : 89;
$pendingRequests = isset($pendingRequests) ? $pendingRequests : 7;
$totalClinics = isset($totalClinics) ? $totalClinics : 45;

?>
<link rel="stylesheet" href="/PETVET/public/css/admin/styles.css">
<style>
  /* small view-level overrides for dashboard layout */
  .dashboard-top-cards{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:18px}
  .stat-card{background:#fff;border-radius:10px;padding:18px;box-shadow:0 6px 24px rgba(2,6,23,0.04)}
  .stat-card h4{margin:0 0 8px;font-size:13px;color:#374151}
  .stat-value{font-size:24px;font-weight:700;color:#0f172a}
  .stat-sub{font-size:12px;color:#10b981;margin-top:6px}

  .dashboard-grid{display:grid;grid-template-columns: 1fr 360px;gap:18px}
  .chart-large{background:#fff;padding:18px;border-radius:10px;min-height:420px;box-shadow:0 6px 24px rgba(2,6,23,0.04)}
  .chart-side{background:#fff;padding:18px;border-radius:10px;box-shadow:0 6px 24px rgba(2,6,23,0.04);min-height:420px}
  .chart-placeholder{height:360px;border-radius:8px;background:linear-gradient(180deg,#f8fafc,#ffffff);display:flex;align-items:center;justify-content:center;color:#9ca3af}
  .donut-placeholder{height:260px;display:flex;align-items:center;justify-content:center}

  @media (max-width:980px){
    .dashboard-top-cards{grid-template-columns:repeat(2,1fr)}
    .dashboard-grid{grid-template-columns:1fr}
    .chart-side{order:2}
  }
</style>

<div class="main-content">
  <header class="topbar">
    <h2>Dashboard Overview</h2>
    <div class="actions">
      <input type="text" placeholder="Search system data..." class="search-bar" />
      <div class="profile"><div class="circle">AJ</div><span>Admin User</span></div>
    </div>
  </header>

  <section class="overview">
    <div class="dashboard-top-cards">
      <div class="stat-card">
        <h4>Total Users</h4>
        <div class="stat-value"><?php echo number_format($totalUsers); ?></div>
        <div class="stat-sub"><?php echo htmlspecialchars($usersGrowth); ?> since last month</div>
      </div>
      <div class="stat-card">
        <h4>Active Users Today</h4>
        <div class="stat-value"><?php echo htmlspecialchars($activeUsersToday); ?></div>
        <div class="stat-sub">+5% since yesterday</div>
      </div>
      <div class="stat-card">
        <h4>Pending Registration Requests</h4>
        <div class="stat-value"><?php echo htmlspecialchars($pendingRequests); ?></div>
        <div class="stat-sub">awaiting approval</div>
      </div>
      <div class="stat-card">
        <h4>Total Clinics Registered</h4>
        <div class="stat-value"><?php echo htmlspecialchars($totalClinics); ?></div>
        <div class="stat-sub">+1 since last week</div>
      </div>
    </div>

    <div class="dashboard-grid">
      <div class="chart-large">
        <h3>User Growth</h3>
        <p class="muted">Monthly active users — last 12 months</p>
        <div class="chart-placeholder" id="growthChart">[Line chart placeholder]</div>
      </div>

      <div class="chart-side">
        <h3>Role Distribution</h3>
        <p class="muted">Owners, Sitters, Trainers, Groomers, Clinics</p>
        <div class="donut-placeholder" id="roleDonut">[Donut chart placeholder]</div>
        <div style="margin-top:14px">
          <div><span style="display:inline-block;width:10px;height:10px;background:#3b82f6;border-radius:6px;margin-right:8px"></span> Owners</div>
          <div><span style="display:inline-block;width:10px;height:10px;background:#60a5fa;border-radius:6px;margin-right:8px"></span> Sitters</div>
          <div><span style="display:inline-block;width:10px;height:10px;background:#10b981;border-radius:6px;margin-right:8px"></span> Trainers</div>
          <div><span style="display:inline-block;width:10px;height:10px;background:#f59e0b;border-radius:6px;margin-right:8px"></span> Groomers</div>
          <div><span style="display:inline-block;width:10px;height:10px;background:#ef4444;border-radius:6px;margin-right:8px"></span> Clinics</div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Optional: small script to draw simple placeholders (can be replaced with Chart.js later) -->
<script>
  // simple SVG line and donut placeholders
  (function(){
    const growth = document.getElementById('growthChart');
    if(growth){
      // draw line plus month labels
      const svgParts = [];
      svgParts.push('<svg width="100%" height="100%" viewBox="0 0 600 260" preserveAspectRatio="none">');
      svgParts.push('<polyline points="0,200 60,170 120,190 180,140 240,160 300,120 360,140 420,100 480,120 540,80 600,60" fill="none" stroke="#2563eb" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" opacity="0.95"/>');
      // month labels
      const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
      months.forEach((m,i)=>{
        const x = 20 + i*48; // spread across
        svgParts.push(`<text x="${x}" y="240" font-size="10" fill="#9ca3af">${m}</text>`);
      });
      svgParts.push('</svg>');
      growth.innerHTML = svgParts.join('');
    }
    const donut = document.getElementById('roleDonut');
    if(donut){
      // simple donut with segments for Owners, Sitters, Trainers, Groomers, Clinics
      donut.innerHTML = `
        <svg width="180" height="180" viewBox="0 0 42 42">
          <circle r="15.91549431" cx="21" cy="21" fill="transparent" stroke="#e6eef7" stroke-width="6"></circle>
          <circle r="15.91549431" cx="21" cy="21" fill="transparent" stroke="#3b82f6" stroke-width="6" stroke-dasharray="30 70" stroke-dashoffset="0"></circle>
          <circle r="15.91549431" cx="21" cy="21" fill="transparent" stroke="#60a5fa" stroke-width="6" stroke-dasharray="10 90" stroke-dashoffset="-30"></circle>
          <circle r="15.91549431" cx="21" cy="21" fill="transparent" stroke="#10b981" stroke-width="6" stroke-dasharray="15 85" stroke-dashoffset="-40"></circle>
          <circle r="15.91549431" cx="21" cy="21" fill="transparent" stroke="#f59e0b" stroke-width="6" stroke-dasharray="20 80" stroke-dashoffset="-55"></circle>
          <circle r="15.91549431" cx="21" cy="21" fill="transparent" stroke="#ef4444" stroke-width="6" stroke-dasharray="5 95" stroke-dashoffset="-75"></circle>
          <circle r="10" cx="21" cy="21" fill="#fff"></circle>
        </svg>`;
    }
  })();
</script>
