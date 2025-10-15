<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Medical Records</title>
  <link rel="stylesheet" href="/PETVET/public/css/pet-owner/medical-records.css"/>
</head>
<body>
  <main class="main-content">

  <!-- Header -->
  <header class="top-card">
    <div>
      <h1 id="petName"><?php echo htmlspecialchars($pet['name']); ?></h1>
      <p id="petInfo"><?php echo htmlspecialchars($pet['species'] . ' • ' . $pet['breed'] . ' • ' . $pet['age'] . 'y'); ?></p>
      <?php if (!empty($pet['microchip'])): ?><span class="badge microchip">Microchipped</span><?php endif; ?>
      <?php if (!empty($pet['vaccinated'])): ?><span class="badge vaccine">Vaccinated</span><?php endif; ?>
    </div>
    <button class="btn" onclick="openForm()">+ Add Record</button>
  </header>

  <!-- Overview -->
  <div class="section">
    <h2>Overview</h2>
    <p><b>Last Vaccination:</b> <?php echo htmlspecialchars($pet['last_vaccination']); ?></p>
    <p><b>Vet Contact:</b> <?php echo htmlspecialchars($pet['vet_contact']); ?></p>
    <p><b>Allergies:</b> <?php echo htmlspecialchars($pet['allergies']); ?></p>
    <div class="emergency">Blood Type: <?php echo htmlspecialchars($pet['blood_type']); ?></div>
  </div>

  <!-- Clinic Visits -->
  <div class="section">
    <h2>Clinic Visits</h2>
    <div id="clinicVisits">
      <?php if (empty($clinic_visits)): ?>
        <p class="empty-state">No clinic visits recorded yet.</p>
      <?php else: ?>
        <?php foreach ($clinic_visits as $v): ?>
          <div class="visit-card"><b><?php echo htmlspecialchars($v['date']); ?>:</b> <?php echo htmlspecialchars($v['title']); ?><br><?php echo htmlspecialchars($v['details']); ?></div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Vaccinations -->
  <div class="section">
    <h2>Vaccinations</h2>
    <?php if (empty($vaccinations)): ?>
      <p class="empty-state">No vaccination records available.</p>
    <?php else: ?>
      <div class="table-wrapper">
        <table id="vaccinationTable">
          <thead>
            <tr><th>Vaccine</th><th>Date</th><th>Next Due</th><th>Vet</th></tr>
          </thead>
          <tbody>
            <?php foreach ($vaccinations as $v): ?>
            <tr>
              <td><?php echo htmlspecialchars($v['vaccine']); ?></td>
              <td><?php echo htmlspecialchars($v['date']); ?></td>
              <td><?php echo htmlspecialchars($v['nextDue']); ?></td>
              <td><?php echo htmlspecialchars($v['vet']); ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <!-- Reports -->
  <div class="section">
    <h2>Reports</h2>
    <div id="reports">
      <?php if (empty($reports)): ?>
        <p class="empty-state">No reports uploaded yet.</p>
      <?php else: ?>
        <?php foreach ($reports as $index => $r): ?>
          <div class="report-card">
            <div>
              <b><?php echo htmlspecialchars($r['date']); ?>:</b> <?php echo htmlspecialchars($r['title']); ?><br><?php echo htmlspecialchars($r['details']); ?>
            </div>
            <button class="btn" onclick="previewReport(<?php echo (int)$index; ?>)">Preview</button>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Add Record Modal -->
  <div class="modal-overlay" id="recordModal" hidden>
    <div class="modal-card record" role="dialog" aria-modal="true" aria-labelledby="recordModalTitle">
      <button class="close-btn" onclick="closeForm()" aria-label="Close">×</button>
      <div class="modal-header"><h3 id="recordModalTitle">Add New Record</h3></div>
      <form id="recordForm" class="modal-body form-grid" autocomplete="off">
        <div class="grid-2">
          <div class="field"><label for="recordType">Record Type</label><select id="recordType" class="input" required><option value="clinic">Clinic Visit</option><option value="vaccination">Vaccination</option><option value="report">Report</option></select></div>
          <div class="field"><label for="recordDate">Date</label><input type="date" id="recordDate" class="input" required></div>
        </div>
        <div class="field"><label for="recordTitle">Title</label><input type="text" id="recordTitle" class="input" required placeholder="eg. Annual Vaccination"></div>
        <div class="field"><label for="recordDetails">Details</label><textarea id="recordDetails" class="input" rows="4" placeholder="Notes, dosage, follow-up..." ></textarea><div class="help-text">Keep it concise. You can edit later.</div></div>
        <div class="field"><label for="recordFile">File Upload</label><input type="file" id="recordFile" class="input" accept="image/*"><div id="filePreview"></div><div class="help-text">Optional image of prescription, report, etc.</div></div>
        <div class="modal-actions"><button type="button" class="btn secondary" onclick="closeForm()">Cancel</button><button type="submit" class="btn" id="saveRecordBtn">Save Record</button></div>
      </form>
    </div>
  </div>

  <!-- Preview Modal -->
  <div class="modal-overlay" id="previewModal" hidden>
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="previewModalTitle">
      <button class="close-btn" onclick="closePreview()" aria-label="Close">×</button>
      <div class="modal-header"><h3 id="previewModalTitle">Report Preview</h3></div>
      <div class="modal-body" id="previewContent"></div>
      <div class="modal-actions"><button class="btn secondary" onclick="closePreview()">Close</button></div>
    </div>
  </div>

  <script>
    // Modal helpers (re-usable) with body scroll lock
    function showOverlay(id){
      const el=document.getElementById(id); 
      if(!el) return;
      // close others
      document.querySelectorAll('.modal-overlay:not([hidden])').forEach(o=>{ 
        if(o.id!==id){
          o.hidden=true;
        }
      });
      el.hidden=false; 
      el.querySelector('[role="dialog"]').focus?.(); 
      document.body.classList.add('modal-open');
    }
    
    function hideOverlay(id){
      const el=document.getElementById(id); 
      if(el){
        el.hidden=true; 
        if(!document.querySelector('.modal-overlay:not([hidden])')) {
          document.body.classList.remove('modal-open');
        }
      }
    }
    
    function openForm(){
      hideOverlay('previewModal'); 
      showOverlay('recordModal');
    }
    
    function closeForm(){
      hideOverlay('recordModal');
    }
    
    function closePreview(){
      hideOverlay('previewModal');
    }

    // Preview state
    const previewState={images:[],current:0};
  function previewReport(index){hideOverlay('recordModal'); const reports=<?php echo json_encode($reports); ?>; const report=reports[index]; previewState.images=report.images||[]; previewState.current=0; renderPreviewImage(); showOverlay('previewModal');}
    function renderPreviewImage(){const content=document.getElementById('previewContent'); if(previewState.images.length){content.innerHTML=`<img src="${previewState.images[previewState.current]}" alt="Report Image" style="max-width:100%;border-radius:12px;">\n<div style="margin-top:12px;display:flex;justify-content:center;gap:14px;flex-wrap:wrap;">\n<button class=\"btn secondary\" onclick=\"prevPreviewImg()\" ${previewState.current===0?'disabled':''}>Prev</button>\n<span style=\"font-size:14px;align-self:center;\">${previewState.current+1} / ${previewState.images.length}</span>\n<button class=\"btn secondary\" onclick=\"nextPreviewImg()\" ${previewState.current===previewState.images.length-1?'disabled':''}>Next</button>\n</div>`;} else {content.innerHTML='<p>No images available</p>';}}
    function prevPreviewImg(){ if(previewState.current>0){previewState.current--; renderPreviewImage();}}
    function nextPreviewImg(){ if(previewState.current<previewState.images.length-1){previewState.current++; renderPreviewImage();}}

    // Close on Esc
    window.addEventListener('keydown',e=>{if(e.key==='Escape'){if(!document.getElementById('previewModal').hidden) closePreview(); if(!document.getElementById('recordModal').hidden) closeForm();}});
    // Backdrop click close
    document.addEventListener('click',e=>{if(e.target.classList.contains('modal-overlay')){hideOverlay(e.target.id);}});
    // File preview
    document.getElementById('recordFile').addEventListener('change',function(){const preview=document.getElementById('filePreview'); preview.innerHTML=''; const file=this.files[0]; if(file){const reader=new FileReader(); reader.onload=e=>{preview.innerHTML=`<img src='${e.target.result}' alt='Preview' style='max-width:100%;margin-top:8px;border-radius:12px;'>`;}; reader.readAsDataURL(file);}});
  </script>
  </main>
</body>
</html>
