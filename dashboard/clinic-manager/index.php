<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Overview</title>
  <link rel="stylesheet" href="../../styles/dashboard/clinic-manager/dashboard.css">
  <style>
    .main-content {
      margin-left: 240px;
      padding: 24px;
    }
  </style>
</head>
<body>
  <?php require_once '../sidebar.php'; ?>

  <div class="main-content">
    <div class="welcomeNdate">
      <h1>Welcome, Clinic Manager</h1>
      <?php echo '<p>Today is ', date("l, F j, Y"), '</p>'; ?>
    </div>

    <div class="card-container">
      <div class="stat-card card1">
        <p>12</p>
        <h3>Appointments Today</h3>
      </div>
      <div class="stat-card card2">
        <p>3</p>
        <h3>Active Vets</h3>
      </div>
      <div class="stat-card card3">
        <p>4</p>
        <h3>Pending Shop Orders</h3>
      </div>
      <div class="stat-card card4">
        <p>9</p>
        <h3>Staff on Duty Today</h3>
      </div>
    </div>

    <div class="section-container">
      <div class="section">
        <div class="section-header">
          <h2>Today's Appointments</h2>
          <a href="#" class="view-all">View all</a>
        </div>
        <div class="table-scroll">
          <table>
            <thead>
              <tr>
                <th>Time</th>
                <th>Pet</th>
                <th>Client</th>
                <th>Vet</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>08:00</td>
                <td>Rocky</td>
                <td>John</td>
                <td>Dr. Silva</td>
                <td><span class="status-badge status-confirmed">Confirmed</span></td>
              </tr>
              <tr>
                <td>10:00</td>
                <td>Bella</td>
                <td>Sarah</td>
                <td>Dr. Silva</td>
                <td><span class="status-badge status-confirmed">Confirmed</span></td>
              </tr>
              <tr>
                <td>11:30</td>
                <td>Max</td>
                <td>David</td>
                <td>Dr. Perera</td>
                <td><span class="status-badge status-completed">Completed</span></td>
              </tr>
              <tr>
                <td>01:00</td>
                <td>Rosie</td>
                <td>Emma</td>
                <td>Dr. Nuwan</td>
                <td><span class="status-badge status-confirmed">Confirmed</span></td>
              </tr>
              <tr>
                <td>02:30</td>
                <td>Lola</td>
                <td>James</td>
                <td>Dr. Nuwan</td>
                <td><span class="status-badge status-confirmed">Confirmed</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="section section-right">
        <h2>Staff on Duty Today</h2>
        <div class="table-scroll">
            <ul class="staff-list">
                <li>
                    <div class="staff-item-left">
                    <span class="dot green"></span>
                    Dr. Silva
                    </div>
                    <div class="staff-item-right">9 AM – 1 PM</div>
                </li>
                <li>
                    <div class="staff-item-left">
                    <span class="dot green"></span>
                    Dr. Perera
                    </div>
                    <div class="staff-item-right">10 AM – 6 PM</div>
                </li>
                <li>
                    <div class="staff-item-left">
                    <span class="dot green"></span>
                    Receptionist
                    </div>
                    <div class="staff-item-right">8 AM – 2 PM</div>
                </li>
            </ul>
        </div>
        <button class="add-btn">+ Add Staff</button>
      </div>
    </div>
  </div>
</body>
</html>
