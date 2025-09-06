<?php
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'vet';
$_SESSION['user_name'] = 'Nethmi';

$currentPage = basename($_SERVER['PHP_SELF']);
include '../sidebar.php';

// === Appointments Data as array of associative arrays ===
$appointments = [
    [
        "id" => 1,
        "time" => "10:30 AM",
        "pet" => "Charlie",
        "owner" => "Sarah Johnson",
        "reason" => "Annual Checkup"
    ],
    [
        "id" => 2,
        "time" => "11:00 AM",
        "pet" => "Milo",
        "owner" => "John Doe",
        "reason" => "Vaccination"
    ],
    [
        "id" => 3,
        "time" => "12:00 PM",
        "pet" => "Lucy",
        "owner" => "Emma Watson",
        "reason" => "Dental Check"
    ],
    [
        "id" => 4,
        "time" => "12:30 PM",
        "pet" => "Bella",
        "owner" => "James Brown",
        "reason" => "Dental Check"
    ]
];

// === Separate ongoing and upcoming appointments ===
$ongoing = $appointments[0]; // first appointment is ongoing
$todayAppointments = array_slice($appointments, 1); // rest are upcoming

// === Stats as array ===
$stats = [
    "today" => count($appointments),
    "week"  => count($appointments) // for example
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vet Dashboard</title>
    <link rel="stylesheet" href="../../styles/dashboard/vet/dashboard.css">
</head>
<body>
<div class="main-content">

    <!-- Header -->
    <header class="dashboard-header">
        <div class="header-left">
            <h2>Welcome, Dr. <?php echo $_SESSION['user_name']; ?></h2>
            <p>Manage appointments, medical records, and prescriptions — all in one place.</p>
        </div>
        <div class="header-right">
            <span class="date"><?php echo date("l, F j, Y"); ?></span>
        </div>
    </header>
    <br/>

    <!-- Dashboard Cards -->
    <div class="cards">
        <?php foreach ($stats as $label => $count): ?>
            <div class="card <?php echo $label == 'today' ? 'navy' : 'red'; ?>">
                <h3><?php echo $count; ?></h3>
                <p><?php echo $label == 'today' ? "Appointments Today" : "Appointments This Week"; ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Ongoing Appointment -->
    <div class="appointment ongoing">
    <h3>Ongoing Appointment</h3>
    <br/>
    <p><b>ID:</b> <?php echo $ongoing['id']; ?></p>
    <p><b>Time:</b> <?php echo $ongoing['time']; ?></p>
    <p><b>Pet:</b> <?php echo $ongoing['pet']; ?></p>
    <p><b>Owner:</b> <?php echo $ongoing['owner']; ?></p>
    <p><b>Reason:</b> <?php echo $ongoing['reason']; ?></p>

    <div class="actions">
        <button class="btn navy" onclick="window.location.href='medical-records.php?appointment_id=<?php echo $ongoing['id']; ?>&pet=<?php echo urlencode($ongoing['pet']); ?>'">Record</button>
        <button class="btn navy" onclick="window.location.href='prescriptions.php?appointment_id=<?php echo $ongoing['id']; ?>&pet=<?php echo urlencode($ongoing['pet']); ?>'">Prescription</button>
        <button id="completeBtn" class="btn navy">Complete</button>
        <button id="cancelBtn" class="btn red">Cancel</button>
    </div>
</div>


    <!-- Today's Appointments List -->
    <div class="appointment-list">
        <div class="header-row">
            <h3>Today's Upcoming Appointments</h3>
            <input type="text" id="searchBar" placeholder="Search by pet, owner, or reason...">
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Time</th>
                    <th>Pet</th>
                    <th>Owner</th>
                    <th>Reason</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="appointmentsTable">
    <?php foreach ($todayAppointments as $index => $appt): ?>
        <tr data-index="<?php echo $index; ?>">
            <td><?php echo $appt['id']; ?></td>
            <td><?php echo $appt['time']; ?></td>
            <td><?php echo $appt['pet']; ?></td>
            <td><?php echo $appt['owner']; ?></td>
            <td><?php echo $appt['reason']; ?></td>
            <td>
                <button class="btn navy" onclick="window.location.href='medical-records.php?appointment_id=<?php echo $appt['id']; ?>&pet=<?php echo urlencode($appt['pet']); ?>'">Record</button>
                <button class="btn navy" onclick="window.location.href='prescriptions.php?appointment_id=<?php echo $appt['id']; ?>&pet=<?php echo urlencode($appt['pet']); ?>'">Prescription</button>
                <button class="btn red cancel-btn" data-index="<?php echo $index; ?>">Cancel</button>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
        </table>
    </div>

</div>
<script src="../../scripts/dashboard/vet/dashboard.js"></script>
</body>
</html>
