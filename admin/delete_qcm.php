<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: manage_qcm.php");
    exit;
}

$qcm_id = (int)$_GET['id'];
$qcm = $db->query("SELECT id FROM qcm WHERE id = $qcm_id")->fetch_assoc();

if ($qcm) {
    $db->query("DELETE FROM qcm_submissions WHERE qcm_id = $qcm_id");
    $db->query("DELETE FROM qcm_choices WHERE question_id IN (SELECT id FROM qcm_questions WHERE qcm_id = $qcm_id)");
    $db->query("DELETE FROM qcm_questions WHERE qcm_id = $qcm_id");
    $db->query("DELETE FROM qcm WHERE id = $qcm_id");
    $_SESSION['message'] = "QCM supprimé avec succès.";
}

header("Location: manage_qcm.php");
exit;