<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['session_id'])) {
    $session_id = $_POST['session_id'];
    
    try {
        // Get the student_id from the session
        $stmt = $db->prepare("SELECT student_id FROM user_sessions WHERE id = ?");
        $stmt->bind_param("i", $session_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $session = $result->fetch_assoc();
        
        if (!$session) {
            throw new Exception("Session not found");
        }
        
        $student_id = $session['student_id'];
        $stmt->close();
        
        // Start transaction
        $db->begin_transaction();
        
        // Update the session to mark it as logged out
        $stmt = $db->prepare("UPDATE user_sessions SET logout_time = NOW() WHERE id = ? AND logout_time IS NULL");
        $stmt->bind_param("i", $session_id);
        $stmt->execute();
        $stmt->close();
        
        // Also update any other active sessions for this student
        $stmt = $db->prepare("UPDATE user_sessions SET logout_time = NOW() WHERE student_id = ? AND logout_time IS NULL");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $stmt->close();
        
        // Check if session_status column exists
        $result = $db->query("SHOW COLUMNS FROM students LIKE 'session_status'");
        if ($result->num_rows === 0) {
            // Column doesn't exist, add it
            $db->query("ALTER TABLE students ADD COLUMN session_status VARCHAR(20) DEFAULT 'active'");
        }
        
        // Update the student's session status in the database
        $stmt = $db->prepare("UPDATE students SET session_status = 'logged_out' WHERE id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $stmt->close();
        
        // Commit transaction
        $db->commit();
        
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // Rollback transaction on error
        $db->rollback();
        error_log("Logout error: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No session ID provided']);
}
?> 