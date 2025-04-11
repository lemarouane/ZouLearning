<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$subjects = $db->query("SELECT s.id, s.name, l.name AS level_name FROM subjects s JOIN levels l ON s.level_id = l.id ORDER BY s.name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $db->real_escape_string($_POST['title']);
    $subject_id = (int)$_POST['subject_id'];
    $difficulty = $db->real_escape_string($_POST['difficulty']);

    // Insert course into courses table
    $stmt = $db->prepare("INSERT INTO courses (title, subject_id, difficulty) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $title, $subject_id, $difficulty);
    $stmt->execute();
    $course_id = $db->insert_id;

    // Handle PDF upload
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/pdfs/';
        $content_path = $upload_dir . basename($_FILES['pdf_file']['name']);
        move_uploaded_file($_FILES['pdf_file']['tmp_name'], $content_path);
        $stmt = $db->prepare("INSERT INTO course_contents (course_id, content_type, content_path) VALUES (?, 'PDF', ?)");
        $stmt->bind_param("is", $course_id, $content_path);
        $stmt->execute();
    }

    // Handle Video URL
    if (!empty($_POST['video_url'])) {
        $content_path = $db->real_escape_string($_POST['video_url']);
        $stmt = $db->prepare("INSERT INTO course_contents (course_id, content_type, content_path) VALUES (?, 'Video', ?)");
        $stmt->bind_param("is", $course_id, $content_path);
        $stmt->execute();
    }

    // Log the action
    $stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (?, 'admin', 'Added course', ?)");
    $details = "Added course: $title for subject ID $subject_id";
    $stmt->bind_param("is", $_SESSION['admin_id'], $details);
    $stmt->execute();

    $_SESSION['message'] = "Cours ajouté avec succès !";
    header("Location: manage_courses.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Cours - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-plus-circle"></i> Ajouter un Nouveau Cours</h1>
        <form method="POST" class="form-container" enctype="multipart/form-data">
            <div class="form-group">
                <label><i class="fas fa-book"></i> Titre du Cours</label>
                <input type="text" name="title" placeholder="ex., Bases d'Algèbre" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-book-open"></i> Matière</label>
                <select name="subject_id" required>
                    <?php while ($sub = $subjects->fetch_assoc()): ?>
                        <option value="<?php echo $sub['id']; ?>"><?php echo htmlspecialchars($sub['name'] . " (" . $sub['level_name'] . ")"); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label><i class="fas fa-tachometer-alt"></i> Difficulté</label>
                <select name="difficulty" required>
                    <option value="Easy">Facile</option>
                    <option value="Medium">Moyen</option>
                    <option value="Hard">Difficile</option>
                </select>
            </div>
            <div class="form-group">
                <label><i class="fas fa-file-pdf"></i> Fichier PDF (facultatif)</label>
                <input type="file" name="pdf_file" accept=".pdf">
            </div>
            <div class="form-group">
                <label><i class="fas fa-video"></i> URL Vidéo (facultatif)</label>
                <input type="url" name="video_url" placeholder="ex., https://youtube.com/watch?v=xyz">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-action"><i class="fas fa-save"></i> Ajouter le Cours</button>
                <a href="manage_courses.php" class="btn-action cancel"><i class="fas fa-times"></i> Annuler</a>
            </div>
        </form>
    </main>
    <?php include '../includes/footer.php'; ?>

    <script>
        $(document).ready(function() {
            // No need for content type toggle anymore—both can coexist
        });
    </script>
</body>
</html>