<?php
session_start();
require_once '../includes/db_connect.php';

if (isset($_SESSION['student_id']) && isset($_SESSION['session_id'])) {
    $session_id = (int)$_SESSION['session_id'];
    $stmt = $db->prepare("UPDATE user_sessions SET logout_time = NOW() WHERE id = ?");
    $stmt->bind_param("i", $session_id);
    $stmt->execute();
}

session_unset();
session_destroy();
header("Location: login.php");
exit;
?>