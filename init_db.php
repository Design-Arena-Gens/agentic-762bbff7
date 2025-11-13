<?php
require_once __DIR__ . '/config.php';
$conn = db_connect();
$sql = file_get_contents(__DIR__ . '/db.sql');
if ($sql === false) { die('Schema file not found'); }
if (!mysqli_multi_query($conn, $sql)) {
    die('Error applying schema: ' . mysqli_error($conn));
}
do { /* flush multiple results */ } while (mysqli_more_results($conn) && mysqli_next_result($conn));
echo "Database initialized.";
