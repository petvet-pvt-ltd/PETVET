<?php
$currentPage = basename($_SERVER['PHP_SELF']);
// Simulate fetching appointments from DB
// ...existing code...
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Appointments | Clinic Manager</title>
  <link rel="stylesheet" href="/PETVET/public/css/clinic-manager/enhanced-global.css">
  <link rel="stylesheet" href="/PETVET/public/css/clinic-manager/appointments.css">
  <style>
    .calendar-controls {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
      gap: 20px;
    }
    
    .view-toggle {
      display: flex;
      gap: 4px;
      background: var(--gray-100);
      padding: 4px;
      border-radius: var(--border-radius-sm);
    }
    
    .toggle-pill {
      padding: 8px 16px;
      border: none;
      background: transparent;
      color: var(--gray-600);
      font-weight: 600;
      border-radius: 6px;
      cursor: pointer;
      transition: var(--transition);
      font-size: 14px;
    }
    
    .toggle-pill.active {
      background: var(--primary);
      color: white;
      box-shadow: var(--shadow-sm);
    }
    
    .toggle-pill:hover:not(.active) {
      background: var(--gray-200);
      color: var(--gray-700);
    }
    
    .vet-filter {
      display: flex;
      align-items: center;
      gap: 12px;
      background: white;
      padding: 12px 16px;
      border: 1px solid var(--gray-300);
      border-radius: var(--border-radius-sm);
      box-shadow: var(--shadow-sm);
    }
    
    .calendar-view {
      display: none;
      background: white;
      border: 1px solid var(--gray-200);
      border-radius: var(--border-radius);
      box-shadow: var(--shadow-sm);
      overflow: hidden;
    }
    
    .calendar-view.active {
      display: block;
    }
    
    .calendar-header {
      background: var(--gradient-header);
      padding: 16px 20px;
      border-bottom: 1px solid var(--gray-200);
    }
    
    .calendar-header h3 {
      margin: 0;
      font-size: 18px;
      font-weight: 700;
      color: var(--gray-900);
    }
    
    .calendar-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1px;
      background: var(--gray-200);
    }
    
    .day-col {
      background: white;
      min-height: 300px;
      padding: 16px;
    }
    
    .day-header {
      font-weight: 700;
      color: var(--gray-900);
      margin-bottom: 12px;
      padding-bottom: 8px;
      border-bottom: 2px solid var(--gray-200);
      font-size: 15px;
    }
    
    .today-col {
      background: var(--blue-50);
      border: 2px solid var(--primary);
    }
    
    .today-col .day-header {
      color: var(--primary);
      border-color: var(--primary);
    }
    
    .appointment-item {
      background: var(--gradient-card);
      border: 1px solid var(--gray-200);
      border-radius: var(--border-radius-sm);
      padding: 12px;
      margin-bottom: 8px;
      cursor: pointer;
      transition: var(--transition);
    }
    
    .appointment-item:hover {
      border-color: var(--primary);
      box-shadow: var(--shadow-md);
      transform: translateY(-1px);
    }
    
    .appointment-time {
      font-weight: 700;
      color: var(--primary);
      font-size: 13px;
    }
    
    .appointment-details {
      margin-top: 4px;
      font-size: 13px;
      color: var(--gray-700);
    }
    
    .appointment-vet {
      font-weight: 600;
      color: var(--gray-600);
      font-size: 12px;
      margin-top: 2px;
    }
    
    .add-appointment-btn {
      position: fixed;
      bottom: 24px;
      right: 24px;
      width: 56px;
      height: 56px;
      border-radius: 50%;
      background: var(--gradient-primary);
      color: white;
      border: none;
      box-shadow: var(--shadow-lg);
      cursor: pointer;
      transition: var(--transition);
      font-size: 24px;
      z-index: 100;
    }
    
    .add-appointment-btn:hover {
      box-shadow: var(--shadow-xl);
      transform: scale(1.05);
    }
  </style>
</head>
<body>

<div class="main-content">
  <div class="page-header">
    <div>
      <h1 class="page-title">Appointments</h1>
      <p class="page-subtitle">Manage and schedule patient appointments</p>
    </div>
    <div style="display: flex; gap: 12px;">
      <button class="btn btn-secondary" onclick="exportAppointments()">
        📊 Export
      </button>
      <button class="btn btn-primary" onclick="openAddModal()">
        ➕ New Appointment
      </button>
    </div>
  </div>

  <?php 
    if (!isset($vetNames)) {
      $tmpSet = [];
      foreach($appointments as $d=>$items){ foreach($items as $a){ $tmpSet[$a['vet']] = true; } }
      $vetNames = array_keys($tmpSet); sort($vetNames);
    }
    $selectedVet = $selectedVet ?? ($_GET['vet'] ?? 'all');
    $view = $_GET['view'] ?? 'today';
  ?>

  <div class="calendar-controls">
    <div class="view-toggle" role="tablist" aria-label="Calendar View Modes">
      <button class="toggle-pill <?= $view==='today'?'active':'' ?>" id="btn-today" onclick="showCalendarView('today')">Today</button>
      <button class="toggle-pill <?= $view==='week'?'active':'' ?>" id="btn-week" onclick="showCalendarView('week')">Week</button>
      <button class="toggle-pill <?= $view==='month'?'active':'' ?>" id="btn-month" onclick="showCalendarView('month')">Month</button>
    </div>
    
    <form method="get" class="vet-filter" id="vetFilterForm">
      <input type="hidden" name="module" value="clinic-manager" />
      <input type="hidden" name="page" value="appointments" />
      <input type="hidden" name="view" id="currentViewInput" value="<?= htmlspecialchars($view) ?>" />
      <label for="vetSelect" style="font-weight: 600; color: var(--gray-700); margin-right: 8px;">Filter by Vet:</label>
      <select id="vetSelect" name="vet" class="select" style="min-width: 160px;" aria-label="Filter by vet">
        <option value="all" <?= $selectedVet==='all'?'selected':''; ?>>All Vets</option>
        <?php foreach($vetNames as $vn): ?>
          <option value="<?= htmlspecialchars($vn); ?>" <?= $selectedVet===$vn?'selected':''; ?>><?= htmlspecialchars($vn); ?></option>
        <?php endforeach; ?>
      </select>
      <?php if($selectedVet !== 'all'): ?>
        <a class="btn btn-ghost btn-sm" href="/PETVET/index.php?module=clinic-manager&page=appointments&vet=all" title="Clear filter">Clear</a>
      <?php endif; ?>
    </form>
  </div>

  <!-- Today View -->
  <div class="calendar-view <?= $view==='today'?'active':'' ?>" id="calendar-today">
    <div class="calendar" style="grid-template-columns: 1fr;">
      <div class="day-col today-col">
        <div class="day-date-stripe">
          <?= date('M j - D') ?>
        </div>
        <div style="margin-top: 8px;">
        <?php
          $todayStr = (new DateTime())->format('Y-m-d');
          if (!empty($appointments[$todayStr])):
            foreach ($appointments[$todayStr] as $appt):
        ?>
          <div class="event"
               data-pet="<?= htmlspecialchars($appt['pet']) ?>"
               data-client="<?= htmlspecialchars($appt['client']) ?>"
               data-vet="<?= htmlspecialchars($appt['vet']) ?>"
               data-date="<?= $todayStr ?>"
               data-time="<?= $appt['time'] ?>"
               onclick="openDetailsFromEl(this)">
            <span class="evt-time"><?= date('g:i A', strtotime($appt['time'])) ?></span>
            <span class="evt-vet"><?= htmlspecialchars($appt['vet']) ?></span>
          </div>
        <?php
            endforeach;
          else:
        ?>
          <div style="color:#64748b; font-size:15px; text-align:center; margin-top:18px;">No appointments for today.</div>
        <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Week View (dynamic) -->
  <?php
    $today = new DateTime();
    $weekDays = [];
    for ($i = 0; $i < 7; $i++) {
      $d = clone $today;
      $d->modify("+{$i} day");
      $weekDays[] = $d;
    }
  ?>
  <div class="calendar-view <?= $view==='week'?'active':'' ?>" id="calendar-week">
    <div class="calendar">
      <?php foreach ($weekDays as $i => $dateObj):
        $dateStr = $dateObj->format('Y-m-d');
        $isToday = ($i === 0);
      ?>
      <div class="day-col<?= $isToday ? ' today-col' : '' ?>">
        <div class="day-date-stripe">
          <?= $dateObj->format('M j - D') ?>
        </div>
        <?php if (!empty($appointments[$dateStr])): ?>
          <?php foreach ($appointments[$dateStr] as $appt): ?>
            <div class="event"
                 data-pet="<?= htmlspecialchars($appt['pet']) ?>"
                 data-client="<?= htmlspecialchars($appt['client']) ?>"
                 data-vet="<?= htmlspecialchars($appt['vet']) ?>"
                 data-date="<?= $dateStr ?>"
                 data-time="<?= $appt['time'] ?>"
                 onclick="openDetailsFromEl(this)">
              <span class="evt-time"><?= date('g:i A', strtotime($appt['time'])) ?></span>
              <span class="evt-vet"><?= htmlspecialchars($appt['vet']) ?></span>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Month View (dynamic) -->
  <?php
    $monthRows = 5; // Show 5 weeks
    $monthDays = [];
    $start = new DateTime();
    for ($row = 0; $row < $monthRows; $row++) {
      $week = [];
      for ($col = 0; $col < 7; $col++) {
        $d = clone $start;
        $d->modify("+" . ($row * 7 + $col) . " day");
        $week[] = $d;
      }
      $monthDays[] = $week;
    }
  ?>
  <div class="calendar-view <?= $view==='month'?'active':'' ?>" id="calendar-month">
    <div style="display: flex; flex-direction: column; gap: 16px;">
      <?php foreach ($monthDays as $week): ?>
        <div class="calendar" style="margin-bottom:0;">
          <?php foreach ($week as $i => $dateObj):
            $dateStr = $dateObj->format('Y-m-d');
            $isToday = ($dateStr === (new DateTime())->format('Y-m-d'));
          ?>
          <div class="day-col<?= $isToday ? ' today-col' : '' ?>">
            <div class="day-date-stripe">
              <?= $dateObj->format('M j - D') ?>
            </div>
            <?php if (!empty($appointments[$dateStr])): ?>
              <?php foreach ($appointments[$dateStr] as $appt): ?>
                <div class="event"
                     data-pet="<?= htmlspecialchars($appt['pet']) ?>"
                     data-client="<?= htmlspecialchars($appt['client']) ?>"
                     data-vet="<?= htmlspecialchars($appt['vet']) ?>"
                     data-date="<?= $dateStr ?>"
                     data-time="<?= $appt['time'] ?>"
                     onclick="openDetailsFromEl(this)">
                  <span class="evt-time"><?= date('g:i A', strtotime($appt['time'])) ?></span>
                  <span class="evt-vet"><?= htmlspecialchars($appt['vet']) ?></span>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="modal hidden">
  <div class="modal-content">
    <span class="close" onclick="closeModal('detailsModal')">&times;</span>
    <h3>Appointment Details</h3>
    <p><strong>Pet:</strong> <span id="dPet"></span></p>
    <p><strong>Client:</strong> <span id="dClient"></span></p>
    <p><strong>Vet:</strong> <span id="dVet"></span></p>
    <div class="input-row">
      <div>
        <label>Date</label>
        <input type="date" id="dDate">
      </div>
      <div>
        <label>Time</label>
        <input type="time" id="dTime">
      </div>
    </div>
    <div class="modal-actions">
      <button class="btn primary">Reschedule</button>
      <button class="btn danger">Cancel</button>
    </div>
  </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="modal hidden">
  <div class="modal-content">
    <span class="close" onclick="closeModal('addModal')">&times;</span>
    <h3>Add Appointment</h3>
    <label>Pet <input type="text"></label>
    <label>Client <input type="text"></label>
    <label>Vet <input type="text"></label>
    <div class="input-row">
      <div>
        <label>Date</label>
        <input type="date">
      </div>
      <div>
        <label>Time</label>
        <input type="time">
      </div>
    </div>
    <div class="modal-actions">
      <button class="btn primary">Save</button>
    </div>
  </div>
</div>

<script src="/PETVET/public/js/clinic-manager/appointments.js"></script>
<script>
// Auto-submit vet filter on change & active tab class management
(function(){
  const sel = document.getElementById('vetSelect');
  const viewInput = document.getElementById('currentViewInput');
  if(sel){ sel.addEventListener('change', ()=> sel.form.submit()); }
  const tabs = ['today','week','month'];
  window.showCalendarView = function(view){
    tabs.forEach(v=>{
      const cal = document.getElementById('calendar-'+v);
      const b = document.getElementById('btn-'+v);
      if(cal) cal.classList.remove('active');
      if(b) b.classList.remove('active');
    });
    const targetCal = document.getElementById('calendar-'+view);
    const targetBtn = document.getElementById('btn-'+view);
    if(targetCal) targetCal.classList.add('active');
    if(targetBtn) targetBtn.classList.add('active');
    if(viewInput) viewInput.value = view; // persist selection on next submit
  }
})();
</script>
</body>
</html>
