<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch only non-archived students
$students = $db->query("SELECT s.*, l.name AS level_name FROM students s LEFT JOIN levels l ON s.level_id = l.id WHERE s.is_archived = 0 ORDER BY s.created_at DESC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les Étudiants - Zouhair E-Learning</title>
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
        <h1><i class="fas fa-users"></i> Gérer les Étudiants</h1>
        <?php if (isset($_GET['success'])): ?>
            <div class="success-message"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>
        <div class="form-actions">
            <a href="archive_students.php" class="btn-action archive" style="background-color: #007bff; color: white;"><i class="fas fa-archive"></i> Voir les Archives</a>
        </div>
        <table id="studentsTable" class="display">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Statut</th>
                    <th>Niveau</th>
                    <th>Inscrit</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($student = $students->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $student['id']; ?></td>
                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                        <td><?php echo ucfirst($student['status']); ?></td>
                        <td><?php echo $student['level_name'] ? htmlspecialchars($student['level_name']) : 'N/A'; ?></td>
                        <td><?php echo $student['created_at']; ?></td>
                        <td>
                            <a href="view_student.php?id=<?php echo $student['id']; ?>" class="btn-action view" title="Voir"><i class="fas fa-eye"></i></a>
                            <a href="edit_student.php?id=<?php echo $student['id']; ?>" class="btn-action edit" title="Modifier"><i class="fas fa-edit"></i></a>
                            <a href="delete_student.php?id=<?php echo $student['id']; ?>" class="btn-action delete" title="Archiver" onclick="return confirm('Êtes-vous sûr de vouloir archiver cet étudiant ? Il sera déplacé vers les archives.');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
    <?php include '../includes/footer.php'; ?>

    <script>
        $(document).ready(function() {
            $('#studentsTable').DataTable({ 
                pageLength: 10, 
                lengthChange: false,
                language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json' }
            });
        });
    </script>
</body>
</html>