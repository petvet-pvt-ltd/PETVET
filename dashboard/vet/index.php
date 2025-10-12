<?php
session_start();
if(!isset($_SESSION['user_name'])){
    $_SESSION['user_name']='Dr. Smith';
    $_SESSION['user_id']=1;
    $_SESSION['user_role']='vet';
}
$currentPage = basename($_SERVER['PHP_SELF']);
include '../sidebar.php'; 

$data = [
  'appointments' => [
    ['id'=>'A001','date'=>'2025-10-12','time'=>'09:00','petName'=>'Bella','ownerName'=>'John Perera','reason'=>'Vaccination','status'=>'ongoing','notes'=>'Bring card'],
    ['id'=>'A002','date'=>'2025-10-12','time'=>'10:00','petName'=>'Max','ownerName'=>'Nimali Silva','reason'=>'Check-up','status'=>'scheduled','notes'=>''],
    ['id'=>'A003','date'=>'2025-10-12','time'=>'11:00','petName'=>'Charlie','ownerName'=>'Kevin','reason'=>'Dental','status'=>'scheduled','notes'=>''],
    ['id'=>'A004','date'=>'2025-10-13','time'=>'09:30','petName'=>'Luna','ownerName'=>'Saman','reason'=>'Skin issue','status'=>'scheduled','notes'=>''],
    ['id'=>'A005','date'=>'2025-09-30','time'=>'14:00','petName'=>'Rocky','ownerName'=>'Anna','reason'=>'Follow-up','status'=>'completed','notes'=>''],
    ['id'=>'A006','date'=>'2025-09-29','time'=>'15:00','petName'=>'Milo','ownerName'=>'Ravi','reason'=>'Vaccination','status'=>'cancelled','notes'=>''],
    ['id'=>'A007','date'=>'2025-10-12','time'=>'12:00','petName'=>'Oscar','ownerName'=>'Naveen','reason'=>'Check-up','status'=>'scheduled','notes'=>''],
    ['id'=>'A008','date'=>'2025-10-12','time'=>'13:30','petName'=>'Daisy','ownerName'=>'Leena','reason'=>'Dental','status'=>'scheduled','notes'=>''],
    ['id'=>'A009','date'=>'2025-10-12','time'=>'14:30','petName'=>'Muffin','ownerName'=>'Suresh','reason'=>'Vaccination','status'=>'scheduled','notes'=>''],
    ['id'=>'A010','date'=>'2025-10-12','time'=>'15:00','petName'=>'Lily','ownerName'=>'Kamal','reason'=>'Check-up','status'=>'scheduled','notes'=>'']
  ],
  'medicalRecords' => [
    ['id'=>'M001','appointmentId'=>'A005','petName'=>'Rocky','ownerName'=>'Anna','date'=>'2025-09-30','symptoms'=>'Itchy skin','diagnosis'=>'Dermatitis','treatment'=>'Topical cream'],
    ['id'=>'M002','appointmentId'=>'A003','petName'=>'Charlie','ownerName'=>'Kevin','date'=>'2025-10-12','symptoms'=>'Tooth pain','diagnosis'=>'Cavities','treatment'=>'Cleaning']
  ],
  'prescriptions' => [
    ['id'=>'P001','appointmentId'=>'A005','petName'=>'Rocky','ownerName'=>'Anna','date'=>'2025-09-30','medication'=>'Antihistamine','dosage'=>'5ml','notes'=>'Twice a day'],
    ['id'=>'P002','appointmentId'=>'A001','petName'=>'Bella','ownerName'=>'John Perera','date'=>'2025-10-12','medication'=>'Deworm','dosage'=>'1 tab','notes'=>'Today']
  ],
  'vaccinations' => [
    ['id'=>'V001','appointmentId'=>'A001','petName'=>'Bella','ownerName'=>'John Perera','date'=>'2025-10-12','vaccine'=>'Rabies','nextDue'=>'2026-10-12'],
    ['id'=>'V002','appointmentId'=>'A006','petName'=>'Milo','ownerName'=>'Ravi','date'=>'2025-09-29','vaccine'=>'Distemper','nextDue'=>'2026-09-29']
  ]
];

// server-side: expose data to JS for initialization
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PetVet — Overview</title>
  <link rel="stylesheet" href="../../styles/dashboard/vet/dashboard.css">
</head>
<body>
  <div class="main-content">
    <header class="dashboard-header">
      <h2>Welcome, Dr. <?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>
      <div class="date"><?php echo date("l, F j, Y", strtotime('2025-10-12')); ?></div>
    </header>

    <div class="cards">
      <div class="card">
        <h3 id="kpi-today">—</h3>
        <p>Appointments Today</p>
      </div>
      <div class="card">
        <h3 id="kpi-total">—</h3>
        <p>Total Appointments</p>
      </div>
    </div>

    <section id="ongoing-section">
      <h3>Ongoing Appointment</h3>
      <div id="ongoing-container"><!-- filled by JS --></div>
    </section>

    <section>
      <h3>Today's Upcoming Appointments</h3>
      <input id="searchBar" placeholder="Search by pet, owner, reason...">
      <div style="overflow:auto">
        <table id="upcomingTable"><thead><tr><th>ID</th><th>Time</th><th>Pet</th><th>Owner</th><th>Reason</th><th>Action</th></tr></thead>
        <tbody></tbody></table>
      </div>
    </section>
  </div>

  <script>window.PETVET_INITIAL_DATA = <?php echo json_encode($data, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>; window.PETVET_TODAY='2025-10-12';</script>
  <script src="../../scripts/dashboard/vet/dashboard.js"></script>
</body>
</html>
