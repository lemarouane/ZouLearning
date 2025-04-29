<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/device_utils.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $device_fingerprint = trim($_POST['device_fingerprint']);
    $latitude = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null;
    $longitude = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Please fill in all fields.';
        header("Location: login.php");
        exit;
    }

    $stmt = $db->prepare("SELECT id, email, password FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    if ($student && password_verify($password, $student['password'])) {
        // Check device
        $stmt = $db->prepare("SELECT id FROM student_devices WHERE student_id = ? AND device_fingerprint = ? AND status = 'approved'");
        $stmt->bind_param("is", $student['id'], $device_fingerprint);
        $stmt->execute();
        $device = $stmt->get_result()->fetch_assoc();

        if ($device) {
            // Reset session_status to active
            $stmt = $db->prepare("UPDATE students SET session_status = 'active' WHERE id = ?");
            $stmt->bind_param("i", $student['id']);
            $stmt->execute();
            $stmt->close();
            
            // Approved device, log session
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $device_info = $_SERVER['HTTP_USER_AGENT'];
            $stmt = $db->prepare("INSERT INTO user_sessions (student_id, login_time, latitude, longitude, device_info, ip_address) VALUES (?, NOW(), ?, ?, ?, ?)");
            $stmt->bind_param("iddss", $student['id'], $latitude, $longitude, $device_info, $ip_address);
            $stmt->execute();

            $_SESSION['student_id'] = $student['id'];
            $_SESSION['session_id'] = $db->insert_id; // For logout tracking
            header("Location: dashboard.php");
            exit;
        } else {
            // New device, log attempt
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $device_info = $_SERVER['HTTP_USER_AGENT'];
            $stmt = $db->prepare("INSERT INTO device_attempts (student_id, device_fingerprint, device_info, ip_address, latitude, longitude, attempted_at, status) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'pending')");
            $stmt->bind_param("isssdd", $student['id'], $device_fingerprint, $device_info, $ip_address, $latitude, $longitude);
            $stmt->execute();

            $_SESSION['error'] = 'Cet appareil nécessite l\'approbation de l\'administrateur. Réessayez plus tard ou utilisez votre appareil d\'origine.';
            header("Location: pending.php");
            exit;
        }
    } else {
        $_SESSION['error'] = 'Invalid email or password.';
        header("Location: login.php");
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
?>