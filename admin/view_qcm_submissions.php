<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$qcm_id = isset($_GET['qcm_id']) ? (int)$_GET['qcm_id'] : 0;
$where_clause = $qcm_id ? "WHERE qs.qcm_id = $qcm_id" : "";

// Fetch submissions
$submissions = $db->query("
    SELECT qs.id, qs.qcm_id, qs.student_id, qs.score, qs.passed, qs.submitted_at,
           q.title AS qcm_title, s.name AS subject_name,
           st.full_name AS student_name, st.email AS student_email
    FROM qcm_submissions qs
    JOIN qcm q ON qs.qcm_id = q.id
    JOIN subjects s ON q.subject_id = s.id
    JOIN students st ON qs.student_id = st.id
    $where_clause
    ORDER BY qs.submitted_at DESC
");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voir les Soumissions QCM - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .btn-action { background: #1e3c72; color: #fff; padding: 5px 10px; border-radius: 5px; text-decoration: none; margin-right: 5px; }
        .btn-action:hover { background: #152a55; }
        .btn-back { background: #6b7280; }
        .btn-back:hover { background: #4b5563; }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-list-alt"></i> Soumissions QCM <?php echo $qcm_id ? "pour " . htmlspecialchars($submissions->fetch_assoc()['qcm_title']) : " - Toutes"; ?></h1>
        <?php if (isset($_SESSION['message'])): ?>
            <p class="success-message"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error-message"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <a href="manage_qcm.php" class="btn-action btn-back"><i class="fas fa-arrow-left"></i> Retour aux QCM</a>
        <table id="submissionTable" class="course-table">
            <thead>
                <tr>
                    <th>QCM</th>
                    <th>Étudiant</th>
                    <th>Email</th>
                    <th>Score (%)</th>
                    <th>Statut</th>
                    <th>Date Soumission</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $submissions->data_seek(0); // Reset pointer after title fetch ?>
                <?php while ($submission = $submissions->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($submission['qcm_title']); ?></td>
                        <td><?php echo htmlspecialchars($submission['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($submission['student_email']); ?></td>
                        <td><?php echo number_format($submission['score'], 2); ?></td>
                        <td><?php echo $submission['passed'] ? 'Réussi' : 'Non passé'; ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($submission['submitted_at'])); ?></td>
                        <td>
                            <a href="view_qcm_submission_details.php?id=<?php echo $submission['id']; ?><?php echo $qcm_id ? '&qcm_id=' . $qcm_id : ''; ?>" class="btn-action" title="Voir Détails"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
    <?php include '../includes/footer.php'; ?>
    <script>
        $(document).ready(function() {
            $('#submissionTable').DataTable({
                pageLength: 10,
                lengthChange: false,
                language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json' },
                order: [[5, 'desc']] // Sort by Submission Date
            });
        });
    </script>
</body>
</html>