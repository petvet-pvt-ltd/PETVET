<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'breeder';
$GLOBALS['currentPage'] = 'pets.php';
$GLOBALS['module'] = 'breeder';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Pets - PetVet</title>
<link rel="stylesheet" href="/PETVET/public/css/breeder/dashboard.css">
</head>
<body>
<main class="main-content">
<div class="dashboard-header">
<h1>My Pets</h1>
<p>Manage your breeding stock</p>
</div>
<div class="content-section">
<h2>Available Pets</h2>
<?php if (!empty($pets)): ?>
<?php foreach ($pets as $pet): ?>
<div class="pet-card">
<div class="pet-info">
<div class="pet-name"><?php echo htmlspecialchars($pet['name']); ?></div>
<div class="pet-breed"><?php echo htmlspecialchars($pet['breed']); ?></div>
<div class="pet-details"><?php echo htmlspecialchars($pet['age']); ?> • <?php echo htmlspecialchars($pet['gender']); ?> • <?php echo htmlspecialchars($pet['health_status']); ?></div>
</div>
<div class="pet-price">$<?php echo number_format($pet['price']); ?></div>
</div>
<?php endforeach; ?>
<?php else: ?>
<p>No pets available</p>
<?php endif; ?>
</div>
</main>
</body>
</html>
