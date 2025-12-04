<?php
require_once '../config/connect.php';

echo "=== CLINIC_BLOCKED_DAYS TABLE STRUCTURE ===\n";
$result = mysqli_query($conn, 'DESCRIBE clinic_blocked_days');
while($row = mysqli_fetch_assoc($result)) {
    echo $row['Field'] . ' (' . $row['Type'] . ')' . "\n";
}
