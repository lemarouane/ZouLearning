<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: manage_courses.php");
    exit;
}

$course_id = (int)$_GET['id'];
$course_query = $db->query("SELECT c.*, s.name AS subject_name FROM courses c JOIN subjects s ON c.subject_id = s.id WHERE c.id = $course_id");
if (!$course_query || $course_query->num_rows == 0) {
    header("Location: manage_courses.php");
    exit;
}
$course = $course_query->fetch_assoc();

// Fetch existing contents
$contents_query = $db->query("SELECT id, content_type, content_path FROM course_contents WHERE course_id = $course_id");
$contents = [];
while ($row = $contents_query->fetch_assoc()) {
    $contents[$row['content_type']] = ['id' => $row['id'], 'path' => $row['content_path']];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $db->real_escape_string($_POST['title']);
    $subject_id = (int)$_POST['subject_id'];
    $difficulty = $db->real_escape_string($_POST['difficulty']);

    // Update course
    $stmt = $db->prepare("UPDATE courses SET title = ?, subject_id = ?, difficulty = ? WHERE id = ?");
    $stmt->bind_param("sisi", $title, $subject_id, $difficulty, $course_id);
    $stmt->execute();

    // Handle PDF
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == 0) {
        $pdf_path = "../uploads/pdfs/" . basename($_FILES['pdf_file']['name']);
        move_uploaded_file($_FILES['pdf_file']['tmp_name'], $pdf_path);
        if (isset($contents['PDF'])) {
            $stmt = $db->prepare("UPDATE course_contents SET content_path = ? WHERE id = ?");
            $stmt->bind_param("si", $pdf_path, $contents['PDF']['id']);
        } else {
            $stmt = $db->prepare("INSERT INTO course_contents (course_id, content_type, content_path) VALUES (?, 'PDF', ?)");
            $stmt->bind_param("is", $course_id, $pdf_path);
        }
        $stmt->execute();
    } elseif (isset($_POST['remove_pdf']) && isset($contents['PDF'])) {
        $db->query("DELETE FROM course_contents WHERE id = " . (int)$contents['PDF']['id']);
        @unlink($contents['PDF']['path']);
    }

    // Handle Video
    if (!empty($_POST['video_url'])) {
        $video_url = $db->real_escape_string($_POST['video_url']);
        if (isset($contents['Video'])) {
            $stmt = $db->prepare("UPDATE course_contents SET content_path = ? WHERE id = ?");
            $stmt->bind_param("si", $video_url, $contents['Video']['id']);
        } else {
            $stmt = $db->prepare("INSERT INTO course_contents (course_id, content_type, content_path) VALUES (?, 'Video', ?)");
            $stmt->bind_param("is", $course_id, $video_url);
        }
        $stmt->execute();
    } elseif (isset($_POST['remove_video']) && isset($contents['Video'])) {
        $db->query("DELETE FROM course_contents WHERE id = " . (int)$contents['Video']['id']);
    }

    $_SESSION['message'] = "Cours modifié avec succès !";
    header("Location: manage_courses.php");
    exit;
}

$subjects = $db->query("SELECT id, name FROM subjects");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Cours - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-edit"></i> Modifier Cours</h1>
        <form method="POST" enctype="multipart/form-data" class="edit-form">
            <div class="form-group">
                <label><i class="fas fa-book"></i> Titre</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($course['title']); ?>" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-folder"></i> Matière</label>
                <select name="subject_id" required>
                    <?php while ($subject = $subjects->fetch_assoc()): ?>
                        <option value="<?php echo $subject['id']; ?>" <?php echo $course['subject_id'] == $subject['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($subject['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label><i class="fas fa-tachometer-alt"></i> Difficulté</label>
                <select name="difficulty" required>
                    <option value="Easy" <?php echo $course['difficulty'] == 'Easy' ? 'selected' : ''; ?>>Facile</option>
                    <option value="Medium" <?php echo $course['difficulty'] == 'Medium' ? 'selected' : ''; ?>>Moyen</option>
                    <option value="Hard" <?php echo $course['difficulty'] == 'Hard' ? 'selected' : ''; ?>>Difficile</option>
                </select>
            </div>
            <div class="form-group">
                <label><i class="fas fa-file-pdf"></i> Fichier PDF</label>
                <?php if (isset($contents['PDF'])): ?>
                    <p>Actuel: <?php echo htmlspecialchars(basename($contents['PDF']['path'])); ?></p>
                    <input type="checkbox" name="remove_pdf" id="remove_pdf" value="1">
                    <label for="remove_pdf">Supprimer le PDF</label><br>
                <?php endif; ?>
                <input type="file" name="pdf_file" accept=".pdf">
            </div>
            <div class="form-group">
                <label><i class="fas fa-video"></i> URL Vidéo</label>
                <?php if (isset($contents['Video'])): ?>
                    <p>Actuelle: <?php echo htmlspecialchars($contents['Video']['path']); ?></p>
                    <input type="checkbox" name="remove_video" id="remove_video" value="1">
                    <label for="remove_video">Supprimer la vidéo</label><br>
                <?php endif; ?>
                <input type="url" name="video_url" value="<?php echo isset($contents['Video']) ? htmlspecialchars($contents['Video']['path']) : ''; ?>" placeholder="https://youtube.com/watch?v=...">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-action"><i class="fas fa-save"></i> Enregistrer</button>
                <a href="manage_courses.php" class="btn-action back"><i class="fas fa-arrow-left"></i> Retour</a>
            </div>
        </form>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>