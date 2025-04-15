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
        $_SESSION['error'] = 'Veuillez remplir tous les champs.';
        header("Location: register.php");
        exit;
    }

    $stmt = $db->prepare("SELECT id FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['error'] = 'Cet email est déjà enregistré.';
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

        $_SESSION['message'] = 'Inscription réussie ! Veuillez vous connecter.';
        header("Location: login.php");
        exit;
    } else {
        $_SESSION['error'] = 'Une erreur s’est produite. Veuillez réessayer.';
        header("Location: register.php");
        exit;
    }
} else {
    header("Location: register.php");
    exit;
}
?>