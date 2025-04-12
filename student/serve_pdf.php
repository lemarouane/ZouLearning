<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['student_id'])) {
    header("HTTP/1.1 403 Forbidden");
    exit("Accès refusé.");
}

if (!isset($_GET['file']) || !isset($_GET['course_id']) || !isset($_GET['content_id'])) {
    header("HTTP/1.1 400 Bad Request");
    exit("Paramètres manquants.");
}

$file = urldecode($_GET['file']);
$course_id = (int)$_GET['course_id'];
$content_id = (int)$_GET['content_id'];
$student_id = (int)$_SESSION['student_id'];

// Verify access
$stmt = $db->prepare("
    SELECT cc.content_path
    FROM course_contents cc
    JOIN (
        SELECT sc.course_id
        FROM student_courses sc
        WHERE sc.student_id = ?
        UNION
        SELECT c.id AS course_id
        FROM student_subjects ss
        JOIN courses c ON ss.subject_id = c.subject_id
        WHERE ss.student_id = ? AND ss.all_courses = 1
    ) AS accessible_courses ON cc.course_id = accessible_courses.course_id
    WHERE cc.id = ? AND cc.course_id = ? AND cc.content_type = 'PDF'
");
$stmt->bind_param("iiii", $student_id, $student_id, $content_id, $course_id);
$stmt->execute();
$result = $stmt->get_result();
$content = $result->fetch_assoc();

if (!$content) {
    header("HTTP/1.1 403 Forbidden");
    exit("Accès non autorisé au contenu.");
}

$full_path = realpath($content['content_path']);
$base_dir = realpath('../Uploads/pdfs');

if ($full_path === false || strpos($full_path, $base_dir) !== 0) {
    header("HTTP/1.1 403 Forbidden");
    exit("Chemin de fichier invalide.");
}

if (!file_exists($full_path)) {
    header("HTTP/1.1 404 Not Found");
    exit("Fichier non trouvé.");
}

// Serve the PDF
header('Content-Type: application/pdf');
header('Content-Length: ' . filesize($full_path));
header('Cache-Control: no-cache');
readfile($full_path);
exit;