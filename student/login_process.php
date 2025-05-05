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
        $_SESSION['error'] = 'Veuillez remplir tous les champs.';
        header("Location: login.php");
        exit;
    }

    $stmt = $db->prepare("SELECT id, email, password, full_name FROM students WHERE email = ? AND is_archived = 0");
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
            $stmt->close();

            // Send admin notification email via Google Apps Script
            $admin_email = 'marouanehaddad08@gmail.com'; // Your admin email
            $location = getLocationName($latitude, $longitude);
            $attempt_time = date('Y-m-d H:i:s'); // Current time
            $admin_data = [
                'event' => 'admin_new_device_attempt',
                'full_name' => $student['full_name'],
                'email' => $email,
                'admin_email' => $admin_email,
                'details' => [
                    'device_fingerprint' => $device_fingerprint,
                    'device_info' => $device_info,
                    'ip_address' => $ip_address,
                    'location' => $location,
                    'attempt_time' => $attempt_time
                ]
            ];
            $script_url = 'https://script.google.com/macros/s/AKfycbwYOkPT1TgE5i0uGIT4TcsER4NmWcnf78iVyiqpeufeYo5rvvm7TjhcAVRmP7meDvHK/exec';
            $admin_options = [
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-Type: application/json',
                    'content' => json_encode($admin_data)
                ]
            ];
            $admin_context = stream_context_create($admin_options);
            $http_response_header = null; // Reset to capture response headers
            $admin_result = file_get_contents($script_url, false, $admin_context);
            
            if ($admin_result === false) {
                $error = error_get_last();
                error_log('Échec de l\'envoi de l\'email admin (new device attempt): ' . ($error['message'] ?? 'Unknown error'));
                if ($http_response_header) {
                    error_log('HTTP Response Headers: ' . print_r($http_response_header, true));
                }
            } else {
                $response = json_decode($admin_result, true);
                error_log('Admin device attempt email response: ' . print_r($response, true));
                if ($response['status'] !== 'success') {
                    error_log('Erreur Google Apps Script (admin device attempt): ' . ($response['message'] ?? 'No message'));
                }
            }

            $_SESSION['error'] = 'Cet appareil nécessite l\'approbation de l\'administrateur. Réessayez plus tard ou utilisez votre appareil d\'origine.';
            header("Location: pending.php");
            exit;
        }
    } else {
        $_SESSION['error'] = 'Email ou mot de passe invalide, ou compte archivé.';
        header("Location: login.php");
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
?>