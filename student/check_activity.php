<?php
session_start();

// Check if student is logged in
if (!isset($_SESSION['student_id']) || !isset($_POST['student_id']) || (int)$_SESSION['student_id'] !== (int)$_POST['student_id']) {
    echo json_encode(['isActive' => false]);
    exit;
}

// Check if activity timestamp exists and is recent
$isActive = false;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) < 10) {
    $isActive = true;
}

echo json_encode(['isActive' => $isActive]);
?>