<?php
session_start();
require_once '../includes/db_connect.php'; // Using $db from your file

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = mysqli_real_escape_string($db, $_POST['full_name']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $device_id = mysqli_real_escape_string($db, $_POST['device_id']);
    $device_name = mysqli_real_escape_string($db, $_POST['device_name']);
    $latitude = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : NULL;
    $longitude = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : NULL;
    $status = 'pending';

    $sql = "INSERT INTO students (full_name, email, password, status, device_id, device_name, latitude, longitude) 
            VALUES ('$full_name', '$email', '$password', '$status', '$device_id', '$device_name', '$latitude', '$longitude')";
    
    if ($db->query($sql) === TRUE) {
        $_SESSION['message'] = "Registration successful! Awaiting admin approval.";
        header("Location: login.php");
        exit;
    } else {
        $_SESSION['error'] = "Registration failed: " . $db->error;
        error_log("Registration error: " . $db->error); // Log to C:\xampp\php\logs\php_error_log
        header("Location: register.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: register.php");
    exit;
}

$db->close();
?>