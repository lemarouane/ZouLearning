<?php
session_start();
require_once '../includes/db_connect.php'; // Using $db from your setup

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = $_POST['password']; // Not escaping since we’re verifying hash
    $device_id = mysqli_real_escape_string($db, $_POST['device_id']);

    // Fetch student by email
    $sql = "SELECT * FROM students WHERE email = '$email'";
    $result = $db->query($sql);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Check status and device
            if ($user['status'] == 'approved') {
                if ($user['device_id'] === $device_id) {
                    $_SESSION['student_id'] = $user['id'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['message'] = "Login successful! Welcome, " . $user['full_name'] . "!";
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $_SESSION['error'] = "Login failed: This device doesn’t match your registered device.";
                    header("Location: login.php");
                    exit;
                }
            } elseif ($user['status'] == 'pending') {
                $_SESSION['error'] = "Your account is awaiting admin approval.";
                header("Location: login.php");
                exit;
            } else {
                $_SESSION['error'] = "Your account has been rejected by the admin.";
                header("Location: login.php");
                exit;
            }
        } else {
            $_SESSION['error'] = "Invalid email or password.";
            header("Location: login.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Invalid email or password.";
        header("Location: login.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: login.php");
    exit;
}

$db->close();
?>