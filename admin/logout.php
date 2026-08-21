<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
clear_auth_cookie('admin');
unset($_SESSION['admin']);
header('Location: /admin/login.php');
exit;
