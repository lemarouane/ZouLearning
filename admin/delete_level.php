<?php
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: manage_levels.php");
    exit;
}

$level_id = $_GET['id'];
$level = $db->query("SELECT name FROM levels WHERE id = $level_id")->fetch_assoc();
if (!$level) {
    header("Location: manage_levels.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $db->prepare("DELETE FROM levels WHERE id = ?");
    $stmt->bind_param("i", $level_id);
    $stmt->execute();

    // Log the action
    $stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (?, 'admin', 'Deleted level', ?)");
    $details = "Deleted level ID $level_id: " . $level['name'];
    $stmt->bind_param("is", $_SESSION['admin_id'], $details);
    $stmt->execute();

    header("Location: manage_levels.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer un Niveau - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">

    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-trash-alt"></i> Supprimer un Niveau</h1>
        <div class="confirmation">
            <p>Êtes-vous sûr de vouloir supprimer <strong><?php echo htmlspecialchars($level['name']); ?></strong> ? Cela supprimera également toutes les matières et cours associés.</p>
            <form method="POST">
                <button type="submit" class="btn-action delete"><i class="fas fa-trash"></i> Oui, Supprimer</button>
                <a href="manage_levels.php" class="btn-action cancel"><i class="fas fa-times"></i> Annuler</a>
            </form>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>