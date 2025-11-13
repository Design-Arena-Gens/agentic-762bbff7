<?php
require_once __DIR__ . '/_auth.php';
$_SESSION['admin_logged_in'] = false;
session_regenerate_id(true);
header('Location: /admin/login.php');
exit;