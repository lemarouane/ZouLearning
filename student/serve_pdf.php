<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['file']) || !isset($_GET['course_id'])) {
    http_response_code(400);
    exit("Requête invalide");
}

$course_id = (int)$_GET['course_id'];
$student_id = (int)$_SESSION['student_id'];
$file_name = basename($_GET['file']);
$file_path = "../uploads/pdfs/" . $file_name;

// Verify student access to the course content
$stmt = $db->prepare("
    SELECT cc.content_path 
    FROM (
        SELECT sc.course_id 
        FROM student_courses sc 
        WHERE sc.student_id = ?
        UNION
        SELECT c.id AS course_id 
        FROM student_subjects ss 
        JOIN courses c ON ss.subject_id = c.subject_id 
        WHERE ss.student_id = ? AND ss.all_courses = 1
    ) AS unique_courses
    JOIN course_contents cc ON unique_courses.course_id = cc.course_id 
    WHERE cc.course_id = ? AND cc.content_type = 'PDF'
");
$stmt->bind_param("iii", $student_id, $student_id, $course_id);
$stmt->execute();
$result = $stmt->get_result();
$course_content = $result->fetch_assoc();
if (!$course_content || basename($course_content['content_path']) !== $file_name) {
    http_response_code(403);
    exit("Accès refusé");
}

if (!file_exists($file_path)) {
    http_response_code(404);
    exit("Fichier introuvable: " . $file_path);
}

$file_size = filesize($file_path);
if ($file_size === 0) {
    http_response_code(500);
    exit("Fichier vide sur le serveur: " . $file_path);
}

ob_clean();
flush();

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $file_name . '"');
header('Content-Length: ' . $file_size);
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$fp = fopen($file_path, 'rb');
while (!feof($fp)) {
    echo fread($fp, 8192);
    flush();
}
fclose($fp);
exit;