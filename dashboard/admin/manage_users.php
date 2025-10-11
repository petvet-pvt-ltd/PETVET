<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users - Pet Vet Admin Dashboard</title>
  <link rel="stylesheet" href="manage_users.css">
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
          <button class="btn">⬇ Export Users</button>
          <button class="btn primary">👥 Add New User</button>
          <div class="profile">
            <div class="circle">AJ</div>
            <span>Admin User</span>
          </div>
        </div>
      </header>

      <section class="overview">
        <h1>Manage Users</h1>
        <p>View and manage system users</p>

        <div class="cards">
          <div class="card">
            <h3>Total Users</h3>
            <div class="value-with-icon">
              <p class="number">842</p>
              <div class="icon">👤</div>
            </div>
            <span class="success">+12% from last month</span>
          </div>
          <div class="card">
            <h3>Active Users</h3>
            <div class="value-with-icon">
              <p class="number">765</p>
              <div class="icon">✅</div>
            </div>
            <span class="success">91% of total users</span>
          </div>
          <div class="card">
            <h3>New Users</h3>
            <div class="value-with-icon">
              <p class="number">48</p>
              <div class="icon">🆕</div>
            </div>
            <span class="success">+8% from last week</span>
          </div>
          <div class="card">
            <h3>Inactive Users</h3>
            <div class="value-with-icon">
              <p class="number">29</p>
              <div class="icon">❌</div>
            </div>
            <span class="error">3.4% of total users</span>
          </div>
        </div>

        <div class="search-filter">
          <input type="text" placeholder="Search users by name, email, or ID..." />
          <select>
            <option>All Users</option>
            <option>Active</option>
            <option>Inactive</option>
          </select>
        </div>

        <table>
          <thead>
            <tr>
              <th>User</th>
              <th>Role</th>
              <th>Status</th>
              <th>Pets</th>
              <th>Join Date</th>
              <th>Last Login</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <div class="avatar">JS</div>
                <div class="user-info">
                  <strong>John Smith</strong><br>
                  <small>john.smith@example.com</small><br>
                  <small>ID: USR001</small>
                </div>
              </td>
              <td><span class="badge blue">Pet Owner</span></td>
              <td><span class="badge green">Active</span></td>
              <td>2</td>
              <td>2023-01-15</td>
              <td>2023-07-12</td>
              <td>👁 ✏️ 📩 🗑</td>
            </tr>
            <tr>
              <td>
                <div class="avatar">SJ</div>
                <div class="user-info">
                  <strong>Sarah Johnson</strong><br>
                  <small>sarah.j@example.com</small><br>
                  <small>ID: USR002</small>
                </div>
              </td>
              <td><span class="badge blue">Pet Owner</span></td>
              <td><span class="badge green">Active</span></td>
              <td>1</td>
              <td>2023-02-20</td>
              <td>2023-07-14</td>
              <td>👁 ✏️ 📩 🗑</td>
            </tr>
            <tr>
              <td>
                <div class="avatar">DMC</div>
                <div class="user-info">
                  <strong>Dr. Michael Chen</strong><br>
                  <small>dr.chen@petvet.com</small><br>
                  <small>ID: USR003</small>
                </div>
              </td>
              <td><span class="badge purple">Veterinarian</span></td>
              <td><span class="badge green">Active</span></td>
              <td>0</td>
              <td>2022-11-05</td>
              <td>2023-07-15</td>
              <td>👁 ✏️ 📩 🗑</td>
            </tr>
          </tbody>
        </table>
      </section>
    </main>
  </div>
</body>
</html>
