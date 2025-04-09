<?php
require_once '../../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    exit; // No output if not authenticated
}

if (!isset($_GET['level_id'])) {
    echo '<option value="">Select a level first</option>';
    exit;
}

$level_id = $_GET['level_id'];
$stmt = $db->prepare("SELECT id, name FROM subjects WHERE level_id = ? ORDER BY name ASC");
$stmt->bind_param("i", $level_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($subject = $result->fetch_assoc()) {
        echo "<option value='{$subject['id']}'>" . htmlspecialchars($subject['name']) . "</option>";
    }
} else {
    echo '<option value="">No subjects available</option>';
}
?>