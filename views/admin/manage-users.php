<link rel="stylesheet" href="/PETVET/public/css/admin/manage_users.css">
<div class="main-content">
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
          <p class="number"><?php echo $stats['totalUsers']; ?></p>
          <div class="icon">👤</div>
        </div>
        <span class="success"><?php echo $stats['usersGrowth']; ?> from last month</span>
      </div>
      <div class="card">
        <h3>Active Users</h3>
        <div class="value-with-icon">
          <p class="number"><?php echo $stats['activeUsers']; ?></p>
          <div class="icon">✅</div>
        </div>
        <span class="success"><?php echo $stats['activePercent']; ?> of total users</span>
      </div>
      <div class="card">
        <h3>Vets</h3>
        <div class="value-with-icon">
          <p class="number"><?php echo $stats['vets']; ?></p>
          <div class="icon">🆕</div>
        </div>
        <span class="success">Medical Professionals</span>
      </div>
      <div class="card">
        <h3>Clinic Managers</h3>
        <div class="value-with-icon">
          <p class="number"><?php echo $stats['clinicManagers']; ?></p>
          <div class="icon">❌</div>
        </div>
        <span class="success">Administrative Staff</span>
      </div>
    </div>

    <div class="search-filter">
      <input type="text" placeholder="Search users by name, email, or ID..." class="search-filter-input" />
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
        <?php foreach ($users as $user): ?>
        <tr>
          <td>
            <div class="avatar"><?php echo strtoupper(substr($user['name'], 0, 1) . substr(strstr($user['name'], ' '), 1, 1)); ?></div>
            <div class="user-info">
              <strong><?php echo htmlspecialchars($user['name']); ?></strong><br>
              <small><?php echo htmlspecialchars($user['email']); ?></small><br>
              <small>ID: <?php echo htmlspecialchars($user['id']); ?></small>
            </div>
          </td>
          <td>
            <span class="badge <?php echo $user['role'] === 'Vet' ? 'green' : ($user['role'] === 'Clinic Manager' ? 'orange' : 'blue'); ?>">
              <?php echo htmlspecialchars($user['role']); ?>
            </span>
          </td>
          <td>
            <span class="badge <?php echo $user['status'] === 'Active' ? 'green' : 'red'; ?>">
              <?php echo htmlspecialchars($user['status']); ?>
            </span>
          </td>
          <td>-</td>
          <td><?php echo htmlspecialchars($user['joinDate']); ?></td>
          <td><?php echo htmlspecialchars($user['lastLogin']); ?></td>
          <td>👁 ✏️ 📩 🗑</td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</div>
