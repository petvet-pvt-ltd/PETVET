<?php
$currentPage = basename($_SERVER['PHP_SELF']);

/* ---------- FILTERS (GET) ---------- */
$q      = isset($_GET['q']) ? trim($_GET['q']) : '';
$spec   = $_GET['spec']   ?? 'all';
$status = $_GET['status'] ?? 'all';
$avail  = $_GET['avail']  ?? '';

$filtered = array_filter($vets, function($v) use ($q,$spec,$status,$avail){
  $okQ      = $q === '' || stripos($v['name'].' '.$v['specialization'].' '.$v['email'], $q) !== false;
  $okSpec   = $spec   === 'all' || $v['specialization'] === $spec;
  $okStatus = $status === 'all' || $v['status'] === $status;
  $okAvail  = $avail  === ''    || in_array($avail, $v['on_duty_dates']);
  return $okQ && $okSpec && $okStatus && $okAvail;
});

/* ---------- SUMMARY ---------- */
$total_active = count(array_filter($vets, fn($v)=>$v['status']==='Active'));
$today = date('Y-m-d');
$on_duty_today = array_column(array_filter($vets, fn($v)=>in_array($today, $v['on_duty_dates'])), 'name');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Veterinarians | Clinic Manager</title>
  <link rel="stylesheet" href="/PETVET/public/css/clinic-manager/enhanced-global.css">
  <link rel="stylesheet" href="/PETVET/public/css/clinic-manager/vets.css">
  <style>
    .status-indicator {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 4px 12px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 600;
    }
    
    .status-active {
      background: #dcfce7;
      color: #166534;
    }
    
    .status-leave {
      background: #fef3c7;
      color: #92400e;
    }
    
    .status-suspended {
      background: #fee2e2;
      color: #991b1b;
    }
    
    .status-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: currentColor;
    }
    
    .vet-specialization {
      display: inline-block;
      background: var(--blue-100);
      color: var(--blue-600);
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .pending-requests {
      background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
      border: 1px solid #f59e0b;
      color: #92400e;
    }
    
    .pending-requests:hover {
      background: linear-gradient(135deg, #fde68a 0%, #fcd34d 100%);
    }
  </style>
</head>
<body>

<main class="main-content">
  <!-- Header -->
  <header class="page-header">
    <div>
      <h1 class="page-title">Veterinarians</h1>
      <p class="page-subtitle">Manage veterinary staff and schedules</p>
    </div>
    <div class="cmc-actions" style="display: flex; gap: 12px;">
      <button class="btn pending-requests" id="openDrawer">
        🔔 Pending Requests <span class="badge" style="background: #dc2626; color: white; margin-left: 8px;"><?= count($pending ?? []) ?></span>
      </button>
    </div>
  </header>

  <!-- Summary cards -->
  <section class="cmc-cards">
    <article class="card">
      <div class="card-label">Total Active Vets</div>
      <div class="card-value"><?= $total_active ?></div>
      <div class="card-sub">Currently practicing</div>
    </article>
    <article class="card">
      <div class="card-label">On Duty Today</div>
      <div class="card-value"><?= count($on_duty_today) ?></div>
      <div class="card-sub">
        <?= $on_duty_today ? implode(', ', array_slice($on_duty_today, 0, 2)) . (count($on_duty_today) > 2 ? '...' : '') : 'None scheduled' ?>
      </div>
    </article>
    <article class="card">
      <div class="card-label">Specializations</div>
      <div class="card-value"><?= count(array_unique(array_column($vets, 'specialization'))) ?></div>
      <div class="card-sub">Different expertise areas</div>
    </article>
  </section>

  <!-- Filters -->
  <form method="get" class="cmc-filters">
    <input type="hidden" name="module" value="clinic-manager">
    <input type="hidden" name="page" value="vets">
    <div class="field full-grow">
      <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" class="input"
             placeholder="Search by name, specialization, or email">
    </div>
    <div class="field">
      <label class="label" for="spec">Specialization</label>
      <select name="spec" id="spec" class="select">
        <option value="all" <?= $spec==='all'?'selected':'' ?>>All</option>
        <option value="General" <?= $spec==='General'?'selected':'' ?>>General</option>
        <option value="Surgery" <?= $spec==='Surgery'?'selected':'' ?>>Surgery</option>
        <option value="Dental" <?= $spec==='Dental'?'selected':'' ?>>Dental</option>
        <option value="Dermatology" <?= $spec==='Dermatology'?'selected':'' ?>>Dermatology</option>
        <option value="Radiology" <?= $spec==='Radiology'?'selected':'' ?>>Radiology</option>
      </select>
    </div>
    <div class="field">
      <label class="label" for="status">Status</label>
      <select name="status" id="status" class="select">
        <option value="all" <?= $status==='all'?'selected':'' ?>>All</option>
        <option value="Active" <?= $status==='Active'?'selected':'' ?>>Active</option>
        <option value="On Leave" <?= $status==='On Leave'?'selected':'' ?>>On Leave</option>
        <option value="Suspended" <?= $status==='Suspended'?'selected':'' ?>>Suspended</option>
      </select>
    </div>
    <div class="field">
      <label class="label" for="avail">Availability</label>
      <input type="date" name="avail" id="avail" value="<?= htmlspecialchars($avail) ?>" class="input">
    </div>
    <div class="field">
      <button type="submit" class="btn btn-primary">Apply Filters</button>
    </div>
    <div class="field">
      <a class="btn btn-ghost" href="/PETVET/index.php?module=clinic-manager&page=vets">Clear Filters</a>
    </div>
  </form>

  <!-- Table -->
  <section class="cmc-table-card">
    <div class="cmc-table-wrap">
      <table class="cmc-table">
        <thead>
          <tr>
            <th>Full Name</th>
            <th>Specialization</th>
            <th>Contact</th>
            <th>Status</th>
            <th>Next Appointment</th>
            <th class="col-actions">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($filtered): foreach ($filtered as $v): ?>
            <tr>
              <td>
                <div class="cell-user">
                  <img class="avatar-sm" src="<?= htmlspecialchars($v['photo']) ?>" alt="">
                  <a class="link" href="#"><?= htmlspecialchars($v['name']) ?></a>
                </div>
              </td>
              <td><?= htmlspecialchars($v['specialization']) ?></td>
              <td>
                <div><?= htmlspecialchars($v['phone']) ?></div>
                <div class="muted"><?= htmlspecialchars($v['email']) ?></div>
              </td>
              <td>
                <?php
                  $cls = ($v['status']==='Active')?'status-active':
                         (($v['status']==='On Leave')?'status-leave':'status-suspended');
                ?>
                <span class="status-pill <?= $cls ?>"><?= htmlspecialchars($v['status']) ?></span>
              </td>
              <td><?= $v['next_slot'] ? date('M d, Y, h:i A', strtotime($v['next_slot'])) : '—' ?></td>
              <td>
                <div  class="row-actions">
                  <!-- Schedule (first) -->
                  <button type="button" class="action-btn btn-schedule" data-vet="<?= htmlspecialchars($v['name']) ?>" title="View Schedule / Filter Appointments">📅</button>
                  <!-- On Leave toggle (disabled when suspended) -->
                  <?php
                    $isSuspended = $v['status'] === 'Suspended';
                    $isOnLeave = $v['status'] === 'On Leave';
                    $leaveBtnTitle = $isSuspended ? 'On Leave disabled while suspended' : ($isOnLeave ? 'Mark Active' : 'Set On Leave');
                    $leaveIcon = $isOnLeave ? '✅' : '⏳';
                  ?>
                  <button type="button"
                          class="action-btn btn-leave-toggle <?= $isSuspended ? 'disabled' : '' ?>"
                          data-user-id="<?= $v['user_id'] ?>"
                          data-name="<?= htmlspecialchars($v['name']) ?>"
                          data-leave="<?= $isOnLeave ? 'leave' : 'active' ?>"
                          title="<?= $leaveBtnTitle ?>">
                          <?= $leaveIcon ?>
                  </button>
                  <!-- Suspend / Activate -->
                  <button
                    type="button"
                    class="action-btn vet-toggle-status"
                    data-user-id="<?= $v['user_id'] ?>"
                    data-name="<?= htmlspecialchars($v['name']) ?>"
                    data-status="<?= htmlspecialchars($v['status']) ?>"
                    title="<?= $v['status']==='Suspended'?'Activate':'Suspend' ?>">
                    <?= $v['status']==='Suspended'?'▶️':'⛔' ?>
                  </button>
                </div>
                
              </td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="6" class="empty">No veterinarians found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>

<!-- Pending Requests Drawer -->
<aside id="drawer" class="drawer">
  <div class="drawer-header">
    <h2>Pending Vet Requests</h2>
    <button id="closeDrawer" class="icon-btn" aria-label="Close">✕</button>
  </div>
  <div class="drawer-body">
    <?php if (isset($pending) && $pending): foreach ($pending as $p): ?>
    <div class="pending-item">
      <img class="avatar-sm" src="<?= htmlspecialchars($p['photo']) ?>" alt="">
      <div class="pending-info">
        <div class="pending-name"><?= htmlspecialchars($p['name']) ?></div>
        <div class="pending-meta">
          <span><?= htmlspecialchars($p['specialization']) ?></span> •
          <span>License: <?= htmlspecialchars($p['license']) ?></span> •
          <span><?= htmlspecialchars($p['experience']) ?></span>
        </div>
        <div class="pending-docs">
          <?php foreach ($p['docs'] as $d): ?>
            <a class="doc-link" href="<?= htmlspecialchars($d['url']) ?>" target="_blank"><?= htmlspecialchars($d['label']) ?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="pending-actions">
        <button class="btn btn-success btn-sm">Approve</button>
        <button class="btn btn-danger btn-sm">Decline</button>
      </div>
    </div>
    <?php endforeach; else: ?>
      <div class="empty">No pending requests.</div>
    <?php endif; ?>
  </div>
</aside>
<div id="backdrop" class="backdrop"></div>

<!-- Confirm Modal -->
<div id="statusConfirmModal" class="cmc-modal" role="dialog" aria-modal="true" aria-labelledby="statusModalTitle">
  <div class="cmc-modal-dialog">
    <h3 id="statusModalTitle">Confirm Action</h3>
    <p id="statusModalMessage">Are you sure?</p>
    <div class="cmc-modal-actions">
      <button type="button" class="btn btn-ghost" id="statusModalCancel">Cancel</button>
      <button type="button" class="btn btn-danger" id="statusModalConfirm">Yes, Continue</button>
    </div>
  </div>
 </div>

<script>
const drawer = document.getElementById('drawer');
const backdrop = document.getElementById('backdrop');
const openBtn = document.getElementById('openDrawer');
const closeBtn = document.getElementById('closeDrawer');

function openDrawer(){
  drawer.classList.add('open');
  backdrop.classList.add('show');
}
function closeDrawer(){
  drawer.classList.remove('open');
  backdrop.classList.remove('show');
}

openBtn.addEventListener('click', openDrawer);
closeBtn.addEventListener('click', closeDrawer);
backdrop.addEventListener('click', closeDrawer);

// Custom modal confirmation for Suspend / Activate
const statusModal = document.getElementById('statusConfirmModal');
const statusMsg = document.getElementById('statusModalMessage');
const statusCancel = document.getElementById('statusModalCancel');
const statusConfirm = document.getElementById('statusModalConfirm');
let pendingToggleBtn = null;

function openStatusModal(msg, btn){
  statusMsg.textContent = msg;
  pendingToggleBtn = btn;
  statusModal.classList.add('show');
  // Keep scrollbar visible (no overflow hidden) per request
  statusCancel.focus();
}
function closeStatusModal(){
  statusModal.classList.remove('show');
  pendingToggleBtn = null;
}
statusCancel.addEventListener('click', closeStatusModal);
statusModal.addEventListener('click', e=>{ if(e.target===statusModal) closeStatusModal(); });
document.addEventListener('keydown', e=>{ if(e.key==='Escape' && statusModal.classList.contains('show')) closeStatusModal(); });

statusConfirm.addEventListener('click', async () => {
  if(!pendingToggleBtn) return closeStatusModal();
  const btn = pendingToggleBtn;
  const pill = btn.closest('tr').querySelector('.status-pill');
  const isSuspended = btn.dataset.status === 'Suspended';
  const userId = btn.dataset.userId;
  const newStatus = isSuspended ? 'Active' : 'Inactive'; // Inactive = Suspended in database
  
  // Find leave button in same row
  const leaveBtn = btn.closest('tr').querySelector('.btn-leave-toggle');
  
  // Store original state for rollback
  const originalStatus = btn.dataset.status;
  const originalPillText = pill ? pill.textContent : '';
  const originalPillClasses = pill ? pill.className : '';
  
  try {
    // Update UI optimistically
    if (pill) {
      if (isSuspended) {
        pill.textContent = 'Active';
        pill.classList.remove('status-suspended');
        pill.classList.add('status-active');
        btn.dataset.status = 'Active';
        btn.title = 'Suspend';
        btn.textContent = '⛔';
        // Re-enable leave button when activating
        if(leaveBtn){
          leaveBtn.classList.remove('disabled');
          if(leaveBtn.dataset.leave === 'leave'){
            // If previously on leave, keep pill consistent
            pill.textContent = 'On Leave';
            pill.classList.remove('status-active','status-suspended');
            pill.classList.add('status-leave');
          }
        }
      } else {
        pill.textContent = 'Suspended';
        pill.classList.remove('status-active','status-leave');
        pill.classList.add('status-suspended');
        btn.dataset.status = 'Suspended';
        btn.title = 'Activate';
        btn.textContent = '▶️';
        // Disable leave button while suspended
        if(leaveBtn){
          leaveBtn.classList.add('disabled');
        }
      }
    }
    
    closeStatusModal();
    
    // Call API to update database
    const response = await fetch('/PETVET/api/clinic-manager/update-vet-status.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ user_id: userId, status: newStatus })
    });
    
    const data = await response.json();
    
    if (!data.success) {
      throw new Error(data.error || 'Failed to update status');
    }
    
  } catch (error) {
    console.error('Error updating vet status:', error);
    alert('Failed to update status: ' + error.message);
    
    // Revert UI changes on error
    btn.dataset.status = originalStatus;
    if(pill) {
      pill.textContent = originalPillText;
      pill.className = originalPillClasses;
    }
    if(originalStatus === 'Suspended') {
      btn.title = 'Activate';
      btn.textContent = '▶️';
    } else {
      btn.title = 'Suspend';
      btn.textContent = '⛔';
    }
    if(leaveBtn) {
      if(originalStatus === 'Suspended') {
        leaveBtn.classList.add('disabled');
      } else {
        leaveBtn.classList.remove('disabled');
      }
    }
  }
});

document.querySelectorAll('.vet-toggle-status').forEach(btn => {
  btn.addEventListener('click', () => {
    const name = btn.dataset.name || 'this vet';
    const isSuspended = btn.dataset.status === 'Suspended';
    const actionVerb = isSuspended ? 'activate' : 'suspend';
    openStatusModal(`Do you really want to ${actionVerb} ${name}?`, btn);
  });
});

// Schedule button: redirect to appointments with vet filter
document.querySelectorAll('.btn-schedule').forEach(btn => {
  btn.addEventListener('click', () => {
    const vet = encodeURIComponent(btn.dataset.vet);
    window.location.href = `/PETVET/index.php?module=clinic-manager&page=appointments&vet=${vet}`;
  });
});

// On Leave toggle logic (with API call)
document.querySelectorAll('.btn-leave-toggle').forEach(btn => {
  btn.addEventListener('click', async () => {
    if(btn.classList.contains('disabled')) return; // safety
    const row = btn.closest('tr');
    const pill = row.querySelector('.status-pill');
    const statusToggleBtn = row.querySelector('.vet-toggle-status');
    const currentLeaveState = btn.dataset.leave; // 'leave' means currently On Leave
    const userId = btn.dataset.userId;
    const newStatus = (currentLeaveState === 'leave') ? 'Active' : 'On Leave';
    
    // Update UI optimistically
    const originalLeaveState = currentLeaveState;
    const originalPillText = pill ? pill.textContent : '';
    const originalPillClasses = pill ? pill.className : '';
    
    try {
      // Update UI first
      if(currentLeaveState === 'leave') {
        // Set Active
        btn.dataset.leave = 'active';
        btn.title = 'Set On Leave';
        btn.textContent = '⏳';
        if(pill && statusToggleBtn && statusToggleBtn.dataset.status !== 'Suspended'){
          pill.textContent = 'Active';
          pill.classList.remove('status-leave','status-suspended');
          pill.classList.add('status-active');
        }
      } else {
        // Set On Leave
        btn.dataset.leave = 'leave';
        btn.title = 'Mark Active';
        btn.textContent = '✅';
        if(pill && statusToggleBtn && statusToggleBtn.dataset.status !== 'Suspended'){
          pill.textContent = 'On Leave';
          pill.classList.remove('status-active','status-suspended');
          pill.classList.add('status-leave');
        }
      }
      
      // Call API to update database
      const response = await fetch('/PETVET/api/clinic-manager/update-vet-status.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ user_id: userId, status: newStatus })
      });
      
      const data = await response.json();
      
      if (!data.success) {
        throw new Error(data.error || 'Failed to update status');
      }
      
      // Update status toggle button dataset
      if(statusToggleBtn) {
        statusToggleBtn.dataset.status = newStatus;
      }
      
    } catch (error) {
      console.error('Error updating vet status:', error);
      alert('Failed to update status: ' + error.message);
      
      // Revert UI changes on error
      btn.dataset.leave = originalLeaveState;
      if(pill) {
        pill.textContent = originalPillText;
        pill.className = originalPillClasses;
      }
      if(originalLeaveState === 'leave') {
        btn.title = 'Mark Active';
        btn.textContent = '✅';
      } else {
        btn.title = 'Set On Leave';
        btn.textContent = '⏳';
      }
    }
  });
});
</script>
</body>
</html>