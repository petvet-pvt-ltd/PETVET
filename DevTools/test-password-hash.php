<?php
$password = 'password123';
$hash = '$2y$10$uas4JEHS2CH5ESxh4fv7XuRDDeYeuq3kz0Y009va76Tf4BkmgYB42';

if (password_verify($password, $hash)) {
    echo "✅ Password verification SUCCESS\n";
} else {
    echo "❌ Password verification FAILED\n";
}

// Also generate a new one to be sure
$newHash = password_hash($password, PASSWORD_DEFAULT);
echo "\nNew hash for '$password': $newHash\n";

if (password_verify($password, $newHash)) {
    echo "✅ New hash verification SUCCESS\n";
}
