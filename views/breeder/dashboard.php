<?php
// This view expects data from BreederController::dashboard()
// Variables available: $stats, $upcomingBreedingDates

if (!isset($stats)) $stats = ['pending_requests' => 0, 'approved_requests' => 0, 'total_breedings' => 0, 'active_pets' => 0];
if (!isset($upcomingBreedingDates)) $upcomingBreedingDates = [];

$module = 'breeder';
$currentPage = 'dashboard.php';
$GLOBALS['module'] = $module;
$GLOBALS['currentPage'] = $currentPage;
require_once dirname(__DIR__, 2) . '/config/config.php';
$pageTitle = "Dashboard";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - PetVet Breeder</title>
    <link rel="stylesheet" href="<?= asset('css/breeder/dashboard.css') ?>">
</head>
<body>
    <?php include __DIR__ . '/../shared/sidebar/sidebar.php'; ?>

    <main class="main-content">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <h1>Welcome, Pet Breeder!</h1>
            <p>Manage your breeding requests and pets</p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['pending_requests']; ?></div>
                <div class="stat-label">Pending Requests</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['approved_requests']; ?></div>
                <div class="stat-label">Approved Requests</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_breedings']; ?></div>
                <div class="stat-label">Total Breedings</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['active_pets']; ?></div>
                <div class="stat-label">Active Breeding Pets</div>
            </div>
        </div>

        <!-- Upcoming Breeding Dates -->
        <div class="upcoming-section">
            <h2 class="section-title">Upcoming Breeding Dates</h2>
            <div class="bookings-list">
                <?php if (!empty($upcomingBreedingDates)): ?>
                    <?php foreach (array_slice($upcomingBreedingDates, 0, 5) as $breeding): ?>
                    <div class="booking-item">
                        <div class="booking-time"><?php echo date('M d, Y', strtotime($breeding['breeding_date'])); ?></div>
                        <div class="booking-details">
                            <div class="booking-customer"><?php echo htmlspecialchars($breeding['breeder_pet_name']) . ' × ' . htmlspecialchars($breeding['customer_pet_name']); ?></div>
                            <div class="booking-category"><?php echo htmlspecialchars($breeding['breed']) . ' • ' . htmlspecialchars($breeding['owner_name']); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-bookings">No upcoming breeding dates scheduled.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="<?= asset('js/breeder/dashboard.js') ?>"></script>
</body>
</html>
