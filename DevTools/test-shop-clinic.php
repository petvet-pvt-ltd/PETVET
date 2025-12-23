<?php
session_start();
require_once __DIR__ . '/config/connect.php';
require_once __DIR__ . '/controllers/GuestController.php';

$_GET['clinic_id'] = 1;

$controller = new GuestController();
$controller->shopClinic();
