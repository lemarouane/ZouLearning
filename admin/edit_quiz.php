<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: manage_quizzes.php");
    exit;
}

$quiz_id = (int)$_GET['id'];
$quiz = $db->query("
    SELECT q.*, s.level_id
    FROM quizzes q
    JOIN subjects s ON q.subject_id = s.id
    WHERE q.id = $quiz_id
")->fetch_assoc();

if (!$quiz) {
    header("Location: manage_quizzes.php");
    exit;
}

$levels = $db->query("SELECT id, name FROM levels ORDER BY name");
$subjects = $db->query("SELECT id, name, level_id FROM subjects ORDER BY name");
$errors = [];

// Parse start_datetime
$start_datetime = new DateTime($quiz['start_datetime'], new DateTimeZone('Africa/Casablanca'));
$start_date = $start_datetime->format('Y-m-d');
$start_time = $start_datetime->format('H:i');

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

    // Combine date and time
    $start_datetime_str = "$start_date $start_time:00";
    $start_datetime_obj = DateTime::createFromFormat('Y-m-d H:i:s', $start_datetime_str, new DateTimeZone('Africa/Casablanca'));
    if (!$start_datetime_obj) {
        $errors[] = "Date ou heure invalide.";
    } else {
        $start_datetime_formatted = $start_datetime_obj->format('Y-m-d H:i:s');
    }

    $file_path = $quiz['pdf_path'];
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] != UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['pdf_file'];
        $allowed_types = ['application/pdf'];
        $max_size = 5 * 1024 * 1024; // 5MB
        if (!in_array($file['type'], $allowed_types) || $file['size'] > $max_size) {
            $errors[] = "Le fichier doit être un PDF de moins de 5 Mo.";
        } else {
            $upload_dir = '../Uploads/quizzes/';
            $file_name = uniqid() . '_' . basename($file['name']);
            $new_file_path = $upload_dir . $file_name;
            if (move_uploaded_file($file['tmp_name'], $new_file_path)) {
                $file_path = $new_file_path;
                if (file_exists($quiz['pdf_path'])) {
                    unlink($quiz['pdf_path']);
                }
            } else {
                $errors[] = "Erreur lors de l'upload du fichier.";
            }
        }
    }

    if (empty($errors)) {
        $stmt = $db->prepare("
            UPDATE quizzes 
            SET title = ?, subject_id = ?, description = ?, pdf_path = ?, start_datetime = ?, duration_hours = ?
            WHERE id = ?
        ");
        $stmt->bind_param("sissdsi", $title, $subject_id, $description, $file_path, $start_datetime_formatted, $duration_hours, $quiz_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Quiz modifié avec succès.";
            header("Location: manage_quizzes.php");
            exit;
        } else {
            $errors[] = "Erreur lors de la modification du quiz.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Quiz - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-edit"></i> Modifier un Quiz</h1>
        <?php foreach ($errors as $error): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endforeach; ?>
        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title"><i class="fas fa-heading"></i> Titre</label>
                    <input type="text" name="title" id="title" class="course-input" value="<?php echo htmlspecialchars($quiz['title']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="level_id"><i class="fas fa-layer-group"></i> Niveau</label>
                    <select name="level_id" id="level_id" class="course-select" required>
                        <option value="">Sélectionner un niveau</option>
                        <?php while ($level = $levels->fetch_assoc()): ?>
                            <option value="<?php echo $level['id']; ?>" <?php echo $quiz['level_id'] == $level['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($level['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="subject_id"><i class="fas fa-book-open"></i> Matière</label>
                    <select name="subject_id" id="subject_id" class="course-select" required>
                        <option value="">Sélectionner une matière</option>
                        <?php
                        $subjects->data_seek(0);
                        while ($subject = $subjects->fetch_assoc()):
                            if ($subject['level_id'] == $quiz['level_id']):
                        ?>
                            <option value="<?php echo $subject['id']; ?>" <?php echo $quiz['subject_id'] == $subject['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($subject['name']); ?></option>
                        <?php endif; endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="description"><i class="fas fa-comment"></i> Description</label>
                    <textarea name="description" id="description" class="course-textarea"><?php echo htmlspecialchars($quiz['description']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="start_date"><i class="fas fa-calendar-alt"></i> Date de début (GMT+1)</label>
                    <input type="date" name="start_date" id="start_date" class="course-input" value="<?php echo htmlspecialchars($start_date); ?>" required>
                </div>
                <div class="form-group">
                    <label for="start_time"><i class="fas fa-clock"></i> Heure de début (GMT+1)</label>
                    <input type="time" name="start_time" id="start_time" class="course-input" value="<?php echo htmlspecialchars($start_time); ?>" required>
                </div>
                <div class="form-group">
                    <label for="duration_hours"><i class="fas fa-hourglass"></i> Durée (heures)</label>
                    <input type="number" name="duration_hours" id="duration_hours" class="course-input" step="0.1" min="0.1" max="24" value="<?php echo htmlspecialchars($quiz['duration_hours']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="pdf_file"><i class="fas fa-file-pdf"></i> Fichier PDF (laisser vide pour conserver l'actuel)</label>
                    <input type="file" name="pdf_file" id="pdf_file" accept="application/pdf">
                    <p class="mt-2"><a href="../includes/serve_quiz_pdf.php?quiz_id=<?php echo $quiz['id']; ?>" target="_blank">Voir le PDF actuel</a></p>
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