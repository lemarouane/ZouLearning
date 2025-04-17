<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = (int)$_SESSION['student_id'];

// Fetch student data with prepared statement
$stmt = $db->prepare("SELECT full_name, email, password FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    $_SESSION['error'] = "Utilisateur non trouvé. Veuillez vous reconnecter.";
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $db->real_escape_string($_POST['full_name']);
    $email = $db->real_escape_string($_POST['email']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $student['password'];

    $stmt = $db->prepare("UPDATE students SET full_name = ?, email = ?, password = ? WHERE id = ?");
    $stmt->bind_param("sssi", $full_name, $email, $password, $student_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Profil mis à jour avec succès !";
    } else {
        $_SESSION['error'] = "Erreur lors de la mise à jour du profil.";
    }
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
    <style>
        .dashboard {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1.5rem;
            font-family: 'Inter', sans-serif;
            background: #f5f8fc;
            color: #2d3748;
        }
        h1 {
            font-size: 1.5rem;
            color: #1e3c72;
            margin-bottom: 1.25rem;
        }
        .edit-form {
            background: #fff;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 600px;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            font-size: 0.9rem;
            color: #1e3c72;
            margin-bottom: 0.5rem;
        }
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
        }
        .btn-action {
            background: #1e3c72;
            color: #fff;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
        }
        .btn-action:hover {
            background: #152a55;
        }
        .success-message {
            color: #4caf50;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .error-message {
            color: #e53e3e;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        /* Support Arabic text */
        input[name="full_name"] {
            direction: ltr; /* Adjust based on input; use 'rtl' if Arabic names are common */
        }
    </style>
</head>
<body>
    <?php include '../includes/student_header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-user"></i> Mon Profil</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <p class="success-message"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error-message"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form method="POST" class="edit-form">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Nom Complet</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($student['full_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($student['email'] ?? ''); ?>" required>
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