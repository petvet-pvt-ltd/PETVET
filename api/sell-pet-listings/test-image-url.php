<?php
require_once __DIR__ . '/../../config/connect.php';

$result = mysqli_query($conn, 'SELECT image_url FROM sell_pet_listing_images LIMIT 1');
$row = mysqli_fetch_assoc($result);
echo "Image URL in DB: " . ($row['image_url'] ?? 'No images found') . "\n";
?>
