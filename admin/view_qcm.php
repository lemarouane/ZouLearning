<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: manage_qcm.php");
    exit;
}

$qcm_id = (int)$_GET['id'];
$qcm = $db->query("
    SELECT q.*, s.name AS subject_name, l.name AS level_name, 
           c1.title AS course_before, c2.title AS course_after
    FROM qcm q
    JOIN subjects s ON q.subject_id = s.id
    JOIN levels l ON s.level_id = l.id
    JOIN courses c1 ON q.course_before_id = c1.id
    JOIN courses c2 ON q.course_after_id = c2.id
    WHERE q.id = $qcm_id
")->fetch_assoc();

if (!$qcm) {
    header("Location: manage_qcm.php");
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
    $choices[$question['id']] = $db->query("
        SELECT id, choice_text, is_correct
        FROM qcm_choices
        WHERE question_id = {$question['id']}
    ")->fetch_all(MYSQLI_ASSOC);
}

// Base URL for images (adjust if needed)
$base_url = '/ZouLearning'; // Update to your domain or path if different
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voir QCM - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/admin.css?v=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .qcm-details { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9; }
        .question-block { margin: 15px 0; padding: 15px; border: 1px solid #ccc; border-radius: 5px; background: #fff; }
        .question-content img { max-width: 800px !important; height: auto; border-radius: 5px; margin: 10px 0; }
        .question-content .image-error { color: #f44336; font-style: italic; }
        .choice-item { margin: 5px 0; }
        .choice-item.correct { color: #4caf50; font-weight: bold; }
        body.dark-mode .qcm-details { background: #2d3748; border-color: #4a5568; }
        body.dark-mode .question-block { background: #2d3748; border-color: #4a5568; }
        body.dark-mode .question-content img { border: 1px solid #718096; }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-question-circle"></i> Détails du QCM</h1>
        <div class="qcm-details">
            <h3><i class="fas fa-info"></i> Informations</h3>
            <p><strong>Titre :</strong> <?php echo htmlspecialchars($qcm['title']); ?></p>
            <p><strong>Matière :</strong> <?php echo htmlspecialchars($qcm['subject_name']); ?></p>
            <p><strong>Niveau :</strong> <?php echo htmlspecialchars($qcm['level_name']); ?></p>
            <p><strong>Cours Avant :</strong> <?php echo htmlspecialchars($qcm['course_before']); ?></p>
            <p><strong>Cours Après :</strong> <?php echo htmlspecialchars($qcm['course_after']); ?></p>
            <p><strong>Seuil de réussite (%) :</strong> <?php echo number_format($qcm['threshold'], 2); ?></p>
            <p><strong>Description :</strong> <?php echo htmlspecialchars($qcm['description'] ?: 'Aucune'); ?></p>
        </div>
        <div class="questions-section">
            <h3><i class="fas fa-question"></i> Questions</h3>
            <?php if (empty($questions)): ?>
                <p class="empty-message">Aucune question trouvée.</p>
            <?php else: ?>
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
                                <div class="choice-item <?php echo $choice['is_correct'] ? 'correct' : ''; ?>">
                                    <i class="fas fa-<?php echo $choice['is_correct'] ? 'check' : 'times'; ?>"></i>
                                    <?php echo htmlspecialchars($choice['choice_text']); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <a href="manage_qcm.php" class="back-btn"><i class="fas fa-arrow-left"></i> Retour</a>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>