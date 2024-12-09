<?php
session_start();
@include 'connect.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token exists in the database
    $query = "SELECT * FROM user_tbl WHERE v_token = ?";
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, 's', $token);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            // Token found, mark the user as verified
            $query = "UPDATE user_tbl SET verified = 1 WHERE v_token = ?";
            if ($stmt = mysqli_prepare($conn, $query)) {
                mysqli_stmt_bind_param($stmt, 's', $token);
                mysqli_stmt_execute($stmt);
                echo "<script>alert('Your email has been verified! You can now login.'); window.location.href = 'index.php';</script>";
            }
        } else {
            echo "<script>alert('Invalid token or token expired.'); window.location.href = 'index.php';</script>";
        }
    }
}
?>
