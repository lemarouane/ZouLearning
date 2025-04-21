<?php
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$levels = $db->query("SELECT * FROM levels ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $level_id = $_POST['level_id'];

    $stmt = $db->prepare("INSERT INTO subjects (name, level_id) VALUES (?, ?)");
    $stmt->bind_param("si", $name, $level_id);
    $stmt->execute();

    // Log the action
    $stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (?, 'admin', 'Added subject', ?)");
    $details = "Added subject: $name for level ID $level_id";
    $stmt->bind_param("is", $_SESSION['admin_id'], $details);
    $stmt->execute();

    header("Location: manage_subjects.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Matière - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">

    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-plus-circle"></i> Ajouter une Nouvelle Matière</h1>
        <form method="POST" class="form-container">
            <div class="form-group">
                <label><i class="fas fa-book-open"></i> Nom de la Matière</label>
                <input type="text" name="name" placeholder="ex. : Maths" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-layer-group"></i> Niveau</label>
                <select name="level_id" required>
                    <?php while ($level = $levels->fetch_assoc()): ?>
                        <option value="<?php echo $level['id']; ?>"><?php echo htmlspecialchars($level['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn-action"><i class="fas fa-save"></i> Ajouter la Matière</button>
            <a href="manage_subjects.php" class="btn-action cancel"><i class="fas fa-times"></i> Annuler</a>
        </form>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>