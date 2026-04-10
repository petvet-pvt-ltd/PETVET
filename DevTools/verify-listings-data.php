<?php
require_once __DIR__ . '/../config/connect.php';
global $conn;

echo "=== Approved Listings by Type ===\n\n";

$sql = "SELECT l.id, l.name, l.user_id, l.listing_type, 
        CONCAT(u.first_name, ' ', u.last_name) as owner_name
        FROM sell_pet_listings l
        LEFT JOIN users u ON l.user_id = u.id
        WHERE l.status = 'approved' 
        ORDER BY l.listing_type, l.id";

$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    echo sprintf(
        "%s - ID:%d - %s - User:%d (%s)\n",
        strtoupper($row['listing_type']),
        $row['id'],
        $row['name'],
        $row['user_id'],
        $row['owner_name']
    );
}

mysqli_close($conn);
?>
