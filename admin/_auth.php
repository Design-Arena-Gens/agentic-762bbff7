<?php
require_once __DIR__ . '/../config.php';

function is_admin() {
    return !empty($_SESSION['admin_logged_in']);
}

function require_admin() {
    if (!is_admin()) {
        header('Location: /admin/login.php');
        exit;
    }
}
