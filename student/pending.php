<?php
session_start();
require_once '../includes/db_connect.php';
if (isset($_SESSION['student_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Approval - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="login-page">
    <div class="login-container">
        <h2><i class="fas fa-hourglass-half"></i> Pending Approval</h2>
        <p class="error">Your device requires admin approval. Please try again later or use your original device.</p>
        <div class="form-actions">
            <a href="login.php" class="btn-action"><i class="fas fa-arrow-left"></i> Back to Login</a>
        </div>
    </div>
</body>
</html>