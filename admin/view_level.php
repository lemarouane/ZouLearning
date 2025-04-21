<?php
require_once '../includes/db_connect.php';
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Validate and sanitize the level ID from the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_levels.php");
    exit;
}

$level_id = intval($_GET['id']); // Sanitize the level ID

// Fetch the level details
$level_query = $db->prepare("SELECT * FROM levels WHERE id = ?");
$level_query->bind_param("i", $level_id);
$level_query->execute();
$level = $level_query->get_result()->fetch_assoc();

if (!$level) {
    header("Location: manage_levels.php");
    exit;
}

// Fetch associated subjects for the level
$subjects_query = $db->prepare("SELECT name FROM subjects WHERE level_id = ?");
$subjects_query->bind_param("i", $level_id);
$subjects_query->execute();
$subjects = $subjects_query->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voir le Niveau - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">

    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-layer-group"></i> Détails du Niveau</h1>
        <div class="detail-card">
            <h3><i class="fas fa-info"></i> Informations sur le Niveau</h3>
            <p><strong>Nom :</strong> <?php echo htmlspecialchars($level['name']); ?></p>
            <p><strong>Description :</strong> <?php echo htmlspecialchars($level['description'] ?: 'N/A'); ?></p>
            <p><strong>Créé le :</strong> <?php echo $level['created_at']; ?></p>
        </div>
        <div class="detail-card">
            <h3><i class="fas fa-book-open"></i> Matières Associées</h3>
            <ul>
                <?php if ($subjects->num_rows > 0): ?>
                    <?php while ($sub = $subjects->fetch_assoc()): ?>
                        <li><?php echo htmlspecialchars($sub['name']); ?></li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>Aucune matière assignée à ce niveau.</li>
                <?php endif; ?>
            </ul>
        </div>
        <a href="manage_levels.php" class="btn-action back"><i class="fas fa-arrow-left"></i> Retour aux Niveaux</a>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>