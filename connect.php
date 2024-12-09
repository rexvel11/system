<?php
// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'system_db');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
