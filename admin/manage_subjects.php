<?php
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$subjects = $db->query("SELECT s.*, l.name AS level_name FROM subjects s JOIN levels l ON s.level_id = l.id ORDER BY s.name ASC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les Matières - Zouhair E-Learning</title>
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
        <h1><i class="fas fa-book-open"></i> Gérer les Matières</h1>
        <a href="add_subject.php" class="btn-action add"><i class="fas fa-plus"></i> Ajouter une Nouvelle Matière</a>
        <table id="subjectsTable" class="display">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Niveau</th>
                    <th>Créé le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($subject = $subjects->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $subject['id']; ?></td>
                        <td><?php echo htmlspecialchars($subject['name']); ?></td>
                        <td><?php echo htmlspecialchars($subject['level_name']); ?></td>
                        <td><?php echo $subject['created_at']; ?></td>
                        <td>
                            <a href="view_subject.php?id=<?php echo $subject['id']; ?>" class="btn-action view" title="Voir"><i class="fas fa-eye"></i></a>
                            <a href="edit_subject.php?id=<?php echo $subject['id']; ?>" class="btn-action edit" title="Modifier"><i class="fas fa-edit"></i></a>
                            <a href="delete_subject.php?id=<?php echo $subject['id']; ?>" class="btn-action delete" onclick="return confirm('Êtes-vous sûr ?');" title="Supprimer"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
    <?php include '../includes/footer.php'; ?>

    <script>
        $(document).ready(function() {
            $('#subjectsTable').DataTable({
                pageLength: 10,
                lengthChange: false,
                order: [[1, 'asc']] // Trier par nom par défaut
            });
        });
    </script>
</body>
</html>