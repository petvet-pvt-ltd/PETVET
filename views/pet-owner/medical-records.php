<?php
// Sample data - Replace with actual database queries
$pet = [
  'name' => 'Max',
  'species' => 'Dog',
  'breed' => 'Golden Retriever',
  'age' => '3',
  'microchip' => true,
  'vaccinated' => true,
  'last_vaccination' => 'October 5, 2025',
  'vet_contact' => 'Dr. Smith - (555) 123-4567',
  'allergies' => 'Chicken, Dairy',
  'blood_type' => 'DEA 1.1 Positive'
];

$clinic_visits = [
  ['date' => 'October 10, 2025', 'title' => 'Regular Checkup', 'details' => 'All vitals normal. Weight: 32kg'],
  ['date' => 'September 15, 2025', 'title' => 'Skin Irritation', 'details' => 'Prescribed topical cream for rash']
];

$vaccinations = [
  ['pet' => 'Max', 'vaccine' => 'Rabies', 'date' => 'October 5, 2025', 'nextDue' => 'October 5, 2026', 'vet' => 'Dr. Smith'],
  ['pet' => 'Max', 'vaccine' => 'DHPP', 'date' => 'October 5, 2025', 'nextDue' => 'October 5, 2026', 'vet' => 'Dr. Smith'],
  ['pet' => 'Max', 'vaccine' => 'Bordetella', 'date' => 'April 10, 2025', 'nextDue' => 'April 10, 2026', 'vet' => 'Dr. Johnson']
];

$prescriptions = [
  ['pet' => 'Max', 'medication' => 'Amoxicillin', 'dosage' => '250mg', 'frequency' => 'Twice daily', 'startDate' => 'September 15, 2025', 'endDate' => 'September 25, 2025', 'vet' => 'Dr. Smith'],
  ['pet' => 'Max', 'medication' => 'Anti-inflammatory Cream', 'dosage' => 'Apply thin layer', 'frequency' => 'Twice daily', 'startDate' => 'September 15, 2025', 'endDate' => 'September 30, 2025', 'vet' => 'Dr. Smith']
];

$reports = [
  ['date' => 'October 10, 2025', 'title' => 'Blood Test Results', 'details' => 'All parameters within normal range', 'images' => [
    '/PETVET/views/shared/images/petser.jpg',
    '/PETVET/views/shared/images/vet-reg-cover.jpg',
    '/PETVET/views/shared/images/vet-reg-cover2.jpg'
  ]],
  ['date' => 'September 15, 2025', 'title' => 'X-Ray Report', 'details' => 'No abnormalities detected', 'images' => [
    'https://www.pommri.com/blog/wp-content/uploads/2019/03/hand-xray-177559095.jpg',
    '/PETVET/views/shared/images/petser.jpg'
  ]],
  ['date' => 'August 20, 2025', 'title' => 'Ultrasound Scan', 'details' => 'Abdominal scan completed successfully', 'images' => [
    '/PETVET/views/shared/images/vet-reg-cover.jpg'
  ]]
];
?>
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
            <tr><th>Pet</th><th>Vaccine</th><th>Date</th><th>Next Due</th><th>Vet</th></tr>
          </thead>
          <tbody>
            <?php foreach ($vaccinations as $v): ?>
            <tr>
              <td><?php echo htmlspecialchars($v['pet']); ?></td>
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

  <!-- Prescriptions -->
  <div class="section">
    <h2>Prescriptions</h2>
    <?php if (empty($prescriptions)): ?>
      <p class="empty-state">No prescription records available.</p>
    <?php else: ?>
      <div class="table-wrapper">
        <table id="prescriptionTable">
          <thead>
            <tr><th>Pet</th><th>Medication</th><th>Dosage</th><th>Frequency</th><th>Start Date</th><th>End Date</th><th>Vet</th></tr>
          </thead>
          <tbody>
            <?php foreach ($prescriptions as $p): ?>
            <tr>
              <td><?php echo htmlspecialchars($p['pet']); ?></td>
              <td><?php echo htmlspecialchars($p['medication']); ?></td>
              <td><?php echo htmlspecialchars($p['dosage']); ?></td>
              <td><?php echo htmlspecialchars($p['frequency']); ?></td>
              <td><?php echo htmlspecialchars($p['startDate']); ?></td>
              <td><?php echo htmlspecialchars($p['endDate']); ?></td>
              <td><?php echo htmlspecialchars($p['vet']); ?></td>
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
    <div class="modal-card preview-modal" role="dialog" aria-modal="true" aria-labelledby="previewModalTitle">
      <button class="close-btn" onclick="closePreview()" aria-label="Close">×</button>
      <div class="modal-header">
        <h3 id="previewModalTitle">Report Preview</h3>
        <div id="previewImageCounter" style="font-size: 14px; color: #64748b;"></div>
      </div>
      <div class="modal-body preview-body">
        <div id="previewContent"></div>
      </div>
      <div class="modal-actions preview-actions" id="previewNavigation">
        <button class="btn" onclick="prevPreviewImg()" id="prevBtn">Previous</button>
        <button class="btn" onclick="nextPreviewImg()" id="nextBtn">Next</button>
      </div>
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
    function previewReport(index){
      hideOverlay('recordModal'); 
      const reports=<?php echo json_encode($reports); ?>; 
      const report=reports[index]; 
      previewState.images=report.images||[]; 
      previewState.current=0; 
      renderPreviewImage(); 
      showOverlay('previewModal');
    }
    
    function renderPreviewImage(){
      const content=document.getElementById('previewContent'); 
      const counter=document.getElementById('previewImageCounter');
      const prevBtn=document.getElementById('prevBtn');
      const nextBtn=document.getElementById('nextBtn');
      const navigation=document.getElementById('previewNavigation');
      
      if(previewState.images.length){
        // Update image
        content.innerHTML=`<img src="${previewState.images[previewState.current]}" alt="Report Image" class="preview-image">`;
        
        // Update counter
        if(previewState.images.length > 1){
          counter.textContent = `Image ${previewState.current+1} of ${previewState.images.length}`;
          counter.style.display = 'block';
        } else {
          counter.style.display = 'none';
        }
        
        // Update navigation buttons
        prevBtn.disabled = previewState.current === 0;
        nextBtn.disabled = previewState.current === previewState.images.length - 1;
        
        // Hide navigation if only one image
        if(previewState.images.length === 1){
          prevBtn.style.display = 'none';
          nextBtn.style.display = 'none';
        } else {
          prevBtn.style.display = 'inline-block';
          nextBtn.style.display = 'inline-block';
        }
      } else {
        content.innerHTML='<p style="text-align:center;color:#64748b;">No images available</p>';
        counter.style.display = 'none';
        prevBtn.style.display = 'none';
        nextBtn.style.display = 'none';
      }
    }
    
    function prevPreviewImg(){ 
      if(previewState.current>0){
        previewState.current--; 
        renderPreviewImage();
      }
    }
    
    function nextPreviewImg(){ 
      if(previewState.current<previewState.images.length-1){
        previewState.current++; 
        renderPreviewImage();
      }
    }
    
    // Keyboard navigation for preview
    window.addEventListener('keydown', function(e){
      if(!document.getElementById('previewModal').hidden){
        if(e.key === 'ArrowLeft') prevPreviewImg();
        if(e.key === 'ArrowRight') nextPreviewImg();
      }
    });

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
