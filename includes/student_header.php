<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php';

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: ../student/login.php");
    exit;
}

// Check if session_status column exists
$result = $db->query("SHOW COLUMNS FROM students LIKE 'session_status'");
if ($result->num_rows === 0) {
    // Column doesn't exist, add it
    $db->query("ALTER TABLE students ADD COLUMN session_status VARCHAR(20) DEFAULT 'active'");
}

// Check if session is still valid
$stmt = $db->prepare("SELECT session_status FROM students WHERE id = ?");
$stmt->bind_param("i", $_SESSION['student_id']);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if ($student['session_status'] === 'logged_out') {
    // Session was logged out by admin, destroy the session and redirect to login
    session_destroy();
    header("Location: ../student/login.php");
    exit;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zouhair E-Learning - Étudiant</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        // Function to check session status
        function checkSessionStatus() {
            $.ajax({
                url: '../student/check_session.php',
                method: 'POST',
                data: { student_id: <?php echo $_SESSION['student_id']; ?> },
                success: function(response) {
                    if (response.status === 'logged_out') {
                        // Show logout message
                        alert('Vous avez été déconnecté par un administrateur.');
                        // Redirect to login page
                        window.location.href = '../student/login.php';
                    }
                }
            });
        }

        // Check session status every 5 seconds
        setInterval(checkSessionStatus, 5000);
    </script>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-graduation-cap"></i> Zouhair E-Learning</h2>
            <p class="admin-name"><i class="fas fa-user"></i> Étudiant</p>
        </div>
        <nav class="sidebar-nav">
        <ul>
                <li><a href="../student/dashboard.php" <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'class="active"' : ''; ?>><i class="fas fa-tachometer-alt"></i> <span>Tableau de Bord</span></a></li>
                <li><a href="../student/courses.php" <?php echo basename($_SERVER['PHP_SELF']) == 'courses.php' ? 'class="active"' : ''; ?>><i class="fas fa-book"></i> <span>Mes Cours</span></a></li>
                <li><a href="../student/quizzes.php" <?php echo basename($_SERVER['PHP_SELF']) == 'quizzes.php' ? 'class="active"' : ''; ?>><i class="fas fa-question-circle"></i> <span>Examens</span></a></li>
                <li><a href="../student/logout.php"><i class="fas fa-sign-out-alt"></i> <span>Déconnexion</span></a></li>
            </ul>
        </nav>
    </aside>
    <div class="main-content">