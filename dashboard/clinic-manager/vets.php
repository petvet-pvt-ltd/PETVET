<?php
$currentPage = basename($_SERVER['PHP_SELF']);

/* ---------- MOCK DATA (swap with DB later) ---------- */
$vets = [
  [
    'id'=>1,'name'=>'Dr. Robert Fox','photo'=>'https://i.pravatar.cc/64?img=11',
    'specialization'=>'General','phone'=>'02 9445 6721','email'=>'rob@clinic.lk',
    'status'=>'Active','next_slot'=>'2025-09-09 09:00:00','on_duty_dates'=>['2025-09-09','2025-09-11']
  ],
  [
    'id'=>2,'name'=>'Theresa Webb','photo'=>'https://i.pravatar.cc/64?img=48',
    'specialization'=>'Surgery','phone'=>'031 452 4910','email'=>'theresa@clinic.lk',
    'status'=>'On Leave','next_slot'=>'','on_duty_dates'=>[]
  ],
  [
    'id'=>3,'name'=>'Marvin McKinney','photo'=>'https://i.pravatar.cc/64?img=34',
    'specialization'=>'Dental','phone'=>'02 9445 6721','email'=>'marvin@clinic.lk',
    'status'=>'Active','next_slot'=>'2025-09-10 13:30:00','on_duty_dates'=>['2025-09-09']
  ],
  [
    'id'=>4,'name'=>'Dr. Kathryn Murphy','photo'=>'https://i.pravatar.cc/64?img=65',
    'specialization'=>'General','phone'=>'031 452 4910','email'=>'kathryn@clinic.lk',
    'status'=>'Suspended','next_slot'=>'','on_duty_dates'=>[]
  ],
  [
    'id'=>4,'name'=>'Dr. Kathryn Murphy','photo'=>'https://i.pravatar.cc/64?img=65',
    'specialization'=>'General','phone'=>'031 452 4910','email'=>'kathryn@clinic.lk',
    'status'=>'Suspended','next_slot'=>'','on_duty_dates'=>[]
  ],
  [
    'id'=>4,'name'=>'Dr. Kathryn Murphy','photo'=>'https://i.pravatar.cc/64?img=65',
    'specialization'=>'General','phone'=>'031 452 4910','email'=>'kathryn@clinic.lk',
    'status'=>'Suspended','next_slot'=>'','on_duty_dates'=>[]
  ],
  [
    'id'=>4,'name'=>'Dr. Kathryn Murphy','photo'=>'https://i.pravatar.cc/64?img=65',
    'specialization'=>'General','phone'=>'031 452 4910','email'=>'kathryn@clinic.lk',
    'status'=>'Suspended','next_slot'=>'','on_duty_dates'=>[]
  ],
  [
    'id'=>4,'name'=>'Dr. Kathryn Murphy','photo'=>'https://i.pravatar.cc/64?img=65',
    'specialization'=>'General','phone'=>'031 452 4910','email'=>'kathryn@clinic.lk',
    'status'=>'Suspended','next_slot'=>'','on_duty_dates'=>[]
  ],
  [
    'id'=>4,'name'=>'Dr. Kathryn Murphy','photo'=>'https://i.pravatar.cc/64?img=65',
    'specialization'=>'General','phone'=>'031 452 4910','email'=>'kathryn@clinic.lk',
    'status'=>'Suspended','next_slot'=>'','on_duty_dates'=>[]
  ],
  [
    'id'=>4,'name'=>'Dr. Kathryn Murphy','photo'=>'https://i.pravatar.cc/64?img=65',
    'specialization'=>'General','phone'=>'031 452 4910','email'=>'kathryn@clinic.lk',
    'status'=>'Suspended','next_slot'=>'','on_duty_dates'=>[]
  ],
  [
    'id'=>4,'name'=>'Dr. Kathryn Murphy','photo'=>'https://i.pravatar.cc/64?img=65',
    'specialization'=>'General','phone'=>'031 452 4910','email'=>'kathryn@clinic.lk',
    'status'=>'Suspended','next_slot'=>'','on_duty_dates'=>[]
  ],
  [
    'id'=>4,'name'=>'Dr. Kathryn Murphy','photo'=>'https://i.pravatar.cc/64?img=65',
    'specialization'=>'General','phone'=>'031 452 4910','email'=>'kathryn@clinic.lk',
    'status'=>'Suspended','next_slot'=>'','on_duty_dates'=>[]
  ],
  [
    'id'=>4,'name'=>'Dr. Kathryn Murphy','photo'=>'https://i.pravatar.cc/64?img=65',
    'specialization'=>'General','phone'=>'031 452 4910','email'=>'kathryn@clinic.lk',
    'status'=>'Suspended','next_slot'=>'','on_duty_dates'=>[]
  ],
  [
    'id'=>4,'name'=>'Dr. Kathryn Murphy','photo'=>'https://i.pravatar.cc/64?img=65',
    'specialization'=>'General','phone'=>'031 452 4910','email'=>'kathryn@clinic.lk',
    'status'=>'Suspended','next_slot'=>'','on_duty_dates'=>[]
  ],
  [
    'id'=>4,'name'=>'Dr. Kathryn Murphy','photo'=>'https://i.pravatar.cc/64?img=65',
    'specialization'=>'General','phone'=>'031 452 4910','email'=>'kathryn@clinic.lk',
    'status'=>'Suspended','next_slot'=>'','on_duty_dates'=>[]
  ],
  [
    'id'=>4,'name'=>'Dr. Kathryn Murphy','photo'=>'https://i.pravatar.cc/64?img=65',
    'specialization'=>'General','phone'=>'031 452 4910','email'=>'kathryn@clinic.lk',
    'status'=>'Suspended','next_slot'=>'','on_duty_dates'=>[]
  ],
  [
    'id'=>4,'name'=>'Dr. Kathryn Murphy','photo'=>'https://i.pravatar.cc/64?img=65',
    'specialization'=>'General','phone'=>'031 452 4910','email'=>'kathryn@clinic.lk',
    'status'=>'Suspended','next_slot'=>'','on_duty_dates'=>[]
  ],
];

$pending = [
  [
    'id'=>101,'name'=>'Dr. Jane Cooper','photo'=>'https://i.pravatar.cc/64?img=5',
    'specialization'=>'Dermatology','license'=>'SLMC-DE-88221','experience'=>'6 years',
    'docs'=>[['label'=>'Certificate.pdf','url'=>'#'],['label'=>'License.pdf','url'=>'#']]
  ],
  [
    'id'=>102,'name'=>'Dr. Jacob Jones','photo'=>'https://i.pravatar.cc/64?img=22',
    'specialization'=>'Radiology','license'=>'SLMC-RA-77110','experience'=>'8 years',
    'docs'=>[['label'=>'Degree.pdf','url'=>'#'],['label'=>'License.pdf','url'=>'#']]
  ],
  [
    'id'=>102,'name'=>'Dr. Jacob Jones','photo'=>'https://i.pravatar.cc/64?img=22',
    'specialization'=>'Radiology','license'=>'SLMC-RA-77110','experience'=>'8 years',
    'docs'=>[['label'=>'Degree.pdf','url'=>'#'],['label'=>'License.pdf','url'=>'#']]
  ],
];

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
  <title>Vets | Clinic Manager</title>
  <link rel="stylesheet" href="../../styles/dashboard/clinic-manager/vets.css">
</head>
<body>

<?php require_once '../sidebar.php'; ?>

<main class="cmc-main">
  <!-- Header -->
  <header class="cmc-header">
    <h1 class="cmc-page-title">Vets</h1>
    <div class="cmc-actions">
      <button class="btn btn-primary" id="openDrawer">
        Pending Requests <span class="badge"><?= count($pending) ?></span>
      </button>
    </div>
  </header>

  <!-- Summary cards -->
  <section class="cmc-cards">
    <article class="card">
      <div class="card-label">Total Active Vets</div>
      <div class="card-value"><?= $total_active ?></div>
    </article>
    <article class="card">
      <div class="card-label">On Duty Today</div>
      <div class="card-sub">
        <?= count($on_duty_today) ?> — <?= $on_duty_today ? implode(', ', $on_duty_today) : '—' ?>
      </div>
    </article>
  </section>

  <!-- Filters -->
  <form method="get" class="cmc-filters">
    <div class="field full-grow">
      <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" class="input"
             placeholder="Search by name, specialization, or license">
    </div>

    <div class="field">
      <label class="label" for="spec">Specialization</label>
      <select name="spec" id="spec" class="select">
        <option value="all"     <?= $spec==='all'?'selected':'' ?>>All</option>
        <option value="General" <?= $spec==='General'?'selected':'' ?>>General</option>
        <option value="Surgery" <?= $spec==='Surgery'?'selected':'' ?>>Surgery</option>
        <option value="Dental"  <?= $spec==='Dental'?'selected':'' ?>>Dental</option>
        <option value="Dermatology" <?= $spec==='Dermatology'?'selected':'' ?>>Dermatology</option>
        <option value="Radiology"   <?= $spec==='Radiology'?'selected':'' ?>>Radiology</option>
      </select>
    </div>

    <div class="field">
      <label class="label" for="status">Status</label>
      <select name="status" id="status" class="select">
        <option value="all"        <?= $status==='all'?'selected':'' ?>>All</option>
        <option value="Active"     <?= $status==='Active'?'selected':'' ?>>Active</option>
        <option value="On Leave"   <?= $status==='On Leave'?'selected':'' ?>>On Leave</option>
        <option value="Suspended"  <?= $status==='Suspended'?'selected':'' ?>>Suspended</option>
      </select>
    </div>

    <div class="field">
      <label class="label" for="avail">Availability</label>
      <input type="date" name="avail" id="avail" value="<?= htmlspecialchars($avail) ?>" class="input">
    </div>

    <div class="field">
      <button type="submit" class="btn btn-primary">Apply</button>
    </div>
    <div class="field">
      <a class="btn btn-ghost" href="vets.php">Clear</a>
    </div>
  </form>

  <!-- Table (only this area scrolls; header is sticky) -->
  <section class="cmc-table-card">
    <div class="cmc-table-wrap">
      <table class="cmc-table">
        <thead>
          <tr>
            <th>Full Name</th>
            <th>Specialization</th>
            <th>Contact</th>
            <th>Status</th>
            <th>Next Appointment Slot</th>
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
              <td class="row-actions">
                <button class="btn-primary btn" title="View">View</button>
                <button class="action-btn" title="Edit">✏️</button>
                <button class="action-btn" title="<?= $v['status']==='Suspended'?'Activate':'Suspend' ?>">
                  <?= $v['status']==='Suspended'?'▶️':'⛔' ?>
                </button>
                <button class="action-btn" title="Schedule">📅</button>
              </td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="6" class="empty">No vets found.</td></tr>
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
    <?php if ($pending): foreach ($pending as $p): ?>
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
        <button class="btn btn-success">Approve</button>
        <button class="btn btn-danger">Decline</button>
      </div>
    </div>
    <?php endforeach; else: ?>
      <div class="empty">No pending requests.</div>
    <?php endif; ?>
  </div>
</aside>
<div id="backdrop" class="backdrop"></div>

<script>
const drawer = document.getElementById('drawer');
const backdrop = document.getElementById('backdrop');
const open1 = document.getElementById('openDrawer');
const closeBtn = document.getElementById('closeDrawer');

function openDrawer(){
  drawer.classList.add('open');
  backdrop.classList.add('show');
}
function closeDrawer(){
  drawer.classList.remove('open');
  backdrop.classList.remove('show');
}

open1.addEventListener('click', openDrawer);
closeBtn.addEventListener('click', closeDrawer);
backdrop.addEventListener('click', closeDrawer);
</script>
</body>
</html>
