<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: courses.php");
    exit;
}

$qcm_id = (int)$_GET['id'];
$student_id = (int)$_SESSION['student_id'];

// Verify QCM exists and student has access
$qcm = $db->query("
    SELECT q.*, s.name AS subject_name
    FROM qcm q
    JOIN subjects s ON q.subject_id = s.id
    JOIN student_subjects ss ON s.id = ss.subject_id
    WHERE q.id = $qcm_id AND ss.student_id = $student_id
")->fetch_assoc();

if (!$qcm) {
    header("Location: courses.php");
    exit;
}

$questions = $db->query("
    SELECT id, question_text, `order`
    FROM qcm_questions
    WHERE qcm_id = $qcm_id
    ORDER BY `order`
")->fetch_all(MYSQLI_ASSOC);

$choices = [];
foreach ($questions as $question) {
    $choices_result = $db->query("
        SELECT id, choice_text
        FROM qcm_choices
        WHERE question_id = {$question['id']}
    ");
    $question_choices = $choices_result->fetch_all(MYSQLI_ASSOC);
    shuffle($question_choices); // Randomize choices
    $choices[$question['id']] = $question_choices;
}

// Base URL for images (adjust if needed)
$base_url = '/ZouLearning'; // Update to your domain or path if different
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passer QCM - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/admin.css?v=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .qcm-form { margin: 20px 0; }
        .question-block { margin: 15px 0; padding: 15px; border: 1px solid #ccc; border-radius: 5px; background: #fff; }
        .question-content img { max-width: 800px !important; height: auto; border-radius: 5px; margin: 10px 0; }
        .question-content .image-error { color: #f44336; font-style: italic; }
        .choice-item { margin: 5px 0; display: flex; align-items: center; gap: 10px; }
        .error-message { color: #f44336; font-weight: bold; margin-top: 10px; }
        body.dark-mode .question-block { background: #2d3748; border-color: #4a5568; }
        body.dark-mode .question-content img { border: 1px solid #718096; }
    </style>
</head>
<body>
    <?php include '../includes/student_header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-question-circle"></i> QCM: <?php echo htmlspecialchars($qcm['title']); ?></h1>
        <p><strong>Matière:</strong> <?php echo htmlspecialchars($qcm['subject_name']); ?></p>
        <p><strong>Seuil de réussite:</strong> <?php echo number_format($qcm['threshold'], 2); ?>%</p>
        <?php if ($qcm['description']): ?>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($qcm['description']); ?></p>
        <?php endif; ?>
        <form method="POST" action="submit_qcm.php" class="qcm-form" id="qcmForm">
            <input type="hidden" name="qcm_id" value="<?php echo $qcm_id; ?>">
            <?php foreach ($questions as $question): ?>
                <div class="question-block">
                    <h4>Question <?php echo $question['order']; ?>:</h4>
                    <div class="question-content">
                        <?php
                        // Render question_text as HTML
                        $question_text = $question['question_text'];
                        // Fix image paths if needed
                        $question_text = preg_replace(
                            "/src='\/Uploads\/qcm_images\//",
                            "src='$base_url/Uploads/qcm_images/",
                            $question_text
                        );
                        // Remove inline styles from img tags
                        $question_text = preg_replace(
                            "/(<img[^>]+)style='[^']*'([^>]*>)/i",
                            "$1$2",
                            $question_text
                        );
                        // Check for missing images
                        if (preg_match("/<img src='([^']+)'/", $question_text, $match)) {
                            $img_path = $_SERVER['DOCUMENT_ROOT'] . parse_url($match[1], PHP_URL_PATH);
                            if (!file_exists($img_path)) {
                                $question_text = str_replace($match[0], '<span class="image-error">Image non trouvée</span>', $question_text);
                                error_log("Missing image for QCM $qcm_id, question {$question['order']}: $img_path");
                            }
                        }
                        echo $question_text;
                        ?>
                    </div>
                    <div class="choices-container">
                        <?php foreach ($choices[$question['id']] as $choice): ?>
                            <div class="choice-item">
                                <input type="checkbox" name="answers[<?php echo $question['id']; ?>][]" value="<?php echo $choice['id']; ?>" id="choice_<?php echo $choice['id']; ?>">
                                <label for="choice_<?php echo $choice['id']; ?>"><?php echo htmlspecialchars($choice['choice_text']); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="form-actions">
                <button type="submit" class="save-course-btn"><i class="fas fa-save"></i> Soumettre</button>
                <a href="courses.php" class="btn-action cancel"><i class="fas fa-times"></i> Annuler</a>
            </div>
        </form>
    </main>
    <?php include '../includes/footer.php'; ?>
    <script>
        $(document).ready(function() {
            // Form validation
            $('#qcmForm').on('submit', function(e) {
                let valid = true;
                $('.question-block').each(function() {
                    const checkboxes = $(this).find('input[type="checkbox"]:checked');
                    if (checkboxes.length === 0) {
                        valid = false;
                        $(this).find('.error-message').remove();
                        $(this).append('<p class="error-message">Veuillez sélectionner au moins une réponse pour cette question.</p>');
                    } else {
                        $(this).find('.error-message').remove();
                    }
                });
                if (!valid) {
                    e.preventDefault();
                    alert('Veuillez répondre à toutes les questions.');
                }
            });
        });
    </script>
</body>
</html>