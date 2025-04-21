<?php
require_once '../includes/db_connect.php';
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Validate and sanitize the subject ID from the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_subjects.php");
    exit;
}

$subject_id = intval($_GET['id']); // Sanitize the subject ID

// Fetch the subject details
$subject_query = $db->prepare("
    SELECT s.*, l.name AS level_name 
    FROM subjects s 
    JOIN levels l ON s.level_id = l.id 
    WHERE s.id = ?
");
$subject_query->bind_param("i", $subject_id);
$subject_query->execute();
$subject = $subject_query->get_result()->fetch_assoc();

if (!$subject) {
    header("Location: manage_subjects.php");
    exit;
}

// Fetch associated courses for the subject
$courses_query = $db->prepare("
    SELECT title, difficulty 
    FROM courses 
    WHERE subject_id = ?
");
$courses_query->bind_param("i", $subject_id);
$courses_query->execute();
$courses = $courses_query->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voir la Matière - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">

    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-book-open"></i> Détails de la Matière</h1>
        <div class="detail-card">
            <h3><i class="fas fa-info"></i> Informations sur la Matière</h3>
            <p><strong>Nom :</strong> <?php echo htmlspecialchars($subject['name']); ?></p>
            <p><strong>Niveau :</strong> <?php echo htmlspecialchars($subject['level_name']); ?></p>
            <p><strong>Créé le :</strong> <?php echo $subject['created_at']; ?></p>
        </div>
        <div class="detail-card">
            <h3><i class="fas fa-book"></i> Cours Associés</h3>
            <ul>
                <?php if ($courses->num_rows > 0): ?>
                    <?php while ($course = $courses->fetch_assoc()): ?>
                        <li>
                            <?php echo htmlspecialchars($course['title']); ?> 
                            (<?php echo htmlspecialchars($course['difficulty']); ?>)
                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>Aucun cours assigné à cette matière.</li>
                <?php endif; ?>
            </ul>
        </div>
        <a href="manage_subjects.php" class="btn-action back"><i class="fas fa-arrow-left"></i> Retour aux Matières</a>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>