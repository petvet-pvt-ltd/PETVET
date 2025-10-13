<?php
/* Filters */
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$role = $_GET['role'] ?? 'all';
$filtered = array_filter($staff, function($s) use ($q,$role){
  $okQ = $q==='' || stripos($s['name'].' '.$s['email'].' '.$s['role'], $q)!==false;
  $okRole = $role==='all' || $s['role']===$role;
  return $okQ && $okRole;
});

$total_staff = count($staff);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Staff Management | Clinic Manager</title>
<link rel="stylesheet" href="/PETVET/public/css/clinic-manager/enhanced-global.css">
<link rel="stylesheet" href="/PETVET/public/css/clinic-manager/vets.css">
<style>
  .role-badge {
    display: inline-block;
    background: var(--blue-100);
    color: var(--blue-600);
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    text-transform: capitalize;
  }
  
  .contact-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }
  
  .contact-phone {
    font-weight: 600;
    color: var(--gray-900);
  }
  
  .contact-email {
    color: var(--gray-500);
    font-size: 13px;
  }
  
  .staff-modal {
    position: fixed;
    inset: 0;
    display: none;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
  }
  
  .staff-modal.show {
    display: flex;
  }
  
  .staff-modal-dialog {
    background: white;
    width: min(520px, 92%);
    border-radius: var(--border-radius);
    padding: 24px;
    box-shadow: var(--shadow-xl);
    animation: modalSlideIn 0.3s ease;
  }
  
  @keyframes modalSlideIn {
    from {
      opacity: 0;
      transform: translateY(-20px) scale(0.95);
    }
    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }
  
  .staff-form {
    display: grid;
    gap: 16px;
  }
  
  .staff-form .row {
    display: grid;
    gap: 16px;
    grid-template-columns: 1fr 1fr;
  }
  
  .staff-form label {
    display: flex;
    flex-direction: column;
    gap: 6px;
    font-weight: 600;
    color: var(--gray-700);
  }
  
  .staff-modal-actions {
    margin-top: 20px;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
  }
  
  @media (max-width: 640px) {
    .staff-form .row {
      grid-template-columns: 1fr;
    }
  }
</style>
</head>
<body>
<main class="main-content">
  <header class="page-header">
    <div>
      <h1 class="page-title">Staff Management</h1>
      <p class="page-subtitle">Manage clinic support staff and assistants</p>
    </div>
    <div class="cmc-actions">
      <button class="btn btn-primary" id="openAddStaff">➕ Add Staff Member</button>
    </div>
  </header>
  
  <section class="cmc-cards">
    <article class="card">
      <div class="card-label">Total Staff</div>
      <div class="card-value"><?= $total_staff ?></div>
      <div class="card-sub">Active team members</div>
    </article>
    <article class="card">
      <div class="card-label">Roles</div>
      <div class="card-value"><?= count(array_unique(array_column($staff, 'role'))) ?></div>
      <div class="card-sub">Different positions</div>
    </article>
  </section>

  <form method="get" class="cmc-filters">
    <input type="hidden" name="module" value="clinic-manager">
    <input type="hidden" name="page" value="staff">
    <div class="field full-grow"><input class="input" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search by name, email or role"></div>
    <div class="field">
      <label class="label" for="role">Role</label>
      <select class="select" name="role" id="role">
        <option value="all" <?= $role==='all'?'selected':'' ?>>All</option>
        <option value="Veterinary Assistant" <?= $role==='Veterinary Assistant'?'selected':'' ?>>Vet Assistant</option>
        <option value="Front Desk" <?= $role==='Front Desk'?'selected':'' ?>>Front Desk</option>
        <option value="Support Staff" <?= $role==='Support Staff'?'selected':'' ?>>Support</option>
      </select>
    </div>
    <div class="field"><button class="btn btn-primary" type="submit">Apply Filters</button></div>
  <div class="field"><a class="btn btn-ghost" href="/PETVET/index.php?module=clinic-manager&page=staff">Clear</a></div>
  </form>

  <section class="cmc-table-card">
    <div class="cmc-table-wrap">
    <table class="cmc-table staff-table">
  <thead><tr><th>Name</th><th>Role</th><th>Contact</th><th class="col-actions">Actions</th></tr></thead>
        <tbody>
        <?php if($filtered): foreach($filtered as $s): ?>
          <tr>
            <td>
              <div class="cell-user">
                <img class="avatar-sm" src="<?= htmlspecialchars($s['photo']) ?>" alt="">
                <span class="link"><?= htmlspecialchars($s['name']) ?></span>
              </div>
            </td>
            <td><?= htmlspecialchars($s['role']) ?></td>
            <td><div><?= htmlspecialchars($s['phone']) ?></div><div class="muted"><?= htmlspecialchars($s['email']) ?></div></td>
            <td>
              <div class="row-actions">
                <button class="action-btn" title="Edit">✏️</button>
                <button class="action-btn staff-delete" data-name="<?= htmlspecialchars($s['name']) ?>" title="Delete">🗑️</button>
              </div>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="4" class="empty">No staff found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>

<!-- Modal Add Staff -->
<div id="addStaffModal" class="staff-modal" role="dialog" aria-modal="true" aria-labelledby="addStaffTitle">
  <div class="staff-modal-dialog">
    <h3 id="addStaffTitle">Add Staff Member</h3>
    <form id="staffForm" class="staff-form">
      <div class="row">
        <label>Full Name<input name="name" required></label>
        <label>Role<select name="role" required>
          <option value="Veterinary Assistant">Veterinary Assistant</option>
          <option value="Front Desk">Front Desk</option>
          <option value="Support Staff">Support Staff</option>
        </select></label>
      </div>
      <div class="row">
        <label>Email<input type="email" name="email" required></label>
        <label>Phone<input name="phone" required></label>
      </div>
  <!-- status removed for staff -->
      <div class="staff-modal-actions">
        <button type="button" class="btn btn-ghost" id="cancelAddStaff">Cancel</button>
        <button type="submit" class="btn btn-primary">Add Staff</button>
      </div>
    </form>
  </div>
</div>

<!-- Status confirm reuse from vets (simplified) -->
<div id="staffStatusModal" class="cmc-modal" role="dialog" aria-modal="true" aria-labelledby="staffStatusTitle">
  <div class="cmc-modal-dialog">
    <h3 id="staffStatusTitle">Confirm Action</h3>
    <p id="staffStatusMessage">Are you sure?</p>
    <div class="cmc-modal-actions">
      <button type="button" class="btn btn-ghost" id="staffStatusCancel">Cancel</button>
      <button type="button" class="btn btn-danger" id="staffStatusConfirm">Yes, Continue</button>
    </div>
  </div>
</div>

<script>
const addBtn = document.getElementById('openAddStaff');
const addModal = document.getElementById('addStaffModal');
const cancelAdd = document.getElementById('cancelAddStaff');
const staffForm = document.getElementById('staffForm');
function openAdd(){addModal.classList.add('show');}
function closeAdd(){addModal.classList.remove('show');}
addBtn.addEventListener('click',openAdd);cancelAdd.addEventListener('click',closeAdd);
addModal.addEventListener('click',e=>{if(e.target===addModal) closeAdd();});
// Avatar pool for newly added staff (cycled)
const staffAvatars = [
  'https://i.pravatar.cc/64?img=21',
  'https://i.pravatar.cc/64?img=32',
  'https://i.pravatar.cc/64?img=55',
  'https://i.pravatar.cc/64?img=47',
  'https://i.pravatar.cc/64?img=11',
  'https://i.pravatar.cc/64?img=48'
];
let staffAvatarIndex = 0;
 
 staffForm.addEventListener('submit',e=>{
   e.preventDefault();
   const data = Object.fromEntries(new FormData(staffForm).entries());
  const avatar = staffAvatars[staffAvatarIndex % staffAvatars.length];
   staffAvatarIndex++;
   const tbody = document.querySelector('.cmc-table tbody');
   const tr = document.createElement('tr');
  tr.innerHTML = `<td><div class=\"cell-user\"><img class=\"avatar-sm\" src=\"${avatar}\" alt=\"\"><span class=\"link\">${data.name}</span></div></td>`+
     `<td>${data.role}</td>`+
     `<td><div>${data.phone}</div><div class=\"muted\">${data.email}</div></td>`+
     `<td><div class=\"row-actions\"><button class=\"action-btn\" title=\"Edit\">✏️</button>`+
     `<button class=\"action-btn staff-delete\" data-name=\"${data.name}\" title=\"Delete\">🗑️</button></div></td>`;
   tbody.appendChild(tr);
   bindDeleteButtons();
   closeAdd();
   staffForm.reset();
 });

// Delete staff (UI only)
function bindDeleteButtons(){
  document.querySelectorAll('.staff-delete').forEach(btn=>{
    btn.onclick=()=>{
      const name = btn.dataset.name || 'this staff member';
      if(confirm(`Delete ${name}? This cannot be undone (UI only).`)){
        const tr = btn.closest('tr');
        tr && tr.remove();
      }
    };
  });
}
bindDeleteButtons();
</script>
</body>
</html>
