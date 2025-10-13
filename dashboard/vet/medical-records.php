<?php
session_start();
if(!isset($_SESSION['user_name'])){ $_SESSION['user_name']='Dr. Smith'; }
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

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Medical Records</title>
  <link rel="stylesheet" href="../../styles/dashboard/vet/dashboard.css">
</head>
<body>
  <div class="main-content">
    <header class="dashboard-header"><h2>Medical Records</h2></header>

    <!-- Form (visible only when from=ongoing) -->
    <section id="formSection" style="display:none">
      <h3>Add Medical Record</h3>
      <form id="medicalRecordForm">
        <div class="form-row">
          <label>Appointment ID<input name="appointmentId" readonly></label>
          <label>Pet name<input name="petName" readonly></label>
          <label>Owner<input name="ownerName" readonly></label>
        </div>
        <div class="form-row">
          <label>Symptoms<textarea name="symptoms" rows="2" required></textarea></label>
          <label>Diagnosis<textarea name="diagnosis" rows="2" required></textarea></label>
        </div>
        <div class="form-row">
          <label>Treatment<textarea name="treatment" rows="2" required></textarea></label>
        </div>
        <button class="btn navy" type="submit">Save Record</button>
      </form>
    </section>

    <section>
      <h3>Records</h3>
      <input id="searchBar" placeholder="Search records...">
      <div id="recordsContainer"></div>
    </section>
  </div>

  <script>window.PETVET_INITIAL_DATA = <?php echo json_encode( 
  [
  'appointments'=>[
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
  'medicalRecords'=>[
    ['id'=>'M001','appointmentId'=>'A005','petName'=>'Rocky','ownerName'=>'Anna','date'=>'2025-09-30','symptoms'=>'Itchy skin','diagnosis'=>'Dermatitis','treatment'=>'Topical cream'],
    ['id'=>'M002','appointmentId'=>'A003','petName'=>'Charlie','ownerName'=>'Kevin','date'=>'2025-10-12','symptoms'=>'Tooth pain','diagnosis'=>'Cavities','treatment'=>'Cleaning']
  ],
  'prescriptions'=>[
    ['id'=>'P001','appointmentId'=>'A005','petName'=>'Rocky','ownerName'=>'Anna','date'=>'2025-09-30','medication'=>'Antihistamine','dosage'=>'5ml','notes'=>'Twice a day']
  ],
  'vaccinations'=>[
    ['id'=>'V001','appointmentId'=>'A001','petName'=>'Bella','ownerName'=>'John Perera','date'=>'2025-10-12','vaccine'=>'Rabies','nextDue'=>'2026-10-12']
  ]
  ]) ?>;</script>
  <script src="../../scripts/dashboard/vet/medical-records.js"></script>
</body>
</html>
