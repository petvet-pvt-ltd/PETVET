<?php
require_once __DIR__ . '/../../config/connect.php';

$result = mysqli_query($conn, 'DESCRIBE users');
echo "Users table columns:\n";
while($row = mysqli_fetch_assoc($result)) {
    echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
}
?>
