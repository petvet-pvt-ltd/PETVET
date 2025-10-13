<?php
#server information
$server_name = "192.168.1.3";
$server_user = "root";
$server_pass = "your_password";
$server_db = "petvet-test";

#test table - users
$tb_name = "users";

$tb_id = "id";
$tb_email = "email";
$tb_pass = "password";
$tb_role = "role";

function db():PDO {
    global $server_name, $server_user, $server_pass, $server_db;
    $dsn = "mysql:host=$server_name;dbname=$server_db;charset=utf8";
    try {
        $pdo = new PDO($dsn, $server_user, $server_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
?>