<?php
// Generate password hash for password123
$password = 'password123';
$hash = password_hash($password, PASSWORD_BCRYPT);

echo "Password: $password\n";
echo "Hash: $hash\n";
echo "\nCopy this hash and use it in your SQL file.";
?>