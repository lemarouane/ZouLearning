<?php
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: manage_subjects.php");
    exit;
}

$subject_id = $_GET['id'];
$subject = $db->query("SELECT * FROM subjects WHERE id = $subject_id")->fetch_assoc();
if (!$subject) {
    header("Location: manage_subjects.php");
    exit;
}

$levels = $db->query("SELECT * FROM levels WHERE is_archived = 0 ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $level_id = $_POST['level_id'];

    $stmt = $db->prepare("UPDATE subjects SET name = ?, level_id = ? WHERE id = ?");
    $stmt->bind_param("sii", $name, $level_id, $subject_id);
    $stmt->execute();

    // Log the action
    $stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (?, 'admin', 'Edited subject', ?)");
    $details = "Edited subject ID $subject_id: $name";
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
    <title>Modifier une Matière - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">

    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-edit"></i> Modifier une Matière</h1>
        <form method="POST" class="form-container">
            <div class="form-group">
                <label><i class="fas fa-book-open"></i> Nom de la Matière</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($subject['name']); ?>" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-layer-group"></i> Niveau</label>
                <select name="level_id" required>
                    <?php while ($level = $levels->fetch_assoc()): ?>
                        <option value="<?php echo $level['id']; ?>" <?php echo $level['id'] == $subject['level_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($level['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn-action"><i class="fas fa-save"></i> Enregistrer les Modifications</button>
            <a href="manage_subjects.php" class="btn-action cancel"><i class="fas fa-times"></i> Annuler</a>
        </form>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>