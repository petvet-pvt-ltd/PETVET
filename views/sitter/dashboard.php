<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'sitter';
$GLOBALS['currentPage'] = 'dashboard.php';
$GLOBALS['module'] = 'sitter';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sitter Overview - PetVet</title>
<link rel="stylesheet" href="/PETVET/public/css/sitter/dashboard.css">
</head>
<body>
<?php include __DIR__ . '/../shared/sidebar/sidebar.php'; ?>
<main class="main-content">
<?php include __DIR__ . '/../shared/components/user-welcome-header.php'; ?>
<div class="dashboard-header">
<h1>Welcome, Pet Sitter!</h1>
<p>Manage your bookings</p>
</div>
<div class="stats-grid">
<div class="stat-card">
<div class="stat-number"><?php echo $stats['active_bookings']; ?></div>
<div class="stat-label">Active Bookings</div>
</div>
<div class="stat-card">
<div class="stat-number"><?php echo $stats['total_pets_cared']; ?></div>
<div class="stat-label">Total Pets Cared</div>
</div>
<div class="stat-card">
<div class="stat-number"><?php echo $stats['completed_bookings']; ?></div>
<div class="stat-label">Completed</div>
</div>
<div class="stat-card">
<div class="stat-number"><?php echo $stats['pending_requests']; ?></div>
<div class="stat-label">Pending Requests</div>
</div>
</div>

<div class="upcoming-section">
<h2 class="section-title">Upcoming Bookings</h2>
<div class="bookings-list">
<?php if (!empty($upcomingBookings)): ?>
<?php foreach ($upcomingBookings as $booking): ?>
<div class="booking-item">
<div class="booking-time"><?php echo htmlspecialchars($booking['time']); ?></div>
<div class="booking-details">
<div class="booking-customer"><?php echo htmlspecialchars($booking['customer_name']); ?></div>
<div class="booking-category"><?php echo htmlspecialchars($booking['category']); ?></div>
</div>
</div>
<?php endforeach; ?>
<?php else: ?>
<p class="no-bookings">No upcoming bookings scheduled.</p>
<?php endif; ?>
</div>
</div>
</main>
</body>
</html>
