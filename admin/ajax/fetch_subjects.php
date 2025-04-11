<?php
require_once '../../includes/db_connect.php';
$level_id = (int)$_GET['level_id'];
$subjects = $db->query("SELECT id, name FROM subjects WHERE level_id = $level_id");
$output = '<option value="">Choisir une matière</option>';
while ($subject = $subjects->fetch_assoc()) {
    $output .= "<option value='{$subject['id']}'>" . htmlspecialchars($subject['name']) . "</option>";
}
echo $output;
?>