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
      background: var(--primary-btn-bg, #2563eb);
      border: 1px solid var(--primary-btn-border, #2563eb);
      color: var(--primary-btn-color, #fff);
      border-radius: 8px;
      font-weight: 600;
      box-shadow: var(--btn-shadow, none);
      transition: background 0.2s;
    }
    .pending-requests:hover {
      background: var(--primary-btn-hover-bg, #1e40af);
    }
    .pending-badge {
      background: #dc2626;
      color: #fff;
      margin-left: 8px;
      border-radius: 999px;
      font-size: 13px;
      font-weight: 700;
      padding: 2px 10px;
      display: inline-block;
      min-width: 24px;
      text-align: center;
      box-shadow: 0 1px 2px rgba(0,0,0,0.04);
    }
    
    .pending-requests:hover .pending-badge {
      background: #dc2626;
      color: #fff;
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
        <svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true" style="vertical-align:middle;margin-right:6px;"><path d="M10 2a6 6 0 0 0-6 6v3.586l-.707.707A1 1 0 0 0 4 14h12a1 1 0 0 0 .707-1.707L16 11.586V8a6 6 0 0 0-6-6Zm0 16a2 2 0 0 0 2-2H8a2 2 0 0 0 2 2Z" fill="currentColor"/></svg>Pending Requests <span class="badge pending-badge"><?= count($pending ?? []) ?></span>
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
  <?php
    // Build specialization options from vets data
    $specOptions = array_values(array_filter(array_unique(array_map(fn($x) => $x['specialization'] ?? '', $vets))));
    sort($specOptions, SORT_STRING | SORT_FLAG_CASE);
  ?>

  <form method="get" class="cmc-filters" id="vetsFilters">
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
        <?php foreach ($specOptions as $s): ?>
          <?php if ($s === '' ) continue; ?>
          <option value="<?= htmlspecialchars($s) ?>" <?= $spec === $s ? 'selected' : '' ?>><?= htmlspecialchars($s) ?></option>
        <?php endforeach; ?>
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
    <!-- Availability filter removed; filters apply immediately -->
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
                  <button type="button" class="action-btn btn-schedule" data-vet="<?= htmlspecialchars($v['name']) ?>" title="View Schedule / Filter Appointments">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true"><rect x="3" y="5" width="14" height="12" rx="2" fill="#2563eb"/><rect x="7" y="2" width="1.5" height="3" rx=".75" fill="#2563eb"/><rect x="11.5" y="2" width="1.5" height="3" rx=".75" fill="#2563eb"/><rect x="3" y="8" width="14" height="1.5" fill="#1e40af"/></svg>
                  </button>
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
                    <?php if ($isOnLeave): ?>
                      <svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="9" fill="#22c55e"/><path d="M6 10.5l2.5 2.5L14 8.5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <?php else: ?>
                      <svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="9" fill="#f59e42"/><path d="M10 5v5l3 3" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <?php endif; ?>
                  </button>
                  <!-- Suspend / Activate -->
                  <button
                    type="button"
                    class="action-btn vet-toggle-status"
                    data-user-id="<?= $v['user_id'] ?>"
                    data-name="<?= htmlspecialchars($v['name']) ?>"
                    data-status="<?= htmlspecialchars($v['status']) ?>"
                    title="<?= $v['status']==='Suspended'?'Activate':'Suspend' ?>">
                    <?php if ($v['status']==='Suspended'): ?>
                      <svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="9" fill="#22c55e"/><polygon points="8,6 14,10 8,14" fill="#fff"/></svg>
                    <?php else: ?>
                      <svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="9" fill="#ef4444"/><rect x="6" y="9" width="8" height="2" rx="1" fill="#fff"/></svg>
                    <?php endif; ?>
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
    <button id="closeDrawer" class="icon-btn" aria-label="Close">
      <svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="9" fill="#e5e7eb"/><path d="M7 7l6 6M13 7l-6 6" stroke="#374151" stroke-width="2" stroke-linecap="round"/></svg>
    </button>
  </div>
  <div class="drawer-body">
    <?php if (isset($pending) && $pending): foreach ($pending as $p): ?>
    <div class="pending-item" data-user-role-id="<?= (int)$p['id'] ?>">
      <img class="avatar-sm" src="<?= htmlspecialchars($p['photo']) ?>" alt="">
      <div class="pending-info">
        <div class="pending-name">
          <?= htmlspecialchars($p['name']) ?>
          <?php if (!empty($p['applied_date'])): ?>
            <span class="pending-date">Applied: <?= htmlspecialchars($p['applied_date']) ?></span>
          <?php endif; ?>
        </div>
        <div class="pending-meta">
          <span><?= htmlspecialchars($p['specialization']) ?></span> •
          <span>License: <?= htmlspecialchars($p['license']) ?></span> •
          <span><?= htmlspecialchars($p['experience']) ?></span>
        </div>
        <div class="pending-contact">
          <span class="pending-email"><?= htmlspecialchars($p['email']) ?></span>
          <?php if (!empty($p['phone']) && $p['phone'] !== 'N/A'): ?>
            <span class="pending-phone">• <?= htmlspecialchars($p['phone']) ?></span>
          <?php endif; ?>
        </div>
        <div class="pending-docs">
          <?php if (!empty($p['docs'])): foreach ($p['docs'] as $d): ?>
            <a class="doc-link" href="<?= htmlspecialchars($d['url']) ?>" target="_blank" rel="noopener">View PDF</a>
          <?php endforeach; else: ?>
            <span class="doc-empty">No proof document uploaded.</span>
          <?php endif; ?>
        </div>
      </div>
      <div class="pending-actions">
        <button class="btn btn-success btn-sm pending-approve" type="button" title="Approve">
          <svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="9" fill="#22c55e"/><path d="M6 10.5l2.5 2.5L14 8.5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <button class="btn btn-danger btn-sm pending-decline" type="button" title="Decline">
          <svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="9" fill="#ef4444"/><path d="M7 7l6 6M13 7l-6 6" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
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

function setPendingBadgeCount(count) {
  const badge = openBtn ? openBtn.querySelector('.badge') : null;
  if (badge) badge.textContent = String(Math.max(0, count));
}

function getPendingBadgeCount() {
  const badge = openBtn ? openBtn.querySelector('.badge') : null;
  return badge ? parseInt(badge.textContent || '0', 10) || 0 : 0;
}

function openDrawer(){
  drawer.classList.add('open');
  backdrop.classList.add('show');
}
function closeDrawer(){
  drawer.classList.remove('open');
  backdrop.classList.remove('show');
}

if (openBtn) openBtn.addEventListener('click', openDrawer);
if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
if (backdrop) backdrop.addEventListener('click', closeDrawer);

// Pending Vet Requests: Approve / Decline (AJAX)
async function handlePendingAction(buttonEl, action) {
  const item = buttonEl.closest('.pending-item');
  const userRoleId = item ? item.getAttribute('data-user-role-id') : null;
  if (!userRoleId) return;

  const endpoint = action === 'approve'
    ? '/PETVET/api/clinic-manager/approve-vet-request.php'
    : '/PETVET/api/clinic-manager/reject-vet-request.php';

  // Prevent double-clicks
  const originalText = buttonEl.textContent;
  const actionButtons = item ? item.querySelectorAll('.pending-approve, .pending-decline') : [];
  actionButtons.forEach(b => b.disabled = true);
  buttonEl.textContent = action === 'approve' ? 'Approving...' : 'Declining...';

  try {
    const res = await fetch(endpoint, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({ user_role_id: Number(userRoleId) }),
      credentials: 'same-origin'
    });
    const data = await res.json();
    if (!data.success) throw new Error(data.error || 'Request failed');

    // Remove card and decrement badge
    if (item) item.remove();
    setPendingBadgeCount(getPendingBadgeCount() - 1);

    // If no items left, show empty state
    const body = document.querySelector('#drawer .drawer-body');
    const remaining = body ? body.querySelectorAll('.pending-item').length : 0;
    if (body && remaining === 0) {
      body.innerHTML = '<div class="empty">No pending requests.</div>';
    }
  } catch (e) {
    alert(e.message || 'Action failed');
    actionButtons.forEach(b => b.disabled = false);
    buttonEl.textContent = originalText;
  }
}

// Custom modal confirmation for Suspend / Activate
const statusModal = document.getElementById('statusConfirmModal');
const statusMsg = document.getElementById('statusModalMessage');
const statusCancel = document.getElementById('statusModalCancel');
const statusConfirm = document.getElementById('statusModalConfirm');
let pendingToggleBtn = null;
let pendingConfirmAction = null;

function setSuspendToggleButtonUI(btn, suspended){
  if(!btn) return;
  if(suspended){
    btn.title = 'Activate';
    btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="9" fill="#22c55e"/><polygon points="8,6 14,10 8,14" fill="#fff"/></svg>';
  } else {
    btn.title = 'Suspend';
    btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="9" fill="#ef4444"/><rect x="6" y="9" width="8" height="2" rx="1" fill="#fff"/></svg>';
  }
}

function openStatusModal(msg, btn){
  statusMsg.textContent = msg;
  pendingToggleBtn = btn;
  pendingConfirmAction = null;
  statusModal.classList.add('show');
  // Keep scrollbar visible (no overflow hidden) per request
  statusCancel.focus();
}

function openConfirmModal(msg, onConfirm) {
  statusMsg.textContent = msg;
  pendingToggleBtn = null;
  pendingConfirmAction = onConfirm;
  statusModal.classList.add('show');
  statusCancel.focus();
}

function closeStatusModal(){
  statusModal.classList.remove('show');
  pendingToggleBtn = null;
  pendingConfirmAction = null;
}
statusCancel.addEventListener('click', closeStatusModal);
statusModal.addEventListener('click', e=>{ if(e.target===statusModal) closeStatusModal(); });
document.addEventListener('keydown', e=>{ if(e.key==='Escape' && statusModal.classList.contains('show')) closeStatusModal(); });

statusConfirm.addEventListener('click', async () => {
  // Generic confirmation flow (used by pending approve/decline)
  if (typeof pendingConfirmAction === 'function') {
    const fn = pendingConfirmAction;
    closeStatusModal();
    try {
      await fn();
    } catch (e) {
      console.error(e);
    }
    return;
  }

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
        setSuspendToggleButtonUI(btn, false);
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
        setSuspendToggleButtonUI(btn, true);
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
    setSuspendToggleButtonUI(btn, originalStatus === 'Suspended');
    if(leaveBtn) {
      if(originalStatus === 'Suspended') {
        leaveBtn.classList.add('disabled');
      } else {
        leaveBtn.classList.remove('disabled');
      }
    }
  }
});

// Pending actions: event delegation + confirmation
if (drawer) {
  drawer.addEventListener('click', (e) => {
    const approveBtn = e.target.closest('.pending-approve');
    const declineBtn = e.target.closest('.pending-decline');
    const btn = approveBtn || declineBtn;
    if (!btn) return;

    const item = btn.closest('.pending-item');
    const name = item ? (item.querySelector('.pending-name')?.innerText || 'this vet') : 'this vet';
    const action = approveBtn ? 'approve' : 'decline';

    const msg = action === 'approve'
      ? `Approve ${name}? This will allow the vet to log in.`
      : `Decline ${name}? This will reject the vet request.`;

    openConfirmModal(msg, () => handlePendingAction(btn, action));
  });
}

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
        btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="9" fill="#f59e42"/><path d="M10 5v5l3 3" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        if(pill && statusToggleBtn && statusToggleBtn.dataset.status !== 'Suspended'){
          pill.textContent = 'Active';
          pill.classList.remove('status-leave','status-suspended');
          pill.classList.add('status-active');
        }
      } else {
        // Set On Leave
        btn.dataset.leave = 'leave';
        btn.title = 'Mark Active';
        btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="9" fill="#22c55e"/><path d="M6 10.5l2.5 2.5L14 8.5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
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
        btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="9" fill="#22c55e"/><path d="M6 10.5l2.5 2.5L14 8.5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
      } else {
        btn.title = 'Set On Leave';
        btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="9" fill="#f59e42"/><path d="M10 5v5l3 3" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
      }
    }
  });
});
</script>
<script>
(function(){
  const form = document.getElementById('vetsFilters');
  if(!form) return;
  const q = form.querySelector('input[name="q"]');
  const status = form.querySelector('select[name="status"]');
  const spec = form.querySelector('select[name="spec"]');
  let timer = null;
  function submitForm(){
    form.submit();
  }
  if(q){
    q.addEventListener('input', function(){
      clearTimeout(timer);
      timer = setTimeout(submitForm, 300);
    });
  }
  [status,spec].forEach(function(el){
    if(el) el.addEventListener('change', submitForm);
  });
})();
</script>
</body>
</html>