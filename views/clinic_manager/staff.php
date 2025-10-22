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
<link rel="stylesheet" href="/PETVET/public/css/clinic-manager/staff.css">
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
      <button class="btn btn-success" id="openAddReceptionist">➕ Add Receptionist</button>
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
          <tr data-staff-id="<?= $s['id'] ?>">
            <td>
              <span class="link"><?= htmlspecialchars($s['name']) ?></span>
            </td>
            <td><?= htmlspecialchars($s['role']) ?></td>
            <td><div><?= htmlspecialchars($s['phone']) ?></div><div class="muted"><?= htmlspecialchars($s['email']) ?></div></td>
            <td>
              <div class="row-actions">
                <button class="action-btn staff-edit" 
                        data-id="<?= $s['id'] ?>" 
                        data-name="<?= htmlspecialchars($s['name']) ?>" 
                        data-role="<?= htmlspecialchars($s['role']) ?>" 
                        data-email="<?= htmlspecialchars($s['email']) ?>" 
                        data-phone="<?= htmlspecialchars($s['phone']) ?>"
                        data-status="<?= htmlspecialchars($s['status']) ?>"
                        title="Edit">✏️</button>
                <button class="action-btn staff-delete" 
                        data-id="<?= $s['id'] ?>" 
                        data-name="<?= htmlspecialchars($s['name']) ?>" 
                        title="Delete">🗑️</button>
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
      <div class="staff-modal-actions">
        <button type="button" class="btn btn-ghost" id="cancelAddStaff">Cancel</button>
        <button type="submit" class="btn btn-primary">Add Staff</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Staff -->
<div id="editStaffModal" class="staff-modal" role="dialog" aria-modal="true" aria-labelledby="editStaffTitle">
  <div class="staff-modal-dialog">
    <h3 id="editStaffTitle">Edit Staff Member</h3>
    <form id="editStaffForm" class="staff-form">
      <input type="hidden" name="id" id="editStaffId">
      <div class="row">
        <label>Full Name<input name="name" id="editStaffName" required></label>
        <label>Role<select name="role" id="editStaffRole" required>
          <option value="Veterinary Assistant">Veterinary Assistant</option>
          <option value="Front Desk">Front Desk</option>
          <option value="Support Staff">Support Staff</option>
        </select></label>
      </div>
      <div class="row">
        <label>Email<input type="email" name="email" id="editStaffEmail" required></label>
        <label>Phone<input name="phone" id="editStaffPhone" required></label>
      </div>
      <div class="row">
        <label>Status<select name="status" id="editStaffStatus" required>
          <option value="Active">Active</option>
          <option value="Inactive">Inactive</option>
        </select></label>
      </div>
      <div class="staff-modal-actions">
        <button type="button" class="btn btn-ghost" id="cancelEditStaff">Cancel</button>
        <button type="submit" class="btn btn-primary">Update Staff</button>
      </div>
    </form>
  </div>
</div>

<!-- Add Receptionist Modal -->
<div id="addReceptionistModal" class="staff-modal" role="dialog" aria-modal="true">
  <div class="staff-modal-dialog">
    <h3>Create Receptionist Account</h3>
    <form id="receptionistForm" class="staff-form">
      <label>
        Full Name
        <input type="text" name="name" id="recepName" placeholder="Enter full name" required>
      </label>
      <label>
        Email Address
        <input type="email" name="email" id="recepEmail" placeholder="receptionist@clinic.com" required>
      </label>
      <label>
        Password
        <input type="password" name="password" id="recepPassword" placeholder="Create a secure password" required minlength="6">
      </label>
      <label>
        Confirm Password
        <input type="password" name="confirm_password" id="recepConfirmPassword" placeholder="Re-enter password" required minlength="6">
      </label>
      <div class="password-match-indicator" id="passwordMatchIndicator" style="display: none;"></div>
      <div class="staff-modal-actions">
        <button type="button" class="btn btn-ghost" id="cancelAddReceptionist">Cancel</button>
        <button type="submit" class="btn btn-primary">Create Account</button>
      </div>
    </form>
  </div>
</div>

<!-- Success Modal with Credentials -->
<div id="credentialsSuccessModal" class="staff-modal" role="dialog" aria-modal="true">
  <div class="staff-modal-dialog credentials-modal">
    <h3>✓ Account Created Successfully</h3>
    <div class="credentials-content">
      <p class="success-message">The receptionist account has been created successfully. Share these credentials with the new team member:</p>
      
      <div class="credentials-box" id="credentialsBox">
        <div class="credentials-text" id="credentialsText"></div>
      </div>
      
      <div class="credentials-actions">
        <button type="button" class="btn btn-success btn-copy" id="copyCredentialsBtn">
          <span class="copy-icon">📋</span> Copy Credentials
        </button>
        <button type="button" class="btn btn-primary" id="closeCredentialsModal">Done</button>
      </div>
    </div>
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
// ============================================
// ADD STAFF MEMBER
// ============================================
const addBtn = document.getElementById('openAddStaff');
const addModal = document.getElementById('addStaffModal');
const cancelAdd = document.getElementById('cancelAddStaff');
const staffForm = document.getElementById('staffForm');

function openAdd() {
  addModal.classList.add('show');
}

function closeAdd() {
  addModal.classList.remove('show');
  staffForm.reset();
}

addBtn.addEventListener('click', openAdd);
cancelAdd.addEventListener('click', closeAdd);
addModal.addEventListener('click', e => {
  if (e.target === addModal) closeAdd();
});

// Handle add staff form submission
staffForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const formData = new FormData(staffForm);
  const data = Object.fromEntries(formData.entries());
  
  try {
    const response = await fetch('/PETVET/api/clinic-manager/staff.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    });
    
    const result = await response.json();
    
    if (result.success) {
      // Show success message
      alert('Staff member added successfully!');
      
      // Reload page to show new staff member
      window.location.reload();
    } else {
      alert('Error: ' + (result.message || 'Failed to add staff member'));
    }
  } catch (error) {
    console.error('Error adding staff:', error);
    alert('An error occurred. Please try again.');
  }
});

// ============================================
// EDIT STAFF MEMBER
// ============================================
const editModal = document.getElementById('editStaffModal');
const cancelEdit = document.getElementById('cancelEditStaff');
const editStaffForm = document.getElementById('editStaffForm');

function openEdit(staffData) {
  document.getElementById('editStaffId').value = staffData.id;
  document.getElementById('editStaffName').value = staffData.name;
  document.getElementById('editStaffRole').value = staffData.role;
  document.getElementById('editStaffEmail').value = staffData.email;
  document.getElementById('editStaffPhone').value = staffData.phone;
  document.getElementById('editStaffStatus').value = staffData.status;
  
  editModal.classList.add('show');
}

function closeEdit() {
  editModal.classList.remove('show');
  editStaffForm.reset();
}

cancelEdit.addEventListener('click', closeEdit);
editModal.addEventListener('click', e => {
  if (e.target === editModal) closeEdit();
});

// Handle edit staff form submission
editStaffForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const formData = new FormData(editStaffForm);
  const data = Object.fromEntries(formData.entries());
  
  try {
    const response = await fetch('/PETVET/api/clinic-manager/staff.php', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    });
    
    const result = await response.json();
    
    if (result.success) {
      // Show success message
      alert('Staff member updated successfully!');
      
      // Reload page to show updated staff member
      window.location.reload();
    } else {
      alert('Error: ' + (result.message || 'Failed to update staff member'));
    }
  } catch (error) {
    console.error('Error updating staff:', error);
    alert('An error occurred. Please try again.');
  }
});

// Bind edit buttons
function bindEditButtons() {
  document.querySelectorAll('.staff-edit').forEach(btn => {
    btn.onclick = () => {
      const staffData = {
        id: btn.dataset.id,
        name: btn.dataset.name,
        role: btn.dataset.role,
        email: btn.dataset.email,
        phone: btn.dataset.phone,
        status: btn.dataset.status
      };
      openEdit(staffData);
    };
  });
}

// ============================================
// DELETE STAFF MEMBER
// ============================================
async function deleteStaff(id, name) {
  if (!confirm(`Delete ${name}? This cannot be undone.`)) {
    return;
  }
  
  try {
    const response = await fetch(`/PETVET/api/clinic-manager/staff.php?id=${id}`, {
      method: 'DELETE'
    });
    
    const result = await response.json();
    
    if (result.success) {
      alert('Staff member deleted successfully!');
      
      // Remove the row from the table
      const row = document.querySelector(`tr[data-staff-id="${id}"]`);
      if (row) {
        row.remove();
      }
    } else {
      alert('Error: ' + (result.message || 'Failed to delete staff member'));
    }
  } catch (error) {
    console.error('Error deleting staff:', error);
    alert('An error occurred. Please try again.');
  }
}

// Bind delete buttons
function bindDeleteButtons() {
  document.querySelectorAll('.staff-delete').forEach(btn => {
    btn.onclick = () => {
      const id = btn.dataset.id;
      const name = btn.dataset.name || 'this staff member';
      deleteStaff(id, name);
    };
  });
}

// Initialize button bindings
bindEditButtons();
bindDeleteButtons();

// ============================================
// RECEPTIONIST ACCOUNT CREATION
// ============================================

const addReceptionistBtn = document.getElementById('openAddReceptionist');
const receptionistModal = document.getElementById('addReceptionistModal');
const cancelReceptionist = document.getElementById('cancelAddReceptionist');
const receptionistForm = document.getElementById('receptionistForm');
const credentialsModal = document.getElementById('credentialsSuccessModal');
const closeCredentialsBtn = document.getElementById('closeCredentialsModal');
const copyCredentialsBtn = document.getElementById('copyCredentialsBtn');
const passwordMatchIndicator = document.getElementById('passwordMatchIndicator');

// Store created receptionist data temporarily (for UI purposes)
let createdReceptionistData = null;

// Open receptionist modal
function openReceptionistModal() {
  receptionistModal.classList.add('show');
}

// Close receptionist modal
function closeReceptionistModal() {
  receptionistModal.classList.remove('show');
  receptionistForm.reset();
  passwordMatchIndicator.style.display = 'none';
}

// Close credentials modal
function closeCredentials() {
  credentialsModal.classList.remove('show');
  createdReceptionistData = null;
}

// Password match validation
const recepPassword = document.getElementById('recepPassword');
const recepConfirmPassword = document.getElementById('recepConfirmPassword');

function checkPasswordMatch() {
  const password = recepPassword.value;
  const confirmPassword = recepConfirmPassword.value;
  
  if (confirmPassword.length > 0) {
    if (password === confirmPassword) {
      passwordMatchIndicator.style.display = 'block';
      passwordMatchIndicator.textContent = '✓ Passwords match';
      passwordMatchIndicator.className = 'password-match-indicator match';
    } else {
      passwordMatchIndicator.style.display = 'block';
      passwordMatchIndicator.textContent = '✗ Passwords do not match';
      passwordMatchIndicator.className = 'password-match-indicator no-match';
    }
  } else {
    passwordMatchIndicator.style.display = 'none';
  }
}

recepPassword.addEventListener('input', checkPasswordMatch);
recepConfirmPassword.addEventListener('input', checkPasswordMatch);

// Handle receptionist form submission
receptionistForm.addEventListener('submit', function(e) {
  e.preventDefault();
  
  const formData = new FormData(receptionistForm);
  const name = formData.get('name');
  const email = formData.get('email');
  const password = formData.get('password');
  const confirmPassword = formData.get('confirm_password');
  
  // Validate passwords match
  if (password !== confirmPassword) {
    alert('Passwords do not match!');
    return;
  }
  
  // TODO: Backend API call to create receptionist account
  // fetch('/PETVET/api/clinic-manager/create-receptionist.php', {
  //   method: 'POST',
  //   body: formData  // Send FormData directly, PHP will receive via $_POST
  // })
  // .then(response => response.json())
  // .then(data => {
  //   if (data.success) {
  //     createdReceptionistData = data.receptionist;
  //     closeReceptionistModal();
  //     showCredentialsModal(data.receptionist.name, data.receptionist.email, password);
  //     addReceptionistToTable(data.receptionist.name, data.receptionist.email, password);
  //   } else {
  //     alert(data.message || 'Failed to create account');
  //   }
  // })
  // .catch(error => {
  //   console.error('Error:', error);
  //   alert('An error occurred. Please try again.');
  // });
  
      const id = Date.now();
    staffData.push({ id, ...payload });
    renderList();
  // Simulate success
  createdReceptionistData = {
    name: name,
    email: email,
    password: password,
    createdAt: new Date().toISOString()
  };
  
  // Close receptionist modal
  closeReceptionistModal();
  
  // Show credentials modal
  showCredentialsModal(name, email, password);
  
  // Add to staff table as pending
  addReceptionistToTable(name, email, password);
});

// Show credentials modal with copy functionality
function showCredentialsModal(name, email, password) {
  const clinicName = 'PetVet Clinic'; // TODO: Get from backend/session
  
  const credentialsText = `This is your receptionist credentials for ${clinicName}

Username: ${email}
Password: ${password}

Please change your password after your first login for security purposes.`;
  
  document.getElementById('credentialsText').textContent = credentialsText;
  credentialsModal.classList.add('show');
}

// Copy credentials to clipboard
copyCredentialsBtn.addEventListener('click', function() {
  const credentialsText = document.getElementById('credentialsText').textContent;
  
  navigator.clipboard.writeText(credentialsText).then(() => {
    // Show success feedback
    const originalHTML = copyCredentialsBtn.innerHTML;
    copyCredentialsBtn.innerHTML = '<span class="copy-icon">✓</span> Copied to Clipboard!';
    copyCredentialsBtn.classList.add('copied');
    copyCredentialsBtn.disabled = true;
    
    setTimeout(() => {
      copyCredentialsBtn.innerHTML = originalHTML;
      copyCredentialsBtn.classList.remove('copied');
      copyCredentialsBtn.disabled = false;
    }, 2500);
  }).catch(err => {
    console.error('Failed to copy:', err);
    alert('Failed to copy credentials. Please copy manually.');
  });
});

// Add receptionist to table (pending state)
function addReceptionistToTable(name, email, password) {
  const tbody = document.querySelector('.staff-table tbody');
  
  const row = document.createElement('tr');
  row.className = 'receptionist-pending';
  row.dataset.email = email;
  row.dataset.password = password;
  
  row.innerHTML = `
    <td>
      <span>${name}</span>
    </td>
    <td>
      <span class="role-badge">Receptionist</span>
      <span class="badge-pending">Pending</span>
    </td>
    <td>
      <div class="contact-info">
        <span class="contact-email">${email}</span>
        <span class="contact-phone" style="color: #94a3b8; font-size: 12px;">Account not activated</span>
      </div>
    </td>
    <td class="col-actions">
      <button class="icon-btn copy-credentials-btn" data-email="${email}" data-password="${password}" title="Copy Credentials">
        📋
      </button>
      <button class="icon-btn staff-delete" data-name="${name}" title="Delete">🗑️</button>
    </td>
  `;
  
  tbody.insertBefore(row, tbody.firstChild);
  
  // Bind copy credentials button
  bindCopyCredentialsButtons();
  
  // Rebind delete buttons
  bindDeleteButtons();
}

// Bind copy credentials buttons in table
function bindCopyCredentialsButtons() {
  document.querySelectorAll('.copy-credentials-btn').forEach(btn => {
    btn.onclick = function() {
      const email = this.dataset.email;
      const password = this.dataset.password;
      const row = this.closest('tr');
      const name = row.querySelector('span').textContent;
      
      const clinicName = 'PetVet Clinic'; // TODO: Get from backend
      
      const credentialsText = `This is your receptionist credentials for ${clinicName}

Username: ${email}
Password: ${password}

Please change your password after your first login for security purposes.`;
      
      navigator.clipboard.writeText(credentialsText).then(() => {
        // Visual feedback
        const originalText = this.textContent;
        const originalTitle = this.title;
        
        this.textContent = '✓';
        this.title = 'Copied to Clipboard!';
        this.style.background = '#10b981';
        this.style.color = 'white';
        this.disabled = true;
        
        // Show tooltip-like message
        const tooltip = document.createElement('div');
        tooltip.textContent = 'Copied to Clipboard!';
        tooltip.style.cssText = `
          position: absolute;
          background: #10b981;
          color: white;
          padding: 6px 12px;
          border-radius: 6px;
          font-size: 12px;
          font-weight: 600;
          white-space: nowrap;
          z-index: 1000;
          animation: fadeIn 0.2s ease;
          box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        `;
        
        const rect = this.getBoundingClientRect();
        tooltip.style.position = 'fixed';
        tooltip.style.left = rect.left + 'px';
        tooltip.style.top = (rect.top - 35) + 'px';
        
        document.body.appendChild(tooltip);
        
        setTimeout(() => {
          this.textContent = originalText;
          this.title = originalTitle;
          this.style.background = '';
          this.style.color = '';
          this.disabled = false;
          tooltip.remove();
        }, 2500);
      }).catch(err => {
        console.error('Failed to copy:', err);
        alert('Failed to copy credentials.');
      });
    };
  });
}

// Event listeners
addReceptionistBtn.addEventListener('click', openReceptionistModal);
cancelReceptionist.addEventListener('click', closeReceptionistModal);
closeCredentialsBtn.addEventListener('click', closeCredentials);

// Close modals on outside click
receptionistModal.addEventListener('click', function(e) {
  if (e.target === receptionistModal) {
    closeReceptionistModal();
  }
});

credentialsModal.addEventListener('click', function(e) {
  if (e.target === credentialsModal) {
    closeCredentials();
  }
});
</script>
</body>
</html>
