<?php
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch only archived levels
$archived_levels = $db->query("SELECT * FROM levels WHERE is_archived = 1 ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archives des Niveaux - Zouhair E-Learning</title>
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
        <h1><i class="fas fa-archive"></i> Archives des Niveaux</h1>
        <a href="manage_levels.php" class="btn-action back"><i class="fas fa-arrow-left"></i> Retour aux Niveaux</a>
        <table id="archivedLevelsTable" class="display">
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
                <?php while ($level = $archived_levels->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $level['id']; ?></td>
                        <td><?php echo htmlspecialchars($level['name']); ?></td>
                        <td><?php echo htmlspecialchars($level['description'] ?: 'N/A'); ?></td>
                        <td><?php echo $level['created_at']; ?></td>
                        <td>
                            <a href="view_level.php?id=<?php echo $level['id']; ?>" class="btn-action view" title="Voir"><i class="fas fa-eye"></i></a>
                            <a href="restore_level.php?id=<?php echo $level['id']; ?>" class="btn-action restore" onclick="return confirm('Êtes-vous sûr de vouloir restaurer ce niveau ? Il sera remis dans la liste principale.');" title="Restaurer"><i class="fas fa-undo"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
    <?php include '../includes/footer.php'; ?>

    <script>
        $(document).ready(function() {
            $('#archivedLevelsTable').DataTable({
                pageLength: 10,
                lengthChange: false,
                order: [[1, 'asc']] // Trier par nom par défaut
            });
        });
    </script>
</body>
</html>