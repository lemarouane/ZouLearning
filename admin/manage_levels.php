<?php
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch only non-archived levels
$levels = $db->query("SELECT * FROM levels WHERE is_archived = 0 ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les Niveaux - Zouhair E-Learning</title>
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
        <h1><i class="fas fa-layer-group"></i> Gérer les Niveaux</h1>
        <div class="form-actions">
            <a href="add_level.php" class="btn-action add"><i class="fas fa-plus"></i> Ajouter un Nouveau Niveau</a>
            <a href="archive_levels.php" class="btn-action archive" style="background-color: #007bff; color: white;"><i class="fas fa-archive"></i> Voir les Archives</a>
        </div>
        <table id="levelsTable" class="display">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Créé le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($level = $levels->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $level['id']; ?></td>
                        <td><?php echo htmlspecialchars($level['name']); ?></td>
                        <td><?php echo htmlspecialchars($level['description'] ?: 'N/A'); ?></td>
                        <td><?php echo $level['created_at']; ?></td>
                        <td>
                            <a href="view_level.php?id=<?php echo $level['id']; ?>" class="btn-action view" title="Voir"><i class="fas fa-eye"></i></a>
                            <a href="edit_level.php?id=<?php echo $level['id']; ?>" class="btn-action edit" title="Modifier"><i class="fas fa-edit"></i></a>
                            <a href="delete_level.php?id=<?php echo $level['id']; ?>" class="btn-action delete" onclick="return confirm('Êtes-vous sûr de vouloir archiver ce niveau ? Il sera déplacé vers les archives.');" title="Archiver"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
    <?php include '../includes/footer.php'; ?>

    <script>
        $(document).ready(function() {
            $('#levelsTable').DataTable({
                pageLength: 10,
                lengthChange: false,
                order: [[1, 'asc']] // Trier par nom par défaut
            });
        });
    </script>
</body>
</html>