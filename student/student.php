<?php
session_start();
@include '../connect.php';

// Ensure the user is logged in and has the correct user type
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
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

// Fetch the student's data from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM user_tbl WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $query)) {
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $student = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
} else {
    echo "<script>alert('Error fetching student data.');</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Top Bar -->
    <header class="top-bar">
    </header>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <img src="/system/log.png" alt="Logo">
                <h2>Student Management System</h2>
            </div>
            <nav>
                <ul>
                    <li><a href="/system/student/student.php" class="menu-item active"><i class="icon"></i> Student Profile</a></li>
                </ul>

                <div class="user-info">
                    <img src="/system/log.png" alt="Profile Picture">
                    <span>Student</span>
                    <a href="/system/logout.php" class="logout-icon">
                        <i class="fa fa-sign-out-alt"></i> <!-- Logout Icon -->   
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <section class="content-header">
                <h2>Student Information</h2>
            </section>

            <!-- Profile Card -->
            <section class="profile-card">
                <div class="card">
                    <h3>Personal Details</h3>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($student['name']); ?></p>
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($student['username']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
                    <p><strong>Department:</strong> <?php echo htmlspecialchars($student['department']); ?></p>
                    <p><strong>Course:</strong> <?php echo htmlspecialchars($student['course']); ?></p>

                    <!-- Change Password Button -->
                    <a href="/system/forgot/change_password.php" class="change-pass-btn">Change Password</a>
                </div>
            </section>
        </main>
    </div>

</body>
</html>
