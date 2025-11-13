<?php
require_once __DIR__ . '/_auth.php';
require_admin();
$conn = db_connect();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    mysqli_query($conn, "DELETE FROM products WHERE id=$id");
}
header('Location: /admin/products.php');
exit;