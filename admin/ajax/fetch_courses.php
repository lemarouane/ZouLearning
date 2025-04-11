<?php
require_once '../../includes/db_connect.php';
$subject_ids = isset($_POST['subject_ids']) ? array_map('intval', $_POST['subject_ids']) : [];
if (empty($subject_ids)) exit('<option value="">Aucune matière sélectionnée</option>');
$ids = implode(',', $subject_ids);
$courses = $db->query("SELECT id, title FROM courses WHERE subject_id IN ($ids)");
$output = '<option value="">Choisir un cours</option>';
while ($course = $courses->fetch_assoc()) {
    $output .= "<option value='{$course['id']}'>" . htmlspecialchars($course['title']) . "</option>";
}
echo $output;
?>