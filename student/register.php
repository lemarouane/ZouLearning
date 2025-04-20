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
    <title>Inscription Étudiant - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fingerprintjs2/2.1.4/fingerprint2.min.js"></script>
    <style>
        .login-container { max-width: 600px; margin: 50px auto; padding: 30px; background: #fff; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.15); }
        .form-container { display: grid; gap: 15px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-weight: 600; color: #1e3a8a; margin-bottom: 8px; }
        .form-group input, .form-group select { padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 15px; }
        .btn-action { background: linear-gradient(90deg, #10b981, #34d399); color: #fff; padding: 12px; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; }
        .btn-action:hover { background: linear-gradient(90deg, #059669, #10b981); transform: translateY(-2px); box-shadow: 0 6px 16px rgba(16,185,129,0.4); }
        .btn-action:disabled { background: #d1d5db; cursor: not-allowed; }
        .error { color: #dc2626; font-size: 14px; margin-bottom: 15px; }
        .success { color: #4caf50; font-size: 14px; margin-bottom: 15px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    </style>
</head>
<body class="login-page">
    <div class="login-container">
        <h2><i class="fas fa-user-plus"></i> Inscription Étudiant</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['message'])): ?>
            <p class="success"><?php echo htmlspecialchars($_SESSION['message']); ?></p>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        <form id="registerForm" method="POST" action="register_process.php" class="form-container">
            <div class="form-group">
                <label for="full_name"><i class="fas fa-user"></i> Nom Complet</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Mot de Passe</label>
                    <input type="password" id="password" name="password" required minlength="6">
                </div>
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-lock"></i> Confirmer le Mot de Passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                </div>
            </div>
            <div class="form-group">
                <label for="phone"><i class="fas fa-phone"></i> Numéro de Téléphone</label>
                <input type="tel" id="phone" name="phone" pattern="[0-9]{10,15}" placeholder="ex : 0612345678">
            </div>
            <div class="form-group">
                <label for="dob"><i class="fas fa-calendar-alt"></i> Date de Naissance</label>
                <input type="date" id="dob" name="dob" max="<?php echo date('Y-m-d', strtotime('-16 years')); ?>">
            </div>
            <div class="form-group">
                <label for="gender"><i class="fas fa-venus-mars"></i> Genre</label>
                <select id="gender" name="gender">
                    <option value="">Sélectionnez le Genre</option>
                    <option value="Male">Homme</option>
                    <option value="Female">Femme</option>
                    <option value="Other">Autre</option>
                </select>
            </div>
            <div class="form-group">
                <label for="city"><i class="fas fa-city"></i> Ville</label>
                <input type="text" id="city" name="city" placeholder="ex : Tanger">
            </div>
            <input type="hidden" id="device_fingerprint" name="device_fingerprint">
            <input type="hidden" id="device_name" name="device_name">
            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">
            <button type="submit" class="btn-action add" id="submitBtn" disabled><i class="fas fa-sign-in-alt"></i> S'inscrire</button>
        </form>
        <p>Vous avez déjà un compte ? <a href="login.php">Connectez-vous ici</a></p>
    </div>

    <script>
        // Générer l'empreinte de l'appareil
        Fingerprint2.get(function(components) {
            const values = components.map(c => c.value);
            const fingerprint = Fingerprint2.x64hash128(values.join(''), 31);
            document.getElementById('device_fingerprint').value = fingerprint;
            document.getElementById('device_name').value = navigator.userAgent;
            checkFormReady();
        });

        // Enforcer l'accès à la localisation
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                    checkFormReady();
                },
                (error) => {
                    console.error("Erreur de géolocalisation :", error);
                    alert("L'accès à la localisation est requis pour s'inscrire. Veuillez l'activer et actualiser la page.");
                    document.getElementById('submitBtn').disabled = true;
                }
            );
        } else {
            alert("La géolocalisation n'est pas supportée par votre navigateur. L'inscription n'est pas possible.");
            document.getElementById('submitBtn').disabled = true;
        }

        // Activer le bouton de soumission lorsque tous les champs sont prêts
        function checkFormReady() {
            const fingerprint = document.getElementById('device_fingerprint').value;
            const latitude = document.getElementById('latitude').value;
            const longitude = document.getElementById('longitude').value;
            if (fingerprint && latitude && longitude) {
                document.getElementById('submitBtn').disabled = false;
            }
        }

        // Validation côté client
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const phone = document.getElementById('phone').value;
            const dob = document.getElementById('dob').value;

            // Vérification des mots de passe
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas.');
                return;
            }

            // Validation du numéro de téléphone
            if (phone && !/^[0-9]{10,15}$/.test(phone)) {
                e.preventDefault();
                alert('Veuillez entrer un numéro de téléphone valide (10-15 chiffres).');
                return;
            }

            // Validation de la date de naissance (au moins 16 ans)
            if (dob) {
                const dobDate = new Date(dob);
                const minDate = new Date();
                minDate.setFullYear(minDate.getFullYear() - 16);
                if (dobDate > minDate) {
                    e.preventDefault();
                    alert('Vous devez avoir au moins 16 ans pour vous inscrire.');
                    return;
                }
            }
        });
    </script>
</body>
</html>
