<?php
require_once '../../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    exit; // No output if not authenticated
}

if (!isset($_POST['subject_ids']) || !is_array($_POST['subject_ids']) || empty($_POST['subject_ids'])) {
    echo '<option value="">Select subjects first</option>';
    exit;
}

$subject_ids = $_POST['subject_ids'];
$placeholders = implode(',', array_fill(0, count($subject_ids), '?'));
$stmt = $db->prepare("SELECT id, title, difficulty, content_type FROM courses WHERE subject_id IN ($placeholders) ORDER BY title ASC");
foreach ($subject_ids as $i => $id) {
    $stmt->bind_param(str_repeat('i', count($subject_ids)), ...$subject_ids);
}
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($course = $result->fetch_assoc()) {
        echo "<option value='{$course['id']}'>" . htmlspecialchars($course['title'] . " (" . $course['difficulty'] . " - " . $course['content_type'] . ")") . "</option>";
    }
} else {
    echo '<option value="">No courses available</option>';
}
?>