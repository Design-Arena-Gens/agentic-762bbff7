<?php
require_once __DIR__ . '/_auth.php';
require_admin();
header('Location: /admin/products.php');
exit;