<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: view_qcm_submissions.php");
    exit;
}

$submission_id = (int)$_GET['id'];
$qcm_id = isset($_GET['qcm_id']) ? (int)$_GET['qcm_id'] : 0;

// Fetch submission details
$submission = $db->query("
    SELECT qs.*, q.title AS qcm_title, q.threshold, s.name AS subject_name,
           st.full_name AS student_name, st.email AS student_email
    FROM qcm_submissions qs
    JOIN qcm q ON qs.qcm_id = q.id
    JOIN subjects s ON q.subject_id = s.id
    JOIN students st ON qs.student_id = st.id
    WHERE qs.id = $submission_id
")->fetch_assoc();

if (!$submission) {
    $_SESSION['error'] = "Soumission non trouvée.";
    header("Location: view_qcm_submissions.php" . ($qcm_id ? "?qcm_id=$qcm_id" : ""));
    exit;
}

// Fetch questions and answers
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

    // Correct answers (always fetch for admin)
    $correct = $db->query("
        SELECT qc.choice_text
        FROM qcm_choices qc
        WHERE qc.question_id = {$question['id']} AND qc.is_correct = 1
    ")->fetch_all(MYSQLI_ASSOC);
    $correct_answers[$question['id']] = array_column($correct, 'choice_text');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails Soumission QCM - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .submission-details { margin: 20px 0; }
        .question-block { margin: 15px 0; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .answer-list { margin-top: 10px; }
        .btn-action { background: #1e3c72; color: #fff; padding: 10px 15px; border-radius: 5px; text-decoration: none; }
        .btn-action:hover { background: #152a55; }
        .btn-back { background: #6b7280; }
        .btn-back:hover { background: #4b5563; }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-question-circle"></i> Détails Soumission QCM: <?php echo htmlspecialchars($submission['qcm_title']); ?></h1>
        <div class="submission-details">
            <p><strong>Matière:</strong> <?php echo htmlspecialchars($submission['subject_name']); ?></p>
            <p><strong>Étudiant:</strong> <?php echo htmlspecialchars($submission['student_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($submission['student_email']); ?></p>
            <p><strong>Score:</strong> <?php echo number_format($submission['score'], 2); ?>%</p>
            <p><strong>Seuil:</strong> <?php echo number_format($submission['threshold'], 2); ?>%</p>
            <p><strong>Statut:</strong> <?php echo $submission['passed'] ? 'Réussi' : 'Non passé'; ?></p>
            <p><strong>Date:</strong> <?php echo date('d/m/Y H:i', strtotime($submission['submitted_at'])); ?></p>
        </div>
        <h2><i class="fas fa-list"></i> Réponses</h2>
        <?php foreach ($questions as $question): ?>
            <div class="question-block">
                <h4>Question <?php echo $question['order']; ?>: <?php echo htmlspecialchars($question['question_text']); ?></h4>
                <div class="answer-list">
                    <p><strong>Réponses de l'étudiant:</strong> 
                        <?php echo empty($answers[$question['id']]) ? 'Aucune' : implode(', ', array_map('htmlspecialchars', $answers[$question['id']])); ?>
                    </p>
                    <p><strong>Réponses correctes:</strong> 
                        <?php echo empty($correct_answers[$question['id']]) ? 'Aucune' : implode(', ', array_map('htmlspecialchars', $correct_answers[$question['id']])); ?>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="form-actions">
            <a href="view_qcm_submissions.php<?php echo $qcm_id ? '?qcm_id=' . $qcm_id : ''; ?>" class="btn-action btn-back"><i class="fas fa-arrow-left"></i> Retour aux Soumissions</a>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>