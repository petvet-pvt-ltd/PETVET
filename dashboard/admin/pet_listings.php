<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pet Listings - Pet Vet Admin</title>
  <link rel="stylesheet" href="pet_listings.css"/>
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
      <!-- Top bar -->
      <header class="topbar">
        <h2>Admin Dashboard</h2>
        <div class="actions">
          <input type="text" class="search-bar" placeholder="Search..." />
          <button class="btn primary">+ Add New Pet</button>
          <div class="profile">
            <div class="circle">AJ</div>
            <span>Admin User</span>
          </div>
        </div>
      </header>

      <!-- Page header -->
      <section class="page-header">
        <div>
          <h1>Pet Listings</h1>
          <p>Manage pets available for adoption</p>
        </div>
      </section>

      <!-- Filters -->
      <div class="filters">
        <div class="filter-left">
          <input type="text" class="filter-input" placeholder="🔍 Search pets..." />
        </div>
        <select class="filter-select">
          <option>All Statuses</option>
          <option>Available</option>
          <option>Pending</option>
          <option>Adopted</option>
        </select>
        <select class="filter-select">
          <option>All Breeds</option>
          <option>Labrador Retriever</option>
          <option>Siamese</option>
          <option>Beagle</option>
          <option>German Shepherd</option>
          <option>Maine Coon</option>
          <option>Poodle</option>
        </select>
        <select class="filter-select">
          <option>All Ages</option>
          <option>0–1 year</option>
          <option>1–3 years</option>
          <option>3–6 years</option>
          <option>6+ years</option>
        </select>
      </div>

      <!-- Table -->
      <section class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>PET</th>
              <th>BREED</th>
              <th>AGE</th>
              <th>GENDER</th>
              <th>LOCATION</th>
              <th>STATUS</th>
              <th>ACTIONS</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="pet-cell">
                <img class="avatar" src="assets/max.jpg" alt="Max" />
                <div>
                  <strong>Max</strong><br><small>Dog</small>
                </div>
              </td>
              <td>Labrador Retriever</td>
              <td>3 years</td>
              <td>Male</td>
              <td>Seattle, WA</td>
              <td><span class="badge green">✔ Available</span></td>
              <td class="actions-td">👁️ ✏️ 🗑️</td>
            </tr>

            <tr>
              <td class="pet-cell">
                <img class="avatar" src="assets/luna.jpg" alt="Luna" />
                <div>
                  <strong>Luna</strong><br><small>Cat</small>
                </div>
              </td>
              <td>Siamese</td>
              <td>1 year</td>
              <td>Female</td>
              <td>Portland, OR</td>
              <td><span class="badge yellow">⏱ Pending</span></td>
              <td class="actions-td">👁️ ✏️ 🗑️</td>
            </tr>

            <tr>
              <td class="pet-cell">
                <img class="avatar" src="assets/charlie.jpg" alt="Charlie" />
                <div>
                  <strong>Charlie</strong><br><small>Dog</small>
                </div>
              </td>
              <td>Beagle</td>
              <td>2 years</td>
              <td>Male</td>
              <td>San Francisco, CA</td>
              <td><span class="badge gray">✖ Adopted</span></td>
              <td class="actions-td">👁️ ✏️ 🗑️</td>
            </tr>

            <tr>
              <td class="pet-cell">
                <img class="avatar" src="assets/bella.jpg" alt="Bella" />
                <div>
                  <strong>Bella</strong><br><small>Dog</small>
                </div>
              </td>
              <td>German Shepherd</td>
              <td>4 years</td>
              <td>Female</td>
              <td>Los Angeles, CA</td>
              <td><span class="badge green">✔ Available</span></td>
              <td class="actions-td">👁️ ✏️ 🗑️</td>
            </tr>

            <tr>
              <td class="pet-cell">
                <img class="avatar" src="assets/oliver.jpg" alt="Oliver" />
                <div>
                  <strong>Oliver</strong><br><small>Cat</small>
                </div>
              </td>
              <td>Maine Coon</td>
              <td>3 years</td>
              <td>Male</td>
              <td>Chicago, IL</td>
              <td><span class="badge green">✔ Available</span></td>
              <td class="actions-td">👁️ ✏️ 🗑️</td>
            </tr>

            <tr>
              <td class="pet-cell">
                <img class="avatar" src="assets/daisy.jpg" alt="Daisy" />
                <div>
                  <strong>Daisy</strong><br><small>Dog</small>
                </div>
              </td>
              <td>Poodle</td>
              <td>1 year</td>
              <td>Female</td>
              <td>Denver, CO</td>
              <td><span class="badge yellow">⏱ Pending</span></td>
              <td class="actions-td">👁️ ✏️ 🗑️</td>
            </tr>
          </tbody>
        </table>
      </section>
    </main>
  </div>
</body>
</html>
