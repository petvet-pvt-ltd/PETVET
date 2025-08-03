<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../../styles/dashboard/admin/admin-dashboard.css" />
</head>
<body>
  <?php require_once '../sidebar.php'; ?>

  <div class="main-content">
    <h1>Welcome, Admin</h1>
     <?php echo '<p>Today is ', date("l, F j, Y"), '</p>'; ?>

    <div class="stats-container">
      <div class="stat-box">
        <h3>Total Users</h3>
        <p>12,548</p>
        <span class="green">↑ 12% since last month</span>
      </div>
      <div class="stat-box">
        <h3>Appointments</h3>
        <p>1,352</p>
        <span class="green">↑ 5% since last month</span>
      </div>
      <div class="stat-box">
        <h3>Revenue</h3>
        <p>$48,260</p>
        <span class="green">18% since last month</span>
      </div>
      <div class="stat-box">
        <h3>Pets Listed</h3>
        <p>832</p>
        <span class="red">↓ 3% since last month</span>
      </div>
    </div>

    <div class="charts-section">
      <div class="chart-box">
        <h2>Revenue Overview</h2>
        <img src="/mnt/data/b3ad46ef-71aa-4b5f-9d1d-b31938c31a02.png" alt="Revenue Chart" />
      </div>
      <div class="chart-box">
        <h2>Service Distribution</h2>
        <img src="/mnt/data/ebb88282-c546-4ac0-996b-efd7aff409f0.png" alt="Service Pie Chart" />
      </div>
    </div>

    <div class="tables-section">
      <div class="table-box">
        <h2>Recent Appointments</h2>
        <img src="/mnt/data/bb96e1c5-b7bc-43ca-91ab-0b53e7e46c35.png" alt="Appointments Table" />
      </div>
      <div class="table-box">
        <h2>Recent Sales</h2>
        <img src="/mnt/data/7a773a78-9cac-4c72-968c-8dfca6995e8a.png" alt="Sales Table" />
      </div>
    </div>

    <div class="alerts-section">
      <h2>System Alerts</h2>
      <img src="/mnt/data/afc22821-3806-42ef-935e-3600ee04ca19.png" alt="System Alerts" />
    </div>
  </div>
</body>
</html>
