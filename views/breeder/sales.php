<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'breeder';
$GLOBALS['currentPage'] = 'sales.php';
$GLOBALS['module'] = 'breeder';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sales - PetVet</title>
<link rel="stylesheet" href="/PETVET/public/css/breeder/dashboard.css">
</head>
<body>
<main class="main-content">
<div class="dashboard-header">
<h1>Sales Management</h1>
<p>Track your pet sales and revenue</p>
</div>
<div class="content-section">
<h2>Recent Sales</h2>
<?php if (!empty($sales)): ?>
<?php foreach ($sales as $sale): ?>
<div class="sale-item">
<div class="sale-pet"><?php echo htmlspecialchars($sale['pet_name']); ?> - <?php echo htmlspecialchars($sale['breed']); ?></div>
<div class="sale-buyer">Buyer: <?php echo htmlspecialchars($sale['buyer_name']); ?></div>
<div class="sale-amount">$<?php echo number_format($sale['amount']); ?> • <?php echo date('M d, Y', strtotime($sale['sale_date'])); ?></div>
</div>
<?php endforeach; ?>
<?php else: ?>
<p>No sales recorded</p>
<?php endif; ?>
</div>
</main>
</body>
</html>
