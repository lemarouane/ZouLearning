<?php
require_once '../includes/db_connect.php';

// Log the logout action
if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
    $stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (?, 'admin', 'Logged out', 'Admin logged out')");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
}

// Destroy session and redirect
session_destroy();
header("Location: login.php");
exit;
?>