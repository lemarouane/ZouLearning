<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['quiz_id'])) {
    header("Location: manage_quizzes.php");
    exit;
}

// Set timezone for consistency
date_default_timezone_set('Africa/Casablanca');

$quiz_id = (int)$_GET['quiz_id'];
$quiz = $db->query("SELECT title FROM quizzes WHERE id = $quiz_id")->fetch_assoc();
if (!$quiz) {
    header("Location: manage_quizzes.php");
    exit;
}

$submissions = $db->query("
    SELECT qs.id, qs.quiz_id, qs.student_id, qs.response_path, qs.grade, qs.feedback, qs.submitted_at,
           q.start_datetime, q.duration_hours, s.full_name AS student_name
    FROM quiz_submissions qs
    JOIN quizzes q ON qs.quiz_id = q.id
    JOIN students s ON qs.student_id = s.id
    WHERE qs.quiz_id = $quiz_id
    ORDER BY qs.submitted_at DESC
");

$success = '';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submission_id'])) {
    $submission_id = (int)$_POST['submission_id'];
    $grade = floatval($_POST['grade']);
    $feedback = trim($_POST['feedback'] ?? '');
    if ($grade < 0 || $grade > 20) {
        $errors[] = "La note doit être entre 0 et 20.";
    } else {
        $stmt = $db->prepare("UPDATE quiz_submissions SET grade = ?, feedback = ?, graded_at = NOW() WHERE id = ?");
        $stmt->bind_param("dsi", $grade, $feedback, $submission_id);
        if ($stmt->execute()) {
            $success = "Note et commentaire enregistrés avec succès.";
        } else {
            $errors[] = "Erreur lors de l'enregistrement.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noter Quiz: <?php echo htmlspecialchars($quiz['title']); ?> - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-pen"></i> Noter Quiz: <?php echo htmlspecialchars($quiz['title']); ?></h1>
        <?php if ($success): ?>
            <p class="success-message"><?php echo $success; ?></p>
        <?php endif; ?>
        <?php foreach ($errors as $error): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endforeach; ?>
        <a href="manage_quizzes.php" class="btn-action back"><i class="fas fa-arrow-left"></i> Retour</a>
        <table id="submissionsTable" class="course-table">
            <thead>
                <tr>
                    <th>Étudiant</th>
                    <th>Date de Soumission</th>
                    <th>Statut</th>
                    <th>Note</th>
                    <th>Commentaires</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($submission = $submissions->fetch_assoc()): ?>
                    <?php
                    try {
                        $start_datetime = new DateTime($submission['start_datetime'], new DateTimeZone('Africa/Casablanca'));
                        $deadline = clone $start_datetime;
                        // Convert duration_hours to seconds for precision
                        $duration_seconds = $submission['duration_hours'] * 3600;
                        $deadline->modify("+{$duration_seconds} seconds");
                        $submitted_at = new DateTime($submission['submitted_at'], new DateTimeZone('Africa/Casablanca'));
                        $is_on_time = $submitted_at <= $deadline;
                        $row_class = $is_on_time ? 'submission-on-time' : 'submission-late';
                        $status_icon = $is_on_time ? '<i class="fas fa-check-circle status-on-time" title="Soumis à temps"></i>' : '<i class="fas fa-times-circle status-late" title="Soumis en retard"></i>';
                        error_log("Quiz: {$quiz['title']}, Student: {$submission['student_name']}, " .
                            "Submitted: {$submitted_at->format('Y-m-d H:i:s')}, " .
                            "Start: {$start_datetime->format('Y-m-d H:i:s')}, " .
                            "Duration: {$submission['duration_hours']} hours, " .
                            "Deadline: {$deadline->format('Y-m-d H:i:s')}, " .
                            "On-time: " . ($is_on_time ? 'Yes' : 'No'));
                    } catch (Exception $e) {
                        error_log("Error processing submission ID {$submission['id']}: {$e->getMessage()}");
                        $row_class = 'submission-late';
                        $status_icon = '<i class="fas fa-times-circle status-late" title="Erreur de calcul"></i>';
                    }
                    ?>
                    <tr class="<?php echo $row_class; ?>">
                        <td><?php echo htmlspecialchars($submission['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($submission['submitted_at']); ?></td>
                        <td><?php echo $status_icon; ?></td>
                        <td>
                            <?php echo $submission['grade'] !== null ? number_format($submission['grade'], 2) . '/20' : '<span class="badge pending">En attente</span>'; ?>
                        </td>
                        <td><?php echo $submission['feedback'] ? htmlspecialchars(substr($submission['feedback'], 0, 50)) . '...' : '-'; ?></td>
                        <td>
                            <a href="../includes/serve_quiz_pdf.php?submission_id=<?php echo $submission['id']; ?>" class="btn-action view" title="Voir Réponse"><i class="fas fa-eye"></i></a>
                            <button class="btn-action edit grade-modal-trigger" data-submission-id="<?php echo $submission['id']; ?>" data-grade="<?php echo $submission['grade'] !== null ? number_format($submission['grade'], 2) : ''; ?>" data-feedback="<?php echo htmlspecialchars($submission['feedback'] ?? ''); ?>" title="Noter"><i class="fas fa-pen"></i></button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div id="gradeModal" class="modal">
            <div class="modal-content">
                <span class="modal-close">×</span>
                <h2><i class="fas fa-pen"></i> Noter la Soumission</h2>
                <form method="POST" id="gradeForm">
                    <input type="hidden" name="submission_id" id="modal_submission_id">
                    <div class="form-group">
                        <label for="grade"><i class="fas fa-star"></i> Note (0-20)</label>
                        <input type="number" name="grade" id="grade" class="course-input" step="0.01" min="0" max="20" required>
                    </div>
                    <div class="form-group">
                        <label for="feedback"><i class="fas fa-comment"></i> Commentaires</label>
                        <textarea name="feedback" id="feedback" class="course-textarea"></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="save-course-btn"><i class="fas fa-save"></i> Enregistrer</button>
                        <button type="button" class="btn-action cancel modal-close"><i class="fas fa-times"></i> Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
    <script>
        $(document).ready(function() {
            const table = $('#submissionsTable').DataTable({
                pageLength: 10,
                lengthChange: false,
                language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json' }
            });

            $('#submissionsTable').on('click', '.grade-modal-trigger', function() {
                const submissionId = $(this).data('submission-id');
                const grade = $(this).data('grade');
                const feedback = $(this).data('feedback');
                $('#modal_submission_id').val(submissionId);
                $('#grade').val(grade);
                $('#feedback').val(feedback);
                $('#gradeModal').show();
            });

            $('.modal-close').on('click', function() {
                $('#gradeModal').hide();
                $('#gradeForm')[0].reset();
                $('#modal_submission_id').val('');
            });

            $(window).on('click', function(event) {
                if (event.target.id === 'gradeModal') {
                    $('#gradeModal').hide();
                    $('#gradeForm')[0].reset();
                    $('#modal_submission_id').val('');
                }
            });
        });
    </script>
</body>
</html>