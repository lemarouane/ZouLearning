<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$levels = $db->query("SELECT id, name FROM levels ORDER BY name");
$subjects = $db->query("SELECT id, name, level_id FROM subjects ORDER BY name");
$courses = $db->query("SELECT c.id, c.title, s.level_id, s.id AS subject_id FROM courses c JOIN subjects s ON c.subject_id = s.id ORDER BY c.title");
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title'] ?? '');
    $subject_id = (int)($_POST['subject_id'] ?? 0);
    $course_before_id = (int)($_POST['course_before_id'] ?? 0);
    $course_after_id = (int)($_POST['course_after_id'] ?? 0);
    $threshold = floatval($_POST['threshold'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $questions = $_POST['questions'] ?? [];

    // Debug: Log received questions
    error_log("Received POST questions: " . print_r($questions, true));

    // Validation
    if (empty($title)) {
        $errors[] = "Le titre est requis.";
    }
    if ($subject_id <= 0) {
        $errors[] = "Veuillez sélectionner une matière.";
    }
    if ($course_before_id <= 0 || $course_after_id <= 0) {
        $errors[] = "Veuillez sélectionner les cours avant et après.";
    }
    if ($course_before_id == $course_after_id) {
        $errors[] = "Les cours avant et après doivent être différents.";
    }
    if ($threshold < 0 || $threshold > 100) {
        $errors[] = "Le seuil doit être entre 0 et 100%.";
    }
    if (empty($questions)) {
        $errors[] = "Au moins une question est requise.";
    }

    foreach ($questions as $q_index => $question) {
        if (empty(trim($question['text'] ?? ''))) {
            $errors[] = "La question " . ($q_index + 1) . " est vide.";
        }
        if (!isset($question['choices']) || count($question['choices']) != 4) {
            $errors[] = "La question " . ($q_index + 1) . " doit avoir exactement 4 choix.";
        }
        $has_correct = false;
        foreach ($question['choices'] as $c_index => $choice) {
            if (empty(trim($choice['text'] ?? ''))) {
                $errors[] = "Un choix pour la question " . ($q_index + 1) . " est vide.";
            }
            if (isset($choice['is_correct']) && $choice['is_correct'] == '1') {
                $has_correct = true;
            }
        }
        if (!$has_correct) {
            $errors[] = "La question " . ($q_index + 1) . " doit avoir au moins un choix correct.";
        }
    }

    if (empty($errors)) {
        $db->begin_transaction();
        try {
            // Insert QCM
            $stmt = $db->prepare("
                INSERT INTO qcm (title, subject_id, course_before_id, course_after_id, threshold, description, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->bind_param("siiids", $title, $subject_id, $course_before_id, $course_after_id, $threshold, $description);
            $stmt->execute();
            $qcm_id = $db->insert_id;
            $stmt->close();

            // Insert Questions
            foreach ($questions as $q_index => $question) {
                $question_text = trim($question['text']);
                $order = $q_index + 1;
                $stmt = $db->prepare("
                    INSERT INTO qcm_questions (qcm_id, question_text, `order`, created_at)
                    VALUES (?, ?, ?, NOW())
                ");
                $stmt->bind_param("isi", $qcm_id, $question_text, $order);
                if (!$stmt->execute()) {
                    throw new Exception("Erreur lors de l'insertion de la question " . ($q_index + 1));
                }
                $question_id = $db->insert_id;
                $stmt->close();

                // Insert Choices
                foreach ($question['choices'] as $c_index => $choice) {
                    $choice_text = trim($choice['text']);
                    $is_correct = isset($choice['is_correct']) && $choice['is_correct'] == '1' ? 1 : 0;
                    $stmt = $db->prepare("
                        INSERT INTO qcm_choices (question_id, choice_text, is_correct, created_at)
                        VALUES (?, ?, ?, NOW())
                    ");
                    $stmt->bind_param("isi", $question_id, $choice_text, $is_correct);
                    if (!$stmt->execute()) {
                        throw new Exception("Erreur lors de l'insertion du choix " . ($c_index + 1) . " pour la question " . ($q_index + 1));
                    }
                    $stmt->close();
                }
            }

            $db->commit();
            $_SESSION['message'] = "QCM ajouté avec succès.";
            header("Location: manage_qcm.php");
            exit;
        } catch (Exception $e) {
            $db->rollback();
            $errors[] = "Erreur lors de l'ajout du QCM : " . $e->getMessage();
            error_log("QCM insertion error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un QCM - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-plus-circle"></i> Ajouter un QCM</h1>
        <?php foreach ($errors as $error): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endforeach; ?>
        <div class="form-container">
            <form method="POST" id="qcmForm">
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
                    <label for="course_before_id"><i class="fas fa-book"></i> Cours Avant</label>
                    <select name="course_before_id" id="course_before_id" class="course-select" required>
                        <option value="">Sélectionner un cours</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="course_after_id"><i class="fas fa-book"></i> Cours Après</label>
                    <select name="course_after_id" id="course_after_id" class="course-select" required>
                        <option value="">Sélectionner un cours</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="threshold"><i class="fas fa-percentage"></i> Seuil de réussite (%)</label>
                    <input type="number" name="threshold" id="threshold" class="course-input" step="0.1" min="0" max="100" value="<?php echo isset($_POST['threshold']) ? htmlspecialchars($_POST['threshold']) : '70'; ?>" required>
                </div>
                <div class="form-group">
                    <label for="description"><i class="fas fa-comment"></i> Description</label>
                    <textarea name="description" id="description" class="course-textarea"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>
                <div id="questions-container">
                    <h2><i class="fas fa-question"></i> Questions</h2>
                    <div class="question-block" data-question-index="0">
                        <div class="form-group">
                            <label>Question 1</label>
                            <textarea name="questions[0][text]" class="course-textarea" required><?php echo isset($_POST['questions'][0]['text']) ? htmlspecialchars($_POST['questions'][0]['text']) : ''; ?></textarea>
                        </div>
                        <div class="choices-container">
                            <?php for ($c = 0; $c < 4; $c++): ?>
                                <div class="choice-block">
                                    <input type="text" name="questions[0][choices][<?php echo $c; ?>][text]" class="course-input" placeholder="Choix <?php echo $c + 1; ?>" value="<?php echo isset($_POST['questions'][0]['choices'][$c]['text']) ? htmlspecialchars($_POST['questions'][0]['choices'][$c]['text']) : ''; ?>" required>
                                    <label><input type="checkbox" name="questions[0][choices][<?php echo $c; ?>][is_correct]" value="1" <?php echo isset($_POST['questions'][0]['choices'][$c]['is_correct']) && $_POST['questions'][0]['choices'][$c]['is_correct'] == '1' ? 'checked' : ''; ?>> Correct</label>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
                <button type="button" class="add-question-btn"><i class="fas fa-plus"></i> Ajouter une question</button>
                <div class="form-actions">
                    <button type="submit" class="save-course-btn"><i class="fas fa-save"></i> Enregistrer</button>
                    <a href="manage_qcm.php" class="btn-action cancel"><i class="fas fa-times"></i> Annuler</a>
                </div>
            </form>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
    <script>
        $(document).ready(function() {
            const subjects = <?php echo json_encode($subjects->fetch_all(MYSQLI_ASSOC)); ?>;
            const courses = <?php echo json_encode($courses->fetch_all(MYSQLI_ASSOC)); ?>;
            let questionIndex = 1;

            // Populate subjects based on level
            $('#level_id').change(function() {
                const levelId = $(this).val();
                $('#subject_id').empty().append('<option value="">Sélectionner une matière</option>');
                $('#course_before_id, #course_after_id').empty().append('<option value="">Sélectionner un cours</option>');
                if (levelId) {
                    subjects.forEach(subject => {
                        if (subject.level_id == levelId) {
                            $('#subject_id').append(`<option value="${subject.id}">${subject.name}</option>`);
                        }
                    });
                }
            });

            // Populate courses based on subject
            $('#subject_id').change(function() {
                const subjectId = $(this).val();
                $('#course_before_id, #course_after_id').empty().append('<option value="">Sélectionner un cours</option>');
                if (subjectId) {
                    courses.forEach(course => {
                        if (course.subject_id == subjectId) {
                            $('#course_before_id').append(`<option value="${course.id}">${course.title}</option>`);
                            $('#course_after_id').append(`<option value="${course.id}">${course.title}</option>`);
                        }
                    });
                }
            });

            // Add new question
            $('.add-question-btn').click(function() {
                const questionBlock = `
                    <div class="question-block" data-question-index="${questionIndex}">
                        <div class="form-group">
                            <label>Question ${questionIndex + 1}</label>
                            <textarea name="questions[${questionIndex}][text]" class="course-textarea" required></textarea>
                        </div>
                        <div class="choices-container">
                            <div class="choice-block">
                                <input type="text" name="questions[${questionIndex}][choices][0][text]" class="course-input" placeholder="Choix 1" required>
                                <label><input type="checkbox" name="questions[${questionIndex}][choices][0][is_correct]" value="1"> Correct</label>
                            </div>
                            <div class="choice-block">
                                <input type="text" name="questions[${questionIndex}][choices][1][text]" class="course-input" placeholder="Choix 2" required>
                                <label><input type="checkbox" name="questions[${questionIndex}][choices][1][is_correct]" value="1"> Correct</label>
                            </div>
                            <div class="choice-block">
                                <input type="text" name="questions[${questionIndex}][choices][2][text]" class="course-input" placeholder="Choix 3" required>
                                <label><input type="checkbox" name="questions[${questionIndex}][choices][2][is_correct]" value="1"> Correct</label>
                            </div>
                            <div class="choice-block">
                                <input type="text" name="questions[${questionIndex}][choices][3][text]" class="course-input" placeholder="Choix 4" required>
                                <label><input type="checkbox" name="questions[${questionIndex}][choices][3][is_correct]" value="1"> Correct</label>
                            </div>
                        </div>
                    </div>`;
                $('#questions-container').append(questionBlock);
                questionIndex++;
            });

            // Initialize form with existing values if available
            if ($('#level_id').val()) {
                $('#level_id').trigger('change');
                if ($('#subject_id').val()) {
                    $('#subject_id').trigger('change');
                }
            }
        });
    </script>
    <style>
        .question-block { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9; }
        .choices-container { margin-top: 10px; }
        .choice-block { margin: 10px 0; display: flex; align-items: center; gap: 10px; }
        .course-input, .course-textarea { width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; }
        .add-question-btn { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .add-question-btn:hover { background: #218838; }
    </style>
</body>
</html>