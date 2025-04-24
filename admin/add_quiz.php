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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $subject_id = (int)$_POST['subject_id'];
    $description = trim($_POST['description']);
    $start_date = trim($_POST['start_date']);
    $start_time = trim($_POST['start_time']);
    $duration_hours = floatval($_POST['duration_hours']);

    if (empty($title)) {
        $errors[] = "Le titre est requis.";
    }
    if ($subject_id <= 0) {
        $errors[] = "Veuillez sélectionner une matière.";
    }
    if (empty($start_date) || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $start_date)) {
        $errors[] = "Veuillez sélectionner une date valide (YYYY-MM-DD).";
    }
    if (empty($start_time) || !preg_match("/^\d{2}:\d{2}$/", $start_time)) {
        $errors[] = "Veuillez sélectionner une heure valide (HH:MM).";
    }
    if ($duration_hours <= 0 || $duration_hours > 24) {
        $errors[] = "La durée doit être entre 0.1 et 24 heures.";
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

    // Combine date and time (GMT+1 Morocco time)
    $start_datetime = "$start_date $start_time:00";
    if (!DateTime::createFromFormat('Y-m-d H:i:s', $start_datetime, new DateTimeZone('Africa/Casablanca'))) {
        $errors[] = "Date ou heure invalide.";
    }

    if (empty($errors)) {
        $upload_dir = '../uploads/quizzes/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $file_name = uniqid() . '_' . basename($file['name']);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $stmt = $db->prepare("
                INSERT INTO quizzes (title, subject_id, description, pdf_path, start_datetime, duration_hours, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->bind_param("sisssd", $title, $subject_id, $description, $file_path, $start_datetime, $duration_hours);
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
    <title>Ajouter un Examen - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">

    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-plus-circle"></i> Ajouter un Examen</h1>
        <?php foreach ($errors as $error): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endforeach; ?>
        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title"><i class="fas fa-heading"></i> Titre</label>
                    <input type="text" name="title" id="title" class="course-input" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="level_id"><i class="fas fa-layer-group"></i> Niveau</label>
                    <select name="level_id" id="level_id" class="course-select" required>
                        <option value="">Sélectionner un niveau</option>
                        <?php while ($level = $levels->fetch_assoc()): ?>
                            <option value="<?php echo $level['id']; ?>" <?php echo isset($_POST['level_id']) && $_POST['level_id'] == $level['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($level['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="subject_id"><i class="fas fa-book-open"></i> Matière</label>
                    <select name="subject_id" id="subject_id" class="course-select" required>
                        <option value="">Sélectionner une matière</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="description"><i class="fas fa-comment"></i> Description</label>
                    <textarea name="description" id="description" class="course-textarea"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="start_date"><i class="fas fa-calendar-alt"></i> Date de début (GMT+1)</label>
                    <input type="date" name="start_date" id="start_date" class="course-input" value="<?php echo isset($_POST['start_date']) ? htmlspecialchars($_POST['start_date']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="start_time"><i class="fas fa-clock"></i> Heure de début (GMT+1)</label>
                    <input type="time" name="start_time" id="start_time" class="course-input" value="<?php echo isset($_POST['start_time']) ? htmlspecialchars($_POST['start_time']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="duration_hours"><i class="fas fa-hourglass"></i> Durée (heures)</label>
                    <input type="number" name="duration_hours" id="duration_hours" class="course-input" step="0.1" min="0.1" max="24" value="<?php echo isset($_POST['duration_hours']) ? htmlspecialchars($_POST['duration_hours']) : '1'; ?>" required>
                </div>
                <div class="form-group">
                    <label for="pdf_file"><i class="fas fa-file-pdf"></i> Fichier PDF</label>
                    <input type="file" name="pdf_file" id="pdf_file" accept="application/pdf" required>
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