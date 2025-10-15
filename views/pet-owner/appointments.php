<?php /* Pet Owner Appointments - full migrated prototype (UI only) */ ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<link rel="stylesheet" href="/PETVET/public/css/pet-owner/appointments.css">

<div class="main-content">
  <div class="page-header">
    <h2>Upcoming Appointments</h2>
  </div>

  <div class="appointments-wrap">
<?php /* Use data from controller instead of static arrays */ ?>
<?php
// Data comes from the controller via AppointmentsModel
$pets = $pets ?? [];
$appointments = $appointments ?? [];

// Filter and sort appointments by date for display
$byDate = [];
foreach ($appointments as $appt) { 
    $byDate[$appt['date']][] = $appt; 
}

// Helper functions for display formatting
function dayHeader($ymd){ 
    $t=strtotime($ymd); 
    return date('M j', $t).' - '.date('D',$t);
} 

function fmt12($hhmm){ 
    $ts=strtotime("1970-01-01 $hhmm"); 
    return date('h:i A',$ts);
} 
?>
<?php foreach ($byDate as $ymd => $list): ?>
  <section class="day-card" data-day="<?php echo $ymd; ?>">
    <header class="day-card__header">
      <span class="day-card__title"><?php echo htmlspecialchars(dayHeader($ymd)); ?></span>
    </header>
    <div class="day-card__body">
      <?php foreach ($list as $a): $p=$pets[$a['pet_id']]??null; $nm=$p? $p['name']:'Unknown'; $meta=$p?($p['species'].' • '.$p['breed']):''; $time12=fmt12($a['time']); $status=strtolower($a['status']); $type=$a['type']; ?>
      <div class="appt-row">
        <div class="appt-left">
          <div class="avatar">
            <?php if ($p && !empty($p['photo'])): ?><img src="<?php echo htmlspecialchars($p['photo']); ?>" alt="<?php echo htmlspecialchars($nm); ?>"><?php else: ?><div class="avatar-fallback"><?php echo strtoupper(substr($nm,0,1)); ?></div><?php endif; ?>
          </div>
          <div class="appt-info">
            <div class="appt-title">
              <span class="pet-name"><?php echo htmlspecialchars($nm); ?></span>
              <span class="pipe">•</span>
              <span class="appt-time" data-time-24="<?php echo htmlspecialchars($a['time']); ?>"><?php echo $time12; ?></span>
            </div>
            <div class="appt-meta">
              <span class="pet-meta"><?php echo htmlspecialchars($meta); ?></span>
              <span class="dot">·</span>
              <span class="vet-name"><?php echo htmlspecialchars($a['vet']); ?></span>
            </div>
            <div class="appt-tags">
              <span class="badge badge-type"><?php echo htmlspecialchars($type); ?></span>
              <span class="badge badge-status badge-<?php echo $status; ?>"><?php echo htmlspecialchars($a['status']); ?></span>
            </div>
          </div>
        </div>
        <div class="appt-actions">
          <button type="button" class="btn primary" data-appt="<?php echo (int)$a['id']; ?>">Reschedule</button>
          <button type="button" class="btn danger" data-appt="<?php echo (int)$a['id']; ?>">Cancel</button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>
<?php endforeach; ?>
  </div>
</div>

<!-- Modals -->
<div class="modal-overlay" id="rescheduleModal" hidden>
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="rescheduleTitle">
    <h3 id="rescheduleTitle">Reschedule Appointment</h3>
    <form id="rescheduleForm" autocomplete="off">
      <label>New Date:<input type="date" id="newDate" required></label>
      <label>New Time:<input type="time" id="newTime" required></label>
      <div class="modal-actions">
        <button type="button" class="btn ghost" id="cancelReschedule">Cancel</button>
        <button type="submit" class="btn primary">Confirm</button>
      </div>
    </form>
  </div>
</div>
<div class="modal-overlay" id="cancelModal" hidden>
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="cancelTitle">
    <h3 id="cancelTitle">Cancel Appointment?</h3>
    <p class="modal-text">This action cannot be undone. Do you want to cancel this appointment?</p>
    <div class="modal-actions">
      <button type="button" class="btn outline" id="backCancel">Keep</button>
      <button type="button" class="btn danger" id="confirmCancel">Confirm</button>
    </div>
  </div>
</div>
<script src="/PETVET/public/js/pet-owner/appointments.js"></script>
