<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['admin_id']) && !isset($_SESSION['student_id'])) {
    http_response_code(403);
    exit('Accès non autorisé.');
}

if (isset($_GET['quiz_id'])) {
    $quiz_id = (int)$_GET['quiz_id'];
    $query = "SELECT pdf_path FROM quizzes WHERE id = $quiz_id";
    if (isset($_SESSION['student_id'])) {
        $student_id = (int)$_SESSION['student_id'];
        $query .= " AND subject_id IN (
            SELECT subject_id FROM student_subjects WHERE student_id = $student_id
            UNION
            SELECT subject_id FROM student_courses sc
            JOIN courses c ON sc.course_id = c.id
            WHERE sc.student_id = $student_id
        )";
    }
    $result = $db->query($query);
    $quiz = $result->fetch_assoc();
    $file_path = $quiz['pdf_path'];
} elseif (isset($_GET['submission_id']) && isset($_SESSION['admin_id'])) {
    $submission_id = (int)$_GET['submission_id'];
    $result = $db->query("SELECT response_path AS pdf_path FROM quiz_submissions WHERE id = $submission_id");
    $submission = $result->fetch_assoc();
    $file_path = $submission['pdf_path'];
} else {
    http_response_code(400);
    exit('Requête invalide.');
}

if (!$file_path || !file_exists($file_path)) {
    http_response_code(404);
    exit('Fichier non trouvé.');
}

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . basename($file_path) . '"');
readfile($file_path);
exit;