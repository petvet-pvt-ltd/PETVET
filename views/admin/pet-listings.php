<link rel="stylesheet" href="/PETVET/public/css/admin/pet_listings.css" />
<div class="main-content">
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

  <section class="page-header">
    <div>
      <h1>Pet Listings</h1>
      <p>Manage pets available for adoption</p>
    </div>
  </section>

  <div class="filters">
    <div class="filter-left">
      <input type="text" class="filter-input" placeholder="🔍 Search pets..." />
    </div>
    <select class="filter-select"><option>All Statuses</option><option>Available</option><option>Pending</option><option>Adopted</option></select>
    <select class="filter-select"><option>All Breeds</option><option>Labrador Retriever</option><option>Siamese</option><option>Beagle</option><option>German Shepherd</option><option>Maine Coon</option><option>Poodle</option></select>
    <select class="filter-select"><option>All Ages</option><option>0–1 year</option><option>1–3 years</option><option>3–6 years</option><option>6+ years</option></select>
  </div>

  <section class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>PET</th><th>BREED</th><th>AGE</th><th>GENDER</th><th>LOCATION</th><th>STATUS</th><th>ACTIONS</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="pet-cell"><img class="avatar" src="/PETVET/views/shared/images/sidebar/pets.png" alt="Max" /><div><strong>Max</strong><br><small>Dog</small></div></td>
          <td>Labrador Retriever</td><td>3 years</td><td>Male</td><td>Seattle, WA</td><td><span class="badge green">✔ Available</span></td><td class="actions-td">👁️ ✏️ 🗑️</td>
        </tr>
      </tbody>
    </table>
  </section>
</div>
