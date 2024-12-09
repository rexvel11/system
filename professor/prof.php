<?php
session_start();
@include '../connect.php';

// Ensure the user is logged in and has the correct user type
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'professor') {
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

// Fetch all students from the database with the required attributes
$query = "SELECT id, name, username, email, verified, department, course FROM user_tbl WHERE user_type = 'student'";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome -->
    <link rel="stylesheet" href="styles.css">
    <style>
        
    </style>
</head>
<body>
    <!-- Top Bar -->
    <header class="top-bar">
        <!-- Add top bar content if needed -->
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
                    <li><a href="/system/professor/prof.php" class="menu-item active"><i class="icon"></i> Student Information</a></li>
                </ul>

                <div class="user-info">
                    <img src="/system/log.png" alt="Profile Picture">
                    <span>Professor</span>
                    <a href="/system/logout.php" class="logout-icon">
                        <i class="fa fa-sign-out-alt"></i> <!-- Logout Icon -->   
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <section class="content-header">
                <h2>Students Information</h2>
            </section>

            <!-- Students Table -->
            <section class="student-table">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Verified</th>
                            <th>Department</th>
                            <th>Course</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($student = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                                <td><?php echo htmlspecialchars($student['username']); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td>
                                    <?php 
                                    echo $student['verified'] == 1 ? "<span class='verified'>Verified</span>" : "<span class='unverified'>Unverified</span>";
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($student['department']); ?></td>
                                <td><?php echo htmlspecialchars($student['course']); ?></td>
                                <td class="action-btns">
                                <a href="/system/forgot/change_password.php?from=professor&email=<?php echo urlencode($student['email']); ?>" class="btn">Change Password</a>
                                    <a href="delete_student.php?id=<?php echo $student['id']; ?>" class="btn" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>