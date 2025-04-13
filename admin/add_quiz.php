<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$levels = $db->query("SELECT id, name FROM levels ORDER BY name");
$subjects = $db->query("SELECT id, name, level_id FROM subjects ORDER BY name");
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $subject_id = (int)$_POST['subject_id'];
    $description = trim($_POST['description']);
    
    if (empty($title)) {
        $errors[] = "Le titre est requis.";
    }
    if ($subject_id <= 0) {
        $errors[] = "Veuillez sélectionner une matière.";
    }
    if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] == UPLOAD_ERR_NO_FILE) {
        $errors[] = "Veuillez uploader un fichier PDF.";
    } else {
        $file = $_FILES['pdf_file'];
        $allowed_types = ['application/pdf'];
        $max_size = 5 * 1024 * 1024; // 5MB
        if (!in_array($file['type'], $allowed_types) || $file['size'] > $max_size) {
            $errors[] = "Le fichier doit être un PDF de moins de 5 Mo.";
        }
    }

    if (empty($errors)) {
        $upload_dir = '../uploads/quizzes/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $file_name = uniqid() . '_' . basename($file['name']);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $stmt = $db->prepare("INSERT INTO quizzes (title, subject_id, description, pdf_path) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siss", $title, $subject_id, $description, $file_path);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Quiz ajouté avec succès.";
                header("Location: manage_quizzes.php");
                exit;
            } else {
                $errors[] = "Erreur lors de l'ajout du quiz.";
                unlink($file_path);
            }
        } else {
            $errors[] = "Erreur lors de l'upload du fichier.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Quiz - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-plus-circle"></i> Ajouter un Quiz</h1>
        <?php foreach ($errors as $error): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endforeach; ?>
        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title"><i class="fas fa-heading"></i> Titre</label>
                    <input type="text" name="title" id="title" class="course-input" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="level_id"><i class="fas fa-layer-group"></i> Niveau</label>
                    <select name="level_id" id="level_id" class="course-select">
                        <option value="">Sélectionner un niveau</option>
                        <?php while ($level = $levels->fetch_assoc()): ?>
                            <option value="<?php echo $level['id']; ?>" <?php echo isset($_POST['level_id']) && $_POST['level_id'] == $level['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($level['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="subject_id"><i class="fas fa-book-open"></i> Matière</label>
                    <select name="subject_id" id="subject_id" class="course-select">
                        <option value="">Sélectionner une matière</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="description"><i class="fas fa-comment"></i> Description</label>
                    <textarea name="description" id="description" class="course-textarea"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="pdf_file"><i class="fas fa-file-pdf"></i> Fichier PDF</label>
                    <input type="file" name="pdf_file" id="pdf_file" accept="application/pdf">
                </div>
                <div class="form-actions">
                    <button type="submit" class="save-course-btn"><i class="fas fa-save"></i> Enregistrer</button>
                    <a href="manage_quizzes.php" class="btn-action cancel"><i class="fas fa-times"></i> Annuler</a>
                </div>
            </form>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
    <script>
        $(document).ready(function() {
            const subjects = <?php echo json_encode($subjects->fetch_all(MYSQLI_ASSOC)); ?>;
            $('#level_id').change(function() {
                const levelId = $(this).val();
                $('#subject_id').empty().append('<option value="">Sélectionner une matière</option>');
                if (levelId) {
                    subjects.forEach(subject => {
                        if (subject.level_id == levelId) {
                            $('#subject_id').append(`<option value="${subject.id}">${subject.name}</option>`);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>