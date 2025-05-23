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
    $phone = trim($_POST['phone']) ?: null;
    $dob = trim($_POST['dob']) ?: null;
    $gender = trim($_POST['gender']) ?: null;
    $city = trim($_POST['city']) ?: null;
    $university = trim($_POST['university']) ?: null;
    $custom_university = trim($_POST['custom_university']) ?: null;
    $filiere = trim($_POST['filiere']) ?: null;
    $custom_filiere = trim($_POST['custom_filiere']) ?: null;
    $device_fingerprint = trim($_POST['device_fingerprint']);
    $device_name = trim($_POST['device_name']);
    $latitude = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null;
    $longitude = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;

    // Handle university: use custom input if "Autre" is selected
    $final_university = ($university === 'Autre' && $custom_university) ? $custom_university : $university;

    // Handle filiere: use custom input if "Autre" is selected
    $final_filiere = ($filiere === 'Autre' && $custom_filiere) ? $custom_filiere : $filiere;

    // Basic validation
    if (empty($full_name) || empty($email) || empty($password) || empty($device_fingerprint)) {
        $_SESSION['error'] = 'Veuillez remplir tous les champs obligatoires.';
        header("Location: register.php");
        exit;
    }

    // Validate university
    if ($final_university && strlen($final_university) > 100) {
        $_SESSION['error'] = 'Le nom de l\'université ou école ne doit pas dépasser 100 caractères.';
        header("Location: register.php");
        exit;
    }

    // Validate filiere
    if ($final_filiere && strlen($final_filiere) > 100) {
        $_SESSION['error'] = 'Le nom de la filière ne doit pas dépasser 100 caractères.';
        header("Location: register.php");
        exit;
    }

    // Check email uniqueness
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

    // Validate phone
    if ($phone && !preg_match('/^[0-9]{10,15}$/', $phone)) {
        $_SESSION['error'] = 'Numéro de téléphone invalide (10-15 chiffres).';
        header("Location: register.php");
        exit;
    }

    // Validate DOB
    if ($dob) {
        $dob_date = new DateTime($dob);
        $min_date = new DateTime();
        $min_date->modify('-16 years');
        if ($dob_date > $min_date) {
            $_SESSION['error'] = 'Vous devez avoir au moins 16 ans pour vous inscrire.';
            header("Location: register.php");
            exit;
        }
    }

    // Start transaction
    $db->begin_transaction();

    try {
        // Insert into students
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("
            INSERT INTO students (full_name, email, phone, dob, gender, city, university, filiere, password, latitude, longitude, created_at, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'pending')
        ");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $db->error);
        }
        $stmt->bind_param(
            "sssssssssdd",
            $full_name,
            $email,
            $phone,
            $dob,
            $gender,
            $city,
            $final_university,
            $final_filiere,
            $hashed_password,
            $latitude,
            $longitude
        );
        if (!$stmt->execute()) {
            throw new Exception("Insert into students failed: " . $stmt->error);
        }
        $student_id = $db->insert_id;
        $stmt->close();

        // Insert into student_devices (auto-approve first device)
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $default_device_name = 'Device 1';
        $stmt = $db->prepare("
            INSERT INTO student_devices (student_id, device_fingerprint, device_name, device_info, ip_address, latitude, longitude, created_at, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 'approved')
        ");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $db->error);
        }
        $stmt->bind_param("issssdd", $student_id, $device_fingerprint, $default_device_name, $device_name, $ip_address, $latitude, $longitude);
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
        $details = "Étudiant $full_name (ID $student_id) inscrit avec email $email";
        $stmt->bind_param("is", $student_id, $details);
        $stmt->execute();
        $stmt->close();

        // Send student welcome email via Google Apps Script
        $script_url = 'https://script.google.com/macros/s/AKfycbxKlF3bjY6ODVuYkt8C_CrNpU7rew24Z62OLibUmtCnN3sdQgOouwfXHl0kXsHjx9H7/exec';
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
            error_log('Échec de l\'envoi de l\'email étudiant via Google Apps Script');
        } else {
            $response = json_decode($result, true);
            if ($response['status'] !== 'success') {
                error_log('Erreur Google Apps Script (étudiant): ' . $response['message']);
            }
        }

        // Send admin notification email via Google Apps Script
        $admin_email = 'marouanehaddad08@gmail.com'; // Your admin email
        $location = getLocationName($latitude, $longitude);
        $admin_data = [
            'event' => 'admin_new_student',
            'full_name' => $full_name,
            'email' => $email,
            'admin_email' => $admin_email,
            'details' => [
                'phone' => $phone ?: 'Non fourni',
                'dob' => $dob ?: 'Non fourni',
                'gender' => $gender ?: 'Non fourni',
                'city' => $city ?: 'Non fourni',
                'university' => $final_university ?: 'Non fourni',
                'filiere' => $final_filiere ?: 'Non fourni',
                'device_fingerprint' => $device_fingerprint,
                'device_name' => $device_name,
                'ip_address' => $ip_address,
                'location' => $location
            ]
        ];
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
            error_log('Échec de l\'envoi de l\'email admin via Google Apps Script: ' . ($error['message'] ?? 'Unknown error'));
            if ($http_response_header) {
                error_log('HTTP Response Headers: ' . print_r($http_response_header, true));
            }
        } else {
            $response = json_decode($admin_result, true);
            error_log('Admin email response: ' . print_r($response, true));
            if ($response['status'] !== 'success') {
                error_log('Erreur Google Apps Script (admin): ' . ($response['message'] ?? 'No message'));
            }
        }

        // Commit transaction
        $db->commit();

        $_SESSION['message'] = 'Inscription réussie ! Veuillez vous connecter.';
        header("Location: login.php");
        exit;
    } catch (Exception $e) {
        $db->rollback();
        error_log("Erreur dans register_process.php: " . $e->getMessage());
        $_SESSION['error'] = 'Une erreur s’est produite. Veuillez réessayer.';
        header("Location: register.php");
        exit;
    }
} else {
    header("Location: register.php");
    exit;
}