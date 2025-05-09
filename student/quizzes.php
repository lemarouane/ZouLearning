<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = (int)$_SESSION['student_id'];
$quizzes = $db->query("
    SELECT q.id, q.title, q.description, q.start_datetime, q.duration_hours,
           s.name AS subject_name, l.name AS level_name,
           qs.response_path, qs.grade, qs.feedback, qs.submitted_at
    FROM quizzes q
    JOIN subjects s ON q.subject_id = s.id
    JOIN levels l ON s.level_id = l.id
    LEFT JOIN quiz_submissions qs ON q.id = qs.quiz_id AND qs.student_id = $student_id
    WHERE q.is_archived = 0 
    AND s.is_archived = 0
    AND q.subject_id IN (
        SELECT subject_id FROM student_subjects WHERE student_id = $student_id
        UNION
        SELECT subject_id FROM student_courses sc
        JOIN courses c ON sc.course_id = c.id
        WHERE sc.student_id = $student_id AND c.is_archived = 0
    )
    ORDER BY q.start_datetime DESC
");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Examens - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <?php include '../includes/student_header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-question-circle"></i> Mes Examens</h1>
        <table id="quizzesTable" class="course-table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Matière</th>
                    <th>Niveau</th>
                    <th>Début</th>
                    <th>Durée (h)</th>
                    <th>Soumis</th>
                    <th>Note</th>
                    <th>Commentaires</th>
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
                        <td><?php echo $quiz['submitted_at'] ? htmlspecialchars($quiz['submitted_at']) : '<span class="badge pending">Non soumis</span>'; ?></td>
                        <td>
                            <?php echo $quiz['grade'] !== null ? number_format($quiz['grade'], 2) . '/20' : '<span class="badge pending">Non noté</span>'; ?>
                        </td>
                        <td><?php echo $quiz['feedback'] ? htmlspecialchars(substr($quiz['feedback'], 0, 50)) . '...' : '-'; ?></td>
                        <td>
                            <a href="view_quiz.php?id=<?php echo $quiz['id']; ?>" class="btn-action view" title="Voir Quiz"><i class="fas fa-eye"></i></a>
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

        // Activity tracking for session
        $(document).on('mousemove keydown', function() {
            $.ajax({
                url: 'update_activity.php',
                method: 'POST',
                data: { student_id: <?php echo isset($_SESSION['student_id']) ? (int)$_SESSION['student_id'] : 0; ?> },
                error: function() {
                    console.error('Error updating activity');
                }
            });
        });
    });
</script>
</body>
</html>