<?php
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $level_id = $_POST['level_id'];
    $subject_ids = $_POST['subject_ids'] ?? [];
    $course_ids = $_POST['course_ids'] ?? [];
    $all_courses = isset($_POST['all_courses']) && $_POST['all_courses'] == 1;

    // Update student validation and level
    $stmt = $db->prepare("UPDATE students SET is_validated = 1, level_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $level_id, $student_id);
    $stmt->execute();

    // Log the action
    $stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (?, 'admin', 'Validated student', ?)");
    $details = "Validated student ID $student_id with level ID $level_id";
    $stmt->bind_param("is", $_SESSION['admin_id'], $details);
    $stmt->execute();

    // Handle "All Courses" option
    if ($all_courses) {
        foreach ($subject_ids as $subject_id) {
            $stmt = $db->prepare("INSERT IGNORE INTO student_subjects (student_id, subject_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $student_id, $subject_id);
            $stmt->execute();

            // Assign all existing courses for this subject (trigger handles future ones)
            $stmt = $db->prepare("INSERT IGNORE INTO student_courses (student_id, course_id) SELECT ?, id FROM courses WHERE subject_id = ?");
            $stmt->bind_param("ii", $student_id, $subject_id);
            $stmt->execute();
        }
    } else {
        // Assign specific courses only
        foreach ($course_ids as $course_id) {
            $stmt = $db->prepare("INSERT IGNORE INTO student_courses (student_id, course_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $student_id, $course_id);
            $stmt->execute();
        }
    }

    header("Location: manage_students.php");
    exit;
}
?>  