<?php
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'vet';
$_SESSION['user_name'] = 'Nethmi';

$currentPage = basename($_SERVER['PHP_SELF']);
include '../sidebar.php';

// ================= Dummy Appointment Data =================
$appointments = [
    ["id"=>1,"pet"=>"Charlie","owner"=>"Sarah Johnson"],
    ["id"=>2,"pet"=>"Milo","owner"=>"John Doe"],
    ["id"=>3,"pet"=>"Lucy","owner"=>"Emma Watson"],
    ["id"=>4,"pet"=>"Bella","owner"=>"James Brown"]
];

// ================= Dummy Medical Records =================
if (!isset($_SESSION['medicalRecords'])) {
    $_SESSION['medicalRecords'] = [
        ["appointment_id"=>1,"pet"=>"Charlie","date"=>"2025-08-20","symptoms"=>"Coughing","diagnosis"=>"Allergic dermatitis","treatment"=>"Topical ointment"],
        ["appointment_id"=>3,"pet"=>"Lucy","date"=>"2025-08-21","symptoms"=>"Fever","diagnosis"=>"Fever","treatment"=>"Oral fluids"]
    ];
}

$medicalRecords = &$_SESSION['medicalRecords'];

// ================= Handle Form Submission =================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        // Delete record by appointment_id
        $deleteId = $_POST['delete_id'];
        foreach ($medicalRecords as $key => $r) {
            if ($r['appointment_id'] == $deleteId) {
                unset($medicalRecords[$key]);
                break;
            }
        }
        header("Location: medical-records.php");
        exit;
    }

    $record = [
        "appointment_id" => $_POST['appointment_id'] ?? "",
        "pet" => $_POST['pet'] ?? "",
        "date" => $_POST['date'] ?? "",
        "symptoms" => $_POST['symptoms'] ?? "",
        "diagnosis" => $_POST['diagnosis'] ?? "",
        "treatment" => $_POST['treatment'] ?? ""
    ];

    $updated = false;
    foreach ($medicalRecords as &$r) {
        if ($r['appointment_id'] == $record['appointment_id']) {
            $r = $record;
            $updated = true;
            break;
        }
    }
    if (!$updated) {
        $medicalRecords[] = $record;
    }

    header("Location: medical-records.php");
    exit;
}

// ================= Handle Edit Request =================
$editRecord = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['appointment_id'])) {
    foreach ($medicalRecords as $rec) {
        if ($rec['appointment_id'] == $_GET['appointment_id']) {
            $editRecord = $rec;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Medical Records</title>
<link rel="stylesheet" href="../../styles/dashboard/vet/dashboard.css">
<link rel="stylesheet" href="../../styles/dashboard/vet/medical-records.css">
</head>
<body>
<div class="main-content">

    <!-- Header -->
    <header class="dashboard-header">
        <div class="header-left">
            <h2>Medical Records</h2>
            <p>Manage and review medical history of pets.</p>
        </div>
        <div class="header-right">
            <span class="date"><?php echo date("l, F j, Y"); ?></span>
        </div>
    </header>
    <br/>

    <!-- ===== Form Section ===== -->
    <div class="appointment-list" style="margin-bottom:40px;">
        <h3><?php echo $editRecord ? "Edit Medical Record" : "Add Medical Record"; ?></h3>
        <form method="POST">
            <!-- Appointment ID Auto-filled -->
            <label>Appointment ID</label>
            <input type="number" name="appointment_id" 
                   value="<?php echo $editRecord['appointment_id'] ?? htmlspecialchars($_GET['appointment_id'] ?? ''); ?>" 
                   readonly>

            <!-- Pet Name Input -->
            <label>Pet</label>
            <input type="text" name="pet" value="<?php echo $editRecord['pet'] ?? ''; ?>" required>

            <!-- Visit Date -->
            <label>Visit Date</label>
            <input type="date" name="date" value="<?php echo $editRecord['date'] ?? ''; ?>" required>

            <!-- Symptoms -->
            <label>Symptoms</label>
            <textarea name="symptoms"><?php echo $editRecord['symptoms'] ?? ''; ?></textarea>

            <!-- Diagnosis -->
            <label>Diagnosis</label>
            <textarea name="diagnosis"><?php echo $editRecord['diagnosis'] ?? ''; ?></textarea>

            <!-- Treatment Notes -->
            <label>Treatment Notes</label>
            <textarea name="treatment"><?php echo $editRecord['treatment'] ?? ''; ?></textarea>
            
            <br/>
            <button type="submit" class="btn navy"><?php echo $editRecord ? "Update Record" : "Save Record"; ?></button>
        </form>
    </div>

    <!-- ===== Records Table ===== -->
    <div class="appointment-list">
        <h3>Recent Records</h3>
        <table>
            <thead>
                <tr>
                    <th>Appointment ID</th>
                    <th>Pet</th>
                    <th>Owner</th>
                    <th>Date</th>
                    <th>Symptoms</th>
                    <th>Diagnosis</th>
                    <th>Treatment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($medicalRecords as $rec): ?>
                <tr>
                    <td><?php echo $rec['appointment_id']; ?></td>
                    <td><?php echo $rec['pet']; ?></td>
                    <td>
                        <?php
                        $owner = '';
                        foreach ($appointments as $appt) {
                            if ($appt['id'] == $rec['appointment_id']) {
                                $owner = $appt['owner'];
                                break;
                            }
                        }
                        echo $owner ?: 'N/A';
                        ?>
                    </td>
                    <td><?php echo $rec['date']; ?></td>
                    <td><?php echo $rec['symptoms']; ?></td>
                    <td><?php echo $rec['diagnosis']; ?></td>
                    <td><?php echo $rec['treatment']; ?></td>
                    <td class="completed-buttons">
                        <!-- Edit -->
                        <form method="GET" action="medical-records.php" style="display:inline;">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="appointment_id" value="<?php echo $rec['appointment_id']; ?>">
                            <button type="submit" class="btn navy">Edit</button>
                        </form>

                        <!-- Delete -->
                        <form method="POST" action="medical-records.php" style="display:inline;" 
                              onsubmit="return confirm('Are you sure you want to delete this record?');">
                            <input type="hidden" name="delete_id" value="<?php echo $rec['appointment_id']; ?>">
                            <button type="submit" class="btn red">Delete</button>
                        </form>

                        <!-- Add Prescription -->
                        <form method="GET" action="prescriptions.php" style="display:inline;">
                            <input type="hidden" name="appointment_id" value="<?php echo $rec['appointment_id']; ?>">
                            <input type="hidden" name="pet" value="<?php echo $rec['pet']; ?>">
                            <button type="submit" class="btn navy">Add Prescription</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
<script src="../../scripts/medical-records.js"></script>
</body>
</html>
