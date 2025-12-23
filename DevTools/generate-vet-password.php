<?php
// Generate proper password hash for password123
$password = 'password123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: $password\n";
echo "Hash: $hash\n\n";

// Verify it works
if (password_verify($password, $hash)) {
    echo "✓ Hash verification: SUCCESS\n";
} else {
    echo "✗ Hash verification: FAILED\n";
}

// Generate SQL update statement
echo "\n-- SQL Update Statement:\n";
echo "UPDATE users SET password = '$hash' WHERE id IN (18, 19, 20, 13, 21, 22, 23, 24, 25, 26);\n";
