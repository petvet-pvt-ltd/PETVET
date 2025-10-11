<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Medical Records - Pet Vet Admin Dashboard</title>
  <link rel="stylesheet" href="medical_records.css"/>
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

    <!-- Main -->
    <main class="main-content">
      <!-- Topbar (matches appointments.html) -->
      <header class="topbar">
        <input type="text" placeholder="Search..." class="search-bar" />
        <div class="actions">
          <button class="btn">⬇ Export</button>
          <button class="btn primary">➕ Add New Record</button>
          <div class="profile">
            <div class="circle">AJ</div>
            <span>Admin User</span>
          </div>
        </div>
      </header>

      <!-- Overview header + cards -->
      <section class="overview">
        <h1>Medical Records</h1>
        <p>View and manage patient medical records</p>

        <div class="cards">
          <div class="card">
            <h3>Total Records</h3>
            <p class="number">120</p>
            <small>10 new this week</small>
          </div>
          <div class="card">
            <h3>Vaccinations</h3>
            <p class="number">45</p>
            <small>Updated regularly</small>
          </div>
          <div class="card">
            <h3>Chronic Conditions</h3>
            <p class="number">12</p>
            <small>Require ongoing care</small>
          </div>
          <div class="card">
            <h3>Last Updated</h3>
            <p class="number">2 hrs ago</p>
            <small>Latest entry logged</small>
          </div>
        </div>

        <!-- Search & filters (matches appointments.html) -->
        <div class="search-filter">
          <input type="text" placeholder="🔍 Search by pet name, owner, or condition..." />
          <select><option>All Records</option></select>
          <input type="date" />
        </div>

        <!-- Table -->
        <table>
          <thead>
            <tr>
              <th>Record ID</th>
              <th>Pet Name</th>
              <th>Medical Condition</th>
              <th>Veterinarian</th>
              <th>Treatment</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>MR-1001</td>
              <td>Max<br><small>Dog • Labrador Retriever</small></td>
              <td>Vaccination</td>
              <td>Dr. Sarah Johnson</td>
              <td>Annual vaccines administered</td>
              <td>2023-07-15</td>
              <td>👁️ ✏️ 📄</td>
            </tr>
            <tr>
              <td>MR-1002</td>
              <td>Bella<br><small>Cat • Siamese</small></td>
              <td>Dental Cleaning</td>
              <td>Dr. Michael Chen</td>
              <td>Full dental cleaning and examination</td>
              <td>2023-07-12</td>
              <td>👁️ ✏️ 📄</td>
            </tr>
            <tr>
              <td>MR-1003</td>
              <td>Charlie<br><small>Dog • Beagle</small></td>
              <td>Skin Infection</td>
              <td>Dr. Sarah Johnson</td>
              <td>Prescribed antibiotics and medicated shampoo</td>
              <td>2023-07-10</td>
              <td>👁️ ✏️ 📄</td>
            </tr>
            <tr>
              <td>MR-1004</td>
              <td>Luna<br><small>Cat • Maine Coon</small></td>
              <td>Annual Checkup</td>
              <td>Dr. Robert Taylor</td>
              <td>General examination, all clear</td>
              <td>2023-07-08</td>
              <td>👁️ ✏️ 📄</td>
            </tr>
            <tr>
              <td>MR-1005</td>
              <td>Rocky<br><small>Dog • German Shepherd</small></td>
              <td>Hip Dysplasia</td>
              <td>Dr. Michael Chen</td>
              <td>Pain management and physical therapy</td>
              <td>2023-07-05</td>
              <td>👁️ ✏️ 📄</td>
            </tr>
            <tr>
              <td>MR-1006</td>
              <td>Milo<br><small>Cat • Tabby</small></td>
              <td>Urinary Tract Infection</td>
              <td>Dr. Robert Taylor</td>
              <td>Prescribed antibiotics and special diet</td>
              <td>2023-07-03</td>
              <td>👁️ ✏️ 📄</td>
            </tr>
          </tbody>
        </table>
      </section>
    </main>
  </div>

  <script>
    // Keep active state in sidebar
    document.addEventListener("DOMContentLoaded", function () {
      const current = window.location.pathname.split("/").pop();
      document.querySelectorAll(".sidebar nav ul li a").forEach(link => {
        if (link.getAttribute("href") === current) {
          link.parentElement.classList.add("active");
        }
      });
    });
  </script>
</body>
</html>
