<?php
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'vet';
$_SESSION['user_name'] = 'Nethmi';

$currentPage = basename($_SERVER['PHP_SELF']);
include '../sidebar.php';

// ================= Dummy Prescription Data =================
if (!isset($_SESSION['prescriptions'])) {
    $_SESSION['prescriptions'] = [
        [
            "appointment_id"=>1,
            "pet"=>"Charlie",
            "date"=>"2025-08-20",
            "medicine"=>"Antibiotic",
            "dosage"=>"2 pills",
            "frequency"=>"Twice a day",
            "duration"=>"5 days",
            "instructions"=>"Give after meals"
        ],
        [
            "appointment_id"=>3,
            "pet"=>"Lucy",
            "date"=>"2025-08-21",
            "medicine"=>"Painkiller",
            "dosage"=>"1 pill",
            "frequency"=>"Once a day",
            "duration"=>"3 days",
            "instructions"=>"Give in the morning"
        ]
    ];
}

$prescriptions = &$_SESSION['prescriptions'];

// ================= Handle Form Submission =================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'] ?? "";
    $record = [
        "appointment_id" => $appointment_id,
        "pet" => $_POST['pet'] ?? "",
        "date" => $_POST['date'] ?? "",
        "medicine" => $_POST['medicine'] ?? "",
        "dosage" => $_POST['dosage'] ?? "",
        "frequency" => $_POST['frequency'] ?? "",
        "duration" => $_POST['duration'] ?? "",
        "instructions" => $_POST['instructions'] ?? ""
    ];

    // Check if updating existing record
    $updated = false;
    foreach ($prescriptions as &$r) {
        if ($r['appointment_id'] == $appointment_id) {
            $r = $record;
            $updated = true;
            break;
        }
    }

    if (!$updated) {
        $prescriptions[] = $record;
    }

    header("Location: prescriptions.php");
    exit;
}

// ================= Handle Edit Request =================
$editRecord = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['appointment_id'])) {
    foreach ($prescriptions as $rec) {
        if ($rec['appointment_id'] == $_GET['appointment_id']) {
            $editRecord = $rec;
            break;
        }
    }
}

// ================= Handle Delete Request =================
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['appointment_id'])) {
    foreach ($prescriptions as $key => $rec) {
        if ($rec['appointment_id'] == $_GET['appointment_id']) {
            unset($prescriptions[$key]);
            $_SESSION['prescriptions'] = array_values($prescriptions); // reindex
            break;
        }
    }
    header("Location: prescriptions.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Prescriptions</title>
<link rel="stylesheet" href="../../styles/dashboard/vet/prescriptions.css">
</head>
<body>
<div class="main-content">

    <!-- Header -->
    <header class="dashboard-header">
        <div class="header-left">
            <h2>Prescriptions</h2>
            <p>Manage and review prescriptions for pets.</p>
        </div>
        <div class="header-right">
            <span class="date"><?php echo date("l, F j, Y"); ?></span>
        </div>
    </header>
    <br/>

    <!-- ===== Form Section ===== -->
    <div class="appointment-list" style="margin-bottom:40px;">
        <h3><?php echo $editRecord ? "Edit Prescription" : "Add Prescription"; ?></h3>
        <form method="POST">
            <!-- Appointment ID Auto-filled -->
            <label>Appointment ID</label>
            <input type="number" name="appointment_id" 
                   value="<?php echo $editRecord['appointment_id'] ?? htmlspecialchars($_GET['appointment_id'] ?? ''); ?>" 
                   readonly required>

            <label>Pet</label>
            <input type="text" name="pet" value="<?php echo $editRecord['pet'] ?? ''; ?>" required>

            <label>Visit Date</label>
            <input type="date" name="date" value="<?php echo $editRecord['date'] ?? ''; ?>" required>

            <label>Medicine Name</label>
            <input type="text" name="medicine" value="<?php echo $editRecord['medicine'] ?? ''; ?>" required>

            <label>Dosage</label>
            <input type="text" name="dosage" value="<?php echo $editRecord['dosage'] ?? ''; ?>">

            <label>Frequency</label>
            <input type="text" name="frequency" value="<?php echo $editRecord['frequency'] ?? ''; ?>">

            <label>Duration</label>
            <input type="text" name="duration" value="<?php echo $editRecord['duration'] ?? ''; ?>">

            <label>Instructions</label>
            <textarea name="instructions"><?php echo $editRecord['instructions'] ?? ''; ?></textarea>

            <br/>
            <button type="submit" class="btn navy"><?php echo $editRecord ? "Update Prescription" : "Save Prescription"; ?></button>
        </form>
    </div>

    <!-- ===== Prescriptions Table ===== -->
    <div class="appointment-list">
        <h3>Recent Prescriptions</h3>
        <table id="recordsTable">
            <thead>
                <tr>
                    <th>Appointment ID</th>
                    <th>Pet</th>
                    <th>Date</th>
                    <th>Medicine</th>
                    <th>Dosage</th>
                    <th>Frequency</th>
                    <th>Duration</th>
                    <th>Instructions</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prescriptions as $rec): ?>
                <tr>
                    <td><?php echo $rec['appointment_id']; ?></td>
                    <td><?php echo $rec['pet']; ?></td>
                    <td><?php echo $rec['date']; ?></td>
                    <td><?php echo $rec['medicine']; ?></td>
                    <td><?php echo $rec['dosage']; ?></td>
                    <td><?php echo $rec['frequency']; ?></td>
                    <td><?php echo $rec['duration']; ?></td>
                    <td><?php echo $rec['instructions']; ?></td>
                    <td class="completed-buttons">
                        <!-- Edit Button -->
                        <form method="GET" action="prescriptions.php" style="display:inline;">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="appointment_id" value="<?php echo $rec['appointment_id']; ?>">
                            <button type="submit" class="btn navy">Edit</button>
                        </form>

                        <!-- Delete Button -->
                        <form method="GET" action="prescriptions.php" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="appointment_id" value="<?php echo $rec['appointment_id']; ?>">
                            <button type="submit" class="btn red">Delete</button>
                        </form>

                        <!-- Medical Record Button -->
                        <form method="GET" action="medical-records.php" style="display:inline;">
                            <input type="hidden" name="appointment_id" value="<?php echo $rec['appointment_id']; ?>">
                            <button type="submit" class="btn navy">Medical Record</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
<script src="../../scripts/prescriptions.js"></script>
</body>
</html>
