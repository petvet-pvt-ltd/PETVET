<?php
// -----------------------------------------------------
// Lost & Found (Pet Owner) - Standalone View (UI Only)
// - No frameworks/libraries
// - Uses PHP arrays to simulate DB records
// - Toggle shows one list at a time (Lost or Found)
// -----------------------------------------------------

// Dummy data (simulate DB)
$reports = [
  // type: 'lost' | 'found'
  [
    'id' => 201,
    'type' => 'lost',
    'name' => 'Rocky',
    'species' => 'Dog',
    'breed' => 'Golden Retriever',
    'age' => '3y',
    'color' => 'Golden',
    'photo' => '/public/img/pets/rocky.jpg',
    'last_seen' => 'Madison St., Colombo 06',
    'date' => '2025-09-14',
    'notes' => 'Friendly, wears a red collar. Microchipped.',
    'contact' => ['name' => 'Kasun', 'email' => 'kasun@example.com', 'phone' => '+94 77 123 4567']
  ],
  [
    'id' => 202,
    'type' => 'lost',
    'name' => 'Whiskers',
    'species' => 'Cat',
    'breed' => 'Siamese',
    'age' => '2y',
    'color' => 'Cream/Seal',
    'photo' => '/public/img/pets/whiskers.jpg',
    'last_seen' => 'Flower Rd., Colombo 07',
    'date' => '2025-09-12',
    'notes' => 'Needs special diet. Very shy.',
    'contact' => ['name' => 'Nimali', 'email' => 'nimali@example.com', 'phone' => '+94 76 555 1212']
  ],
  [
    'id' => 203,
    'type' => 'found',
    'name' => null,
    'species' => 'Bird',
    'breed' => 'Canary',
    'age' => 'Unknown',
    'color' => 'Yellow',
    'photo' => '/public/img/pets/tweety.jpg',
    'last_seen' => 'Near Green Valley Park, Colombo 04',
    'date' => '2025-09-12',
    'notes' => 'Very tame. Responds to whistling.',
    'contact' => ['name' => 'Tharindu', 'email' => 'tharu@example.com', 'phone' => '+94 71 987 2345']
  ],
  [
    'id' => 204,
    'type' => 'found',
    'name' => null,
    'species' => 'Dog',
    'breed' => 'Mixed',
    'age' => 'Approx 1y',
    'color' => 'Brown/White',
    'photo' => '',
    'last_seen' => 'Marine Dr., Colombo 03',
    'date' => '2025-09-15',
    'notes' => 'No collar. Very calm.',
    'contact' => ['name' => 'Ishara', 'email' => 'ishara@example.com', 'phone' => '+94 75 222 9090']
  ],
];

// Split for counters & server-side rendering
$lostReports  = array_values(array_filter($reports, fn($r) => $r['type'] === 'lost'));
$foundReports = array_values(array_filter($reports, fn($r) => $r['type'] === 'found'));

function esc($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function fmtDate($ymd){
  $t = strtotime($ymd);
  return $t ? date('M j, Y', $t) : esc($ymd);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Lost &amp; Found</title>
  <link rel="stylesheet" href="lost-found.css">
</head>
<body>
  <?php require_once '../sidebar.php'; ?>

  <div class="main-content">
    <header class="lf-header">
      <div>
        <h2>Lost &amp; Found</h2>
        <p class="lf-sub">Report missing pets or browse found pets in your community.</p>
      </div>
      <button type="button" class="btn btn-primary" id="openReport">+ Report Pet</button>
    </header>

    <!-- Controls -->
    <section class="lf-controls">
      <div class="segmented" role="tablist" aria-label="Lost or Found">
        <button type="button" role="tab" aria-selected="true" class="seg-btn is-active" data-view="lost">
          Lost
        </button>
        <button type="button" role="tab" aria-selected="false" class="seg-btn" data-view="found">
          Found
        </button>
      </div>

      <div class="filters">
        <label class="input-wrap">
          <input type="text" id="q" placeholder="Search by name, breed, color, location...">
          <span class="icon">&#128269;</span>
        </label>

        <select id="species">
          <option value="">All species</option>
          <option>Dog</option>
          <option>Cat</option>
          <option>Bird</option>
        </select>

        <select id="sortBy">
          <option value="new">Newest first</option>
          <option value="old">Oldest first</option>
        </select>
      </div>
    </section>

    <!-- Lists -->
    <section id="lostList" class="cards-grid" aria-live="polite">
      <?php if(empty($lostReports)): ?>
        <div class="empty">
          <img src="/public/img/illustrations/empty-lost.svg" alt="" />
          <h3>No lost pets reported.</h3>
          <p>Great news! If you’re missing a pet, post a report so the community can help.</p>
          <button type="button" class="btn" id="emptyReportLost">+ Report Pet</button>
        </div>
      <?php else: ?>
        <?php foreach ($lostReports as $r): ?>
          <article class="card" data-species="<?php echo esc($r['species']); ?>" data-date="<?php echo esc($r['date']); ?>" data-color="<?php echo esc($r['color']); ?>">
            <div class="card-media">
              <?php if(!empty($r['photo'])): ?>
                <img src="<?php echo esc($r['photo']); ?>" alt="<?php echo esc($r['name'] ?: ($r['species'].' (unknown name)')); ?>">
              <?php else: ?>
                <div class="photo-fallback"><?php echo strtoupper(substr($r['species'],0,1)); ?></div>
              <?php endif; ?>
              <span class="badge badge-lost">Lost</span>
            </div>
            <div class="card-body">
              <h4 class="title">
                <?php echo esc($r['name'] ?: 'Unknown Name'); ?>
                <span class="muted">• <?php echo esc($r['species']); ?><?php echo $r['breed'] ? ' · '.esc($r['breed']) : ''; ?><?php echo $r['age'] ? ' · '.esc($r['age']) : ''; ?></span>
              </h4>
              <p class="meta"><strong>Last seen:</strong> <?php echo esc($r['last_seen']); ?> — <?php echo fmtDate($r['date']); ?></p>
              <?php if(!empty($r['notes'])): ?><p class="notes"><?php echo esc($r['notes']); ?></p><?php endif; ?>
              <div class="actions">
                <a class="btn btn-outline" href="mailto:<?php echo esc($r['contact']['email']); ?>?subject=Regarding lost pet <?php echo esc($r['name'] ?: $r['species']); ?>">Contact Owner</a>
                <a class="btn btn-ghost" href="tel:<?php echo esc($r['contact']['phone']); ?>">Call</a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>

    <section id="foundList" class="cards-grid hidden" aria-live="polite">
      <?php if(empty($foundReports)): ?>
        <div class="empty">
          <img src="/public/img/illustrations/empty-found.svg" alt="" />
          <h3>No found pets reported yet.</h3>
          <p>If you’ve found a pet, post a report so the owner can reach you.</p>
          <button type="button" class="btn" id="emptyReportFound">+ Report Pet</button>
        </div>
      <?php else: ?>
        <?php foreach ($foundReports as $r): ?>
          <article class="card" data-species="<?php echo esc($r['species']); ?>" data-date="<?php echo esc($r['date']); ?>" data-color="<?php echo esc($r['color']); ?>">
            <div class="card-media">
              <?php if(!empty($r['photo'])): ?>
                <img src="<?php echo esc($r['photo']); ?>" alt="<?php echo esc($r['species'].' found'); ?>">
              <?php else: ?>
                <div class="photo-fallback"><?php echo strtoupper(substr($r['species'],0,1)); ?></div>
              <?php endif; ?>
              <span class="badge badge-found">Found</span>
            </div>
            <div class="card-body">
              <h4 class="title">
                <?php echo esc($r['name'] ?: 'Unknown Name'); ?>
                <span class="muted">• <?php echo esc($r['species']); ?><?php echo $r['breed'] ? ' · '.esc($r['breed']) : ''; ?><?php echo $r['age'] ? ' · '.esc($r['age']) : ''; ?></span>
              </h4>
              <p class="meta"><strong>Found at:</strong> <?php echo esc($r['last_seen']); ?> — <?php echo fmtDate($r['date']); ?></p>
              <?php if(!empty($r['notes'])): ?><p class="notes"><?php echo esc($r['notes']); ?></p><?php endif; ?>
              <div class="actions">
                <a class="btn btn-outline" href="mailto:<?php echo esc($r['contact']['email']); ?>?subject=Regarding found <?php echo esc($r['species']); ?>">Contact Finder</a>
                <a class="btn btn-ghost" href="tel:<?php echo esc($r['contact']['phone']); ?>">Call</a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </div>

  <!-- Report Pet Modal (UI only) -->
  <div class="modal-overlay" id="reportModal" hidden>
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="reportTitle">
      <h3 id="reportTitle">Report Pet</h3>
      <form id="reportForm" autocomplete="off">
        <div class="row">
          <label class="field">Type
            <select id="rType" required>
              <option value="lost">Lost</option>
              <option value="found">Found</option>
            </select>
          </label>
          <label class="field">Species
            <select id="rSpecies" required>
              <option>Dog</option><option>Cat</option><option>Bird</option>
            </select>
          </label>
        </div>
        <div class="row">
          <label class="field flex-2">Name (optional)
            <input type="text" id="rName" placeholder="Rocky / Unknown">
          </label>
          <label class="field">Color
            <input type="text" id="rColor" placeholder="Golden / Black">
          </label>
        </div>
        <div class="row">
          <label class="field flex-2">Last seen location
            <input type="text" id="rLocation" required placeholder="Street, Area">
          </label>
          <label class="field">Date
            <input type="date" id="rDate" required>
          </label>
        </div>
        <label class="field">Notes
          <textarea id="rNotes" rows="3" placeholder="Collar info, temperament, special needs..."></textarea>
        </label>

        <div class="modal-actions">
          <button type="button" class="btn" id="cancelReport">Cancel</button>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
        <p class="form-hint">* UI only — submission won’t persist (demo).</p>
      </form>
    </div>
  </div>

  <script>
  (function(){
    const qs = s => document.querySelector(s);
    const qsa = s => document.querySelectorAll(s);

    const segBtns = qsa('.seg-btn');
    const lostList  = qs('#lostList');
    const foundList = qs('#foundList');

    const searchInput = qs('#q');
    const speciesSel  = qs('#species');
    const sortSel     = qs('#sortBy');

    const reportModal = qs('#reportModal');
    const openReportBtns = [qs('#openReport'), qs('#emptyReportLost'), qs('#emptyReportFound')].filter(Boolean);
    const cancelReportBtn = qs('#cancelReport');
    const reportForm = qs('#reportForm');

    // Initial view from URL (?view=lost|found) or localStorage
    const params = new URLSearchParams(location.search);
    let currentView = params.get('view') || localStorage.getItem('lfView') || 'lost';
    setView(currentView);

    // Toggle actions
    segBtns.forEach(b=>{
      b.addEventListener('click', ()=>{
        const v = b.getAttribute('data-view');
        setView(v);
      });
    });

    function setView(v){
      currentView = (v === 'found') ? 'found' : 'lost';
      segBtns.forEach(b=>{
        const isActive = b.getAttribute('data-view') === currentView;
        b.classList.toggle('is-active', isActive);
        b.setAttribute('aria-selected', isActive ? 'true' : 'false');
      });
      lostList.classList.toggle('hidden', currentView !== 'lost');
      foundList.classList.toggle('hidden', currentView !== 'found');

      const url = new URL(location.href);
      url.searchParams.set('view', currentView);
      history.replaceState({}, '', url);
      localStorage.setItem('lfView', currentView);

      applyFilters();
    }

    // Filtering & sorting (client-side UI only)
    function applyFilters(){
      const q = (searchInput.value || '').toLowerCase();
      const sp = speciesSel.value;
      const sort = sortSel.value; // new|old

      const container = currentView === 'lost' ? lostList : foundList;
      const cards = container.querySelectorAll('.card');

      const list = Array.from(cards).map(card => {
        const hay = (card.textContent || '').toLowerCase();
        const species = card.getAttribute('data-species');
        const date = card.getAttribute('data-date') || '1970-01-01';
        const matchQ = q === '' || hay.includes(q);
        const matchS = sp === '' || species === sp;
        const visible = matchQ && matchS;
        return { card, visible, date };
      });

      list.forEach(({card, visible}) => {
        card.style.display = visible ? '' : 'none';
      });

      // Sort by date
      const body = container; // grid itself
      const onlyVisible = list.filter(x=>x.visible);
      onlyVisible.sort((a,b)=>{
        const ta = Date.parse(a.date) || 0;
        const tb = Date.parse(b.date) || 0;
        return (sort === 'old') ? (ta - tb) : (tb - ta);
      });
      onlyVisible.forEach(x=> body.appendChild(x.card));
    }

    [searchInput, speciesSel, sortSel].forEach(ctrl=>{
      ctrl && ctrl.addEventListener('input', applyFilters);
      ctrl && ctrl.addEventListener('change', applyFilters);
    });

    // Report modal (UI only)
    openReportBtns.forEach(btn=>{
      btn && btn.addEventListener('click', ()=> { reportModal.hidden = false; });
    });
    cancelReportBtn && cancelReportBtn.addEventListener('click', ()=> { reportModal.hidden = true; reportForm.reset(); });
    reportModal.addEventListener('mousedown', e => {
      if (e.target === reportModal) { reportModal.hidden = true; reportForm.reset(); }
    });
    document.addEventListener('keydown', e=>{
      if(e.key === 'Escape' && !reportModal.hidden){ reportModal.hidden = true; reportForm.reset(); }
    });
    reportForm && reportForm.addEventListener('submit', e=>{
      e.preventDefault();
      alert('Demo only: This would create a new report.');
      reportModal.hidden = true;
      reportForm.reset();
    });

    // Kick off filter pass
    applyFilters();
  })();
  </script>
</body>
</html>
