

<link rel="stylesheet" href="/PETVET/public/css/admin/manage_users.css">

<?php
// Example: Fetching user stats from database (replace with your actual DB queries)
// ensure $pending exists (from controller/model) to avoid warnings
$stats = [
    'totalUsers' => 12500,
    'pendingRequests' => 3,
    'vets' => 5,
    'clinics' => 7,
    'usersGrowth' => '+10%'
];

// Example: Initialize safely to avoid undefined warnings
if (!isset($stats['totalUsers'])) $stats['totalUsers'] = 12500;
if (!isset($stats['pendingRequests'])) $stats['pendingRequests'] = 3;
if (!isset($stats['vets'])) $stats['vets'] = 5;
if (!isset($stats['clinics'])) $stats['clinics'] = 7;
if (!isset($stats['usersGrowth'])) $stats['usersGrowth'] = '+0%';

// Example user list (replace with your query)
$users = [
    [
        'id' => 1,
        'name' => 'Alice Johnson',
        'email' => 'alice@example.com',
    'role' => 'Owner',
    'clinic' => 'PetCare Clinic',
        'status' => 'Active',
        'joinDate' => '2024-05-12',
        'lastLogin' => '2025-10-16'
    ],
    [
        'id' => 2,
        'name' => 'John Clinic',
        'email' => 'john@clinic.com',
    'role' => 'Vet',
    'clinic' => 'Happy Paws',
        'status' => 'Inactive',
        'joinDate' => '2024-09-03',
        'lastLogin' => '2025-09-22'
    ],
    [
        'id' => 3,
        'name' => 'Emily Smith',
        'email' => 'emily.smith@clinic.com',
    'role' => 'Groomer',
    'clinic' => '-',
        'status' => 'Active',
        'joinDate' => '2024-06-15',
        'lastLogin' => '2025-10-20'
    ],
    [
        'id' => 4,
        'name' => 'Animal Clinic',
        'email' => 'info@animalclinic.com',
        'role' => 'Clinic',
        'status' => 'Pending',
        'joinDate' => '2024-11-01',
        'lastLogin' => null
    ],
    [
        'id' => 5,
        'name' => 'Dr. Sarah Lee',
        'email' => 'sarah.lee@vetclinic.com',
    'role' => 'Vet',
    'clinic' => 'City Vet Clinic',
        'status' => 'Active',
        'joinDate' => '2024-05-12',
        'lastLogin' => '2025-10-16'
    ],
    [
        'id' => 6,
        'name' => 'Emy Smith',
        'email' => 'emy.smith@clinic.com',
    'role' => 'Trainer',
    'clinic' => '-',
        'status' => 'Pending',
        'joinDate' => '2024-11-01',
        'lastLogin' => null
    ]
];

// Remove users with role 'Vet' from the displayed list
$users = array_values(array_filter($users, function($u){ return (!isset($u['role']) || $u['role'] !== 'Vet'); }));

// Example owner details (for Pet Owners) and their pets
$ownerDetails = [
  1 => [ // Alice Johnson (not a pet owner in current sample but example)
    'phone' => '0917-555-0101',
    'address' => '123 Pet Street, Animal City, Petland 12345',
    'pets' => [
      ['id'=>101,'name' => 'Buddy', 'type' => 'Dog', 'breed' => 'Golden Retriever', 'age' => 4, 'photo'=>'/PETVET/views/shared/images/dog1.jpeg'],
      ['id'=>102,'name' => 'Lucy', 'type' => 'Cat', 'breed' => 'Siamese', 'age' => 3, 'photo'=>'/PETVET/views/shared/images/cat4.png']
    ],
    'recentActivity' => [
      ['icon'=>'✅','text'=>'Appointment booked with Dr. Emily Carter for Buddy.','time'=>'2 days ago'],
      ['icon'=>'💬','text'=>'Message sent to Happy Paws Grooming.','time'=>'5 days ago'],
      ['icon'=>'✅','text'=>'Appointment completed for Lucy\'s annual check-up.','time'=>'1 week ago']
    ]
  ],
  2 => [
    'phone' => '0922-444-0202',
    'address' => '45 Clinic Rd',
    'pets' => [],
    'recentActivity' => []
  ],
  5 => [
    'phone' => '0917-222-3333',
    'address' => '78 Pet Lane',
    'pets' => [ ['id'=>201,'name'=>'Rex','type'=>'Dog','breed'=>'Beagle','age'=>3,'photo'=>'/PETVET/views/shared/images/placeholder-pet.png'] ],
    'recentActivity' => [ ['icon'=>'✅','text'=>'Vaccination completed for Rex.','time'=>'3 days ago'] ]
  ]
];

// Example clinic details (for Clinic users)
$clinicDetails = [
  4 => [
    'name' => 'Happy Paws Veterinary Clinic',
    'joined' => '2023-01-15',
    'email' => 'contact@happypaws.vet',
    'phone' => '+1 888 123 4567',
    'location' => '456 Oak Avenue, Springfield, IL, 62704',
    'status' => 'Active',
    'services' => ['Vaccinations','Surgery','Boarding','Dental Care','Emergency Care']
  ]
];

// Example groomer details (for Groomer users)
$groomerDetails = [
  3 => [
    'name' => 'John Doe Grooming',
    'location' => 'Salon',
    'email' => 'john.doe@email.com',
    'phone' => '(123) 456-7890',
    'avatar' => '/PETVET/views/shared/images/petser.jpg',
    'services' => ['Full Grooming Package','Breed-Specific Haircut','Bathing & Brushing','Nail Trimming','De-Shedding Treatment']
  ]
];

// Example trainer details (for Trainer users)
$trainerDetails = [
  6 => [
    'name' => 'Alex Doe',
    'status' => 'Active',
    'email' => 'alex.doe@example.com',
    'phone' => '(555) 123-4567',
    'avatar' => '/PETVET/views/shared/images/petowner.jpg',
    'specializations' => ['Obedience Training','Puppy Training','Behavior Modification','Agility Training'],
    'philosophy' => 'Positive reinforcement with consistency and short sessions.',
    'testimonials' => ['Alex helped my dog become calmer and more obedient.','Great trainer — recommended!']
  ]
];

// Role => badge class mapping (extend as needed)
$roleBadge = [
  
  'Pet Owner' => 'blue',
    'Clinic' => 'red',
  'Groomer' => 'yellow',
  'Trainer' => 'green',
  'Sitter' => 'orange',
  
];

// Hardcoded pending requests for demo/testing (will be replaced by controller data)
$pending = [
  [
    'name' => 'Sarah Lee',
    'email' => 'sarah.lee@example.com',
    'role' => 'Sitter',
    'photo' => '/PETVET/views/shared/images/placeholder-avatar.png',
    'license' => 'LIC-998877',
    'experience' => '5 years',
    'docs' => [ ['label'=>'License','url'=>'#'] ]
  ],
  [
    'name' => 'Tom Wilson',
    'email' => 'tom.wilson@example.com',
    'role' => 'Groomer',
    'photo' => '/PETVET/views/shared/images/placeholder-avatar.png',
    'license' => '',
    'experience' => '3 years',
    'docs' => []
  ]
];
// Add third pending 'Animal House' clinic for screenshot parity
$pending[] = [
  'name' => 'Animal House',
  'email' => '',
  'role' => 'Clinic',
  'photo' => '/PETVET/views/shared/images/placeholder-avatar.png',
  'license' => '',
  'experience' => '',
  'docs' => []
];
?>

<link rel="stylesheet" href="/PETVET/public/css/admin/manage_users.css">

<div class="main-content">
  <header class="topbar">
    <div class="search-with-icon">
      <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="11" cy="11" r="8" stroke="#94a3b8" stroke-width="2"/>
        <path d="M21 21l-4.35-4.35" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"/>
      </svg>
      <input type="text" placeholder="Search users, roles, or email..." class="search-bar" id="globalSearch" />
    </div>
    <div class="actions">
      <button type="button" class="btn pending-requests" id="openDrawer" aria-expanded="false">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="vertical-align:middle;margin-right:6px">
          <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Pending Requests 
        <span class="pulse-badge"><?php echo htmlspecialchars($stats['pendingRequests'] ?? 0); ?></span>
      </button>
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
      <div class="card card-hover" data-stat="users">
        <div class="card-icon-bg users-bg">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
          </svg>
        </div>
        <h3>Total Registered Users</h3>
        <div class="value-with-icon">
          <p class="number animated-number" data-target="<?php echo isset($stats['totalUsers']) ? htmlspecialchars($stats['totalUsers']) : 0; ?>">0</p>
        </div>
        <span class="success"><span class="trend-up">↗</span> <?php echo isset($stats['usersGrowth']) ? htmlspecialchars($stats['usersGrowth']) : '+0%'; ?> from last month</span>
      </div>

      <div class="card card-hover" data-stat="pending">
        <div class="card-icon-bg pending-bg">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
            <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </div>
        <h3>Pending Registration Requests</h3>
        <div class="value-with-icon">
          <p class="number animated-number" data-target="<?php echo htmlspecialchars($stats['pendingRequests']); ?>">0</p>
        </div>
        <button class="mini-btn" onclick="document.getElementById('openDrawer').click()">Review Now</button>
      </div>

      <div class="card card-hover" data-stat="professionals">
        <div class="card-icon-bg professionals-bg">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="8.5" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
            <path d="M20 8v6M23 11h-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <h3>Verified Professionals</h3>
        <div class="value-with-icon">
          <p class="number animated-number" data-target="<?php echo isset($stats['verifiedProfessionals']) ? htmlspecialchars($stats['verifiedProfessionals']) : 0; ?>">0</p>
        </div>
        <span class="info-text">Vets: <?php echo $stats['vets'] ?? 0; ?> | Groomers: <?php echo $stats['groomers'] ?? 0; ?> | Breeders: <?php echo $stats['breeders'] ?? 0; ?> | Sitters: <?php echo $stats['sitters'] ?? 0; ?></span>
      </div>

      <div class="card card-hover" data-stat="clinics">
        <div class="card-icon-bg clinics-bg">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M9 22V12h6v10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <h3>Active Clinics</h3>
        <div class="value-with-icon">
          <p class="number animated-number" data-target="<?php echo isset($stats['activeClinics']) ? htmlspecialchars($stats['activeClinics']) : 0; ?>">0</p>
        </div>
        <span class="info-text">Registered Partners</span>
      </div>
    </div>

        <!-- Clinic Modal -->
        <div id="clinicModal" class="cmc-modal" aria-hidden="true" style="display:none;">
          <div class="cmc-modal-backdrop"></div>
          <div class="cmc-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="clinicModalTitle">
            <div class="cmc-modal-content">
              <div class="cmc-modal-header">
                <h3 id="clinicModalTitle">Clinic Profile</h3>
                <button id="clinicModalClose" class="icon-btn">✕</button>
              </div>
              <div id="clinicModalBody" class="cmc-modal-body"><!-- filled by JS --></div>
              <div class="cmc-modal-footer"><button id="clinicModalDone" class="btn">Close</button></div>
            </div>
          </div>
        </div>

            <!-- Groomer Modal -->
            <div id="groomerModal" class="cmc-modal" aria-hidden="true" style="display:none;">
              <div class="cmc-modal-backdrop"></div>
              <div class="cmc-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="groomerModalTitle">
                <div class="cmc-modal-content">
                  <div class="cmc-modal-header">
                    <h3 id="groomerModalTitle">Profile</h3>
                    <button id="groomerModalClose" class="icon-btn">✕</button>
                  </div>
                  <div id="groomerModalBody" class="cmc-modal-body"><!-- filled by JS --></div>
                  <div class="cmc-modal-footer"><button id="groomerModalDone" class="btn">Close</button></div>
                </div>
              </div>
            </div>

                <!-- Trainer Modal -->
                <div id="trainerModal" class="cmc-modal" aria-hidden="true" style="display:none;">
                  <div class="cmc-modal-backdrop"></div>
                  <div class="cmc-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="trainerModalTitle">
                    <div class="cmc-modal-content">
                      <div class="cmc-modal-header">
                        <h3 id="trainerModalTitle">Trainer Details</h3>
                        <button id="trainerModalClose" class="icon-btn">✕</button>
                      </div>
                      <div id="trainerModalBody" class="cmc-modal-body"><!-- filled by JS --></div>
                      <div class="cmc-modal-footer"><button id="trainerModalDone" class="btn">Close</button></div>
                    </div>
                  </div>
                </div>

    <div class="search-filter">
      <div class="filter-group">
        <svg class="filter-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="11" cy="11" r="8" stroke="#64748b" stroke-width="2"/>
          <path d="M21 21l-4.35-4.35" stroke="#64748b" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <input type="text" placeholder="Search users by name, email, or ID..." class="search-filter-input" id="tableSearch" />
      </div>
      <div class="filter-controls">
        <select id="roleFilter" class="filter-select">
          <option value="">All Roles</option>
          <option value="owner">Pet Owners</option>
          <option value="clinic">Clinics</option>
          <option value="groomer">Groomers</option>
          <option value="trainer">Trainers</option>
          <option value="sitter">Sitters</option>
        </select>
        <select id="statusFilter" class="filter-select">
          <option value="">All Status</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
          <option value="pending">Pending</option>
          <option value="suspended">Suspended</option>
        </select>
        <button class="icon-btn-filter" id="exportBtn" title="Export to CSV">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <polyline points="7 10 12 15 17 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <line x1="12" y1="15" x2="12" y2="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th>Profile</th>
          <th>Role</th>
          <th>Contact</th>
          <th>Status</th>
          <th>Date Registered</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
          <td>
            <div style="display:flex; align-items:center; gap:12px;">
              <div class="avatar" aria-hidden="true"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></div>
              <div class="user-info">
                <strong><?php echo htmlspecialchars($user['name']); ?></strong>
              </div>
            </div>
          </td>
          <td><?php $badgeClass = $roleBadge[$user['role']] ?? 'gray'; ?><span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($user['role']); ?></span></td>
          <td><?php echo htmlspecialchars($user['email']); ?></td>
          <td><span class="status-pill <?php echo strtolower($user['status']); ?>"><?php echo htmlspecialchars($user['status']); ?></span></td>
          <td><?php echo htmlspecialchars($user['joinDate']); ?></td>
          <td style="text-align:center;">
            <button class="icon action view-btn" data-user-id="<?php echo htmlspecialchars($user['id']); ?>" title="View">
              <!-- eye SVG -->
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12 5C7 5 3.1 8.1 1.5 12c1.6 3.9 5.5 7 10.5 7s8.9-3.1 10.5-7C20.9 8.1 17 5 12 5z" stroke="#2563eb" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.5" fill="#2563eb"/></svg>
            </button>
            <button class="icon action toggle-status-btn" data-user-id="<?php echo htmlspecialchars($user['id']); ?>" title="Toggle Status">
              <!-- toggle on/off SVG (acts as a status switch) -->
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><rect x="2" y="6" width="20" height="12" rx="6" stroke="#10b981" stroke-width="1.2" fill="#ecfdf5"/><circle cx="17" cy="12" r="3" fill="#10b981"/></svg>
            </button>
            <button class="icon action del-btn" data-user-id="<?php echo htmlspecialchars($user['id']); ?>" title="Delete">
              <!-- trash SVG -->
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3 6h18" stroke="#ef4444" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 6v12a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2V6" stroke="#ef4444" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 3h4l1 3H9l1-3z" stroke="#ef4444" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</div>

<!-- Pending Requests Drawer -->

<div id="pendingDrawer" class="drawer">
<div id="confirmModal" class="cmc-modal" aria-hidden="true" style="display:none;">
  <div class="cmc-modal-backdrop"></div>
  <div class="cmc-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="confirmModalTitle">
    <div class="cmc-modal-content">
      <div class="cmc-modal-header">
        <h3 id="confirmModalTitle">Confirm Deletion</h3>
        <button id="confirmClose" class="icon-btn">✕</button>
      </div>
      <div id="confirmModalBody" class="cmc-modal-body">
        <p>Are you sure you want to delete this user?</p>
      </div>
      <div class="cmc-modal-footer">
        <button id="confirmCancel" class="btn">Cancel</button>
        <button id="confirmYes" class="btn" style="background:#ef4444;color:#fff;border-color:#ef4444">Delete</button>
      </div>
    </div>
  </div>
</div>

<script>
// Enhanced features: animated counters, search, filters, export, tooltips

// Animated number counters
document.addEventListener('DOMContentLoaded', function() {
  const counters = document.querySelectorAll('.animated-number');
  counters.forEach(counter => {
    const target = parseInt(counter.getAttribute('data-target'));
    
    // Reset to 0 first to prevent accumulation
    counter.textContent = '0';
    
    const duration = 2000;
    const step = target / (duration / 16);
    let current = 0;
    
    const timer = setInterval(() => {
      current += step;
      if (current >= target) {
        counter.textContent = target.toLocaleString();
        clearInterval(timer);
      } else {
        counter.textContent = Math.floor(current).toLocaleString();
      }
    }, 16);
  });
});

// Global search functionality
const globalSearch = document.getElementById('globalSearch');
if (globalSearch) {
  globalSearch.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      if (text.includes(searchTerm)) {
        row.style.display = '';
        visibleCount++;
        // Add highlight animation
        if (searchTerm.length > 2) {
          row.classList.add('row-highlight');
          setTimeout(() => row.classList.remove('row-highlight'), 1500);
        }
      } else {
        row.style.display = 'none';
      }
    });
    
    // Show no results message
    updateTableMessage(visibleCount);
  });
}

// Table search
const tableSearch = document.getElementById('tableSearch');
if (tableSearch) {
  tableSearch.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    filterTable();
  });
}

// Role filter
const roleFilter = document.getElementById('roleFilter');
if (roleFilter) {
  roleFilter.addEventListener('change', filterTable);
}

// Status filter
const statusFilter = document.getElementById('statusFilter');
if (statusFilter) {
  statusFilter.addEventListener('change', filterTable);
}

// Combined filter function
function filterTable() {
  const searchTerm = document.getElementById('tableSearch')?.value.toLowerCase() || '';
  const roleValue = document.getElementById('roleFilter')?.value.toLowerCase() || '';
  const statusValue = document.getElementById('statusFilter')?.value.toLowerCase() || '';
  
  const rows = document.querySelectorAll('tbody tr');
  let visibleCount = 0;
  
  rows.forEach(row => {
    const text = row.textContent.toLowerCase();
    const roleCell = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
    const statusCell = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';
    
    const matchesSearch = searchTerm === '' || text.includes(searchTerm);
    const matchesRole = roleValue === '' || roleCell.includes(roleValue);
    const matchesStatus = statusValue === '' || statusCell.includes(statusValue);
    
    if (matchesSearch && matchesRole && matchesStatus) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });
  
  updateTableMessage(visibleCount);
}

// Update table message for no results
function updateTableMessage(count) {
  let messageRow = document.querySelector('.no-results-message');
  const tbody = document.querySelector('tbody');
  
  if (count === 0) {
    if (!messageRow) {
      messageRow = document.createElement('tr');
      messageRow.className = 'no-results-message';
      messageRow.innerHTML = `
        <td colspan="6" style="text-align:center;padding:40px;color:#64748b;">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin:0 auto 12px;display:block;opacity:0.5">
            <circle cx="12" cy="12" r="10" stroke="#cbd5e1" stroke-width="2"/>
            <path d="M12 8v4M12 16h.01" stroke="#cbd5e1" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <div style="font-size:16px;font-weight:600;margin-bottom:4px">No users found</div>
          <div style="font-size:14px">Try adjusting your search or filter criteria</div>
        </td>
      `;
      tbody.appendChild(messageRow);
    }
  } else {
    if (messageRow) {
      messageRow.remove();
    }
  }
}

// Export to CSV functionality
const exportBtn = document.getElementById('exportBtn');
if (exportBtn) {
  exportBtn.addEventListener('click', function() {
    const rows = Array.from(document.querySelectorAll('tbody tr')).filter(row => row.style.display !== 'none');
    
    if (rows.length === 0) {
      alert('No data to export');
      return;
    }
    
    let csvContent = 'Name,Role,Email,Status,Date Registered\n';
    
    rows.forEach(row => {
      const cells = row.querySelectorAll('td');
      const name = cells[0]?.querySelector('strong')?.textContent.trim() || '';
      const role = cells[1]?.querySelector('.badge')?.textContent.trim() || '';
      const email = cells[2]?.textContent.trim() || '';
      const status = cells[3]?.querySelector('.status-pill')?.textContent.trim() || '';
      const date = cells[4]?.textContent.trim() || '';
      
      csvContent += `"${name}","${role}","${email}","${status}","${date}"\n`;
    });
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `users_export_${new Date().toISOString().slice(0,10)}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Show success notification
    showNotification('✓ Export successful', 'success');
  });
}

// Notification system
function showNotification(message, type = 'info') {
  const notification = document.createElement('div');
  notification.className = `notification notification-${type}`;
  notification.textContent = message;
  notification.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    background: ${type === 'success' ? '#10b981' : '#3b82f6'};
    color: white;
    padding: 14px 20px;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    z-index: 10000;
    font-weight: 600;
    animation: slideIn 0.3s ease, slideOut 0.3s ease 2.7s;
  `;
  
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.remove();
  }, 3000);
}

// Add CSS for notification animations
const style = document.createElement('style');
style.textContent = `
  @keyframes slideIn {
    from { transform: translateX(400px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
  }
  @keyframes slideOut {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(400px); opacity: 0; }
  }
`;
document.head.appendChild(style);

// Add tooltips on hover
document.querySelectorAll('.action').forEach(btn => {
  btn.addEventListener('mouseenter', function(e) {
    const title = this.getAttribute('title');
    if (title) {
      const tooltip = document.createElement('div');
      tooltip.className = 'tooltip';
      tooltip.textContent = title;
      tooltip.style.cssText = `
        position: absolute;
        background: #0f172a;
        color: white;
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 12px;
        white-space: nowrap;
        z-index: 1000;
        pointer-events: none;
        transform: translateY(-100%);
        margin-top: -8px;
      `;
      
      this.style.position = 'relative';
      this.appendChild(tooltip);
      
      // Position tooltip
      const rect = this.getBoundingClientRect();
      tooltip.style.left = '50%';
      tooltip.style.transform = 'translateX(-50%) translateY(-100%)';
    }
  });
  
  btn.addEventListener('mouseleave', function() {
    const tooltip = this.querySelector('.tooltip');
    if (tooltip) tooltip.remove();
  });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
  // Ctrl/Cmd + K for search focus
  if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
    e.preventDefault();
    document.getElementById('tableSearch')?.focus();
  }
  
  // Ctrl/Cmd + E for export
  if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
    e.preventDefault();
    document.getElementById('exportBtn')?.click();
  }
});

// Add subtle entrance animation to table rows
window.addEventListener('load', function() {
  const rows = document.querySelectorAll('tbody tr');
  rows.forEach((row, index) => {
    row.style.opacity = '0';
    row.style.transform = 'translateY(20px)';
    row.style.transition = 'all 0.3s ease';
    
    setTimeout(() => {
      row.style.opacity = '1';
      row.style.transform = 'translateY(0)';
    }, index * 50);
  });
});

// ============================================
// DELETE CONFIRMATION & TOGGLE STATUS
// ============================================
let _pendingDeleteUserId = null;
const confirmModal = document.getElementById('confirmModal');
const confirmBody = document.getElementById('confirmModalBody');
const confirmYes = document.getElementById('confirmYes');
const confirmCancel = document.getElementById('confirmCancel');
const confirmClose = document.getElementById('confirmClose');

function openConfirmModal(userId, userName){
  _pendingDeleteUserId = userId;
  confirmBody.innerHTML = `<p>Are you sure you want to delete <strong>${userName}</strong>? This action cannot be undone.</p>`;
  confirmModal.style.display = '';
  confirmModal.classList.add('show');
  confirmModal.setAttribute('aria-hidden','false');
  document.body.classList.add('modal-open');
}

function closeConfirmModal(){
  _pendingDeleteUserId = null;
  confirmModal.classList.remove('show');
  confirmModal.setAttribute('aria-hidden','true');
  confirmModal.style.display = 'none';
  document.body.classList.remove('modal-open');
}

confirmCancel.addEventListener('click', closeConfirmModal);
confirmClose.addEventListener('click', closeConfirmModal);
confirmModal.querySelector('.cmc-modal-backdrop').addEventListener('click', closeConfirmModal);
confirmYes.addEventListener('click', function(){
  if(!_pendingDeleteUserId) return closeConfirmModal();
  // client-side remove row as immediate feedback
  const btn = document.querySelector(`button.del-btn[data-user-id="${_pendingDeleteUserId}"]`);
  const tr = btn ? btn.closest('tr') : null;
  if(tr) tr.remove();
  console.log('TODO: call server to delete user', _pendingDeleteUserId);
  closeConfirmModal();
});

// Delegate clicks for delete buttons anywhere in the table
document.addEventListener('click', function(e){
  // Check if clicked element or its parent is a delete button
  const del = e.target.closest('.del-btn');
  if(del){
    e.preventDefault();
    e.stopPropagation();
    const uid = del.dataset.userId || del.getAttribute('data-user-id');
    const tr = del.closest('tr');
    const name = tr ? (tr.querySelector('.user-info strong')?.textContent.trim() || 'this user') : 'this user';
    openConfirmModal(uid, name);
    return;
  }

  // Check if clicked element or its parent is a toggle status button
  const toggleBtn = e.target.closest('.toggle-status-btn');
  if(toggleBtn){
    e.preventDefault();
    e.stopPropagation();
    const uid = toggleBtn.dataset.userId || toggleBtn.getAttribute('data-user-id');
    const tr = toggleBtn.closest('tr');
    if(!tr) return;
    const statusElem = tr.querySelector('.status-pill');
    if(!statusElem) return;
    const current = statusElem.textContent.trim().toLowerCase();
    // flip between active and inactive (client-side)
    if(current === 'active'){
      statusElem.textContent = 'Inactive';
      statusElem.classList.remove('active');
      statusElem.classList.add('inactive');
    } else {
      statusElem.textContent = 'Active';
      statusElem.classList.remove('inactive');
      statusElem.classList.add('active');
    }
    console.log('TODO: send server request to toggle status for user', uid);
    return;
  }
});
</script>

<aside id="drawer" class="drawer">
  <div class="drawer-header">
    <h2>Pending Requests (<?php echo isset($pendingRequests) ? count($pendingRequests) : 0; ?>)</h2>
    <button id="closeDrawer" class="icon-btn" aria-label="Close">✕</button>
  </div>
  <div class="drawer-body">
    <?php 
    $pendingRequests = $pendingRequests ?? [];
    if (!empty($pendingRequests)): 
      foreach ($pendingRequests as $p): 
    ?>
      <div class="pending-item" data-request-id="<?= htmlspecialchars($p['request_id']) ?>">
        <div class="row">
          <div class="avatar-sm">
            <?php if (!empty($p['avatar'])): ?>
              <img src="<?= htmlspecialchars($p['avatar']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
            <?php else: ?>
              <?= htmlspecialchars(strtoupper(substr($p['name'] ?? '-',0,1))) ?>
            <?php endif; ?>
          </div>
          <div class="meta">
            <div class="name"><?= htmlspecialchars($p['name'] ?? 'Unknown') ?></div>
            <div class="role"><?= htmlspecialchars(($p['role'] ?? 'User') . ($p['email'] ? ' • ' . $p['email'] : '')) ?></div>
            <div class="role" style="font-size:12px;color:#94a3b8;">Applied: <?= htmlspecialchars(date('M d, Y', strtotime($p['applied_at']))) ?></div>
          </div>
        </div>
        <div class="pending-actions">
          <button class="btn-approve" onclick="approveRegistration(<?= htmlspecialchars($p['request_id']) ?>)">✓ Approve</button>
          <button class="btn-reject" onclick="rejectRegistration(<?= htmlspecialchars($p['request_id']) ?>)">✕ Reject</button>
        </div>
      </div>
    <?php endforeach; else: ?>
      <div class="empty">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="12" cy="12" r="10" stroke="#cbd5e1" stroke-width="2"/>
          <path d="M9 12h6M12 9v6" stroke="#cbd5e1" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <div style="font-size:16px;font-weight:600;margin-bottom:4px;color:#64748b">All clear!</div>
        <div style="font-size:14px;color:#94a3b8">No pending requests at the moment</div>
      </div>
    <?php endif; ?>
  </div>
</aside>
<div id="backdrop" class="backdrop"></div>

<script>
(function(){
  const drawer = document.getElementById('drawer');
  const backdrop = document.getElementById('backdrop');
  const closeBtn = document.getElementById('closeDrawer');

  function openDrawer(targetBtn){
    if(!drawer || !backdrop) return;
    drawer.classList.add('open');
    backdrop.classList.add('show');
    if(targetBtn) targetBtn.setAttribute('aria-expanded','true');
    // console debug to help trace issues
    try{ console.debug('Drawer opened'); }catch(e){}
  }
  function closeDrawer(){
    if(!drawer || !backdrop) return;
    drawer.classList.remove('open');
    backdrop.classList.remove('show');
    const btn = document.getElementById('openDrawer');
    if(btn) btn.setAttribute('aria-expanded','false');
    try{ console.debug('Drawer closed'); }catch(e){}
  }

  // Delegated click listener: works even if elements are re-rendered
  document.addEventListener('click', function(e){
    const openBtn = e.target.closest('#openDrawer') || e.target.closest('.pending-requests');
    if(openBtn){
      e.preventDefault();
      openDrawer(openBtn);
      return;
    }

    if(e.target.closest('#closeDrawer') || e.target.closest('.backdrop')){
      e.preventDefault();
      closeDrawer();
      return;
    }
  });

  // also allow ESC to close
  document.addEventListener('keydown', function(e){ if(e.key === 'Escape') closeDrawer(); });
})();
</script>

<!-- Owner Details Modal -->
<div id="ownerModal" class="cmc-modal" aria-hidden="true">
  <div class="cmc-modal-backdrop"></div>
  <div class="cmc-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="ownerModalTitle">
    <div class="cmc-modal-content">
      <div class="cmc-modal-header">
        <h3 id="ownerModalTitle">Owner Details</h3>
        <button id="ownerModalClose" class="icon-btn">✕</button>
      </div>
      <div id="ownerModalBody" class="cmc-modal-body"><!-- Filled by JS --></div>
      <div class="cmc-modal-footer"><button id="ownerModalDone" class="btn">Close</button></div>
    </div>
  </div>
</div>

<script>
// ownerDetails from PHP
const OWNER_DATA = <?php echo json_encode($ownerDetails, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;
    // clinic details from PHP
    const CLINIC_DATA = <?php echo json_encode($clinicDetails, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;
    const GROOMER_DATA = <?php echo json_encode($groomerDetails, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;
    const TRAINER_DATA = <?php echo json_encode($trainerDetails, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;

function showOwnerModal(userId){
  const modal = document.getElementById('ownerModal');
  const body = document.getElementById('ownerModalBody');
  const title = document.getElementById('ownerModalTitle');
  const data = OWNER_DATA[userId] || null;
  // find user basic info from table (or PHP $users could be exposed similarly)
  const tr = document.querySelector(`button.view-btn[data-user-id="${userId}"]`)?.closest('tr');
  const name = tr ? tr.querySelector('.user-info strong').textContent.trim() : 'User';
  // only show for pet owners
  const roleCell = tr ? tr.querySelector('td:nth-child(2) .badge, td:nth-child(2)') : null;
  const roleText = roleCell ? roleCell.textContent.trim().toLowerCase() : '';
  if(roleText.indexOf('owner') === -1){
    // not an owner, show a small info modal
    title.textContent = name + ' — Profile';
    body.innerHTML = '<p class="muted">This user is not a pet owner and has no pets to display.</p>';
  } else {
    title.textContent = 'User Profile';
    // build profile section
    const phone = data.phone || '—';
    const addr = data.address || '—';

    // pets
    let petsHtml = '';
    if(data.pets && data.pets.length){
      petsHtml = data.pets.map(p=>`
        <div class="owner-pet-card">
          <div class="owner-pet-info">
            <div class="owner-pet-thumb"><img src="${p.photo}" alt="${p.name}" style="width:44px;height:44px;border-radius:8px;object-fit:cover"></div>
            <div>
              <div class="owner-pet-name">${p.name}</div>
              <div class="muted">${p.type}, ${p.breed}</div>
            </div>
          </div>
          <div><button class="btn">View Profile</button></div>
        </div>
      `).join('');
    } else {
      petsHtml = '<p class="muted">No pets listed.</p>';
    }

    // recent activity
    let activityHtml = '';
    if(data.recentActivity && data.recentActivity.length){
      activityHtml = data.recentActivity.map(a=>`
        <div class="activity-item">
          <div style="width:28px;text-align:center">${a.icon}</div>
          <div>
            <div>${a.text}</div>
            <div class="muted" style="font-size:12px;margin-top:6px">${a.time}</div>
          </div>
        </div>
      `).join('');
    } else {
      activityHtml = '<p class="muted">No recent activity.</p>';
    }

    body.innerHTML = `
      <div class="owner-profile">
        <div class="owner-avatar">${(name||'')[0].toUpperCase()}</div>
        <div class="owner-meta">
          <h4 style="margin:0">${name}</h4>
          <div class="muted">Pet Owner</div>
          <div style="display:flex;gap:20px;margin-top:10px;">
            <div><div class="muted">Email</div><div>${(tr.querySelector('td:nth-child(4)')||{textContent:''}).textContent.trim()}</div></div>
            <div><div class="muted">Phone</div><div>${phone}</div></div>
          </div>
          <div style="margin-top:10px"><div class="muted">Address</div><div>${addr}</div></div>
        </div>
      </div>

      <h4 style="margin-top:18px">Associated Pets</h4>
      ${petsHtml}

      <h4 style="margin-top:18px">Recent Activity</h4>
      ${activityHtml}

      <div style="display:flex; gap:8px; justify-content:flex-end; margin-top:14px;">
        <button class="btn">Close</button>
        <button class="primary">Edit Profile</button>
      </div>
    `;
  }
  // show with CSS class for animation
  modal.setAttribute('aria-hidden','false');
  modal.classList.add('show');
  document.body.classList.add('modal-open');
}

function closeOwnerModal(){
  const modal = document.getElementById('ownerModal');
  modal.classList.remove('show');
  modal.setAttribute('aria-hidden','true');
  document.body.classList.remove('modal-open');
}

function showClinicModal(userId){
  const modal = document.getElementById('clinicModal');
  const body = document.getElementById('clinicModalBody');
  const title = document.getElementById('clinicModalTitle');
  const data = CLINIC_DATA[userId] || null;
  if(!data){
    title.textContent = 'Clinic Profile';
    body.innerHTML = '<p class="muted">No clinic information available.</p>';
  } else {
    title.textContent = 'Clinic Profile';
    const servicesHtml = (data.services||[]).map(s=>`<div class="clinic-service">${s}</div>`).join('');
    body.innerHTML = `
      <div class="clinic-header">
        <div class="clinic-avatar">${(data.name||'')[0]||'C'}</div>
        <div class="clinic-meta">
          <h4>${data.name}</h4>
          <div class="muted">Date Joined: ${data.joined}</div>
        </div>
      </div>
      <div class="clinic-row">
        <div>
          <div style="margin-top:8px"><strong>Email</strong><div>${data.email}</div></div>
        </div>
        <div>
          <div style="margin-top:8px"><strong>Phone</strong><div>${data.phone}</div></div>
        </div>
        <div>
          <div style="margin-top:8px"><strong>Location</strong><div>${data.location}</div></div>
        </div>
        <div>
          <div style="margin-top:8px"><strong>Clinic Status</strong><div class="clinic-status">${data.status}</div></div>
        </div>
      </div>
      <h4 style="margin-top:14px">Services Offered</h4>
      <div class="clinic-services">${servicesHtml}</div>
    `;
  }
  modal.style.display = '';
  modal.classList.add('show');
  modal.setAttribute('aria-hidden','false');
  document.body.classList.add('modal-open');
}

function closeClinicModal(){
  const modal = document.getElementById('clinicModal');
  modal.classList.remove('show');
  modal.setAttribute('aria-hidden','true');
  modal.style.display = 'none';
  document.body.classList.remove('modal-open');
}

function showGroomerModal(userId){
  const modal = document.getElementById('groomerModal');
  const body = document.getElementById('groomerModalBody');
  const title = document.getElementById('groomerModalTitle');
  const data = GROOMER_DATA[userId] || null;
  if(!data){
    title.textContent = 'Profile';
    body.innerHTML = '<p class="muted">No groomer data available.</p>';
  } else {
    title.textContent = data.name;
    const servicesHtml = (data.services||[]).map(s=>`<div class="groomer-service"><div class="icon">✂️</div><div>${s}</div></div>`).join('');
    body.innerHTML = `
      <div class="groomer-profile">
        <div class="groomer-top">
          <div class="groomer-avatar"><img src="${data.avatar}" alt="${data.name}"></div>
          <div>
            <div style="font-weight:700;font-size:18px">${data.name}</div>
            <div class="muted">Location: ${data.location}</div>
          </div>
        </div>
        <div class="contact-card" style="background:#fff;border:1px solid #eef2f7;padding:12px;border-radius:8px">
          <div><strong>Contact Information</strong></div>
          <div style="margin-top:8px">${data.email}</div>
          <div style="margin-top:6px">${data.phone}</div>
        </div>
        <div>
          <div><strong>Services Offered</strong></div>
          <div class="groomer-services">${servicesHtml}</div>
        </div>
      </div>
    `;
  }
  modal.style.display = '';
  modal.classList.add('show');
  modal.setAttribute('aria-hidden','false');
  document.body.classList.add('modal-open');
}

function closeGroomerModal(){
  const modal = document.getElementById('groomerModal');
  modal.classList.remove('show');
  modal.setAttribute('aria-hidden','true');
  modal.style.display = 'none';
  document.body.classList.remove('modal-open');
}

function showTrainerModal(userId){
  const modal = document.getElementById('trainerModal');
  const body = document.getElementById('trainerModalBody');
  const title = document.getElementById('trainerModalTitle');
  const data = TRAINER_DATA[userId] || null;
  if(!data){
    title.textContent = 'Trainer Details';
    body.innerHTML = '<p class="muted">No trainer data available.</p>';
  } else {
    title.textContent = data.name;
    const specsHtml = (data.specializations||[]).map(s=>`<div class="trainer-spec"><div class="icon">✔️</div><div>${s}</div></div>`).join('');
    const testimonialsHtml = (data.testimonials||[]).map(t=>`<div style="padding:8px 0;border-bottom:1px dashed #eef2f7">${t}</div>`).join('');
    body.innerHTML = `
      <div class="trainer-header">
        <div class="trainer-avatar"><img src="${data.avatar}" alt="${data.name}" style="width:72px;height:72px;object-fit:cover;border-radius:10px"></div>
        <div>
          <div style="font-weight:700;font-size:18px">${data.name} <span class="status-pill ${data.status.toLowerCase()}">${data.status}</span></div>
          <div class="muted">${data.email} | ${data.phone}</div>
          <div class="trainer-contact-buttons">
            <button class="btn" style="background:#1e90ff;color:#fff">Call</button>
            <button class="btn">Email</button>
          </div>
        </div>
      </div>
      <div class="trainer-specs">${specsHtml}</div>
      <div class="trainer-toggle" id="trainerPhilosophyToggle">Training Philosophy <span>▾</span></div>
      <div id="trainerPhilosophy" style="display:none;padding:10px;background:#fff;border:1px solid #eef2f7;border-radius:8px;margin-top:8px">${data.philosophy}</div>
      <div class="trainer-toggle" id="trainerTestimonialsToggle">Client Testimonials <span>▾</span></div>
      <div id="trainerTestimonials" style="display:none;padding:10px;background:#fff;border:1px solid #eef2f7;border-radius:8px;margin-top:8px">${testimonialsHtml}</div>
    `;

    // Add toggle handlers after injecting HTML
    setTimeout(()=>{
      document.getElementById('trainerPhilosophyToggle').addEventListener('click', ()=>{
        const el = document.getElementById('trainerPhilosophy');
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
      });
      document.getElementById('trainerTestimonialsToggle').addEventListener('click', ()=>{
        const el = document.getElementById('trainerTestimonials');
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
      });
    }, 80);
  }
  modal.style.display = '';
  modal.classList.add('show');
  modal.setAttribute('aria-hidden','false');
  document.body.classList.add('modal-open');
}

function closeTrainerModal(){
  const modal = document.getElementById('trainerModal');
  modal.classList.remove('show');
  modal.setAttribute('aria-hidden','true');
  modal.style.display = 'none';
  document.body.classList.remove('modal-open');
}

document.addEventListener('click', function(e){
  const vb = e.target.closest('.view-btn');
  if(vb){
    const uid = vb.dataset.userId;
    if(uid){
      const tr = vb.closest('tr');
      const roleCell = tr ? tr.querySelector('td:nth-child(2) .badge, td:nth-child(2)') : null;
      const roleText = roleCell ? roleCell.textContent.trim().toLowerCase() : '';
      if(roleText.indexOf('clinic') !== -1){
        showClinicModal(uid);
      } else if(roleText.indexOf('groom') !== -1 || roleText.indexOf('groomer') !== -1){
        showGroomerModal(uid);
      } else if(roleText.indexOf('trainer') !== -1){
        showTrainerModal(uid);
      } else {
        showOwnerModal(uid);
      }
    }
  }
  if(e.target.closest('#ownerModalClose') || e.target.closest('#ownerModalDone')) closeOwnerModal();
  // close when clicking on modal backdrop
  if(e.target.classList && e.target.classList.contains('cmc-modal-backdrop')) closeOwnerModal();
});

document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeOwnerModal(); });

// clinic modal close wiring
document.getElementById('clinicModalClose').addEventListener('click', closeClinicModal);
document.getElementById('clinicModalDone').addEventListener('click', closeClinicModal);
document.querySelector('#clinicModal .cmc-modal-backdrop').addEventListener('click', closeClinicModal);
// make Escape close clinic modal also
document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeClinicModal(); });

// groomer modal close wiring
document.getElementById('groomerModalClose').addEventListener('click', closeGroomerModal);
document.getElementById('groomerModalDone').addEventListener('click', closeGroomerModal);
document.querySelector('#groomerModal .cmc-modal-backdrop').addEventListener('click', closeGroomerModal);
document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeGroomerModal(); });

// trainer modal close wiring
document.getElementById('trainerModalClose').addEventListener('click', closeTrainerModal);
document.getElementById('trainerModalDone').addEventListener('click', closeTrainerModal);
document.querySelector('#trainerModal .cmc-modal-backdrop').addEventListener('click', closeTrainerModal);
document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeTrainerModal(); });

// Approve registration function
async function approveRegistration(requestId) {
  if (!confirm('Are you sure you want to approve this registration request?')) {
    return;
  }
  
  try {
    const formData = new FormData();
    formData.append('request_id', requestId);
    formData.append('notes', 'Approved by admin');
    
    const response = await fetch('/PETVET/api/admin/approve-registration.php', {
      method: 'POST',
      body: formData
    });
    
    const result = await response.json();
    
    if (result.success) {
      alert('Registration approved successfully!');
      // Remove the item from UI
      const item = document.querySelector(`[data-request-id="${requestId}"]`);
      if (item) item.remove();
      
      // Update pending count
      const countEl = document.querySelector('#drawer .drawer-header h2');
      if (countEl) {
        const currentCount = parseInt(countEl.textContent.match(/\d+/)[0]);
        countEl.textContent = `Pending Requests (${currentCount - 1})`;
      }
      
      // Reload page to update stats
      setTimeout(() => window.location.reload(), 1000);
    } else {
      alert('Error: ' + (result.message || 'Failed to approve registration'));
    }
  } catch (error) {
    console.error('Error approving registration:', error);
    alert('An error occurred while approving the registration');
  }
}

// Reject registration function
async function rejectRegistration(requestId) {
  const reason = prompt('Please provide a reason for rejection (optional):');
  if (reason === null) {
    return; // User cancelled
  }
  
  if (!confirm('Are you sure you want to reject this registration request?')) {
    return;
  }
  
  try {
    const formData = new FormData();
    formData.append('request_id', requestId);
    formData.append('reason', reason || 'No reason provided');
    
    const response = await fetch('/PETVET/api/admin/reject-registration.php', {
      method: 'POST',
      body: formData
    });
    
    const result = await response.json();
    
    if (result.success) {
      alert('Registration rejected successfully');
      // Remove the item from UI
      const item = document.querySelector(`[data-request-id="${requestId}"]`);
      if (item) item.remove();
      
      // Update pending count
      const countEl = document.querySelector('#drawer .drawer-header h2');
      if (countEl) {
        const currentCount = parseInt(countEl.textContent.match(/\d+/)[0]);
        countEl.textContent = `Pending Requests (${currentCount - 1})`;
      }
      
      // Reload page to update stats
      setTimeout(() => window.location.reload(), 1000);
    } else {
      alert('Error: ' + (result.message || 'Failed to reject registration'));
    }
  } catch (error) {
    console.error('Error rejecting registration:', error);
    alert('An error occurred while rejecting the registration');
  }
}
</script>
