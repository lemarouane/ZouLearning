<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int)$_POST['user_id'];
    $course_id = (int)$_POST['course_id']; // Still needed for DB reference
    $page_num = (int)$_POST['page_num'];
    $course_title = $_POST['course_title'];

    $details = "Screenshot taken on page $page_num of $course_title";
    $stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details, timestamp) VALUES (?, 'student', 'screenshot', ?, NOW())");    $stmt->bind_param("is", $user_id, $details);
    $stmt->execute();
}
?>