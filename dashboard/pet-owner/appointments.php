<?php
// ---------------------------------------------
// Appointments (Pet Owner) - Standalone View
// UI Only (Reschedule + Cancel confirmation)
// ---------------------------------------------

$pets = [
  1 => ['name' => 'Rocky',    'species' => 'Dog', 'breed' => 'Golden Retriever', 'photo' => '/public/img/pets/rocky.jpg'],
  2 => ['name' => 'Whiskers', 'species' => 'Cat', 'breed' => 'Siamese',           'photo' => '/public/img/pets/whiskers.jpg'],
  3 => ['name' => 'Tweety',   'species' => 'Bird','breed' => 'Canary',            'photo' => '/public/img/pets/tweety.jpg'],
];

// Each appointment: id, pet_id, date (Y-m-d), time (H:i), vet, type, status
$appointments = [
  ['id'=>101, 'pet_id'=>1, 'date'=>'2025-09-05', 'time'=>'09:30', 'vet'=>'Dr. Williams', 'type'=>'General Checkup', 'status'=>'Confirmed'],
  ['id'=>102, 'pet_id'=>2, 'date'=>'2025-09-05', 'time'=>'14:00', 'vet'=>'Dr. Taylor',   'type'=>'Vaccination',     'status'=>'Pending'],
  ['id'=>103, 'pet_id'=>1, 'date'=>'2025-09-12', 'time'=>'11:15', 'vet'=>'Dr. Lee',      'type'=>'Grooming',        'status'=>'Confirmed'],
  ['id'=>104, 'pet_id'=>3, 'date'=>'2025-09-12', 'time'=>'16:45', 'vet'=>'Dr. Patel',    'type'=>'Nail Trim',       'status'=>'Confirmed'],
  ['id'=>105, 'pet_id'=>2, 'date'=>'2025-09-18', 'time'=>'10:00', 'vet'=>'Dr. Taylor',   'type'=>'Follow-up',       'status'=>'Confirmed'],
  // past example (won't show)
  ['id'=>106, 'pet_id'=>1, 'date'=>date('Y-m-d', strtotime('-2 days')), 'time'=>'08:30', 'vet'=>'Dr. Lee', 'type'=>'Dental', 'status'=>'Completed'],
];

// --- Filter to upcoming (today + future) and sort ----------------
$nowTs = time();
$upcoming = array_values(array_filter($appointments, function($a) use ($nowTs){
  $ts = strtotime($a['date'].' '.$a['time']);
  return $ts >= strtotime(date('Y-m-d 00:00:00', $nowTs)); // today onwards
}));

usort($upcoming, function($a,$b){
  $ta = strtotime($a['date'].' '.$a['time']);
  $tb = strtotime($b['date'].' '.$b['time']);
  return $ta <=> $tb;
});

// --- Group by date (Y-m-d) --------------------------------------
$byDate = [];
foreach ($upcoming as $appt) {
  $byDate[$appt['date']][] = $appt;
}

function dayHeader($ymd){
  $t = strtotime($ymd);
  return date('M j', $t) . ' - ' . date('D', $t);
}

function fmt12($hhmm){
  $ts = strtotime("1970-01-01 $hhmm");
  return date('h:i A', $ts);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Upcoming Appointments</title>
  <link rel="stylesheet" href="appointments.css">
</head>
<body>
  <?php require_once '../sidebar.php'; ?>

  <div class="main-content">
    <div class="page-header">
      <h2>Upcoming Appointments</h2>
    </div>

    <div class="appointments-wrap">
      <?php foreach ($byDate as $ymd => $list): ?>
        <section class="day-card" data-day="<?php echo $ymd; ?>">
          <header class="day-card__header">
            <span class="day-card__title"><?php echo htmlspecialchars(dayHeader($ymd)); ?></span>
          </header>

          <div class="day-card__body">
            <?php foreach ($list as $a):
              $p  = $pets[$a['pet_id']] ?? null;
              $nm = $p ? $p['name'] : 'Unknown';
              $meta = $p ? ($p['species'].' • '.$p['breed']) : '';
              $time12 = fmt12($a['time']);
              $status = strtolower($a['status']); // for css modifier
              $type   = $a['type'];
            ?>
              <div class="appt-row">
                <div class="appt-left">
                  <div class="avatar">
                    <?php if ($p && !empty($p['photo'])): ?>
                      <img src="<?php echo htmlspecialchars($p['photo']); ?>" alt="<?php echo htmlspecialchars($nm); ?>">
                    <?php else: ?>
                      <div class="avatar-fallback"><?php echo strtoupper(substr($nm,0,1)); ?></div>
                    <?php endif; ?>
                  </div>
                  <div class="appt-info">
                    <div class="appt-title">
                      <span class="pet-name"><?php echo htmlspecialchars($nm); ?></span>
                      <span class="pipe">•</span>
                      <span class="appt-time" data-time-24="<?php echo htmlspecialchars($a['time']); ?>">
                        <?php echo $time12; ?>
                      </span>
                    </div>
                    <div class="appt-meta">
                      <span class="pet-meta"><?php echo htmlspecialchars($meta); ?></span>
                      <span class="dot">·</span>
                      <span class="vet-name"><?php echo htmlspecialchars($a['vet']); ?></span>
                    </div>
                    <div class="appt-tags">
                      <span class="badge badge-type"><?php echo htmlspecialchars($type); ?></span>
                      <span class="badge badge-status badge-<?php echo $status; ?>">
                        <?php echo htmlspecialchars($a['status']); ?>
                      </span>
                    </div>
                  </div>
                </div>

                <div class="appt-actions">
                  <button type="button" class="btn btn-blue" data-appt="<?php echo (int)$a['id']; ?>">Reschedule</button>
                  <button type="button" class="btn btn-red"  data-appt="<?php echo (int)$a['id']; ?>">Cancel</button>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Reschedule Modal -->
  <div class="modal-overlay" id="rescheduleModal" hidden>
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="rescheduleTitle">
      <h3 id="rescheduleTitle">Reschedule Appointment</h3>
      <form id="rescheduleForm" autocomplete="off">
        <label>
          New Date:
          <input type="date" id="newDate" required>
        </label>
        <label>
          New Time:
          <input type="time" id="newTime" required>
        </label>

        <div class="modal-actions">
          <button type="button" class="btn btn-red" id="cancelReschedule">Cancel</button>
          <button type="submit" class="btn btn-blue">Confirm</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Cancel Confirmation Modal -->
  <div class="modal-overlay" id="cancelModal" hidden>
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="cancelTitle">
      <h3 id="cancelTitle">Cancel Appointment?</h3>
      <p class="modal-text">This action cannot be undone. Do you want to cancel this appointment?</p>
      <div class="modal-actions">
        <button type="button" class="btn btn-blue" id="backCancel">Keep Appointment</button>
        <button type="button" class="btn btn-red" id="confirmCancel">Confirm Cancel</button>
      </div>
    </div>
  </div>

  <script>
  document.addEventListener("DOMContentLoaded", () => {
    const wrap = document.querySelector(".appointments-wrap");

    // ======= Reschedule elements
    const resOverlay = document.getElementById("rescheduleModal");
    const resForm = document.getElementById("rescheduleForm");
    const resCancelBtn = document.getElementById("cancelReschedule");
    const newDateInput = document.getElementById("newDate");
    const newTimeInput = document.getElementById("newTime");

    // ======= Cancel elements
    const cancelOverlay = document.getElementById("cancelModal");
    const backCancelBtn = document.getElementById("backCancel");
    const confirmCancelBtn = document.getElementById("confirmCancel");

    // State
    let activeRow = null;
    let currentDateTime = null;
    const modalGuard = new WeakMap(); // overlay -> allowOverlayClose boolean

    // ---------- Generic modal helpers ----------
    function openModal(overlay) {
      overlay.hidden = false;
      modalGuard.set(overlay, false);
      setTimeout(() => modalGuard.set(overlay, true), 150);
    }
    function closeModal(overlay) {
      overlay.hidden = true;
      modalGuard.set(overlay, false);
    }
    function overlayCanClose(overlay) {
      return modalGuard.get(overlay) === true;
    }

    // Close on outside click for both overlays
    [resOverlay, cancelOverlay].forEach(ov => {
      ov.addEventListener("mousedown", e => {
        if (e.target === ov && overlayCanClose(ov)) closeModal(ov);
      });
    });

    // Close on Escape (any open)
    document.addEventListener("keydown", e => {
      if (e.key !== "Escape") return;
      if (!resOverlay.hidden) closeModal(resOverlay);
      if (!cancelOverlay.hidden) closeModal(cancelOverlay);
    });

    // ---------- Event delegation for buttons ----------
    document.addEventListener("click", e => {
      // RESCHEDULE
      const resBtn = e.target.closest(".btn-blue[data-appt]");
      if (resBtn) {
        activeRow = resBtn.closest(".appt-row");

        const dayCard = activeRow.closest(".day-card");
        const existingDate = dayCard.getAttribute("data-day");
        const timeEl = activeRow.querySelector(".appt-time");
        const time24 = timeEl.getAttribute("data-time-24") || convertTo24(timeEl.textContent.trim());

        currentDateTime = toDate(existingDate, time24);

        newDateInput.value = existingDate;
        newDateInput.min = existingDate;
        newTimeInput.value = time24;

        openModal(resOverlay);
        newDateInput.focus();
        return;
      }

      // CANCEL
      const cancelBtn = e.target.closest(".btn-red[data-appt]");
      if (cancelBtn) {
        activeRow = cancelBtn.closest(".appt-row");
        openModal(cancelOverlay);
        return;
      }
    });

    // ---------- Reschedule handlers ----------
    resCancelBtn.addEventListener("click", () => closeModal(resOverlay));

    resForm.addEventListener("submit", e => {
      e.preventDefault();
      if (!activeRow) return;

      const newDate = newDateInput.value;
      const newTime = newTimeInput.value;
      if (!newDate || !newTime) return;

      const newDateTime = toDate(newDate, newTime);
      if (newDateTime <= currentDateTime) {
        alert("Please choose a future date/time after the existing appointment.");
        return;
      }

      // Update time text + data attr
      const timeEl = activeRow.querySelector(".appt-time");
      timeEl.textContent = format12(newTime);
      timeEl.setAttribute("data-time-24", newTime);

      // Set status to Pending (awaiting clinic confirm)
      const badge = activeRow.querySelector(".badge-status");
      badge.textContent = "Pending";
      badge.className = "badge badge-status badge-pending";

      // Move to the target date section (create if missing)
      let targetSection = document.querySelector(`[data-day='${newDate}']`);
      if (!targetSection) {
        targetSection = document.createElement("section");
        targetSection.className = "day-card";
        targetSection.setAttribute("data-day", newDate);
        targetSection.innerHTML = `
          <header class="day-card__header">
            <span class="day-card__title">${formatHeader(newDate)}</span>
          </header>
          <div class="day-card__body"></div>
        `;
        wrap.appendChild(targetSection);
      }
      targetSection.querySelector(".day-card__body").appendChild(activeRow);

      closeModal(resOverlay);
      resForm.reset();
      activeRow = null;
      currentDateTime = null;
    });

    // ---------- Cancel handlers ----------
    backCancelBtn.addEventListener("click", () => {
      closeModal(cancelOverlay);
      activeRow = null;
    });

    confirmCancelBtn.addEventListener("click", () => {
      if (!activeRow) return;

      // Update badge to Cancelled and dim row (UI only)
      const badge = activeRow.querySelector(".badge-status");
      badge.textContent = "Cancelled";
      badge.className = "badge badge-status badge-cancelled";
      activeRow.classList.add("appt-row--cancelled");

      closeModal(cancelOverlay);
      activeRow = null;
    });

    // ---------- Utilities ----------
    function convertTo24(time12h) {
      const s = time12h.replace(/\s+/g, ' ').trim();
      const m = s.match(/^(\d{1,2}):(\d{2})\s*([AP]M)$/i);
      if (!m) return "00:00";
      let h = parseInt(m[1], 10);
      const min = m[2];
      const mer = m[3].toUpperCase();
      if (h === 12) h = 0;
      if (mer === "PM") h += 12;
      return `${String(h).padStart(2, "0")}:${min}`;
    }
    function format12(hhmm24) {
      let [h, m] = hhmm24.split(":").map(n => parseInt(n,10));
      const mer = h >= 12 ? "PM" : "AM";
      h = h % 12; if (h === 0) h = 12;
      return `${String(h).padStart(2,"0")}:${String(m).padStart(2,"0")} ${mer}`;
    }
    function toDate(ymd, hhmm) {
      const [Y, M, D] = ymd.split("-").map(n => parseInt(n,10));
      const [h, m]    = hhmm.split(":").map(n => parseInt(n,10));
      return new Date(Y, M - 1, D, h, m, 0, 0);
    }
    function formatHeader(ymd) {
      const d = new Date(ymd + "T00:00:00");
      const month = d.toLocaleString(undefined, { month: "short" });
      const day = d.getDate();
      const wk = d.toLocaleString(undefined, { weekday: "short" });
      return `${month} ${day} - ${wk}`;
    }
  });
  </script>
</body>
</html>
