<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch only archived quizzes
$quizzes = $db->query("
    SELECT q.id, q.title, q.start_datetime, q.duration_hours, s.name AS subject_name, l.name AS level_name
    FROM quizzes q
    JOIN subjects s ON q.subject_id = s.id
    JOIN levels l ON s.level_id = l.id
    WHERE q.is_archived = 1
    ORDER BY q.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archives des Examens - Zouhair E-Learning</title>
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
        <h1><i class="fas fa-archive"></i> Archives des Examens</h1>
        <?php if (isset($_GET['success'])): ?>
            <div class="success-message"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>
        <a href="manage_quizzes.php" class="btn-action back"><i class="fas fa-arrow-left"></i> Retour aux Examens</a>
        <table id="archivedQuizzesTable" class="course-table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Matière</th>
                    <th>Niveau</th>
                    <th>Début (GMT+1)</th>
                    <th>Durée (h)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($quiz = $quizzes->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($quiz['title']); ?></td>
                        <td><?php echo htmlspecialchars($quiz['subject_name']); ?></td>
                        <td><?php echo htmlspecialchars($quiz['level_name']); ?></td>
                        <td><?php echo htmlspecialchars($quiz['start_datetime']); ?></td>
                        <td><?php echo number_format($quiz['duration_hours'], 2); ?></td>
                        <td>
                            <a href="../includes/serve_quiz_pdf.php?quiz_id=<?php echo $quiz['id']; ?>" class="btn-action view" title="Voir"><i class="fas fa-eye"></i></a>
                            <a href="restore_quiz.php?id=<?php echo $quiz['id']; ?>" class="btn-action restore" title="Restaurer" onclick="return confirm('Êtes-vous sûr de vouloir restaurer cet examen ? Il sera remis dans la liste principale.');"><i class="fas fa-undo"></i></a>
                            <a href="grade_quizzes_by_quiz.php?quiz_id=<?php echo $quiz['id']; ?>" class="btn-action validate" title="Noter"><i class="fas fa-pen"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
    <?php include '../includes/footer.php'; ?>
    <script>
        $(document).ready(function() {
            $('#archivedQuizzesTable').DataTable({
                pageLength: 10,
                lengthChange: false,
                language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json' }
            });
        });
    </script>
</body>
</html>