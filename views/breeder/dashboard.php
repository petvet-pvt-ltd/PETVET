<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'breeder';
$GLOBALS['currentPage'] = 'dashboard.php';
$GLOBALS['module'] = 'breeder';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Breeder Dashboard - PetVet</title>
<link rel="stylesheet" href="/PETVET/public/css/breeder/dashboard.css">
</head>
<body>
<?php include __DIR__ . '/../shared/sidebar/sidebar.php'; ?>
<main class="main-content">
<div class="dashboard-header">
<h1>Welcome, Breeder!</h1>
<p>Manage your breeding business</p>
</div>
<div class="stats-grid">
<div class="stat-card">
<div class="stat-number"><?php echo $stats['total_pets']; ?></div>
<div class="stat-label">Total Pets</div>
</div>
<div class="stat-card">
<div class="stat-number"><?php echo $stats['available_pets']; ?></div>
<div class="stat-label">Available</div>
</div>
<div class="stat-card">
<div class="stat-number"><?php echo $stats['sold_this_month']; ?></div>
<div class="stat-label">Sold This Month</div>
</div>
<div class="stat-card">
<div class="stat-number">$<?php echo number_format($stats['monthly_revenue']); ?></div>
<div class="stat-label">Monthly Revenue</div>
</div>
</div>
</main>
</body>
</html>
