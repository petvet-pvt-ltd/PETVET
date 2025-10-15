<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'sitter';
$GLOBALS['currentPage'] = 'pets.php';
$GLOBALS['module'] = 'sitter';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pets - PetVet</title>
<link rel="stylesheet" href="/PETVET/public/css/sitter/dashboard.css">
</head>
<body>
<main class="main-content">
<div class="dashboard-header">
<h1>Current Pets</h1>
<p>Pets in your care</p>
</div>
<div class="content-section">
<h2>Pets Under Care</h2>
<?php if (!empty($currentPets)): ?>
<?php foreach ($currentPets as $pet): ?>
<div class="pet-item">
<div class="pet-name"><?php echo htmlspecialchars($pet['name']); ?> - <?php echo htmlspecialchars($pet['type']); ?></div>
<div class="pet-breed"><?php echo htmlspecialchars($pet['breed']); ?> • <?php echo $pet['age']; ?> years</div>
<div class="pet-owner">Owner: <?php echo htmlspecialchars($pet['owner']); ?></div>
<div class="pet-dates">Check-in: <?php echo date('M d', strtotime($pet['check_in'])); ?> | Check-out: <?php echo date('M d, Y', strtotime($pet['check_out'])); ?></div>
</div>
<?php endforeach; ?>
<?php else: ?>
<p>No pets currently in care</p>
<?php endif; ?>
</div>
</main>
</body>
</html>
