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
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Connexion Étudiant - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <!-- Font Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FingerprintJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fingerprintjs2/2.1.4/fingerprint2.min.js"></script>
    <style>
        /* Enhanced Styling */
        .display-flex, .signin-content {
            display: flex;
            display: -webkit-flex;
        }
        .display-flex {
            justify-content: space-between;
            align-items: center;
        }

        /* General Styles */
        a:focus, a:active {
            text-decoration: none;
            outline: none;
            transition: all 300ms ease 0s;
        }
        input, select, textarea {
            outline: none;
            appearance: unset !important;
            -webkit-appearance: unset !important;
            transition: all 0.3s ease;
        }
        input::-webkit-outer-spin-button, input::-webkit-inner-spin-button {
            appearance: none !important;
            margin: 0;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
            border-color: #1e40af !important;
        }
        input[type=checkbox] {
            appearance: checkbox !important;
        }
        img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }
        figure {
            margin: 0;
        }
        p {
            margin-bottom: 0;
            font-size: 18px;
            color: #555;
        }
        h2 {
            line-height: 1.5;
            margin: 0;
            padding: 0;
            font-weight: 700;
            color: #1e40af;
            font-family: Poppins;
            font-size: 48px;
        }
        .main {
            background: linear-gradient(135deg, #e0e7ff, #f8f8f8);
            padding: 100px 0;
            min-height: 100vh;
        }
        body {
            font-size: 16px;
            line-height: 1.8;
            color: #333;
            background: #f8f8f8;
            font-weight: 400;
            font-family: Poppins;
            margin: 0;
        }
        .container {
            width: 1100px;
            background: #fff;
            margin: 0 auto;
            box-shadow: 0 20px 30px rgba(0, 0, 0, 0.1);
            border-radius: 30px;
        }
        .signin {
            margin-bottom: 100px;
        }
        .signin-content {
            padding: 90px 0;
        }
        .signin-form, .signin-image {
            width: 50%;
            overflow: hidden;
        }
        .signin-image {
            margin-left: 90px;
            margin-right: 30px;
            margin-top: 60px;
        }
        .form-title {
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        figure {
            margin-bottom: 60px;
            text-align: center;
        }
        .form-submit, .geo-try-again {
            display: inline-block;
            background: linear-gradient(90deg, #3b82f6, #1e40af);
            color: #fff;
            border: none;
            width: auto;
            padding: 18px 50px;
            border-radius: 10px;
            margin-top: 30px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .form-submit:hover, .geo-try-again:hover {
            background: linear-gradient(90deg, #1e40af, #3b82f6);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(59, 130, 246, 0.4);
        }
        .form-submit:disabled {
            background: #d1d5db;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .geo-try-again {
            display: none;
            margin-top: 15px;
            padding: 12px 30px;
        }
        .signin-image-link {
            font-size: 18px;
            color: #1e40af;
            display: block;
            text-align: center;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .signin-image-link:hover {
            color: #3b82f6;
        }
        .signin-form {
            margin-right: 90px;
            margin-left: 90px;
            padding-right: 40px;
        }
        .login-form {
            width: 100%;
        }
        .form-group {
            position: relative;
            margin-bottom: 30px;
            overflow: hidden;
        }
        .form-group:last-child {
            margin-bottom: 0;
        }
        input, select {
            width: 100%;
            display: block;
            border: none;
            border-bottom: 2px solid #d1d5db;
            padding: 12px 40px;
            font-family: Poppins;
            box-sizing: border-box;
            font-size: 18px;
            border-radius: 8px;
        }
        input::-webkit-input-placeholder, select::-webkit-input-placeholder {
            color: #9ca3af;
            font-size: 18px;
        }
        input:focus, select:focus {
            border-bottom: 2px solid #1e40af;
            background: #fff;
        }
        input:focus::-webkit-input-placeholder, select:focus::-webkit-input-placeholder {
            color: #6b7280;
        }
        label {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #1e40af;
            font-size: 24px;
        }
        .material-icons-name {
            font-size: 24px;
        }
        .error {
            color: #dc2626;
            font-size: 16px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .success {
            color: #16a34a;
            font-size: 16px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        /* Responsive Design */
        @media screen and (max-width: 1200px) {
            .container {
                width: calc(100% - 40px);
                max-width: 100%;
            }
        }
        @media screen and (min-width: 1024px) {
            .container {
                max-width: 1200px;
            }
        }
        @media screen and (max-width: 768px) {
            .signin-content {
                flex-direction: column;
                justify-content: center;
            }
            .signin-form {
                margin-left: 0;
                margin-right: 0;
                padding-right: 0;
                padding: 0 40px;
                order: 1;
            }
            .signin-image {
                margin: 0;
                margin-top: 60px;
                order: 2;
            }
            .signin-form, .signin-image {
                width: auto;
            }
            .form-title {
                text-align: center;
                justify-content: center;
            }
            h2 {
                font-size: 36px;
            }
            input, select {
                font-size: 16px;
                padding: 10px 35px;
            }
            input::-webkit-input-placeholder, select::-webkit-input-placeholder {
                font-size: 16px;
            }
            label {
                font-size: 20px;
            }
            .geo-try-again {
                padding: 10px 25px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="main">
        <!-- Sign in form -->
        <section class="signin">
            <div class="container">
                <div class="signin-content">
                    <div class="signin-image">
                        <figure><img src="../assets/img/signin-image.jpg" alt="Connexion"></figure>
                        <a href="register.php" class="signin-image-link">Vous n’avez pas de compte ? Inscrivez-vous</a>
                    </div>
                    <div class="signin-form">
                        <h2 class="form-title"><i class="zmdi zmdi-sign-in"></i> Connexion</h2>
                        <?php if ($error): ?>
                            <p class="error"><?php echo htmlspecialchars($error); ?></p>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['message'])): ?>
                            <p class="success"><?php echo htmlspecialchars($_SESSION['message']); ?></p>
                            <?php unset($_SESSION['message']); ?>
                        <?php endif; ?>
                        <p class="error" id="geo-error" style="display: none;">Veuillez autoriser la géolocalisation pour continuer.</p>
                        <form method="POST" class="login-form" id="loginForm" action="login_process.php" onsubmit="return validateGeolocation()">
                            <div class="form-group">
                                <label for="email"><i class="zmdi zmdi-email"></i></label>
                                <input type="email" name="email" id="email" placeholder="Email" required>
                            </div>
                            <div class="form-group">
                                <label for="password"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="password" id="password" placeholder="Mot de Passe" required>
                            </div>
                            <input type="hidden" id="device_fingerprint" name="device_fingerprint">
                            <input type="hidden" id="latitude" name="latitude">
                            <input type="hidden" id="longitude" name="longitude">
                            <div class="form-group form-button">
                                <input type="submit" name="signin" id="signin" class="form-submit" value="Se connecter" disabled />
                                <button type="button" id="geo-try-again" class="geo-try-again" style="display: none;">Réessayer la géolocalisation</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Generate device fingerprint
        Fingerprint2.get(function(components) {
            const values = components.map(c => c.value);
            const fingerprint = Fingerprint2.x64hash128(values.join(''), 31);
            document.getElementById('device_fingerprint').value = fingerprint;
        });

        // Geolocation enforcement
        const signinButton = document.getElementById('signin');
        const geoError = document.getElementById('geo-error');
        const tryAgainButton = document.getElementById('geo-try-again');
        let isGeolocationAuthorized = false;

        function checkGeolocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        document.getElementById('latitude').value = position.coords.latitude;
                        document.getElementById('longitude').value = position.coords.longitude;
                        isGeolocationAuthorized = true;
                        signinButton.disabled = false;
                        geoError.style.display = 'none';
                        tryAgainButton.style.display = 'none';
                    },
                    (error) => {
                        console.error("Geolocation error:", error);
                        isGeolocationAuthorized = false;
                        signinButton.disabled = true;
                        geoError.style.display = 'block';
                        tryAgainButton.style.display = 'inline-block';
                    }
                );
            } else {
                isGeolocationAuthorized = false;
                signinButton.disabled = true;
                geoError.textContent = 'La géolocalisation n’est pas prise en charge par votre navigateur.';
                geoError.style.display = 'block';
                tryAgainButton.style.display = 'none';
            }
        }

        function validateGeolocation() {
            if (!isGeolocationAuthorized) {
                geoError.style.display = 'block';
                return false;
            }
            return true;
        }

        // Initial geolocation check
        checkGeolocation();

        // Try again button
        tryAgainButton.addEventListener('click', checkGeolocation);
    </script>
</body>
</html>