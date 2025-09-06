<?php
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'vet';
$_SESSION['user_name'] = 'Nethmi';

$currentPage = basename($_SERVER['PHP_SELF']);
include '../sidebar.php';

// ================= Dummy Vaccination Records =================
if (!isset($_SESSION['vaccinations'])) {
    $_SESSION['vaccinations'] = [
        ["pet"=>"Buddy","vaccine"=>"Rabies","date_given"=>"2025-08-01","next_due"=>"2026-08-01"],
        ["pet"=>"Charlie","vaccine"=>"Distemper","date_given"=>"2025-07-15","next_due"=>"2026-07-15"]
    ];
}

$vaccinations = &$_SESSION['vaccinations'];

// ================= Handle Form Submission =================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $record = [
        "pet" => $_POST['pet'] ?? "",
        "vaccine" => $_POST['vaccine'] ?? "",
        "date_given" => $_POST['date_given'] ?? "",
        "next_due" => $_POST['next_due'] ?? ""
    ];

    // Check if updating (based on hidden index)
    if (isset($_POST['index']) && $_POST['index'] !== "") {
        $index = $_POST['index'];
        $vaccinations[$index] = $record;
    } else {
        $vaccinations[] = $record;
    }

    header("Location: vaccinations.php");
    exit;
}

// ================= Handle Edit Request =================
$editRecord = null;
$editIndex = null;
if (isset($_GET['action'], $_GET['index']) && $_GET['action'] === 'edit') {
    $editIndex = $_GET['index'];
    $editRecord = $vaccinations[$editIndex];
}

// ================= Handle Delete Request =================
if (isset($_GET['action'], $_GET['index']) && $_GET['action'] === 'delete') {
    $index = $_GET['index'];
    unset($vaccinations[$index]);
    $_SESSION['vaccinations'] = array_values($vaccinations); // reindex array
    header("Location: vaccinations.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Vaccinations</title>
<link rel="stylesheet" href="../../styles/dashboard/vet/vaccinations.css">
</head>
<body>
<div class="main-content">

    <!-- Header -->
    <header class="dashboard-header">
        <div class="header-left">
            <h2>Vaccinations</h2>
            <p>Manage vaccination records of pets.</p>
        </div>
        <div class="header-right">
            <span class="date"><?php echo date("l, F j, Y"); ?></span>
        </div>
    </header>
    <br/>

    <!-- ===== Form Section ===== -->
    <div class="appointment-list" style="margin-bottom:40px;">
        <h3><?php echo $editRecord ? "Edit Vaccination" : "Add Vaccination"; ?></h3>
        <form method="POST">
            <?php if ($editRecord !== null): ?>
                <input type="hidden" name="index" value="<?php echo $editIndex; ?>">
            <?php endif; ?>

            <label>Pet</label>
            <input type="text" name="pet" value="<?php echo $editRecord['pet'] ?? ''; ?>" required>

            <label>Vaccine Name</label>
            <input type="text" name="vaccine" value="<?php echo $editRecord['vaccine'] ?? ''; ?>" required>

            <label>Date Given</label>
            <input type="date" name="date_given" value="<?php echo $editRecord['date_given'] ?? ''; ?>" required>

            <label>Next Due</label>
            <input type="date" name="next_due" value="<?php echo $editRecord['next_due'] ?? ''; ?>">

            <br/>
            <button type="submit" class="btn navy"><?php echo $editRecord ? "Update Record" : "Save Record"; ?></button>
        </form>
    </div>

    <!-- ===== Records Table ===== -->
    <div class="appointment-list">
        <h3>Vaccination Records</h3>
        <input type="text" id="searchBar" placeholder="Search by Pet, Vaccine, Date">
        <table id="vaccinationTable">
            <thead>
                <tr>
                    <th>Pet</th>
                    <th>Vaccine</th>
                    <th>Date Given</th>
                    <th>Next Due</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vaccinations as $i => $v): ?>
                <tr>
                    <td><?php echo $v['pet']; ?></td>
                    <td><?php echo $v['vaccine']; ?></td>
                    <td><?php echo $v['date_given']; ?></td>
                    <td><?php echo $v['next_due']; ?></td>
                    <td class="completed-buttons">
                        <form method="GET" style="display:inline;">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="index" value="<?php echo $i; ?>">
                            <button type="submit" class="btn navy">Edit</button>
                        </form>
                        <form method="GET" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="index" value="<?php echo $i; ?>">
                            <button type="submit" class="btn red">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
<script src="../../scripts/vaccinations.js"></script>
</body>
</html>
