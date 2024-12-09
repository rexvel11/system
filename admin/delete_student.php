<?php
session_start();
@include '../connect.php';

// Ensure the user is logged in and has the correct user type
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: /system/index.php");
    exit;
}

// Session timeout handling
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > 1800) {
    session_unset();
    session_destroy();
    header("Location: /system/index.php");
    exit;
}
$_SESSION['last_activity'] = time(); // Update session activity

// Check if student ID is provided in the GET request
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $studentId = $_GET['id'];

    // Delete the student from the database
    $deleteQuery = "DELETE FROM user_tbl WHERE id = ? AND user_type = 'student'";
    if ($stmt = mysqli_prepare($conn, $deleteQuery)) {
        mysqli_stmt_bind_param($stmt, 'i', $studentId);
        if (mysqli_stmt_execute($stmt)) {
            // Redirect to professor's page with success message
            echo "<script>alert('Student successfully deleted.'); window.location.href = '/system/admin/admin.php';</script>";
        } else {
            echo "<script>alert('Error deleting student. Please try again later.'); window.location.href = '/system/admin/admin.php';</script>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Database error. Please try again later.'); window.location.href = '/system/admin/admin.php';</script>";
    }
} else {
    echo "<script>alert('Invalid student ID.'); window.location.href = '/system/admin/admin.php';</script>";
}
?>
