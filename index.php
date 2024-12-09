<?php
session_start();
@include 'connect.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
</head>
<body>
        <div class="container"> 
            <img src="/system/log.png" alt="logo" class="logo">
            
        </div>   

    <div class="form-container">
        <label class="centered-label">Welcome to Student Management System <br> Rosewood University</label>

        <form action="login.php" method="POST" class="login-form">
            <h1 class="login-title">Login</h1>
            <div class="input-box">
                <i class='bx bxs-user'></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-box">
                <i class='bx bxs-lock-alt'></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="remember-forgot-box">
                <label for="remember">
                    <input type="checkbox" id="remember">
                    Remember me
                </label>
                <a href="/system/forgot/forgot.php">Forgot Password?</a>
            </div>
            <button type="submit" class="login-btn">Login</button>
            <p class="register">
                Don't have an account?
                <a href="/system/signup.php">Sign Up</a>
            </p>
        </form>
    </div>

</body>
</html>