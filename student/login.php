<?php
session_start();
require_once '../includes/db_connect.php';
if (isset($_SESSION['student_id'])) {
    header("Location: dashboard.php");
    exit;
}
$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fingerprintjs2/2.1.4/fingerprint2.min.js"></script>
</head>
<body class="login-page">
    <div class="login-container">
        <h2><i class="fas fa-sign-in-alt"></i> Student Login</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['message'])): ?>
            <p style="color: #4caf50;"><?php echo htmlspecialchars($_SESSION['message']); ?></p>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        <form method="POST" action="login_process.php" class="form-container" style="box-shadow: none;">
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <input type="hidden" id="device_fingerprint" name="device_fingerprint">
            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">
            <button type="submit" class="btn-action"><i class="fas fa-sign-in-alt"></i> Login</button>
        </form>
        <p>Don’t have an account? <a href="register.php">Register here</a></p>
    </div>

    <script>
        // Generate device fingerprint
        Fingerprint2.get(function(components) {
            const values = components.map(c => c.value);
            const fingerprint = Fingerprint2.x64hash128(values.join(''), 31);
            document.getElementById('device_fingerprint').value = fingerprint;
        });

        // Get geolocation
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                },
                (error) => {
                    console.error("Geolocation error:", error);
                }
            );
        }
    </script>
</body>
</html>