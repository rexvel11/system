<?php
session_start();
@include '../connect.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); // Sanitize email input

    // Validate email input
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Please enter a valid email address.'); window.location.href = 'forgot.php';</script>";
        exit;
    }

    // Check if email exists in the database
    $query = "SELECT id FROM user_tbl WHERE email = ?";
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            // Email exists, redirect to change password page
            header("Location: /system/forgot/change_password.php?from=forgot&email=" . urlencode($email));
            exit;  // Ensure script stops executing after the redirect
        } else {
            // Email doesn't exist
            echo "<script>alert('Email not found in our records.'); window.location.href = 'forgot.php';</script>";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Database error. Please try again later.'); window.location.href = 'forgot.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <form action="forgot.php" method="POST" class="forgot-password-form">
            <h2>Forgot Password</h2>
            <label for="email">Enter your registered email</label>
            <input type="email" id="email" name="email" required placeholder="Your email address">
            <button type="submit">Submit</button>
            <label for="" class="back">
                Wanna go back? <a href="/system/index.php">Click here.</a>
            </label>
        </form>
    </div>
</body>
</html>
