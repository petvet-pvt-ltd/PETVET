<?php
require_once '../config/connect.php';
echo "=== USER_ROLES TABLE ===\n";
$r = mysqli_query($conn, 'DESCRIBE user_roles');
while($row = mysqli_fetch_assoc($r)) {
    echo $row['Field'] . "\n";
}
