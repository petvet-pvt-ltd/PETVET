<?php
$currentPage = basename($_SERVER['PHP_SELF']);

// Set user role for shared components
$userRole = 'clinic-manager';

// Include the shared appointments model
require_once __DIR__ . '/../../models/SharedAppointmentsModel.php';

// Initialize the model
$appointmentsModel = new SharedAppointmentsModel();

// Get filter parameters
$selectedVet = $_GET['vet'] ?? 'all';
$view = $_GET['view'] ?? 'today';

// Get data from model
$appointments = $appointmentsModel->getAppointments($selectedVet);
$vetNames = $appointmentsModel->getVetNames();
$weekDays = $appointmentsModel->getWeekDates();
$monthDays = $appointmentsModel->getMonthDates();
$moduleName = $appointmentsModel->getModuleName($userRole);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Appointments | Clinic Manager</title>
  <link rel="stylesheet" href="/PETVET/public/css/clinic-manager/enhanced-global.css">
  <link rel="stylesheet" href="/PETVET/public/css/clinic-manager/appointments.css">
  <link rel="stylesheet" href="/PETVET/public/css/shared/appointments.css">
  <link rel="stylesheet" href="/PETVET/public/css/receptionist/appointments-receptionist.css">
</head>
<body>

<div class="main-content">
  <div class="page-header">
    <div>
      <h1 class="page-title">Appointments</h1>
      <p class="page-subtitle">Manage and schedule patient appointments</p>
    </div>
    <div>
      <button class="btn btn-primary" onclick="openAddModal()">
        ➕ New Appointment
      </button>
    </div>
  </div>

  <?php
    // Include complete shared appointments component (includes addModal)
    include __DIR__ . '/../shared/appointments/appointments-complete.php';
  ?>
</div>

<script src="/PETVET/public/js/shared/appointments.js"></script>
<script src="/PETVET/public/js/receptionist/receptionist-booking.js"></script>
<script src="/PETVET/public/js/receptionist/appointments-receptionist.js"></script>
</body>
</html>
