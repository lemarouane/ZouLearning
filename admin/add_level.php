<?php
// Activer l'affichage des erreurs pour le débogage (à commenter en production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Journaliser les erreurs dans un fichier
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Charger la connexion à la base de données
try {
    require_once '../includes/db_connect.php';
    if (!$db) {
        throw new Exception("Échec de la connexion à la base de données.");
    }
} catch (Exception $e) {
    error_log("Erreur de connexion DB : " . $e->getMessage());
    die("Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        // Validation des entrées
        if (empty($name)) {
            $_SESSION['error'] = "Le nom du niveau est requis.";
            header("Location: add_level.php");
            exit;
        }

        // Échapper les entrées pour éviter les injections SQL
        $name = $db->real_escape_string($name);
        $description = $db->real_escape_string($description);

        // Insérer le niveau
        $stmt = $db->prepare("INSERT INTO levels (name, description) VALUES (?, ?)");
        if (!$stmt) {
            throw new Exception("Erreur de préparation de la requête : " . $db->error);
        }
        $stmt->bind_param("ss", $name, $description);
        if (!$stmt->execute()) {
            throw new Exception("Erreur lors de l'exécution de la requête : " . $stmt->error);
        }

        // Journaliser l'action
        $stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (?, 'admin', 'Added level', ?)");
        if (!$stmt) {
            throw new Exception("Erreur de préparation du log : " . $db->error);
        }
        $details = "Added level: $name";
        $stmt->bind_param("is", $_SESSION['admin_id'], $details);
        $stmt->execute();

        $_SESSION['message'] = "Niveau ajouté avec succès !";
        header("Location: manage_levels.php");
        exit;
    } catch (Exception $e) {
        error_log("Erreur lors de l'ajout du niveau : " . $e->getMessage());
        $_SESSION['error'] = "Erreur lors de l'ajout du niveau : " . $e->getMessage();
        header("Location: add_level.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Niveau - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-plus-circle"></i> Ajouter un Nouveau Niveau</h1>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error-message"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['message'])): ?>
            <p class="success-message"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></p>
        <?php endif; ?>
        <form method="POST" class="form-container" id="addLevelForm">
            <div class="form-group">
                <label><i class="fas fa-layer-group"></i> Nom du Niveau</label>
                <input type="text" name="name" placeholder="ex., Bac+2" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-info-circle"></i> Description</label>
                <textarea name="description" placeholder="Description optionnelle" rows="4"></textarea>
            </div>
            <button type="submit" class="btn-action" id="submitLevel"><i class="fas fa-save"></i> Ajouter le Niveau</button>
            <a href="manage_levels.php" class="btn-action cancel"><i class="fas fa-times"></i> Annuler</a>
        </form>
    </main>
    <?php include '../includes/footer.php'; ?>

    <script>
        $(document).ready(function() {
            $('#submitLevel').on('click', function(e) {
                console.log('Bouton Ajouter cliqué');
                const form = $('#addLevelForm');
                if (form[0].checkValidity()) {
                    console.log('Formulaire valide, soumission en cours...');
                    form.submit();
                } else {
                    console.log('Formulaire invalide');
                    form[0].reportValidity();
                }
            });
        });
    </script>
</body>
</html>