<?php
// actions/logout.php
require_once __DIR__ . '/../includes/bootstrap.php';
logout_user();
setcookie('remember_token', '', time() - 3600, '/');
header('Location: ../index.php');
exit;
