<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$submissions = $db->query("
    SELECT qs.id, qs.quiz_id, qs.student_id, qs.response_path, qs.grade, qs.feedback, qs.submitted_at,
           q.title AS quiz_title, s.full_name AS student_name
    FROM quiz_submissions qs
    JOIN quizzes q ON qs.quiz_id = q.id
    JOIN students s ON qs.student_id = s.id
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
    <title>Noter les Quiz - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-pen"></i> Noter les Quiz</h1>
        <?php if ($success): ?>
            <p class="success-message"><?php echo $success; ?></p>
        <?php endif; ?>
        <?php foreach ($errors as $error): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endforeach; ?>
        <table id="submissionsTable" class="course-table">
            <thead>
                <tr>
                    <th>Étudiant</th>
                    <th>Quiz</th>
                    <th>Date de Soumission</th>
                    <th>Note</th>
                    <th>Commentaires</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($submission = $submissions->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($submission['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($submission['quiz_title']); ?></td>
                        <td><?php echo htmlspecialchars($submission['submitted_at']); ?></td>
                        <td>
                            <?php echo $submission['grade'] !== null ? number_format($submission['grade'], 2) . '/20' : '<span class="badge pending">En attente</span>'; ?>
                        </td>
                        <td><?php echo $submission['feedback'] ? htmlspecialchars(substr($submission['feedback'], 0, 50)) . '...' : '-'; ?></td>
                        <td>
                            <a href="../includes/serve_quiz_pdf.php?submission_id=<?php echo $submission['id']; ?>" class="btn-action view" title="Voir Réponse"><i class="fas fa-eye"></i></a>
                            <a href="#gradeForm<?php echo $submission['id']; ?>" class="btn-action edit grade-toggle" title="Noter"><i class="fas fa-pen"></i></a>
                        </td>
                    </tr>
                    <tr id="gradeForm<?php echo $submission['id']; ?>" class="grade-form" style="display: none;">
                        <td colspan="6">
                            <div class="form-container">
                                <form method="POST">
                                    <input type="hidden" name="submission_id" value="<?php echo $submission['id']; ?>">
                                    <div class="form-group">
                                        <label for="grade_<?php echo $submission['id']; ?>"><i class="fas fa-star"></i> Note (0-20)</label>
                                        <input type="number" name="grade" id="grade_<?php echo $submission['id']; ?>" class="course-input" step="0.01" min="0" max="20" value="<?php echo $submission['grade'] !== null ? number_format($submission['grade'], 2) : ''; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="feedback_<?php echo $submission['id']; ?>"><i class="fas fa-comment"></i> Commentaires</label>
                                        <textarea name="feedback" id="feedback_<?php echo $submission['id']; ?>" class="course-textarea"><?php echo htmlspecialchars($submission['feedback'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" class="save-course-btn"><i class="fas fa-save"></i> Enregistrer</button>
                                        <a href="#" class="btn-action cancel grade-toggle"><i class="fas fa-times"></i> Annuler</a>
                                    </div>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
    <?php include '../includes/footer.php'; ?>
    <script>
        $(document).ready(function() {
            $('#submissionsTable').DataTable({
                pageLength: 10,
                lengthChange: false,
                language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json' }
            });

            $('.grade-toggle').click(function(e) {
                e.preventDefault();
                const target = $(this).attr('href');
                $(target).toggle();
            });
        });
    </script>
</body>
</html>