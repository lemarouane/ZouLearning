<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="login-page">
    <div class="login-container">
        <h2><i class="fas fa-sign-in-alt"></i> Student Login</h2>
        <?php
        session_start();
        if (isset($_SESSION['error'])) {
            echo "<p class='error'>{$_SESSION['error']}</p>";
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['message'])) {
            echo "<p style='color: #4caf50;'>{$_SESSION['message']}</p>";
            unset($_SESSION['message']);
        }
        ?>
        <form method="POST" action="login_process.php" class="form-container" style="box-shadow: none;">
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <input type="hidden" id="device_id" name="device_id">
            <button type="submit" class="btn-action"><i class="fas fa-sign-in-alt"></i> Login</button>
        </form>
        <p>Don’t have an account? <a href="register.php">Register here</a></p>
    </div>

    <script>
        // Retrieve persistent device ID
        function getDeviceId() {
            let deviceId = localStorage.getItem('device_id');
            if (!deviceId) {
                deviceId = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                    var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                    return v.toString(16);
                });
                localStorage.setItem('device_id', deviceId);
            }
            return deviceId;
        }

        document.getElementById('device_id').value = getDeviceId();
    </script>
</body>
</html>