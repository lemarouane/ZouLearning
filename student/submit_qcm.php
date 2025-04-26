<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['qcm_id'])) {
    header("Location: dashboard.php");
    exit;
}

$qcm_id = (int)$_POST['qcm_id'];
$student_id = (int)$_SESSION['student_id'];
$answers = $_POST['answers'] ?? [];

// Debug: Log received answers
error_log("Received POST answers: " . print_r($answers, true));

// Verify QCM and get threshold
$qcm = $db->query("
    SELECT threshold, course_after_id
    FROM qcm
    WHERE id = $qcm_id
")->fetch_assoc();

if (!$qcm) {
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
    $selected_choice_ids = isset($answers[$question_id]) ? array_map('intval', $answers[$question_id]) : [];

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
$stmt->close();

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

$_SESSION['message'] = "QCM soumis. Votre score: " . number_format($score, 2) . "%. " . ($passed ? "Vous avez réussi et débloqué le cours suivant !" : "Vous n'avez pas atteint le seuil requis.");
header("Location: courses.php");
exit;