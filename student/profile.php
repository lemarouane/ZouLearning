<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = (int)$_SESSION['student_id'];
$student = $db->query("SELECT * FROM students WHERE id = $student_id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $db->real_escape_string($_POST['full_name']);
    $email = $db->real_escape_string($_POST['email']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $student['password'];

    $stmt = $db->prepare("UPDATE students SET full_name = ?, email = ?, password = ? WHERE id = ?");
    $stmt->bind_param("sssi", $full_name, $email, $password, $student_id);
    $stmt->execute();

    $_SESSION['message'] = "Profil mis à jour avec succès !";
    header("Location: profile.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/student_header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-user"></i> Mon Profil</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <p style="color: #4caf50;"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
        <?php endif; ?>
        <form method="POST" class="edit-form">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Nom Complet</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($student['full_name']); ?>" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Nouveau Mot de Passe (laisser vide pour ne pas changer)</label>
                <input type="password" name="password">
            </div>
            <button type="submit" class="btn-action"><i class="fas fa-save"></i> Enregistrer</button>
        </form>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>