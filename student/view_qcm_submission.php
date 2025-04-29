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

$submission_id = (int)$_GET['id'];
$student_id = (int)$_SESSION['student_id'];

// Fetch submission details
$submission = $db->query("
    SELECT qs.*, q.id AS qcm_id, q.title AS qcm_title, q.threshold, s.name AS subject_name
    FROM qcm_submissions qs
    JOIN qcm q ON qs.qcm_id = q.id
    JOIN subjects s ON q.subject_id = s.id
    WHERE qs.id = $submission_id AND qs.student_id = $student_id
")->fetch_assoc();

if (!$submission) {
    $_SESSION['error'] = "Soumission non trouvée ou non autorisée.";
    header("Location: courses.php");
    exit;
}

// Fetch questions and student answers
$questions = $db->query("
    SELECT qq.id, qq.question_text, qq.order
    FROM qcm_questions qq
    WHERE qq.qcm_id = {$submission['qcm_id']}
    ORDER BY qq.order
")->fetch_all(MYSQLI_ASSOC);

$answers = [];
$correct_answers = [];
foreach ($questions as $question) {
    // Student’s selected answers
    $student_answers = $db->query("
        SELECT qc.choice_text
        FROM qcm_submission_answers qsa
        JOIN qcm_choices qc ON qsa.choice_id = qc.id
        WHERE qsa.submission_id = $submission_id AND qsa.question_id = {$question['id']}
    ")->fetch_all(MYSQLI_ASSOC);
    $answers[$question['id']] = array_column($student_answers, 'choice_text');

    // Correct answers (fetch only if passed)
    if ($submission['passed']) {
        $correct = $db->query("
            SELECT qc.choice_text
            FROM qcm_choices qc
            WHERE qc.question_id = {$question['id']} AND qc.is_correct = 1
        ")->fetch_all(MYSQLI_ASSOC);
        $correct_answers[$question['id']] = array_column($correct, 'choice_text');
    }
}

// Base URL for images (adjust if needed)
$base_url = '/ZouLearning'; // Update to your domain or path if different
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voir Soumission QCM - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/admin.css?v=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .submission-details { margin: 20px 0; }
        .question-block { margin: 15px 0; padding: 15px; border: 1px solid #ccc; border-radius: 5px; background: #fff; }
        .question-content img { max-width: 800px !important; height: auto; border-radius: 5px; margin: 10px 0; }
        .question-content .image-error { color: #f44336; font-style: italic; }
        .answer-list { margin-top: 10px; }
        .answer-correct { color: #4caf50; font-weight: bold; }
        .answer-incorrect { color: #f44336; font-weight: bold; }
        .note { color: #888; font-style: italic; margin: 10px 0; }
        .form-actions { margin-top: 20px; }
        .btn-action { background: #1e3c72; color: #fff; padding: 10px 15px; border-radius: 5px; text-decoration: none; margin-right: 10px; }
        .btn-action:hover { background: #152a55; }
        .btn-action.back { background: #6b7280; }
        .btn-action.back:hover { background: #4b5563; }
        .btn-retry { background: #ff9800; }
        .btn-retry:hover { background: #e68a00; }
        body.dark-mode .question-block { background: #2d3748; border-color: #4a5568; }
        body.dark-mode .question-content img { border: 1px solid #718096; }
    </style>
</head>
<body>
    <?php include '../includes/student_header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-question-circle"></i> Soumission QCM: <?php echo htmlspecialchars($submission['qcm_title']); ?></h1>
        <div class="submission-details">
            <p><strong>Matière:</strong> <?php echo htmlspecialchars($submission['subject_name']); ?></p>
            <p><strong>Score:</strong> <?php echo number_format($submission['score'], 2); ?>%</p>
            <p><strong>Statut:</strong> <?php echo $submission['passed'] ? 'Réussi' : 'Non passé'; ?></p>
            <p><strong>Date:</strong> <?php echo date('d/m/Y H:i', strtotime($submission['submitted_at'])); ?></p>
        </div>
        <h2><i class="fas fa-list"></i> Réponses</h2>
        <?php if (!$submission['passed']): ?>
            <p class="note">Note : Les réponses correctes seront visibles une fois que vous aurez réussi le QCM (score ≥ <?php echo number_format($submission['threshold'], 2); ?>%).</p>
        <?php endif; ?>
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
                            error_log("Missing image for QCM {$submission['qcm_id']}, question {$question['order']}: $img_path");
                        }
                    }
                    echo $question_text;
                    ?>
                </div>
                <div class="answer-list">
                    <p><strong>Vos réponses:</strong> 
                        <?php echo empty($answers[$question['id']]) ? 'Aucune' : implode(', ', array_map('htmlspecialchars', $answers[$question['id']])); ?>
                    </p>
                    <?php if ($submission['passed']): ?>
                        <p><strong>Réponses correctes:</strong> 
                            <?php echo empty($correct_answers[$question['id']]) ? 'Aucune' : implode(', ', array_map('htmlspecialchars', $correct_answers[$question['id']])); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="form-actions">
            <?php if (!$submission['passed']): ?>
                <a href="student_take_qcm.php?id=<?php echo $submission['qcm_id']; ?>" class="btn-action btn-retry"><i class="fas fa-redo"></i> Réessayer le QCM</a>
            <?php endif; ?>
            <a href="courses.php" class="btn-action back"><i class="fas fa-arrow-left"></i> Retour aux cours</a>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>