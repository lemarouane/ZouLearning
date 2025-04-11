<?php
session_start();
require_once './db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int)$_POST['user_id'];
    $course_id = (int)$_POST['course_id'];
    $page_num = (int)$_POST['page_num'];
    $course_title = $db->real_escape_string($_POST['course_title']);
    $user_type = isset($_SESSION['admin_id']) ? 'admin' : 'student';

    $stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details, timestamp) VALUES (?, ?, 'Screenshot taken', ?, NOW())");
    $details = "Captured page $page_num of course '$course_title' (ID: $course_id)";
    $stmt->bind_param("iss", $user_id, $user_type, $details);
    $stmt->execute();
}