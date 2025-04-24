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
<html lang="fr">
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
                <li><a href="../admin/dashboard.php" <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'class="active"' : ''; ?>><i class="fas fa-tachometer-alt"></i> <span>Tableau de bord</span></a></li>
                <li><a href="../admin/manage_students.php" <?php echo basename($_SERVER['PHP_SELF']) == 'manage_students.php' ? 'class="active"' : ''; ?>><i class="fas fa-users"></i> <span>Gérer les étudiants</span></a></li>
                <li><a href="../admin/manage_levels.php" <?php echo basename($_SERVER['PHP_SELF']) == 'manage_levels.php' ? 'class="active"' : ''; ?>><i class="fas fa-layer-group"></i> <span>Gérer les niveaux</span></a></li>
                <li><a href="../admin/add_level.php" <?php echo basename($_SERVER['PHP_SELF']) == 'add_level.php' ? 'class="active"' : ''; ?>><i class="fas fa-plus-circle"></i> <span>Ajouter un niveau</span></a></li>
                <li><a href="../admin/manage_subjects.php" <?php echo basename($_SERVER['PHP_SELF']) == 'manage_subjects.php' ? 'class="active"' : ''; ?>><i class="fas fa-book-open"></i> <span>Gérer les matières</span></a></li>
                <li><a href="../admin/add_subject.php" <?php echo basename($_SERVER['PHP_SELF']) == 'add_subject.php' ? 'class="active"' : ''; ?>><i class="fas fa-plus-circle"></i> <span>Ajouter une matière</span></a></li>
                <li><a href="../admin/manage_courses.php" <?php echo basename($_SERVER['PHP_SELF']) == 'manage_courses.php' ? 'class="active"' : ''; ?>><i class="fas fa-book"></i> <span>Gérer les cours</span></a></li>
                <li><a href="../admin/add_course.php" <?php echo basename($_SERVER['PHP_SELF']) == 'add_course.php' ? 'class="active"' : ''; ?>><i class="fas fa-plus-circle"></i> <span>Ajouter un cours</span></a></li>
                <li><a href="../admin/manage_quizzes.php" <?php echo basename($_SERVER['PHP_SELF']) == 'manage_quizzes.php' ? 'class="active"' : ''; ?>><i class="fas fa-question-circle"></i> <span>Gérer les examens</span></a></li>
                <li><a href="../admin/add_quiz.php" <?php echo basename($_SERVER['PHP_SELF']) == 'add_quiz.php' ? 'class="active"' : ''; ?>><i class="fas fa-plus-circle"></i> <span>Ajouter un examen</span></a></li>
                <li><a href="../admin/grade_quizzes.php" <?php echo basename($_SERVER['PHP_SELF']) == 'grade_quizzes.php' ? 'class="active"' : ''; ?>><i class="fas fa-pen"></i> <span>Noter les examens</span></a></li>
                <li><a href="../admin/device_requests.php" <?php echo basename($_SERVER['PHP_SELF']) == 'device_requests.php' ? 'class="active"' : ''; ?>><i class="fas fa-mobile-alt"></i> <span>Demandes d'appareils</span></a></li>
                <li><a href="../admin/user_sessions.php" <?php echo basename($_SERVER['PHP_SELF']) == 'user_sessions.php' ? 'class="active"' : ''; ?>><i class="fas fa-clock"></i> <span>Sessions utilisateur</span></a></li>
                <li><a href="../admin/logout.php"><i class="fas fa-sign-out-alt"></i> <span>Déconnexion</span></a></li>
            </ul>
        </nav>
    </aside>
    <div class="main-content">