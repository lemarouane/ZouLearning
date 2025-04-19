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
$quiz = $db->query("SELECT pdf_path FROM quizzes WHERE id = $quiz_id")->fetch_assoc();

if ($quiz) {
    if (file_exists($quiz['pdf_path'])) {
        unlink($quiz['pdf_path']);
    }
    $db->query("DELETE FROM quiz_submissions WHERE quiz_id = $quiz_id");
    $db->query("DELETE FROM quizzes WHERE id = $quiz_id");
    $_SESSION['message'] = "Quiz supprimé avec succès.";
}

header("Location: manage_quizzes.php");
exit;   