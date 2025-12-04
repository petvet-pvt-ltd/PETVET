<?php
require_once '../config/connect.php';
$r = mysqli_query($conn, 'DESCRIBE users');
while($row = mysqli_fetch_assoc($r)) {
    echo $row['Field'] . "\n";
}
