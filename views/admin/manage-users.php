

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
    <input type="text" placeholder="Search..." class="search-bar" />
    <div class="actions">
  <button type="button" class="btn pending-requests" id="openDrawer" aria-expanded="false">🔔 Pending User Requests <span class="badge" style="background:#dc2626;color:#fff;margin-left:8px"><?php echo htmlspecialchars($stats['pendingRequests'] ?? 0); ?></span></button>
  
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
        <h3>Total Registered Users</h3>
        <div class="value-with-icon">
          <p class="number"><?php echo htmlspecialchars($stats['totalUsers']); ?></p>
          <div class="icon">👤</div>
        </div>
        <span class="success"><?php echo htmlspecialchars($stats['usersGrowth']); ?> from last month</span>
      </div>

      <div class="card">
        <h3>Pending Registration Requests</h3>
        <div class="value-with-icon">
          <p class="number"><?php echo htmlspecialchars($stats['pendingRequests']); ?></p>
        </div>
      </div>

      <div class="card">
        <h3>Verified Professionals</h3>
        <div class="value-with-icon">
          <p class="number"><?php echo htmlspecialchars($stats['vets']); ?></p>
        </div>
       
      </div>

      <div class="card">
        <h3>Active Clinics</h3>
        <div class="value-with-icon">
          <p class="number"><?php echo htmlspecialchars($stats['clinics']); ?></p>
          
        </div>
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

                <style>
                /* Trainer modal styling */
                .trainer-header{display:flex;gap:12px;align-items:center}
                .trainer-avatar{width:72px;height:72px;border-radius:12px;overflow:hidden}
                .trainer-contact-buttons{display:flex;gap:12px;margin-top:12px}
                .trainer-contact-buttons .btn{flex:1}
                .trainer-specs{margin-top:12px}
                .trainer-spec{display:flex;align-items:center;gap:10px;padding:10px;border-radius:8px;background:#fff;border:1px solid #eef2f7;margin-bottom:8px}
                .trainer-toggle{display:flex;justify-content:space-between;align-items:center;padding:10px;border-radius:8px;background:#fff;border:1px solid #eef2f7;margin-top:8px}
                </style>

            <style>
            /* Groomer modal styling - vertical card */
            .groomer-profile{display:flex;flex-direction:column;gap:14px}
            .groomer-top{display:flex;gap:12px;align-items:center}
            .groomer-avatar{width:64px;height:64px;border-radius:12px;overflow:hidden;background:#f3f4f6;display:flex;align-items:center;justify-content:center}
            .groomer-avatar img{width:100%;height:100%;object-fit:cover}
            .groomer-services{margin-top:6px}
            .groomer-service{display:flex;align-items:center;gap:10px;padding:12px;border-radius:10px;background:#fff;border:1px solid #eef2f7;margin-bottom:8px}
            .groomer-service .icon{width:36px;height:36px;border-radius:10px;background:#e6f6f6;display:flex;align-items:center;justify-content:center;color:#10b981}
            </style>

        <style>
        /* Clinic modal specific */
        .clinic-header{display:flex;gap:14px;align-items:center}
        .clinic-avatar{width:48px;height:48px;border-radius:10px;background:#eef2ff;display:flex;align-items:center;justify-content:center;font-weight:700;color:#2563eb}
        .clinic-meta h4{margin:0}
        .clinic-row{display:block;margin-top:12px}
        .clinic-row > div{margin-bottom:10px}
        .clinic-services{display:flex;gap:8px;flex-wrap:wrap;margin-top:12px}
        .clinic-service{background:#eef2ff;padding:6px 10px;border-radius:999px;color:#2563eb;border:1px solid #e1e8ff}
        .clinic-status{display:inline-block;padding:6px 10px;border-radius:999px;background:#ecfdf5;color:#065f46}
        </style>

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
<style>
  /* owner modal specific */
  .owner-profile{display:flex;gap:12px;align-items:center}
  .owner-avatar{width:72px;height:72px;border-radius:12px;background:linear-gradient(135deg,#6fb1ff,#9b8cff);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:28px}
  .owner-meta h4{font-size:18px}
  .owner-pet-card{display:flex;align-items:center;justify-content:space-between;padding:10px;border-radius:10px;background:#fff;margin-top:8px;box-shadow:0 1px 2px rgba(0,0,0,0.04)}
  .owner-pet-info{display:flex;gap:10px;align-items:center}
  .owner-pet-thumb{width:44px;height:44px;flex:0 0 44px;border-radius:8px;overflow:hidden}
  .owner-pet-name{font-weight:600}
  .activity-item{display:flex;gap:10px;align-items:flex-start;padding:8px 0;border-bottom:1px dashed rgba(0,0,0,0.04)}
  .activity-item:last-child{border-bottom:none}
  .muted{color:#6b7280}
/* Minimal drawer/backdrop styles (used by clinic-manager; ensure same behaviour here) */
.drawer {
  position: fixed;
  right: -360px;
  top: 0;
  height: 100%;
  width: 340px;
  background: #fff;
  border-left: 1px solid #e5e7eb;
  box-shadow: -6px 0 18px rgba(15,23,42,0.06);
  transition: right 220ms ease;
  z-index: 1100;
  display: flex;
  flex-direction: column;
}
.drawer.open { right: 0; }
.drawer-header { display:flex; align-items:center; justify-content:space-between; padding:16px; border-bottom:1px solid #eef2f7; }
.drawer-body { padding:16px; overflow:auto; }
.pending-item { background: linear-gradient(180deg, #fff8e1 0%, #fff6d9 100%); border:1px solid #fce7a7; border-radius:8px; padding:12px; margin-bottom:12px; display:block; }
.pending-item .row { display:flex; align-items:center; gap:12px; }
.pending-item .meta { display:block; }
.pending-item .meta .role { color:#6b7280; font-size:13px; margin-top:6px; }
.pending-actions { margin-top:10px; display:flex; gap:10px; }
.btn-approve { background:#22c55e; color:#fff; border:none; padding:8px 18px; border-radius:8px; font-weight:700; }
.btn-reject { background:#ef4444; color:#fff; border:none; padding:8px 18px; border-radius:8px; font-weight:700; }
.avatar-sm { width:40px; height:40px; border-radius:8px; object-fit:cover; }
.backdrop { position:fixed; inset:0; background:rgba(2,6,23,0.45); opacity:0; visibility:hidden; transition:opacity 180ms ease; z-index:1000; }
.backdrop.show { opacity:1; visibility:visible; }
.icon-btn { background:none; border:none; font-size:18px; cursor:pointer; }
.empty { padding:16px; color:#6b7280; }
</style>

<!-- Page polish styles: table, badges, buttons, modal -->
<style>
/* Layout polish */
.main-content { font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; color: #0f172a; }
.overview h1 { font-size: 20px; margin:0 0 6px; }
.cards .card { border-radius: 12px; box-shadow: 0 6px 18px rgba(15,23,42,0.04); }

/* Table polish */
table { width:100%; border-collapse: collapse; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 6px 18px rgba(2,6,23,0.04); }
thead th { background:#f8fafc; color:#334155; font-weight:700; padding:14px; text-align:left; font-size:13px; }
tbody td { padding:14px; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
tbody tr:hover { background:#fbfdff; }

.avatar { width:44px; height:44px; border-radius:10px; display:inline-flex; align-items:center; justify-content:center; background:#0ea5a9; color:#fff; font-weight:800; margin-right:12px; }
.user-info small { color:#64748b; display:block; }

/* Badges */
.badge { padding:6px 10px; border-radius:999px; font-weight:700; text-transform:capitalize; font-size:12px; }
.badge.green { background:#ecfdf5; color:#065f46; border:1px solid #bbf7d0; }
.badge.blue { background:#eff6ff; color:#1e3a8a; border:1px solid #bfdbfe; }
.badge.orange { background:#fff7ed; color:#92400e; border:1px solid #fed7aa; }
.badge.gray { background:#f8fafc; color:#334155; border:1px solid #e6eef7; }
.badge.yellow { background:#fff8e1; color:#92400e; border:1px solid #fde68a; }

/* Action buttons */
.action { border:0; background:transparent; cursor:pointer; font-size:16px; padding:8px; border-radius:8px; transition:all .12s ease; }
.action:hover { background:rgba(2,6,23,0.04); transform:translateY(-1px); }
.view-btn { color:#2563eb; font-weight:700; }
.edit-btn { color:#10b981; }
.msg-btn { color:#7c3aed; }
.del-btn { color:#ef4444; }

/* Status pills (light backgrounds) */
.status-pill { display:inline-block; padding:6px 12px; border-radius:999px; font-weight:700; font-size:13px; }
.status-pill.active { background:#ecfdf5; color:#065f46; }
.status-pill.pending { background:#fff7ed; color:#92400e; }
.status-pill.suspended { background:#fee2e2; color:#991b1b; }
.status-pill.inactive { background:#f1f5f9; color:#334155; border:1px solid #e2e8f0 }

/* Action icons styling */
.icon { border-radius:6px; padding:6px; }
.icon span { font-size:16px; }

/* Drawer card tweak */
.pending-item { transition: transform .12s ease, box-shadow .12s ease; }
.pending-item:hover { transform: translateY(-6px); box-shadow: 0 10px 30px rgba(2,6,23,0.06); }

/* Modal styles */
.cmc-modal { display:none; }
.cmc-modal.show { display:flex; }
.owner-profile { display:flex; gap:12px; align-items:center; }
.owner-avatar { width:68px; height:68px; border-radius:12px; background:#2563eb; color:#fff; display:inline-flex; align-items:center; justify-content:center; font-weight:800; font-size:20px; }
.owner-meta { color:#475569; }
.owner-meta p { margin:6px 0; }
.pet-list { margin-top:12px; }
.pet-list li { margin-bottom:8px; color:#0f172a; }

/* Responsive tweaks */
@media (max-width:720px){
  .owner-avatar { width:56px; height:56px; }
}
</style>

<style>
/* Modal polished styles */
.cmc-modal { position: fixed; inset: 0; display: grid; place-items: center; z-index: 1400; pointer-events: none; }
.cmc-modal .cmc-modal-backdrop { position:absolute; inset:0; background: rgba(2,6,23,0.62); backdrop-filter: blur(5px); opacity:0; transition: opacity .18s ease; }
.cmc-modal .cmc-modal-dialog { position:relative; z-index:2; max-width:920px; width:92%; max-height:86vh; overflow:auto; transform: translateY(18px) scale(.98); transition: transform .18s ease, opacity .12s ease; opacity:0; }
.cmc-modal .cmc-modal-content { background:#fff; border-radius:12px; padding:22px; box-shadow: 0 28px 80px rgba(2,6,23,0.18); }
.cmc-modal .cmc-modal-header { display:flex; align-items:center; justify-content:space-between; }
.cmc-modal .cmc-modal-body { margin-top:12px; color:#334155; }
.cmc-modal .cmc-modal-footer { margin-top:14px; text-align:right; }
.cmc-modal.show { pointer-events: auto; }
.cmc-modal.show .cmc-modal-backdrop { opacity:1; }
.cmc-modal.show .cmc-modal-dialog { transform: translateY(0) scale(1); opacity:1; }

/* ensure centered dialog and disable page scroll while modal open */
body.modal-open{overflow:hidden;height:100%;}
.cmc-modal .cmc-modal-dialog{margin:0 auto}

.pet-list { margin-top:10px; padding-left:18px; }
.pet-list li { background:#f8fafc; border:1px solid #e6eef7; padding:8px 10px; border-radius:8px; margin-bottom:8px; }

.btn { padding:8px 14px; border-radius:8px; border:1px solid #cbd5e1; background:#fff; cursor:pointer; }
.btn:hover { box-shadow: 0 6px 18px rgba(2,6,23,0.06); }
</style>

<!-- Delete confirmation modal -->
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
// Delete confirmation modal logic
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
  const del = e.target.closest('.del-btn');
  if(del){
    const uid = del.dataset.userId || del.getAttribute('data-user-id');
    const tr = del.closest('tr');
    const name = tr ? (tr.querySelector('.user-info strong')?.textContent.trim() || 'this user') : 'this user';
    openConfirmModal(uid, name);
    return;
  }

  const toggleBtn = e.target.closest('.toggle-status-btn');
  if(toggleBtn){
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
    <h2>Pending Requests (<?php echo count($pending); ?>)</h2>
    <button id="closeDrawer" class="icon-btn" aria-label="Close">✕</button>
  </div>
  <div class="drawer-body">
    <?php if (!isset($pending) || !$pending) { $pending = []; } ?>
    <?php if ($pending): foreach ($pending as $p): ?>
      <div class="pending-item">
        <div class="row">
          <div class="avatar-sm" style="background:#2563eb;color:#fff;display:inline-flex;align-items:center;justify-content:center;"><?= htmlspecialchars(strtoupper(substr($p['name'] ?? '-',0,1))) ?></div>
          <div class="meta">
            <div style="font-weight:700"><?= htmlspecialchars($p['name'] ?? 'Unknown') ?></div>
            <div class="role"><?= htmlspecialchars($p['role'] . ($p['email'] ? ' • ' . $p['email'] : '')) ?></div>
          </div>
        </div>
        <div class="pending-actions">
          <button class="btn-approve">Approve</button>
          <button class="btn-reject">Reject</button>
        </div>
      </div>
    <?php endforeach; else: ?>
      <div class="empty">No pending requests.</div>
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
</script>
