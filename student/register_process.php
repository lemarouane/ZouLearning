<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/device_utils.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $device_fingerprint = trim($_POST['device_fingerprint']);
    $device_info = trim($_POST['device_name']);
    $latitude = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null;
    $longitude = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;

    if (empty($full_name) || empty($email) || empty($password)) {
        $_SESSION['error'] = 'Please fill in all fields.';
        header("Location: register.php");
        exit;
    }

    $stmt = $db->prepare("SELECT id FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['error'] = 'This email is already registered.';
        header("Location: register.php");
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO students (full_name, email, password, created_at, status) VALUES (?, ?, ?, NOW(), 'approved')");
    $stmt->bind_param("sss", $full_name, $email, $hashed_password);
    if ($stmt->execute()) {
        $student_id = $db->insert_id;

        // Auto-approve first device
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $stmt = $db->prepare("INSERT INTO student_devices (student_id, device_fingerprint, device_info, ip_address, latitude, longitude, created_at, status) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'approved')");
        $stmt->bind_param("isssdd", $student_id, $device_fingerprint, $device_info, $ip_address, $latitude, $longitude);
        $stmt->execute();

        $_SESSION['message'] = 'Registration successful! Please log in.';
        header("Location: login.php");
        exit;
    } else {
        $_SESSION['error'] = 'An error occurred. Please try again.';
        header("Location: register.php");
        exit;
    }
} else {
    header("Location: register.php");
    exit;
}
?>