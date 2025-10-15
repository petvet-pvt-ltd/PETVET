<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'trainer';
$GLOBALS['currentPage'] = 'appointments.php';
$GLOBALS['module'] = 'trainer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Appointments - PetVet</title>
<link rel="stylesheet" href="/PETVET/public/css/trainer/dashboard.css">
</head>
<body>
<main class="main-content">
<div class="dashboard-header">
<h1>Training Appointments</h1>
<p>Manage your training sessions and schedules</p>
</div>
<div class="content-section">
<h2>Upcoming Appointments</h2>
<?php if (!empty($appointments)): ?>
<?php foreach ($appointments as $apt): ?>
<div class="session-item">
<div class="session-time"><?php echo htmlspecialchars($apt['time']); ?> - <?php echo htmlspecialchars($apt['date']); ?></div>
<div class="session-pet"><?php echo htmlspecialchars($apt['pet_name']); ?> - <?php echo htmlspecialchars($apt['session_type']); ?></div>
<div class="session-type">Client: <?php echo htmlspecialchars($apt['client_name']); ?></div>
</div>
<?php endforeach; ?>
<?php else: ?>
<p>No upcoming appointments</p>
<?php endif; ?>
</div>
</main>
</body>
</html>
