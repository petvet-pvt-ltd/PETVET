<link rel="stylesheet" href="/PETVET/public/css/admin/medical_records.css" />
<div class="main-content">
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

  <section class="overview">
    <h1>Medical Records</h1>
    <p>View and manage patient medical records</p>

    <div class="cards">
      <div class="card"><h3>Total Records</h3><p class="number"><?php echo $stats['totalRecords']; ?></p><small><?php echo htmlspecialchars($stats['newThisWeek']); ?></small></div>
      <div class="card"><h3>Vaccinations</h3><p class="number"><?php echo $stats['vaccinations']; ?></p><small><?php echo htmlspecialchars($stats['vaccinationNote']); ?></small></div>
      <div class="card"><h3>Chronic Conditions</h3><p class="number"><?php echo $stats['chronicConditions']; ?></p><small><?php echo htmlspecialchars($stats['chronicNote']); ?></small></div>
      <div class="card"><h3>Last Updated</h3><p class="number"><?php echo htmlspecialchars($stats['lastUpdated']); ?></p><small><?php echo htmlspecialchars($stats['updateNote']); ?></small></div>
    </div>

    <div class="search-filter">
      <input type="text" placeholder="🔍 Search by pet name, owner, or condition..." class="search-filter-input" />
      <select><option>All Records</option></select>
      <input type="date" />
    </div>

    <table>
      <thead>
        <tr>
          <th>Record ID</th><th>Pet Name</th><th>Medical Condition</th><th>Veterinarian</th><th>Treatment</th><th>Date</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($records as $record): ?>
        <tr>
          <td><?php echo htmlspecialchars($record['id']); ?></td>
          <td><?php echo htmlspecialchars($record['petName']); ?><br><small>Owner: <?php echo htmlspecialchars($record['ownerName']); ?></small></td>
          <td><?php echo htmlspecialchars($record['condition']); ?></td>
          <td><?php echo htmlspecialchars($record['vet']); ?></td>
          <td>Treatment details</td>
          <td><?php echo htmlspecialchars($record['date']); ?></td>
          <td>👁️ ✏️ 📄</td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</div>
