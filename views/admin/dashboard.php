<?php
// Admin Dashboard Overview - migrated from old project index.php (content only)
?>
<link rel="stylesheet" href="/PETVET/public/css/admin/styles.css">
<div class="main-content">
  <header class="topbar">
    <h2>Admin Dashboard</h2>
    <div class="actions">
      <input type="text" placeholder="Search..." class="search-bar" />
      <button class="btn">Export Report</button>
      <button class="btn primary">View All Analytics</button>
      <div class="profile">
        <div class="circle">AJ</div>
        <span>Admin User</span>
      </div>
    </div>
  </header>

  <section class="overview">
    <h1>Dashboard Overview</h1>
    <p>Welcome back! Here's what's happening with PetVet today.</p>

    <div class="cards">
      <div class="card">
        <h3>Total Users</h3>
        <div class="value-with-icon">
          <p class="number"><?php echo number_format($totalUsers); ?></p>
          <div class="icon">👤</div>
        </div>
        <span class="success">↑ <?php echo $usersGrowth; ?> since last month</span>
      </div>
      <div class="card">
        <h3>Appointments</h3>
        <div class="value-with-icon">
          <p class="number"><?php echo number_format($totalAppointments); ?></p>
          <div class="icon">📅</div>
        </div>
        <span class="<?php echo strpos($appointmentsChange, '+') !== false ? 'success' : 'error'; ?>">
          <?php echo strpos($appointmentsChange, '+') !== false ? '↑' : '↓'; ?> <?php echo $appointmentsChange; ?> since last month
        </span>
      </div>
      <div class="card">
        <h3>Revenue</h3>
        <div class="value-with-icon">
          <p class="number">$<?php echo number_format($totalRevenue); ?></p>
          <div class="icon">💲</div>
        </div>
        <span class="success">↑ <?php echo $revenueGrowth; ?> since last month</span>
      </div>
      <div class="card">
        <h3>Pets Listed</h3>
        <div class="value-with-icon">
          <p class="number"><?php echo number_format($totalPets); ?></p>
          <div class="icon">🔘</div>
        </div>
        <span class="success">↑ <?php echo $petsChange; ?> since last month</span>
      </div>
    </div>
  </section>

  <section class="charts">
    <div class="chart-container">
      <h3>Revenue Overview</h3>
      <p>Monthly revenue for current year</p>
      <canvas id="barCanvas" width="400" height="250"></canvas>
    </div>

    <div class="chart-container">
      <h3>Service Distribution</h3>
      <p>Percentage of services provided</p>
      <canvas id="pieCanvas" width="300" height="250"></canvas>
    </div>
  </section>
</div>
<script src="/PETVET/public/js/admin/scripts.js"></script>
