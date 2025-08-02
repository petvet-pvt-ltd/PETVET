<?php
require_once('login-process.php');

session_destroy();
header("Location: ../index.php");
?>