<?php
session_start();
require_once '../includes/db_connect.php';

if (isset($_POST['student_id'])) {
    $student_id = $_POST['student_id'];
    
    // Check session status
    $stmt = $db->prepare("SELECT session_status FROM students WHERE id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    
    if ($student['session_status'] === 'logged_out') {
        // Destroy the session
        session_destroy();
        echo json_encode(['status' => 'logged_out']);
    } else {
        echo json_encode(['status' => 'active']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No student ID provided']);
}
?> 