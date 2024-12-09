<?php
session_start(); // Start the session
@include 'connect.php'; // Ensure the database connection file exists
require 'vendor/autoload.php'; // PHPMailer autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['cpassword'];
    $department = $_POST['department'];
    $course = $_POST['course'];
    $userType = $_POST['user_type']; // Get the user type from the form

    // Validation
    if (empty($name) || empty($username) || empty($email) || empty($password) || empty($confirmPassword) || empty($department) || empty($course)) {
        $errors[] = "All fields are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        // Generate a secure verification token
        $v_token = bin2hex(random_bytes(16)); // 32 characters long token
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $verified = 0; // Default to not verified

        // Use prepared statements to prevent SQL injection
        $query = "INSERT INTO user_tbl (name, username, email, password, department, course, verified, v_token, user_type) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($conn, $query)) {
            // Bind the parameters
            mysqli_stmt_bind_param($stmt, 'sssssssis', $name, $username, $email, $hashedPassword, $department, $course, $verified, $v_token, $userType);

            if (mysqli_stmt_execute($stmt)) {
                // Send verification email
                if (sendVerificationEmail($email, $v_token)) {
                    // Show success alert and then redirect to the login page
                    echo "<script>
                        alert('Registration successful! Please verify your email.');
                        window.location.href = '/system/index.php'; // Redirect to login page
                    </script>";
                } else {
                    echo "<script>alert('Registration successful, but verification email could not be sent.');</script>";
                }
            } else {
                echo "<script>alert('Error during registration: " . mysqli_error($conn) . "');</script>";
            }
            mysqli_stmt_close($stmt); // Close the statement
        } else {
            echo "<script>alert('Failed to prepare the SQL statement.');</script>";
        }
    } else {
        // Display all errors
        echo "<script>alert('" . implode("\\n", $errors) . "');</script>";
    }
}

function sendVerificationEmail($email, $v_token) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'rexzcel11@gmail.com'; // Update with your email
        $mail->Password = 'koxh rusd pwaz zzhz'; // Update with your email app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('rexzcel11@gmail.com', 'Student Management System');
        $mail->addAddress($email);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification';
        $mail->Body = "Click the link to verify your email: 
            <a href='http://localhost/system/verify.php?v_token=$v_token'>Verify Email</a>";
        $mail->AltBody = "Click the link to verify your email: 
            http://localhost/system/verify.php?v_token=$v_token";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}"); // Log email errors
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form action="" method="POST" class="register-form">
        <h1 class="register-title">Sign Up</h1>

        <div class="input-box">
            <i class='bx bxs-user'></i>
            <input type="text" name="name" placeholder="Name" required>
        </div>
        <div class="input-box">
            <i class='bx bxs-user'></i>
            <input type="text" name="username" placeholder="Username" required>
        </div>
        <div class="input-box">
            <i class='bx bxs-user'></i>
            <input type="text" name="address" placeholder="Address" required>
        </div>
        <div class="input-box">
            <i class='bx bxs-envelope'></i>
            <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="input-box">
            <i class='bx bxs-lock-alt'></i>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <span id="password-error" class="error-message">Password must be at least 8 characters long.</span>
        </div>
        <div class="input-box">
            <i class='bx bxs-lock-alt'></i>
            <input type="password" name="cpassword" placeholder="Confirm Password" required>
        </div>
        <div class="input-box">
           <select name="department" required>
                <option value="CICT" <?= (isset($_POST['department']) && $_POST['department'] == 'CICT') ? 'selected' : '' ?>>CICT</option>
                <option value="BME" <?= (isset($_POST['department']) && $_POST['department'] == 'BME') ? 'selected' : '' ?>>BME</option>
                
            </select>
        </div>
        <div class="input-box">
           <select name="course" required>
                <option value="IT" <?= (isset($_POST['course']) && $_POST['course'] == 'IT') ? 'selected' : '' ?>>IT</option>
                <option value="IS" <?= (isset($_POST['course']) && $_POST['course'] == 'IS') ? 'selected' : '' ?>>IS</option>
                <option value="CS" <?= (isset($_POST['course']) && $_POST['course'] == 'CS') ? 'selected' : '' ?>>CS</option>
                <option value="BTVTED" <?= (isset($_POST['course']) && $_POST['course'] == 'BTVTED') ? 'selected' : '' ?>>BTVTED</option>
                <option value="BSA" <?= (isset($_POST['course']) && $_POST['course'] == 'BSA') ? 'selected' : '' ?>>BSA</option>
                <option value="BSAIS" <?= (isset($_POST['course']) && $_POST['course'] == 'BSAIS') ? 'selected' : '' ?>>BSAIS</option>
                <option value="BPA" <?= (isset($_POST['course']) && $_POST['course'] == 'BPA') ? 'selected' : '' ?>>BPA</option>
                <option value="BSE" <?= (isset($_POST['course']) && $_POST['course'] == 'BSE') ? 'selected' : '' ?>>BSE</option>
            </select>
        </div>
        <div class="input-box">
                    <select name="user_type" required>
                        <option value="student" <?= (isset($_POST['user_type']) && $_POST['user_type'] == 'student') ? 'selected' : '' ?>>Student</option>
                        <option value="professor" <?= (isset($_POST['user_type']) && $_POST['user_type'] == 'professor') ? 'selected' : '' ?>>Teacher</option>  
                    </select>
                </div>

        <button type="submit" class="login-btn">Sign Up</button>

        <p class="register">
            Have an account? <a href="/system/index.php">Login</a>
        </p>
    </form>

    <script>
        // Get password input and error message elements
        const passwordInput = document.getElementById('password');
        const passwordError = document.getElementById('password-error');

        // Add input event listener for real-time validation
        passwordInput.addEventListener('input', function () {
            if (passwordInput.value.length < 8) {
                passwordError.style.display = 'inline'; // Show error
            } else {
                passwordError.style.display = 'none'; // Hide error
            }
        });
    </script>
</body>
</html>