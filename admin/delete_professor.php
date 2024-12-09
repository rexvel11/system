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

// Check if the professor ID is provided in the URL
if (isset($_GET['id'])) {
    $professorId = mysqli_real_escape_string($conn, $_GET['id']);

    // Query to delete the professor from the database
    $deleteQuery = "DELETE FROM user_tbl WHERE id = '$professorId' AND user_type = 'professor'";

    // Execute the delete query
    if (mysqli_query($conn, $deleteQuery)) {
        // Successfully deleted the professor, redirect with a success message
        echo "<script>
                alert('Professor has been deleted successfully.');
                window.location.href = '/system/admin/admin.php'; // Redirect to admin page
              </script>";
    } else {
        // Error occurred during deletion
        echo "<script>
                alert('Error deleting professor. Please try again.');
                window.location.href = '/system/admin/admin.php'; // Redirect to admin page
              </script>";
    }
} else {
    // If the professor ID is not provided
    echo "<script>
            alert('No professor selected for deletion.');
            window.location.href = '/system/admin/admin.php'; // Redirect to admin page
          </script>";
}
?>
