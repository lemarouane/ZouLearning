<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['qcm_id'])) {
    $_SESSION['error'] = "Requête invalide.";
    header("Location: courses.php");
    exit;
}

$qcm_id = (int)$_POST['qcm_id'];
$student_id = (int)$_SESSION['student_id'];
$answers = $_POST['answers'] ?? [];

// Debug: Log received answers
error_log("Received POST answers: " . print_r($answers, true));

// Verify QCM and get threshold
$qcm = $db->query("
    SELECT q.id, q.title, q.threshold, q.course_after_id
    FROM qcm q
    JOIN student_subjects ss ON q.subject_id = ss.subject_id
    WHERE q.id = $qcm_id AND ss.student_id = $student_id
")->fetch_assoc();

if (!$qcm) {
    $_SESSION['error'] = "QCM non trouvé ou non autorisé.";
    header("Location: courses.php");
    exit;
}

// Calculate score
$questions = $db->query("
    SELECT id
    FROM qcm_questions
    WHERE qcm_id = $qcm_id
")->fetch_all(MYSQLI_ASSOC);

$total_questions = count($questions);
$correct_answers = 0;

foreach ($questions as $question) {
    $question_id = $question['id'];
    $selected_choice_ids = isset($answers[$question_id]) ? array_map('intval', (array)$answers[$question_id]) : [];

    // Get correct choices for the question
    $correct_choices = $db->query("
        SELECT id
        FROM qcm_choices
        WHERE question_id = $question_id AND is_correct = 1
    ")->fetch_all(MYSQLI_ASSOC);
    $correct_choice_ids = array_column($correct_choices, 'id');

    // Get all choices to check for incorrect selections
    $all_choices = $db->query("
        SELECT id, is_correct
        FROM qcm_choices
        WHERE question_id = $question_id
    ")->fetch_all(MYSQLI_ASSOC);
    $incorrect_choice_ids = array_column(array_filter($all_choices, function($choice) {
        return !$choice['is_correct'];
    }), 'id');

    // Check if the student selected all correct choices and no incorrect ones
    $is_correct = true;
    if (empty($selected_choice_ids)) {
        $is_correct = false; // No selections
    } else {
        // Check if all correct choices are selected
        foreach ($correct_choice_ids as $correct_id) {
            if (!in_array($correct_id, $selected_choice_ids)) {
                $is_correct = false;
                break;
            }
        }
        // Check if any incorrect choices are selected
        foreach ($selected_choice_ids as $selected_id) {
            if (in_array($selected_id, $incorrect_choice_ids)) {
                $is_correct = false;
                break;
            }
        }
        // Check if the number of selected choices matches the number of correct choices
        if (count($selected_choice_ids) != count($correct_choice_ids)) {
            $is_correct = false;
        }
    }

    if ($is_correct) {
        $correct_answers++;
    }
}

$score = ($total_questions > 0) ? ($correct_answers / $total_questions) * 100 : 0;
$passed = $score >= $qcm['threshold'] ? 1 : 0;

// Save submission
$stmt = $db->prepare("
    INSERT INTO qcm_submissions (qcm_id, student_id, score, passed, submitted_at)
    VALUES (?, ?, ?, ?, NOW())
");
$stmt->bind_param("iidi", $qcm_id, $student_id, $score, $passed);
$stmt->execute();
$submission_id = $db->insert_id;
$stmt->close();

// Save student answers to qcm_submission_answers
foreach ($answers as $question_id => $choice_ids) {
    $question_id = (int)$question_id;
    foreach ((array)$choice_ids as $choice_id) {
        $choice_id = (int)$choice_id;
        $stmt = $db->prepare("
            INSERT INTO qcm_submission_answers (submission_id, question_id, choice_id)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iii", $submission_id, $question_id, $choice_id);
        $stmt->execute();
        $stmt->close();
    }
}

// If passed, unlock the next course
if ($passed) {
    $course_id = $qcm['course_after_id'];
    $stmt = $db->prepare("
        INSERT IGNORE INTO student_courses (student_id, course_id)
        VALUES (?, ?)
    ");
    $stmt->bind_param("ii", $student_id, $course_id);
    $stmt->execute();
    $stmt->close();
}

// Log activity
$db->query("
    INSERT INTO activity_logs (user_id, user_type, action, details, timestamp)
    VALUES ($student_id, 'student', 'Submitted QCM', 'Submitted QCM ID $qcm_id: {$qcm['title']}', NOW())
");

$_SESSION['message'] = "QCM soumis. Votre score: " . number_format($score, 2) . "%. " . ($passed ? "Vous avez réussi et débloqué le cours suivant !" : "Vous n'avez pas atteint le seuil requis.");
header("Location: courses.php");
exit;
?>