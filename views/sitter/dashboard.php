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
</main>
</body>
</html>
