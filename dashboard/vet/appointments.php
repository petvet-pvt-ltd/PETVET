<?php
session_start();
if(!isset($_SESSION['user_name'])){ $_SESSION['user_name']='Dr. Smith'; $_SESSION['user_id']=1; }
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
    ['id'=>'M001','appointmentId'=>'A005','petName'=>'Rocky','ownerName'=>'Anna','date'=>'2025-09-30','symptoms'=>'Itchy skin','diagnosis'=>'Dermatitis','treatment'=>'Topical cream']
  ],
  'prescriptions' => [
    ['id'=>'P001','appointmentId'=>'A005','petName'=>'Rocky','ownerName'=>'Anna','date'=>'2025-09-30','medication'=>'Antihistamine','dosage'=>'5ml','notes'=>'Twice a day']
  ],
  'vaccinations' => [
    ['id'=>'V001','appointmentId'=>'A001','petName'=>'Bella','ownerName'=>'John Perera','date'=>'2025-10-12','vaccine'=>'Rabies','nextDue'=>'2026-10-12']
  ]
];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Appointments</title>
  <link rel="stylesheet" href="../../styles/dashboard/vet/dashboard.css">
</head>
<body>
  <div class="main-content">
    <header class="dashboard-header">
      <h2>Appointments</h2>
    </header>

    <section>
      <h3>Ongoing Appointments</h3>
      <input id="searchBar" data-target="ongoingTableContainer" placeholder="Search ...">
      <div id="ongoingTableContainer"></div>
    </section>

    <section>
      <h3>Upcoming Appointments</h3>
      <input id="searchBar" data-target="upcomingTableContainer" placeholder="Search ...">
      <div id="upcomingTableContainer"></div>
    </section>

    <section>
      <h3>Completed Appointments</h3>
      <input id="searchBar" data-target="completedTableContainer" placeholder="Search ...">
      <div id="completedTableContainer"></div>
    </section>

    <section>
      <h3>Cancelled Appointments</h3>
      <input id="searchBar" data-target="cancelledTableContainer" placeholder="Search ...">
      <div id="cancelledTableContainer"></div>
    </section>
  </div>

  <script>
    window.PETVET_INITIAL_DATA = <?php echo json_encode($data); ?>;
    window.PETVET_TODAY='2025-10-12';
  </script>
  <script src="../../scripts/dashboard/vet/appointments.js"></script>
</body>
</html>
