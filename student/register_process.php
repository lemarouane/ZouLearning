<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/device_utils.php';

// Debug: Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $device_fingerprint = trim($_POST['device_fingerprint']);
    $device_name = trim($_POST['device_name']);
    $latitude = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null;
    $longitude = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;

    if (empty($full_name) || empty($email) || empty($password) || empty($device_fingerprint)) {
        $_SESSION['error'] = 'Veuillez remplir tous les champs.';
        header("Location: register.php");
        exit;
    }

    // Check if email exists
    $stmt = $db->prepare("SELECT id FROM students WHERE email = ?");
    if (!$stmt) {
        error_log("Prepare failed: " . $db->error);
        $_SESSION['error'] = 'Erreur serveur. Veuillez réessayer.';
        header("Location: register.php");
        exit;
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['error'] = 'Cet email est déjà enregistré.';
        header("Location: register.php");
        exit;
    }
    $stmt->close();

    // Truncate device_fingerprint to fit students.device_id (varchar(36))
    $device_id = substr($device_fingerprint, 0, 36);

    // Start transaction
    $db->begin_transaction();

    try {
        // Insert into students with device_id, device_name, latitude, longitude
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("
            INSERT INTO students (full_name, email, password, device_id, device_name, latitude, longitude, created_at, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 'approved')
        ");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $db->error);
        }
        $stmt->bind_param("sssssdd", $full_name, $email, $hashed_password, $device_id, $device_name, $latitude, $longitude);
        if (!$stmt->execute()) {
            throw new Exception("Insert into students failed: " . $stmt->error);
        }
        $student_id = $db->insert_id;
        $stmt->close();

        // Insert into student_devices (auto-approve first device)
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $stmt = $db->prepare("
            INSERT INTO student_devices (student_id, device_fingerprint, device_info, ip_address, latitude, longitude, created_at, status)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), 'approved')
        ");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $db->error);
        }
        $stmt->bind_param("isssdd", $student_id, $device_fingerprint, $device_name, $ip_address, $latitude, $longitude);
        if (!$stmt->execute()) {
            throw new Exception("Insert into student_devices failed: " . $stmt->error);
        }
        $stmt->close();

        // Log action
        $stmt = $db->prepare("
            INSERT INTO activity_logs (user_id, user_type, action, details, timestamp)
            VALUES (?, 'student', 'Registered', ?, NOW())
        ");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $db->error);
        }
        $details = "Student $full_name (ID $student_id) registered with email $email";
        $stmt->bind_param("is", $student_id, $details);
        $stmt->execute();
        $stmt->close();

        // Send email via Google Apps Script
        $script_url = 'https://script.google.com/macros/s/AKfycbxsHRHBuWr-343_MbQ-NpzD8PMz853fArEMKcVm6_FwSd0UZ8dj-Plr6ayh5qm7aLBE/exec';
        $data = [
            'event' => 'register',
            'full_name' => $full_name,
            'email' => $email
        ];
        $options = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode($data)
            ]
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($script_url, false, $context);

        if ($result === false) {
            error_log('Failed to send email via Google Apps Script');
        } else {
            $response = json_decode($result, true);
            if ($response['status'] !== 'success') {
                error_log('Google Apps Script error: ' . $response['message']);
            }
        }

        // Commit transaction
        $db->commit();

        $_SESSION['message'] = 'Inscription réussie ! Veuillez vous connecter.';
        header("Location: login.php");
        exit;
    } catch (Exception $e) {
        $db->rollback();
        error_log("Error in register_process.php: " . $e->getMessage());
        $_SESSION['error'] = 'Une erreur s’est produite. Veuillez réessayer.';
        header("Location: register.php");
        exit;
    }
} else {
    header("Location: register.php");
    exit;
}
?>