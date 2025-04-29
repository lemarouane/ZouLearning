<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch only archived courses
$courses_query = $db->query("
    SELECT c.id, c.title, c.difficulty, s.name AS subject_name, 
           COUNT(cf.id) AS folder_count,
           (SELECT COUNT(*) FROM course_contents cc WHERE cc.course_id = c.id) AS content_count
    FROM courses c
    JOIN subjects s ON c.subject_id = s.id
    LEFT JOIN course_folders cf ON c.id = cf.course_id
    WHERE c.is_archived = 1
    GROUP BY c.id
");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archives des Cours - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-archive"></i> Archives des Cours</h1>
        <?php if (isset($_GET['success'])): ?>
            <div class="success-message"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>
        <a href="manage_courses.php" class="btn-action back"><i class="fas fa-arrow-left"></i> Retour aux Cours</a>
        <table id="archivedCoursesTable" class="course-table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Matière</th>
                    <th>Difficulté</th>
                    <th>Dossiers</th>
                    <th>Fichiers</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($course = $courses_query->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course['title']); ?></td>
                        <td><?php echo htmlspecialchars($course['subject_name']); ?></td>
                        <td><?php echo $course['difficulty']; ?></td>
                        <td><?php echo $course['folder_count']; ?></td>
                        <td><?php echo $course['content_count']; ?></td>
                        <td>
                            <a href="view_course.php?id=<?php echo $course['id']; ?>" class="btn-action view" title="Voir"><i class="fas fa-eye"></i></a>
                            <a href="restore_course.php?id=<?php echo $course['id']; ?>" class="btn-action restore" title="Restaurer" onclick="return confirm('Êtes-vous sûr de vouloir restaurer ce cours ? Il sera remis dans la liste principale.');"><i class="fas fa-undo"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
    <?php include '../includes/footer.php'; ?>

    <script>
        $(document).ready(function() {
            $('#archivedCoursesTable').DataTable({ 
                pageLength: 10, 
                lengthChange: false,
                language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json' }
            });
        });
    </script>
</body>
</html>