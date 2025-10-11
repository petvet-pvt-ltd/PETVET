<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Finance Panel - Pet Vet Admin</title>
  <link rel="stylesheet" href="styles.css"/>
</head>
<body>
  <div class="dashboard">
    <aside class="sidebar">
     <img src="../../images/petvett.png" alt="PetVett Logo">
 
       <nav>
        <ul>
  <li class="active"><a href="index.php"><span class="icon">𝄜</span>Dashboard Overview</a></li>
  <li><a href="manage_users.php"><span class="icon">⚗</span>Manage Users</a></li>
  <li><a href="appointments.php"><span class="icon">✎</span>  Appointments</a></li>
  <li><a href="medical_records.php"><span class="icon">✉</span>Medical Records</a></li>
  <li><a href="#"><span class="icon">𓃠</span>Pet Shop</a></li>
  <li><a href="pet_listings.php"><span class="icon">☰</span>Pet Listings</a></li>
  <li><a href="#"><span class="icon">🔍︎</span>Lost & Found</a></li>
 
  <li><a href="#"><span class="icon">🗁</span>Reports & Analytics</a></li>
  <li><a href="finance-panel.php"><span class="icon">$</span>Finance Panel</a></li>
</ul>
        <a href="../index.php" class="logout">↩ Logout</a>
      </nav>
    </aside>

    <!-- Main -->
    <main class="main-content">
      <!-- Topbar -->
      <header class="topbar">
        <h2>Finance Panel</h2>
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

      <!-- Finance Content -->
      <section class="overview">
        <h1>Finance Panel</h1>
        <p>Manage financial transactions and reporting</p>

        <!-- Stats -->
        <div class="cards">
          <div class="card">
            <h3>Total Revenue</h3>
            <div class="value-with-icon">
              <p class="number">$48,260</p>
              <div class="icon">💲</div>
            </div>
            <span class="success">↑ +18%</span>
          </div>
          <div class="card">
            <h3>Expenses</h3>
            <div class="value-with-icon">
              <p class="number">$23,580</p>
              <div class="icon">💳</div>
            </div>
            <span class="error">↑ +5%</span>
          </div>
          <div class="card">
            <h3>Net Profit</h3>
            <div class="value-with-icon">
              <p class="number">$24,680</p>
              <div class="icon">📈</div>
            </div>
            <span class="success">↑ +32%</span>
          </div>
          <div class="card">
            <h3>Pending Payments</h3>
            <div class="value-with-icon">
              <p class="number">$3,450</p>
              <div class="icon">📅</div>
            </div>
            <span class="success">↓ -8%</span>
          </div>
        </div>
      </section>

      <!-- Charts -->
      <section class="charts">
        <div class="chart-container">
          <h3>Revenue Overview</h3>
          <p>Monthly revenue for current year</p>
          <canvas id="lineChart" width="400" height="250"></canvas>
        </div>
        <div class="chart-container">
          <h3>Revenue by Service</h3>
          <p>Distribution of revenue sources</p>
          <canvas id="pieChart" width="300" height="250"></canvas>
        </div>
      </section>

      <!-- Transactions -->
      <section class="tables">
        <div class="card table-card">
          <h3>Recent Transactions</h3>
          <table>
            <thead>
              <tr><th>Invoice ID</th><th>Customer</th><th>Service</th><th>Amount</th><th>Date</th><th>Status</th></tr>
            </thead>
            <tbody>
              <tr><td>INV-1023</td><td>John Smith</td><td>Pet Checkup</td><td>$120</td><td>2023-07-15</td><td>Paid</td></tr>
              <tr><td>INV-1022</td><td>Sarah Johnson</td><td>Dental Cleaning</td><td>$185.50</td><td>2023-07-15</td><td>Paid</td></tr>
              <tr><td>INV-1021</td><td>Michael Brown</td><td>Surgery</td><td>$350</td><td>2023-07-14</td><td>Pending</td></tr>
            </tbody>
          </table>
        </div>

        <div class="card table-card">
          <h3>Recent Expenses</h3>
          <table>
            <thead>
              <tr><th>Expense ID</th><th>Category</th><th>Vendor</th><th>Amount</th><th>Date</th><th>Status</th></tr>
            </thead>
            <tbody>
              <tr><td>EXP-1023</td><td>Medical Supplies</td><td>PetMed Inc</td><td>$1,250</td><td>2023-07-10</td><td>Paid</td></tr>
              <tr><td>EXP-1022</td><td>Staff Salaries</td><td>Payroll</td><td>$8,500</td><td>2023-07-01</td><td>Paid</td></tr>
              <tr><td>EXP-1021</td><td>Rent</td><td>Property LLC</td><td>$3,200</td><td>2023-07-01</td><td>Paid</td></tr>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>

  <script src="script-finance.js"></script>
</body>
</html>
