<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['file']) || !isset($_GET['course_id'])) {
    http_response_code(400);
    exit("Requête invalide");
}

$course_id = (int)$_GET['course_id'];
$file_name = urldecode(basename($_GET['file']));
$file_path = "../Uploads/pdfs/" . $file_name;

// Verify file exists
if (!file_exists($file_path)) {
    error_log("File not found: $file_path");
    http_response_code(404);
    exit("Fichier introuvable");
}

// Verify course content in DB
$stmt = $db->prepare("SELECT content_path FROM course_contents WHERE course_id = ? AND content_type = 'PDF' AND content_path LIKE ?");
$like_path = "%" . $file_name;
$stmt->bind_param("is", $course_id, $like_path);
$stmt->execute();
$result = $stmt->get_result();
$course_content = $result->fetch_assoc();
if (!$course_content) {
    error_log("No matching content for course_id=$course_id, file=$file_name");
    http_response_code(403);
    exit("Accès refusé");
}

$file_size = filesize($file_path);
if ($file_size === 0) {
    error_log("Empty file: $file_path");
    http_response_code(500);
    exit("Fichier vide");
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