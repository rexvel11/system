<?php
session_start();
@include '../connect.php';

// Determine the flow type: forgot, professor, or student
$isForgotFlow = isset($_GET['from']) && $_GET['from'] === 'forgot' && isset($_GET['email']);
$isProfessorFlow = isset($_GET['from']) && $_GET['from'] === 'professor' && isset($_GET['email']);
$isStudentFlow = isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'student';

// Initialize email variable and set the flow type
$email = null;
$from = $isForgotFlow ? 'forgot' : ($isStudentFlow ? 'student' : ($isProfessorFlow ? 'professor' : null));

// Handle forgot password flow
if ($isForgotFlow) {
    // Fetch and sanitize email from GET
    $email = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);

    // Validate the email format
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email address.'); window.location.href = '/system/forgot/forgot.php';</script>";
        exit;
    }

    // Check if email exists in the database
    $query = "SELECT id FROM user_tbl WHERE email = ?";
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) === 0) {
            echo "<script>alert('Email not found.'); window.location.href = '/system/forgot/forgot.php';</script>";
            exit;
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Database error. Please try again later.'); window.location.href = '/system/forgot/forgot.php';</script>";
        exit;
    }
} elseif ($isStudentFlow) {
    // Handle student flow: Fetch the logged-in student's email
    $user_id = $_SESSION['user_id'];
    $query = "SELECT email FROM user_tbl WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $student = mysqli_fetch_assoc($result);
        $email = $student['email'];
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Error fetching student data.'); window.location.href = 'student.php';</script>";
        exit;
    }
} elseif ($isProfessorFlow) {
    // Handle professor flow: Fetch the student's email from the GET parameter
    $email = $_GET['email'];

    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email address.'); window.location.href = '/system/professor/prof.php';</script>";
        exit;
    }

    // Fetch the student's email to ensure it exists
    $query = "SELECT email FROM user_tbl WHERE email = ?";
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) === 0) {
            echo "<script>alert('Student not found.'); window.location.href = '/system/professor/prof.php';</script>";
            exit;
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Database error. Please try again later.'); window.location.href = '/system/professor/prof.php';</script>";
        exit;
    }
} else {
    // If an invalid flow, redirect to home
    header("Location: /system/index.php");
    exit;
}

// Handle password change submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate passwords
    if (empty($newPassword) || empty($confirmPassword)) {
        echo "<script>alert('Both password fields are required.'); window.location.href = 'change_password.php?from={$from}&email=" . urlencode($email) . "';</script>";
        exit;
    }

    if ($newPassword !== $confirmPassword) {
        echo "<script>alert('Passwords do not match.'); window.location.href = 'change_password.php?from={$from}&email=" . urlencode($email) . "';</script>";
        exit;
    }

    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    // Update password for the email
    $updateQuery = "UPDATE user_tbl SET password = ? WHERE email = ?";
    if ($updateStmt = mysqli_prepare($conn, $updateQuery)) {
        mysqli_stmt_bind_param($updateStmt, 'ss', $hashedPassword, $email);
        if (mysqli_stmt_execute($updateStmt)) {
            // Redirect based on the flow
            if ($from === 'student') {
                echo "<script>alert('Password successfully reset.'); window.location.href = '/system/student/student.php';</script>";
            } elseif ($from === 'professor') {
                echo "<script>alert('Password successfully reset.'); window.location.href = '/system/professor/prof.php';</script>";
            } else {
                echo "<script>alert('Password successfully reset.'); window.location.href = '/system/index.php';</script>";
            }
            exit;
        } else {
            echo "<script>alert('Error updating password. Please try again later.');</script>";
        }
        mysqli_stmt_close($updateStmt);
    } else {
        echo "<script>alert('Database error. Please try again later.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <form action="change_password.php?from=<?= $from ?>&email=<?= urlencode($email) ?>" method="POST" class="change-password-form">
            <h2>Change Password</h2>
            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" required>
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <button type="submit">Change Password</button>
            <label class="back">Wanna go back? 
                <a href="/system/index.php">Click here.</a>
            </label>
        </form>
    </div>
</body>
</html>
