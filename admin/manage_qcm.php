<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch only non-archived QCMs
$qcms = $db->query("
    SELECT q.id, q.title, q.threshold, s.name AS subject_name, l.name AS level_name, 
           c1.title AS course_before, c2.title AS course_after
    FROM qcm q
    JOIN subjects s ON q.subject_id = s.id
    JOIN levels l ON s.level_id = l.id
    JOIN courses c1 ON q.course_before_id = c1.id
    JOIN courses c2 ON q.course_after_id = c2.id
    WHERE q.is_archived = 0
    ORDER BY q.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les QCM - Zouhair E-Learning</title>
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
        <h1><i class="fas fa-question-circle"></i> Gérer les QCM</h1>
        <?php if (isset($_GET['success'])): ?>
            <div class="success-message"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>
        <div class="form-actions">
            <a href="add_qcm.php" class="add-course-btn"><i class="fas fa-plus"></i> Ajouter un QCM</a>
            <a href="view_qcm_submissions.php" class="add-course-btn" style="background: #2196f3;"><i class="fas fa-list"></i> Voir Toutes les Soumissions</a>
            <a href="archive_qcm.php" class="btn-action archive" style="background-color: #007bff; color: white;"><i class="fas fa-archive"></i> Voir les Archives</a>
        </div>
        <table id="qcmTable" class="course-table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Matière</th>
                    <th>Niveau</th>
                    <th>Avant Cours</th>
                    <th>Après Cours</th>
                    <th>Seuil (%)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($qcm = $qcms->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($qcm['title']); ?></td>
                        <td><?php echo htmlspecialchars($qcm['subject_name']); ?></td>
                        <td><?php echo htmlspecialchars($qcm['level_name']); ?></td>
                        <td><?php echo htmlspecialchars($qcm['course_before']); ?></td>
                        <td><?php echo htmlspecialchars($qcm['course_after']); ?></td>
                        <td><?php echo number_format($qcm['threshold'], 2); ?></td>
                        <td>
                            <a href="view_qcm.php?id=<?php echo $qcm['id']; ?>" class="btn-action view" title="Voir"><i class="fas fa-eye"></i></a>
                            <a href="edit_qcm.php?id=<?php echo $qcm['id']; ?>" class="btn-action edit" title="Modifier"><i class="fas fa-edit"></i></a>
                            <a href="delete_qcm.php?id=<?php echo $qcm['id']; ?>" class="btn-action delete" title="Archiver" onclick="return confirm('Êtes-vous sûr de vouloir archiver ce QCM ? Il sera déplacé vers les archives.');"><i class="fas fa-trash"></i></a>
                            <a href="view_qcm_submissions.php?qcm_id=<?php echo $qcm['id']; ?>" class="btn-action submissions" title="Voir les Soumissions"><i class="fas fa-list-alt"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
    <?php include '../includes/footer.php'; ?>
    <script>
        $(document).ready(function() {
            $('#qcmTable').DataTable({
                pageLength: 10,
                lengthChange: false,
                language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json' }
            });
        });
    </script>
</body>
</html>