<?php
$currentPage = basename($_SERVER['PHP_SELF']);
// Simulate fetching appointments from DB
// ...existing code...
$appointments = [
  '2025-08-11' => [
    [ 'pet' => 'Rocky', 'client' => 'John', 'vet' => 'Dr. Silva',    'time' => '09:00' ],
    [ 'pet' => 'Bella', 'client' => 'Sarah', 'vet' => 'Dr. Silva',    'time' => '10:30' ],
    [ 'pet' => 'Milo',  'client' => 'Kevin', 'vet' => 'Dr. Ajantha',  'time' => '14:00' ],
  ],
  '2025-08-12' => [
    [ 'pet' => 'Max',   'client' => 'David',  'vet' => 'Dr. Perera',  'time' => '12:00' ],
    [ 'pet' => 'Coco',  'client' => 'Nimal',  'vet' => 'Dr. Silva',   'time' => '15:30' ],
  ],
  '2025-08-13' => [
    [ 'pet' => 'Charlie', 'client' => 'Ravi',  'vet' => 'Dr. Silva',   'time' => '09:30' ],
    [ 'pet' => 'Misty',   'client' => 'Dilani','vet' => 'Dr. Perera',  'time' => '11:00' ],
    [ 'pet' => 'Shadow',  'client' => 'Nadeesha','vet' => 'Dr. Ajantha','time' => '16:00' ],
  ],
  '2025-08-14' => [
    [ 'pet' => 'Luna',  'client' => 'Emma',    'vet' => 'Dr. Silva',   'time' => '15:00' ],
    [ 'pet' => 'Tuffy', 'client' => 'Chaminda','vet' => 'Dr. Ajantha', 'time' => '16:00' ],
  ],
  '2025-08-15' => [
    [ 'pet' => 'Oreo',     'client' => 'Tharindu','vet' => 'Dr. Silva',   'time' => '10:00' ],
    [ 'pet' => 'Daisy',    'client' => 'Sanduni', 'vet' => 'Dr. Perera',  'time' => '13:00' ],
    [ 'pet' => 'Buddy',    'client' => 'Isuru',   'vet' => 'Dr. Silva',   'time' => '15:30' ],
  ],
  '2025-08-16' => [
    [ 'pet' => 'Mochi',    'client' => 'Harsha',  'vet' => 'Dr. Ajantha', 'time' => '09:00' ],
    [ 'pet' => 'Simba',    'client' => 'Rashmi',  'vet' => 'Dr. Perera',  'time' => '11:30' ],
  ],
  '2025-08-17' => [
    [ 'pet' => 'Ginger',   'client' => 'Anushka', 'vet' => 'Dr. Silva',   'time' => '10:00' ],
    [ 'pet' => 'Snowy',    'client' => 'Priyan',  'vet' => 'Dr. Ajantha', 'time' => '14:30' ],
    [ 'pet' => 'Pablo',    'client' => 'Vishva',  'vet' => 'Dr. Perera',  'time' => '16:00' ],
  ],
  '2025-08-18' => [
    [ 'pet' => 'Loki',     'client' => 'Janani',  'vet' => 'Dr. Silva',   'time' => '09:00' ],
    [ 'pet' => 'Pepper',   'client' => 'Mahesh',  'vet' => 'Dr. Perera',  'time' => '11:00' ],
    [ 'pet' => 'Zara',     'client' => 'Madhavi', 'vet' => 'Dr. Ajantha', 'time' => '15:00' ],
  ],
  '2025-08-19' => [
    [ 'pet' => 'Leo',      'client' => 'Roshan',  'vet' => 'Dr. Perera',  'time' => '10:00' ],
    [ 'pet' => 'Nala',     'client' => 'Ruwani',  'vet' => 'Dr. Silva',   'time' => '13:30' ],
  ],
  '2025-08-20' => [
    [ 'pet' => 'Tiger',    'client' => 'Suresh',  'vet' => 'Dr. Silva',   'time' => '09:30' ],
    [ 'pet' => 'Mango',    'client' => 'Chathura','vet' => 'Dr. Ajantha', 'time' => '12:00' ],
    [ 'pet' => 'Chico',    'client' => 'Lalith',  'vet' => 'Dr. Perera',  'time' => '15:00' ],
  ],
  '2025-08-21' => [
    [ 'pet' => 'Biscuit',  'client' => 'Hasini',  'vet' => 'Dr. Silva',   'time' => '11:00' ],
    [ 'pet' => 'Rex',      'client' => 'Amal',    'vet' => 'Dr. Perera',  'time' => '14:30' ],
  ],
  '2025-08-22' => [
    [ 'pet' => 'Lucky',    'client' => 'Chamali', 'vet' => 'Dr. Ajantha', 'time' => '10:00' ],
    [ 'pet' => 'Bailey',   'client' => 'Shehan',  'vet' => 'Dr. Silva',   'time' => '12:30' ],
    [ 'pet' => 'Chester',  'client' => 'Dineth',  'vet' => 'Dr. Perera',  'time' => '16:00' ],
  ],
];
?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Appointments</title>
  <link rel="stylesheet" href="../../styles/dashboard/clinic-manager/appointments.css">
  <style>
    .calendar-view-toggle {
      display: flex;
      gap: 10px;
      margin-bottom: 18px;
    }
    .calendar-view-toggle .btn.active {
      background: #2563eb;
      color: #fff;
    }
    .calendar-view {
      display: none;
    }
    .calendar-view.active {
      display: block;
    }
    .today-col {
      border: 2px solid #2563eb;
      background: #e0e7ff;
    }
  </style>
</head>
<body>
<?php require_once '../sidebar.php'; ?>

<div class="main-content">
  <h2 class="page-title">Appointments</h2>

  <!-- Toolbar -->
  <div class="toolbar">
    <button class="btn primary" onclick="openAddModal()">+ Add Appointment</button>
  </div>

  <!-- Calendar View Toggle -->
  <div class="calendar-view-toggle">
    <button class="btn" id="btn-today" onclick="showCalendarView('today')">Today</button>
    <button class="btn active" id="btn-week" onclick="showCalendarView('week')">Week</button>
    <button class="btn" id="btn-month" onclick="showCalendarView('month')">Month</button>
  </div>

  <!-- Today View -->
  <div class="calendar-view" id="calendar-today">
    <div class="calendar" style="grid-template-columns: 1fr;">
      <div class="day-col today-col">
        <h4>Today (<?= date('l, M j') ?>)</h4>
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
            <?= date('g:i A', strtotime($appt['time'])) ?> - <?= htmlspecialchars($appt['pet']) ?>
          </div>
        <?php
            endforeach;
          endif;
        ?>
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
  <div class="calendar-view active" id="calendar-week">
    <div class="calendar">
      <?php foreach ($weekDays as $i => $dateObj):
        $dateStr = $dateObj->format('Y-m-d');
        $isToday = ($i === 0);
      ?>
      <div class="day-col<?= $isToday ? ' today-col' : '' ?>">
        <h4><?= $dateObj->format('M j - D') ?><?= $isToday ? ' (Today)' : '' ?></h4>
        <?php if (!empty($appointments[$dateStr])): ?>
          <?php foreach ($appointments[$dateStr] as $appt): ?>
            <div class="event"
                 data-pet="<?= htmlspecialchars($appt['pet']) ?>"
                 data-client="<?= htmlspecialchars($appt['client']) ?>"
                 data-vet="<?= htmlspecialchars($appt['vet']) ?>"
                 data-date="<?= $dateStr ?>"
                 data-time="<?= $appt['time'] ?>"
                 onclick="openDetailsFromEl(this)">
              <?= date('g:i A', strtotime($appt['time'])) ?> - <?= htmlspecialchars($appt['pet']) ?>
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
  <div class="calendar-view" id="calendar-month">
    <div style="display: flex; flex-direction: column; gap: 16px;">
      <?php foreach ($monthDays as $week): ?>
        <div class="calendar" style="margin-bottom:0;">
          <?php foreach ($week as $i => $dateObj):
            $dateStr = $dateObj->format('Y-m-d');
            $isToday = ($dateStr === (new DateTime())->format('Y-m-d'));
          ?>
          <div class="day-col<?= $isToday ? ' today-col' : '' ?>">
            <h4><?= $dateObj->format('M j - D') ?><?= $isToday ? ' (Today)' : '' ?></h4>
            <?php if (!empty($appointments[$dateStr])): ?>
              <?php foreach ($appointments[$dateStr] as $appt): ?>
                <div class="event"
                     data-pet="<?= htmlspecialchars($appt['pet']) ?>"
                     data-client="<?= htmlspecialchars($appt['client']) ?>"
                     data-vet="<?= htmlspecialchars($appt['vet']) ?>"
                     data-date="<?= $dateStr ?>"
                     data-time="<?= $appt['time'] ?>"
                     onclick="openDetailsFromEl(this)">
                  <?= date('g:i A', strtotime($appt['time'])) ?> - <?= htmlspecialchars($appt['pet']) ?>
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

<script>
function showCalendarView(view) {
  document.getElementById('btn-today').classList.remove('active');
  document.getElementById('btn-week').classList.remove('active');
  document.getElementById('btn-month').classList.remove('active');
  document.getElementById('btn-' + view).classList.add('active');
  document.getElementById('calendar-today').classList.remove('active');
  document.getElementById('calendar-week').classList.remove('active');
  document.getElementById('calendar-month').classList.remove('active');
  document.getElementById('calendar-' + view).classList.add('active');
}

function openDetailsFromEl(el) {
  document.getElementById('dPet').textContent = el.dataset.pet || '';
  document.getElementById('dClient').textContent = el.dataset.client || '';
  document.getElementById('dVet').textContent = el.dataset.vet || '';

  if (el.dataset.date) document.getElementById('dDate').value = el.dataset.date;
  if (el.dataset.time) document.getElementById('dTime').value = el.dataset.time;

  document.getElementById('detailsModal').classList.remove('hidden');
}

function openAddModal() {
  document.getElementById('addModal').classList.remove('hidden');
}

function closeModal(id) {
  document.getElementById(id).classList.add('hidden');
}
</script>
</body>
</html>
