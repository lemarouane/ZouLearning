<?php
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/login.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];
$admin = $db->query("SELECT username FROM admins WHERE id = $admin_id")->fetch_assoc();
$admin_name = $admin ? htmlspecialchars($admin['username']) : 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zouhair E-Learning - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-graduation-cap"></i> Zouhair E-Learning</h2>
            <p class="admin-name"><i class="fas fa-user-shield"></i> <?php echo $admin_name; ?></p>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="../admin/dashboard.php" <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'class="active"' : ''; ?>><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
                <li><a href="../admin/manage_students.php" <?php echo basename($_SERVER['PHP_SELF']) == 'manage_students.php' ? 'class="active"' : ''; ?>><i class="fas fa-users"></i> <span>Manage Students</span></a></li>
                <li><a href="../admin/manage_levels.php" <?php echo basename($_SERVER['PHP_SELF']) == 'manage_levels.php' ? 'class="active"' : ''; ?>><i class="fas fa-layer-group"></i> <span>Manage Levels</span></a></li>
                <li><a href="../admin/manage_subjects.php" <?php echo basename($_SERVER['PHP_SELF']) == 'manage_subjects.php' ? 'class="active"' : ''; ?>><i class="fas fa-book-open"></i> <span>Manage Subjects</span></a></li>
                <li><a href="../admin/manage_courses.php" <?php echo basename($_SERVER['PHP_SELF']) == 'manage_courses.php' ? 'class="active"' : ''; ?>><i class="fas fa-book"></i> <span>Manage Courses</span></a></li>
                <li><a href="../admin/add_course.php" <?php echo basename($_SERVER['PHP_SELF']) == 'add_course.php' ? 'class="active"' : ''; ?>><i class="fas fa-plus-circle"></i> <span>Add Course</span></a></li>
                <li><a href="../admin/manage_quizzes.php" <?php echo basename($_SERVER['PHP_SELF']) == 'manage_quizzes.php' ? 'class="active"' : ''; ?>><i class="fas fa-question-circle"></i> <span>Manage Quizzes</span></a></li>
                <li><a href="../admin/activity_logs.php" <?php echo basename($_SERVER['PHP_SELF']) == 'activity_logs.php' ? 'class="active"' : ''; ?>><i class="fas fa-history"></i> <span>Activity Logs</span></a></li>
                <li><a href="../admin/logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </nav>
    </aside>
    <div class="main-content">