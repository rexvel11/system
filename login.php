<?php
session_start();
@include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        echo "<script>alert('Please fill in all fields.'); window.location.href = 'index.php';</script>";
        exit;
    }

    $query = "SELECT * FROM user_tbl WHERE username = ?";
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if ($row['verified'] == 0) {
                // Send another verification email
                $v_token = $row['v_token'];
                $verification_link = "http://yourdomain.com/verify.php?token=$v_token";
                $subject = "Resend Verification Email";
                $message = "Please verify your email by clicking on the following link: $verification_link";
                $headers = "From: no-reply@yourdomain.com";

                mail($row['email'], $subject, $message, $headers);

                echo "<script>alert('Your account is not verified. A new verification email has been sent.'); window.location.href = 'index.php';</script>";
                exit;
            }

            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_type'] = $row['user_type'];
                $_SESSION['last_activity'] = time();

                // Redirect based on user type
                if ($row['user_type'] === 'admin') {
                    header("Location: /system/admin/admin.php");
                } elseif ($row['user_type'] === 'student') {
                    header("Location: /system/student/student.php");
                } elseif ($row['user_type'] === 'professor') {
                    header("Location: /system/professor/prof.php");
                }
                exit;
            } else {
                echo "<script>alert('Invalid password.'); window.location.href = 'index.php';</script>";
                exit;
            }
        } else {
            echo "<script>alert('User does not exist.'); window.location.href = 'index.php';</script>";
            exit;
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Database query failed.');</script>";
    }
}
?>
