<?php
#server information
$server_name = "localhost";
$server_user = "root";
$server_pass = "";
$server_db = "petvet-test";

#test table - users
$tb_name = "users";

$tb_id = "id";
$tb_email = "email";
$tb_pass = "password";
$tb_role = "role";

$conn = new mysqli($server_name, $server_user, $server_pass, $server_db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

?>
