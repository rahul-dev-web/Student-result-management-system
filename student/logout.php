<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
clear_auth_cookie('student');
unset($_SESSION['student']);
header('Location: /login.php');
exit;
