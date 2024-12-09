<?php
session_start();

// Set inactivity limit (5 minutes)
$inactiveLimit = 5 * 60; 

if (isset($_SESSION['last_activity'])) {
    $inactiveDuration = time() - $_SESSION['last_activity'];
    if ($inactiveDuration > $inactiveLimit) {
        session_unset(); // Unset session variables
        session_destroy(); // Destroy session
        header("Location: /system/index.php");
        exit;
    }
}

// Update last activity time
$_SESSION['last_activity'] = time();
?>
