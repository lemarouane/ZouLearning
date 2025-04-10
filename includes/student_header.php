<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: /student/login.php");
    exit;
}
?>

<header class="header">
    <button class="menu-toggle"><i class="fas fa-bars"></i></button>
    <h1>Zouhair E-Learning</h1>
</header>
<div class="sidebar">
    <div class="sidebar-header">
        <h2>Student Portal</h2>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li><a href="/student/index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="/student/courses.php"><i class="fas fa-book"></i> Courses</a></li>
            <li><a href="/student/profile.php"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="/student/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
</div>
<script>
    document.querySelector('.menu-toggle').addEventListener('click', () => {
        document.querySelector('.sidebar').classList.toggle('active');
    });
</script>