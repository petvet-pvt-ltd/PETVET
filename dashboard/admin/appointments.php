<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Appointments - Pet Vet Admin Dashboard</title>
  <link rel="stylesheet" href="appointments.css" />
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
        <button class="logout">↩ Logout</button>
      </nav>
    </aside>

    <main class="main-content">
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
            <p class="number">12</p>
            <small>3 completed, 2 in progress, 7 upcoming</small>
          </div>
          <div class="card">
            <h3>This Week</h3>
            <p class="number">48</p>
            <small>10 more than last week</small>
          </div>
          <div class="card">
            <h3>Cancellations</h3>
            <p class="number">3</p>
            <small>5% cancellation rate</small>
          </div>
          <div class="card">
            <h3>Avg. Duration</h3>
            <p class="number">35 min</p>
            <small>2 min less than last month</small>
          </div>
        </div>

        <div class="search-filter">
          <input type="text" placeholder="🔍 Search appointments..." />
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
            <tr>
              <td>APT001</td>
              <td>
                Max<br><small>Dog</small><br><small>Owner: John Smith</small>
              </td>
              <td>Vaccination</td>
              <td>2023-07-18<br>09:00 AM</td>
              <td>Dr. Sarah Johnson</td>
              <td><span class="badge blue">Confirmed</span></td>
              <td>👁 ✏️ 🗑</td>
            </tr>
            <tr>
              <td>APT002</td>
              <td>
                Luna<br><small>Cat</small><br><small>Owner: Sarah Johnson</small>
              </td>
              <td>Check-up</td>
              <td>2023-07-18<br>10:30 AM</td>
              <td>Dr. Michael Chen</td>
              <td><span class="badge green">Completed</span></td>
              <td>👁 ✏️ 🗑</td>
            </tr>
            <tr>
              <td>APT003</td>
              <td>
                Bella<br><small>Dog</small><br><small>Owner: Michael Brown</small>
              </td>
              <td>Surgery</td>
              <td>2023-07-18<br>01:00 PM</td>
              <td>Dr. Sarah Johnson</td>
              <td><span class="badge yellow">In Progress</span></td>
              <td>👁 ✏️ 🗑</td>
            </tr>
          </tbody>
        </table>
      </section>
    </main>
  </div>
</body>
</html>
