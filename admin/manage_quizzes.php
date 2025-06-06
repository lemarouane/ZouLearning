<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch only non-archived quizzes
$quizzes = $db->query("
    SELECT q.id, q.title, q.start_datetime, q.duration_hours, s.name AS subject_name, l.name AS level_name
    FROM quizzes q
    JOIN subjects s ON q.subject_id = s.id
    JOIN levels l ON s.level_id = l.id
    WHERE q.is_archived = 0
    ORDER BY q.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les Examens - Zouhair E-Learning</title>
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
        <h1><i class="fas fa-question-circle"></i> Gérer les Examens</h1>
        <?php if (isset($_GET['success'])): ?>
            <div class="success-message"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>
        <div class="form-actions">
            <a href="add_quiz.php" class="add-course-btn"><i class="fas fa-plus"></i> Ajouter un Examen</a>
            <a href="archive_quizzes.php" class="btn-action archive" style="background-color: #007bff; color: white;"><i class="fas fa-archive"></i> Voir les Archives</a>
        </div>
        <table id="quizzesTable" class="course-table">
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
                            <a href="edit_quiz.php?id=<?php echo $quiz['id']; ?>" class="btn-action edit" title="Modifier"><i class="fas fa-edit"></i></a>
                            <a href="grade_quizzes_by_quiz.php?quiz_id=<?php echo $quiz['id']; ?>" class="btn-action validate" title="Noter"><i class="fas fa-pen"></i></a>
                            <a href="delete_quiz.php?id=<?php echo $quiz['id']; ?>" class="btn-action delete" title="Archiver" onclick="return confirm('Êtes-vous sûr de vouloir archiver cet examen ? Il sera déplacé vers les archives.');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
    <?php include '../includes/footer.php'; ?>
    <script>
        $(document).ready(function() {
            $('#quizzesTable').DataTable({
                pageLength: 10,
                lengthChange: false,
                language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json' }
            });
        });
    </script>
</body>
</html>