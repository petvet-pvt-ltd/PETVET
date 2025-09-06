<?php
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'vet';
$_SESSION['user_name'] = 'Nethmi';

$currentPage = basename($_SERVER['PHP_SELF']);
include '../sidebar.php';

// === Appointments Data ===
$appointments = [
    ["id"=>1,"date"=>"2025-09-01","time"=>"10:30 AM","pet"=>"Charlie","owner"=>"Sarah Johnson","reason"=>"Annual Checkup","status"=>"scheduled","prescription"=>"","record"=>""],
    ["id"=>2,"date"=>"2025-09-01","time"=>"11:00 AM","pet"=>"Milo","owner"=>"John Doe","reason"=>"Vaccination","status"=>"completed","prescription"=>"Vaccine given","record"=>"All good"],
    ["id"=>3,"date"=>"2025-09-02","time"=>"12:00 PM","pet"=>"Lucy","owner"=>"Emma Watson","reason"=>"Dental Check","status"=>"cancelled","prescription"=>"","record"=>""],
    ["id"=>4,"date"=>"2025-09-02","time"=>"12:30 PM","pet"=>"Bella","owner"=>"James Brown","reason"=>"Follow-up","status"=>"ongoing","prescription"=>"","record"=>""]
];

// === Split appointments by status using arrays for JS use ===
$appointmentsArray = [
    "upcoming" => array_values(array_filter($appointments, fn($a) => in_array($a['status'], ['scheduled', 'ongoing']))),
    "completed" => array_values(array_filter($appointments, fn($a) => in_array($a['status'], ['completed', 'cancelled'])))
];

// === KPI Stats as array ===
$stats = [
    "upcoming" => count($appointmentsArray['upcoming']),
    "completed" => count($appointmentsArray['completed']),
    "cancelled" => count(array_filter($appointments, fn($a) => $a['status'] === 'cancelled'))
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Appointments Dashboard</title>
<link rel="stylesheet" href="../../styles/dashboard/vet/appointments.css">
</head>
<body>
<div class="main-content">

    <!-- Header -->
    <header class="dashboard-header">
        <div class="header-left">
            <h2>Appointments</h2>
            <p>View, filter, and manage all appointments.</p>
        </div>
        <div class="header-right">
            <span class="date"><?php echo date("l, F j, Y"); ?></span>
        </div>
    </header>
    <br/>

    <!-- KPI Cards -->
<div class="cards">
    <?php 
    // Define colors for each KPI
    $colors = [
        "upcoming" => "navy",
        "completed" => "green",
        "cancelled" => "red"
    ];
    foreach ($stats as $label => $count): ?>
        <div class="card <?php echo $colors[$label]; ?>">
            <h3><?php echo $count; ?></h3>
            <p><?php echo ucfirst($label); ?></p>
        </div>
    <?php endforeach; ?>
</div>


    <!-- Upcoming Appointments Table -->
    <div class="appointment-list">
        <h3>Upcoming Appointments</h3>
        <div class="filters" style="display:flex; gap:10px; align-items:center; margin-bottom:10px;">
            <input type="date" id="upcomingDateFilter">
            <select id="upcomingStatusFilter">
                <option value="">All Status</option>
                <option value="scheduled">Scheduled</option>
                <option value="ongoing">Ongoing</option>
            </select>
            <button id="applyUpcomingFilter" class="btn navy">Apply Filter</button>
            <input type="text" id="searchUpcoming" placeholder="Search upcoming appointments...">
        </div>
        <br/>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Pet</th>
                    <th>Owner</th>
                    <th>Reason</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="upcomingTable">
                <?php foreach ($appointmentsArray['upcoming'] as $appt): ?>
                    <tr data-id="<?php echo $appt['id']; ?>" data-status="<?php echo $appt['status']; ?>" data-date="<?php echo $appt['date']; ?>">
                        <td><?php echo $appt['id']; ?></td>
                        <td><?php echo $appt['date']; ?></td>
                        <td><?php echo $appt['time']; ?></td>
                        <td><?php echo $appt['pet']; ?></td>
                        <td><?php echo $appt['owner']; ?></td>
                        <td><?php echo $appt['reason']; ?></td>
                        <td><?php echo ucfirst($appt['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Completed Appointments Table -->
    <div class="appointment-list" style="margin-top:40px;">
        <h3>Completed Appointments</h3>
        <div class="filters" style="display:flex; gap:10px; align-items:center; margin-bottom:10px;">
            <input type="date" id="completedDateFilter">
            <select id="completedStatusFilter">
                <option value="">All Status</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <button id="applyCompletedFilter" class="btn navy">Apply Filter</button>
            <input type="text" id="searchCompleted" placeholder="Search completed appointments...">
        </div>
        <br/>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Pet</th>
                    <th>Owner</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Record</th>
                    <th>Prescription</th>
                </tr>
            </thead>
<tbody id="completedTable">
<?php foreach ($appointmentsArray['completed'] as $appt): ?>
    <tr data-id="<?php echo $appt['id']; ?>" data-status="<?php echo $appt['status']; ?>" data-date="<?php echo $appt['date']; ?>">
        <td><?php echo $appt['id']; ?></td>
        <td><?php echo $appt['date']; ?></td>
        <td><?php echo $appt['time']; ?></td>
        <td><?php echo $appt['pet']; ?></td>
        <td><?php echo $appt['owner']; ?></td>
        <td><?php echo $appt['reason']; ?></td>
        <td><?php echo ucfirst($appt['status']); ?></td>

        <!-- ✅ Record Add/Edit button -->
        <td>
            <?php if($appt['status'] === 'completed'): ?>
                <a href="medical-records.php?appointment_id=<?php echo $appt['id']; ?>&action=<?php echo $appt['record'] ? 'edit' : 'add'; ?>" 
                   class="btn navy">
                   <?php echo $appt['record'] ? "Edit" : "Add"; ?>
                </a>
            <?php endif; ?>
        </td>

        <!-- ✅ Prescription Add/Edit button -->
        <td>
            <?php if($appt['status'] === 'completed'): ?>
                <a href="prescriptions.php?appointment_id=<?php echo $appt['id']; ?>&action=<?php echo $appt['prescription'] ? 'edit' : 'add'; ?>" 
                   class="btn navy">
                   <?php echo $appt['prescription'] ? "Edit" : "Add"; ?>
                </a>
            <?php endif; ?>
        </td>
    </tr>
<?php endforeach; ?>
</tbody>
        </table>
    </div>

</div>
<script src="../../scripts/dashboard/vet/appointments.js"></script>
</body>
</html>
