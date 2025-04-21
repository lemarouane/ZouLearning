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
$level = $db->query("SELECT * FROM levels WHERE id = $level_id")->fetch_assoc();
if (!$level) {
    header("Location: manage_levels.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];

    $stmt = $db->prepare("UPDATE levels SET name = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $description, $level_id);
    $stmt->execute();

    // Log the action
    $stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (?, 'admin', 'Edited level', ?)");
    $details = "Edited level ID $level_id: $name";
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
    <title>Modifier un Niveau - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">

    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-edit"></i> Modifier un Niveau</h1>
        <form method="POST" class="form-container">
            <div class="form-group">
                <label><i class="fas fa-layer-group"></i> Nom du Niveau</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($level['name']); ?>" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-info-circle"></i> Description</label>
                <textarea name="description" rows="4"><?php echo htmlspecialchars($level['description'] ?: ''); ?></textarea>
            </div>
            <button type="submit" class="btn-action"><i class="fas fa-save"></i> Enregistrer les Modifications</button>
            <a href="manage_levels.php" class="btn-action cancel"><i class="fas fa-times"></i> Annuler</a>
        </form>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>