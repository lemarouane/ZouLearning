<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="login-page">
    <div class="login-container">
        <h2><i class="fas fa-user-plus"></i> Student Registration</h2>
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
        <form id="registerForm" method="POST" action="register_process.php" class="form-container" style="box-shadow: none;">
            <div class="form-group">
                <label for="full_name"><i class="fas fa-user"></i> Full Name</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <input type="hidden" id="device_id" name="device_id">
            <input type="hidden" id="device_name" name="device_name">
            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">
            <button type="submit" class="btn-action add" id="submitBtn" disabled><i class="fas fa-sign-in-alt"></i> Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>

    <script>
        // Generate or retrieve persistent device ID
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

        // Populate initial fields
        document.getElementById('device_id').value = getDeviceId();
        document.getElementById('device_name').value = navigator.userAgent;

        // Enforce location access
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                    document.getElementById('submitBtn').disabled = false; // Enable button only after location
                },
                (error) => {
                    console.error("Geolocation error:", error);
                    alert("Location access is required to register. Please enable it and refresh the page.");
                }
            );
        } else {
            alert("Geolocation is not supported by your browser. Registration is not possible.");
            document.getElementById('submitBtn').disabled = true;
        }
    </script>
</body>
</html>