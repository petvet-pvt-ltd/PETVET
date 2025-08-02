<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vets</title>
    <style>
        .main-content {
            margin-left: 240px; /* same as sidebar width */
            padding: 20px;
        }
    </style>
</head>
<body>
    <?php require_once '../sidebar.php' ?>
    <div class="main-content">
        <h1>Welcome Manager</h1>
        <h2>Manage your vets</h2>
        <p><?php echo $currentPage; ?></p>

    </div>
</body>
</html>