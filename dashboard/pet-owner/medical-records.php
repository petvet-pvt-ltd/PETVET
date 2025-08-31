<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Pet Medical Records</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f6fa;
      margin:0 ;
      margin-left: 240px;
      padding: 0;
    }

    /* Top card */
    .top-card {
      background: #fff;
      margin: 20px auto;
      max-width: 1000px;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-left:350px;
    }
    .top-card h1 {
      margin: 0;
      font-size: 22px;
      color: #333;
    }
    .top-card p {
      margin: 5px 0 10px;
      color: #666;
    }
    .badge {
      display: inline-block;
      padding: 4px 8px;
      border-radius: 8px;
      font-size: 12px;
      margin-right: 5px;
      
    }
    .microchip { background: #d1f7c4; color: #2d7a1f; }
    .vaccine { background: #c4e1f7; color: #1f497a; }

    /* Section cards */
    .section {
      background: #fff;
      margin: 20px auto;
      max-width: 1000px;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
      margin-left:350px;
    }
    .section h2 {
      margin-top: 0;
      color: #333;
    }

    /* Emergency info */
    .emergency {
      background: #ffdddd;
      border: 1px solid #ff5c5c;
      padding: 10px;
      border-radius: 8px;
      color: #a30000;
      font-weight: bold;
      margin: 10px 0;
    }

    /* Clinic visits */
    .visit-card {
      border-left: 4px solid #4a90e2;
      padding: 10px;
      margin: 10px 0;
      background: #f9f9f9;
      border-radius: 8px;
    }

    /* Vaccination table */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    table th, table td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: left;
    }
    table th {
      background: #f0f0f0;
    }

    /* Reports */
    .report-card {
      background: #f9f9f9;
      border-radius: 8px;
      padding: 10px;
      margin: 10px 0;
      border-left: 4px solid #e67e22;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    /* Buttons */
    .btn {
      background: #4a90e2;
      color: white;
      padding: 8px 14px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 14px;
    }
    .btn:hover {
      background: #357abd;
    }
    .btn-secondary {
      background: #ccc;
      color: #333;
    }

    /* Modal */
    .modal {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.6);
      justify-content: center;
      align-items: center;
    }
    .modal-content {
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      max-width: 500px;
      width: 90%;
      text-align: center;
      position: relative;
    }
    .modal-content img {
      max-width: 100%;
      border-radius: 8px;
      margin-top: 10px;
    }
    .close-btn {
      position: absolute;
      top: 10px; right: 10px;
      background: #ff5c5c;
      border: none;
      color: white;
      border-radius: 50%;
      width: 28px;
      height: 28px;
      cursor: pointer;
}
     
    
  </style>
</head>
<body>
 <?php require_once '../sidebar.php'; ?>

<?php
// Simulated database records
$pet = array(
  'name' => 'Rocky',
  'species' => 'Dog',
  'breed' => 'Golden Retriever',
  'age' => 3,
  'microchip' => true,
  'vaccinated' => true,
  'last_vaccination' => '2025-05-10',
  'vet_contact' => 'Dr. Smith (555-1234)',
  'allergies' => 'Chicken',
  'blood_type' => 'B+',
);

$clinic_visits = array(
  array('date' => '2025-04-12', 'title' => 'General Checkup', 'details' => 'Healthy, routine check.'),
  array('date' => '2025-03-05', 'title' => 'Allergy Treatment', 'details' => 'Prescribed antihistamines.'),
);

$vaccinations = array(
  array('vaccine' => 'Rabies', 'date' => '2025-05-10', 'nextDue' => '2026-05-10', 'vet' => 'Dr. Smith'),
);

$reports = array(
  array(
    'date' => '2025-04-20',
    'title' => 'X-Ray',
    'details' => 'Fracture healing well.',
    'images' => array(
      'https://www.nylabone.com/-/media/project/oneweb/nylabone/images/dog101/activities-fun/10-great-small-dog-breeds/maltese-portrait.jpg?h=448&w=740&hash=B111F1998758CA0ED2442A4928D5105D',
      'https://images.unsplash.com/photo-1518717758536-85ae29035b6d?auto=format&fit=crop&w=800&q=80'
    ),
  ),
);
?>

  <!-- Header -->
  <header class="top-card">
    <div>
      <h1 id="petName"><?php echo htmlspecialchars($pet['name']); ?></h1>
      <p id="petInfo"><?php echo htmlspecialchars($pet['species'] . ' • ' . $pet['breed'] . ' • ' . $pet['age'] . 'y'); ?></p>
      <?php if ($pet['microchip']): ?><span class="badge microchip">Microchipped</span><?php endif; ?>
      <?php if ($pet['vaccinated']): ?><span class="badge vaccine">Vaccinated</span><?php endif; ?>
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
      <?php foreach ($clinic_visits as $v): ?>
        <div class="visit-card"><b><?php echo htmlspecialchars($v['date']); ?>:</b> <?php echo htmlspecialchars($v['title']); ?><br><?php echo htmlspecialchars($v['details']); ?></div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Vaccinations -->
  <div class="section">
    <h2>Vaccinations</h2>
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

  <!-- Reports -->
  <div class="section">
    <h2>Reports</h2>
    <div id="reports">
      <?php foreach ($reports as $index => $r): ?>
        <div class="report-card">
          <div>
            <b><?php echo htmlspecialchars($r['date']); ?>:</b> <?php echo htmlspecialchars($r['title']); ?><br><?php echo htmlspecialchars($r['details']); ?>
          </div>
          <button class="btn" onclick="previewReport(<?php echo $index; ?>)">Preview</button>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Back button removed -->

  <!-- Add Record Modal -->
  <div class="modal" id="recordModal">
    <div class="modal-content">
      <button class="close-btn" onclick="closeForm()">×</button>
      <h2>Add New Record</h2>
      <form id="recordForm">
        <label>Record Type:
          <select id="recordType" required>
            <option value="clinic">Clinic Visit</option>
            <option value="vaccination">Vaccination</option>
            <option value="report">Report</option>
          </select>
        </label><br><br>
        <label>Date: <input type="date" id="recordDate" required></label><br><br>
        <label>Title: <input type="text" id="recordTitle" required></label><br><br>
        <label>Details:<br>
          <textarea id="recordDetails" rows="3"></textarea>
        </label><br><br>
        <label>File Upload: <input type="file" id="recordFile" accept="image/*"></label>
        <div id="filePreview"></div><br>
        <button type="submit" class="btn">Save Record</button>
      </form>
    </div>
  </div>

  <!-- Preview Modal -->
  <div class="modal" id="previewModal">
    <div class="modal-content">
      <button class="close-btn" onclick="closePreview()">×</button>
      <h2>Report Preview</h2>
      <div id="previewContent"></div>
    </div>
  </div>

  <script>
    // Form Modal
    function openForm() { document.getElementById("recordModal").style.display = "flex"; }
    function closeForm() { document.getElementById("recordModal").style.display = "none"; }

    // Preview Modal
    var previewState = { images: [], current: 0 };
function previewReport(index) {
  var reports = <?php echo json_encode($reports); ?>;
  var report = reports[index];
  var content = document.getElementById("previewContent");
  previewState.images = report.images || [];
  previewState.current = 0;
  renderPreviewImage();
  document.getElementById("previewModal").style.display = "flex";
}

function renderPreviewImage() {
  var content = document.getElementById("previewContent");
  if (previewState.images.length > 0) {
    content.innerHTML = `
      <img src="${previewState.images[previewState.current]}" alt="Report Image" style="max-width:100%;margin-bottom:10px;border-radius:8px;">
      <div style="margin-top:8px;display:flex;justify-content:center;gap:16px;">
        <button class="btn" onclick="prevPreviewImg()" ${previewState.current === 0 ? 'disabled' : ''}>Prev</button>
        <span style="font-size:15px;">${previewState.current+1} / ${previewState.images.length}</span>
        <button class="btn" onclick="nextPreviewImg()" ${previewState.current === previewState.images.length-1 ? 'disabled' : ''}>Next</button>
      </div>
    `;
  } else {
    content.innerHTML = "<p>No images available</p>";
  }
}

function prevPreviewImg() {
  if (previewState.current > 0) {
    previewState.current--;
    renderPreviewImage();
  }
}
function nextPreviewImg() {
  if (previewState.current < previewState.images.length-1) {
    previewState.current++;
    renderPreviewImage();
  }
}

    function closePreview() { document.getElementById("previewModal").style.display = "none"; }

    // File preview before save
    document.getElementById("recordFile").addEventListener("change", function() {
      const preview = document.getElementById("filePreview");
      preview.innerHTML = "";
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = e => {
          preview.innerHTML = `<img src="${e.target.result}" alt="Preview" style="max-width:100%;margin-top:10px;border-radius:8px;">`;
        };
        reader.readAsDataURL(file);
      }
    });
  </script>
</body>
</html>
