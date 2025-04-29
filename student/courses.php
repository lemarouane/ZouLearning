<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = (int)$_SESSION['student_id'];

// Fetch student’s assigned course IDs (for QCM filtering)
$assigned_courses = $db->query("
    SELECT course_id
    FROM student_courses
    JOIN courses c ON student_courses.course_id = c.id
    WHERE student_id = $student_id AND c.is_archived = 0
")->fetch_all(MYSQLI_ASSOC);
$assigned_course_ids = array_column($assigned_courses, 'course_id');

// Fetch non-archived subjects assigned to the student
$subjects_query = $db->query("
    SELECT s.id, s.name
    FROM subjects s
    JOIN student_subjects ss ON s.id = ss.subject_id
    WHERE ss.student_id = $student_id AND s.is_archived = 0
    ORDER BY s.name
");
if (!$subjects_query) {
    die("Erreur dans la requête des matières : " . $db->error);
}
$subjects = $subjects_query->fetch_all(MYSQLI_ASSOC);

// Fetch courses and QCMs grouped by subject
$courses_by_subject = [];
$qcms_by_subject = [];
foreach ($subjects as $subject) {
    $subject_id = $subject['id'];

    // Fetch non-archived courses for this subject
    $courses_query = $db->query("
        SELECT DISTINCT c.id, c.title, s.name AS subject
        FROM (
            SELECT sc.course_id
            FROM student_courses sc
            JOIN courses c ON sc.course_id = c.id
            WHERE sc.student_id = $student_id AND c.is_archived = 0
            UNION
            SELECT c.id AS course_id
            FROM student_subjects ss
            JOIN courses c ON ss.subject_id = c.subject_id
            JOIN subjects s ON c.subject_id = s.id
            WHERE ss.student_id = $student_id AND ss.all_courses = 1 AND c.is_archived = 0 AND s.is_archived = 0
        ) AS unique_courses
        JOIN courses c ON unique_courses.course_id = c.id
        JOIN subjects s ON c.subject_id = s.id
        LEFT JOIN qcm q ON q.course_after_id = c.id
        LEFT JOIN qcm_submissions qs ON q.id = qs.qcm_id AND qs.student_id = $student_id AND qs.passed = 1
        WHERE s.id = $subject_id AND s.is_archived = 0
        AND (q.id IS NULL OR qs.id IS NOT NULL OR q.is_archived = 0)
    ");
    if (!$courses_query) {
        die("Erreur dans la requête des cours pour la matière {$subject['name']} : " . $db->error);
    }
    $courses_by_subject[$subject_id] = $courses_query->fetch_all(MYSQLI_ASSOC);

    // Fetch non-archived QCMs for this subject
    $qcms_query = $db->query("
        SELECT q.id, q.title, s.name AS subject, qs.passed, qs.score, qs.id AS submission_id
        FROM qcm q
        JOIN subjects s ON q.subject_id = s.id
        JOIN student_subjects ss ON s.id = ss.subject_id
        LEFT JOIN qcm_submissions qs ON q.id = qs.qcm_id AND qs.student_id = $student_id
        WHERE ss.student_id = $student_id
        AND s.id = $subject_id
        AND s.is_archived = 0
        AND q.is_archived = 0
        AND q.course_after_id IN (" . (empty($assigned_course_ids) ? '0' : implode(',', $assigned_course_ids)) . ")
    ");
    if (!$qcms_query) {
        die("Erreur dans la requête des QCM pour la matière {$subject['name']} : " . $db->error);
    }
    $qcms_by_subject[$subject_id] = $qcms_query->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Cours - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .subject-section { margin-bottom: 30px; }
        .subject-section h3 { color: #333; margin-bottom: 15px; }
        .tables-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
        }
        .table-wrapper { margin-bottom: 20px; }
        .table-wrapper h4 { margin-bottom: 10px; color: #555; }
        .dataTable { width: 100%; }
        .empty-message { color: #888; font-style: italic; }
        .status-passed { color: #4caf50; font-weight: bold; }
        .status-not-passed { color: #f44336; font-weight: bold; }
        @media (max-width: 768px) {
            .tables-container { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <?php include '../includes/student_header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-book"></i> Mes Cours</h1>
        <?php if (isset($_GET['success'])): ?>
            <div class="success-message"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>
        <?php if (empty($subjects)): ?>
            <p class="empty-message">Aucune matière assignée.</p>
        <?php else: ?>
            <?php foreach ($subjects as $index => $subject): ?>
                <div class="subject-section">
                    <h3><i class="fas fa-book-open"></i> <?php echo htmlspecialchars($subject['name']); ?></h3>
                    <div class="tables-container">
                        <!-- QCM Table -->
                        <div class="table-wrapper">
                            <h4><i class="fas fa-question-circle"></i> QCM Disponibles</h4>
                            <?php if (!empty($qcms_by_subject[$subject['id']])): ?>
                                <table id="qcmTable_<?php echo $subject['id']; ?>" class="display dataTable">
                                    <thead>
                                        <tr>
                                            <th>Titre</th>
                                            <th>Statut</th>
                                            <th>Score</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($qcms_by_subject[$subject['id']] as $qcm): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($qcm['title']); ?></td>
                                                <td>
                                                    <?php if ($qcm['submission_id']): ?>
                                                        <span class="<?php echo $qcm['passed'] ? 'status-passed' : 'status-not-passed'; ?>">
                                                            <?php echo $qcm['passed'] ? 'Réussi' : 'Non passé'; ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span>Non tenté</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php echo $qcm['score'] !== null ? number_format($qcm['score'], 2) . '%' : '-'; ?>
                                                </td>
                                                <td>
                                                    <?php if ($qcm['submission_id']): ?>
                                                        <a href="view_qcm_submission.php?id=<?php echo $qcm['submission_id']; ?>" class="btn-action view" title="Voir les réponses">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="student_take_qcm.php?id=<?php echo $qcm['id']; ?>" class="btn-action view" title="Passer QCM">
                                                            <i class="fas fa-play"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="empty-message">Aucun QCM disponible.</p>
                            <?php endif; ?>
                        </div>
                        <!-- Courses Table -->
                        <div class="table-wrapper">
                            <h4><i class="fas fa-book"></i> Cours Disponibles</h4>
                            <?php if (!empty($courses_by_subject[$subject['id']])): ?>
                                <table id="courseTable_<?php echo $subject['id']; ?>" class="display dataTable">
                                    <thead>
                                        <tr>
                                            <th>Titre</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($courses_by_subject[$subject['id']] as $course): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($course['title']); ?></td>
                                                <td>
                                                    <a href="view_course.php?id=<?php echo $course['id']; ?>" 
                                                       class="btn-action view" 
                                                       title="Voir" 
                                                       onclick="console.log('Clicked View for Course ID: <?php echo $course['id']; ?>');">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="empty-message">Aucun cours disponible.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
    <?php include '../includes/footer.php'; ?>
    <script>
        $(document).ready(function() {
            // Initialize DataTables for each subject
            <?php foreach ($subjects as $subject): ?>
                $('#qcmTable_<?php echo $subject['id']; ?>').DataTable({
                    pageLength: 3,
                    lengthChange: false,
                    searching: false,
                    ordering: true,
                    info: false
                });
                $('#courseTable_<?php echo $subject['id']; ?>').DataTable({
                    pageLength: 3,
                    lengthChange: false,
                    searching: false,
                    ordering: true,
                    info: false
                });
            <?php endforeach; ?>
            $('.btn-action.view').on('click', function(e) {
                var id = $(this).attr('href').split('id=')[1];
                console.log('Redirecting to ?id=' + id);
            });
        });
    </script>
</body>
</html>