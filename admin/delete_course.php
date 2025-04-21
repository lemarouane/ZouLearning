<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: manage_courses.php");
    exit;
}

$course_id = (int)$_GET['id'];
$course = $db->query("SELECT title FROM courses WHERE id = $course_id")->fetch_assoc();
if (!$course) {
    header("Location: manage_courses.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $db->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();

    // Log the action
    $stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (?, 'admin', 'Deleted course', ?)");
    $details = "Deleted course ID $course_id: " . $course['title'];
    $stmt->bind_param("is", $_SESSION['admin_id'], $details);
    $stmt->execute();

    $_SESSION['message'] = "Cours supprimé avec succès !";
    header("Location: manage_courses.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer un Cours - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">

    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-trash-alt"></i> Supprimer un Cours</h1>
        <div class="confirmation">
            <p>Êtes-vous sûr de vouloir supprimer <strong><?php echo htmlspecialchars($course['title']); ?></strong> ? Cela le retirera de toutes les affectations des étudiants.</p>
            <form method="POST">
                <button type="submit" class="btn-action delete"><i class="fas fa-trash"></i> Oui, Supprimer</button>
                <a href="manage_courses.php" class="btn-action cancel"><i class="fas fa-times"></i> Annuler</a>
            </form>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>