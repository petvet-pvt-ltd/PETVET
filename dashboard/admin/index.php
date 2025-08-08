<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pet Vet Admin Dashboard</title>
  <link rel="stylesheet" href="styles.css"/>
</head>
<body>
  <div class="dashboard">
    <aside class="sidebar">
      <div class="logo">PET VET<br><span>Admin Dashboard</span></div>
      <nav>
        <ul>
          <li class="active">📊 Dashboard Overview</li>
          <li><a href="manage_users.php">👥 Manage Users</a></li>
          <li><a href="appointments.php">📅 Appointments</a></li>
          <li><a href="medical_records.php">📋 Medical Records</a></li>
          <li>🏪 Pet Shop</li>
          <li><a href="pet_listings.php">🐶 Pet Listings</a></li>
          <li>🔍 Lost & Found</li>
          <li>📝 Content Management</li>
          <li>📈 Reports & Analytics</li>
          <li>💵 Finance Panel</li>
        </ul>
        <button class="logout">↩ Logout</button>
      </nav>
    </aside>

    <main class="main-content">
      <header class="topbar">
        <h2>Admin Dashboard</h2>
        <div class="actions">
          <input type="text" placeholder="Search..." />
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
              <p class="number">12,548</p>
              <div class="icon">👤</div>
            </div>
            <span class="success">↑ +12% since last month</span>
          </div>
          <div class="card">
            <h3>Appointments</h3>
            <div class="value-with-icon">
              <p class="number">1,352</p>
              <div class="icon">📅</div>
            </div>
            <span class="success">↑ +5% since last month</span>
          </div>
          <div class="card">
            <h3>Revenue</h3>
            <div class="value-with-icon">
              <p class="number">$48,260</p>
              <div class="icon">💲</div>
            </div>
            <span class="success">↑ +18% since last month</span>
          </div>
          <div class="card">
            <h3>Pets Listed</h3>
            <div class="value-with-icon">
              <p class="number">832</p>
              <div class="icon">🔘</div>
            </div>
            <span class="error">↓ -3% since last month</span>
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
    </main>
  </div>

  <script src="scripts.js"></script>
</body>
</html>
