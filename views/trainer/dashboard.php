<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'trainer';
$GLOBALS['currentPage'] = 'dashboard.php';
$GLOBALS['module'] = 'trainer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Trainer Overview - PetVet</title>
<link rel="stylesheet" href="/PETVET/public/css/trainer/dashboard.css">
</head>
<body>
<?php include __DIR__ . '/../shared/sidebar/sidebar.php'; ?>
<main class="main-content">
<div class="dashboard-header">
<h1>Welcome, Pet Trainer!</h1>
<p>Manage your training sessions</p>
</div>
<div class="stats-grid">
<div class="stat-card">
<div class="stat-number"><?php echo $stats['active_sessions']; ?></div>
<div class="stat-label">Active Sessions</div>
</div>
<div class="stat-card">
<div class="stat-number"><?php echo $stats['total_pets_trained']; ?></div>
<div class="stat-label">Total Pets Trained</div>
</div>
<div class="stat-card">
<div class="stat-number"><?php echo $stats['completed_sessions']; ?></div>
<div class="stat-label">Completed</div>
</div>
<div class="stat-card">
<div class="stat-number"><?php echo $stats['pending_requests']; ?></div>
<div class="stat-label">Pending Requests</div>
</div>
</div>

<div class="upcoming-section">
<h2 class="section-title">Upcoming Appointments</h2>
<div class="appointments-list">
<?php if (!empty($upcomingAppointments)): ?>
<?php foreach ($upcomingAppointments as $apt): ?>
<div class="appointment-item">
<div class="appointment-time"><?php echo htmlspecialchars($apt['time']); ?></div>
<div class="appointment-details">
<div class="appointment-customer"><?php echo htmlspecialchars($apt['customer_name']); ?></div>
<div class="appointment-location"><?php echo htmlspecialchars($apt['location']); ?></div>
</div>
</div>
<?php endforeach; ?>
<?php else: ?>
<p class="no-appointments">No upcoming appointments scheduled.</p>
<?php endif; ?>
</div>
</div>
</main>
</body>
</html>
