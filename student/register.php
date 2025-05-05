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
    <title>Inscription Étudiant - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <!-- Font Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FingerprintJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fingerprintjs2/2.1.4/fingerprint2.min.js"></script>
    <style>
        /* Enhanced Styling */
        .display-flex, .signup-content {
            display: flex;
            display: -webkit-flex;
        }
        .display-flex {
            justify-content: space-between;
            align-items: center;
        }
        .socials {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
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
        .signup {
            margin-bottom: 100px;
        }
        .signup-content {
            padding: 90px 0;
        }
        .signup-form, .signup-image {
            width: 50%;
            overflow: hidden;
        }
        .signup-image {
            margin: 0 70px;
        }
        .form-title {
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: -22px;
        }
        .signup-image {
            margin-top: 60px;
        }
        figure {
            margin-bottom: 60px;
            text-align: center;
        }
        .form-submit {
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
        .form-submit:hover {
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
        .signup-image-link {
            font-size: 18px;
            color: #1e40af;
            display: block;
            text-align: center;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .signup-image-link:hover {
            color: #3b82f6;
        }
        .term-service {
            font-size: 16px;
            color: #1e40af;
            text-decoration: underline;
            cursor: pointer;
        }
        .signup-form {
            margin-left: 90px;
            margin-right: 90px;
            padding-left: 40px;
        }
        .register-form {
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
        input[type=checkbox]:not(old) {
            width: 2.5em;
            margin: 0;
            padding: 0;
            font-size: 1.2em;
            display: none;
        }
        input[type=checkbox]:not(old) + label {
            display: inline-block;
            line-height: 1.8em;
            margin-top: 8px;
        }
        input[type=checkbox]:not(old) + label > span {
            display: inline-block;
            width: 18px;
            height: 18px;
            margin-right: 20px;
            margin-bottom: 4px;
            border: 2px solid #9ca3af;
            border-radius: 4px;
            background: white;
            vertical-align: bottom;
        }
        input[type=checkbox]:not(old):checked + label > span:before {
            content: '\f26b';
            display: block;
            color: #1e40af;
            font-size: 14px;
            line-height: 1.3;
            text-align: center;
            font-family: 'Material-Design-Iconic-Font';
            font-weight: bold;
        }
        .agree-term {
            display: inline-block;
            width: auto;
        }
        label {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #1e40af;
            font-size: 24px;
        }
        .label-agree-term {
            position: relative;
            top: 0;
            transform: translateY(0);
            font-size: 16px;
            color: #333;
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
        .hidden {
            display: none;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            overflow: auto;
            animation: fadeIn 0.3s ease-in;
        }
        .modal-content {
            background: #fff;
            margin: 5% auto;
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 700px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            position: relative;
            font-family: Poppins;
            animation: slideIn 0.3s ease-in-out;
        }
        .modal-content h3 {
            margin-bottom: 20px;
            font-size: 28px;
            color: #1e40af;
            font-weight: 600;
        }
        .modal-content p, .modal-content li {
            font-size: 16px;
            color: #333;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .modal-content ul {
            padding-left: 20px;
            margin-bottom: 20px;
        }
        .modal-content li {
            list-style-type: disc;
        }
        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            color: #6b7280;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        .modal-close:hover {
            color: #1e40af;
        }
        .modal-content .close-btn {
            display: inline-block;
            background: linear-gradient(90deg, #3b82f6, #1e40af);
            color: #fff;
            padding: 12px 30px;
            border-radius: 10px;
            border: none;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        .modal-content .close-btn:hover {
            background: linear-gradient(90deg, #1e40af, #3b82f6);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(59, 130, 246, 0.4);
        }
        @media screen and (max-width: 768px) {
            .modal-content {
                width: 95%;
                margin: 10% auto;
                padding: 20px;
            }
            .modal-content h3 {
                font-size: 24px;
            }
            .modal-content p, .modal-content li {
                font-size: 14px;
            }
            .modal-close {
                font-size: 24px;
            }
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
            .signup-content {
                flex-direction: column;
                justify-content: center;
            }
            .signup-form {
                margin-left: 0;
                margin-right: 0;
                padding-left: 0;
                padding: 0 40px;
            }
            .signup-image {
                margin: 0;
                margin-top: 60px;
            }
            .signup-form, .signup-image {
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
        }
    </style>
</head>
<body>
    <div class="main">
        <!-- Sign up form -->
        <section class="signup">
            <div class="container">
                <div class="signup-content">
                    <div class="signup-form">
                        <h2 class="form-title"><i class="zmdi zmdi-account"></i> Inscription</h2>
                        <?php if ($error): ?>
                            <p class="error"><?php echo htmlspecialchars($error); ?></p>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['message'])): ?>
                            <p class="success"><?php echo htmlspecialchars($_SESSION['message']); ?></p>
                            <?php unset($_SESSION['message']); ?>
                        <?php endif; ?>
                        <form method="POST" class="register-form" id="registerForm" action="register_process.php">
                            <div class="form-group">
                                <label for="full_name"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                <input type="text" name="full_name" id="full_name" placeholder="Nom Complet" required>
                            </div>
                            <div class="form-group">
                                <label for="email"><i class="zmdi zmdi-email"></i></label>
                                <input type="email" name="email" id="email" placeholder="Email" required>
                            </div>
                            <div class="form-group">
                                <label for="password"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="password" id="password" placeholder="Mot de Passe" required minlength="6">
                            </div>
                            <div class="form-group">
                                <label for="confirm_password"><i class="zmdi zmdi-lock-outline"></i></label>
                                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirmer le Mot de Passe" required minlength="6">
                            </div>
                            <div class="form-group">
                                <label for="phone"><i class="zmdi zmdi-phone"></i></label>
                                <input type="tel" name="phone" id="phone" pattern="[0-9]{10,15}" placeholder="Numéro de Téléphone (ex: 0612345678)">
                            </div>
                            <div class="form-group">
    <label for="dob"></i></label>
    <input type="date" name="dob" id="dob" max="<?php echo date('Y-m-d', strtotime('-16 years')); ?>" required>
    <small class="form-text text-muted">Veuillez sélectionner votre date de naissance</small>
</div>

                            <div class="form-group">
                                <label for="gender"><i class="zmdi zmdi-male-female"></i></label>
                                <select name="gender" id="gender">
                                    <option value="">Sélectionnez le Genre</option>
                                    <option value="Male">Homme</option>
                                    <option value="Female">Femme</option>
                                </select>
                            </div>
<div class="form-group">
    <label for="city"><i class="zmdi zmdi-city"></i></label>
    <select name="city" id="city" class="form-control">
        <option value="">Sélectionnez une ville</option>
        <option value="Agadir">Agadir</option>
        <option value="Al Hoceima">Al Hoceïma</option>
        <option value="Azilal">Azilal</option>
        <option value="Beni Mellal">Béni Mellal</option>
        <option value="Casablanca">Casablanca</option>
        <option value="Chefchaouen">Chefchaouen</option>
        <option value="El Jadida">El Jadida</option>
        <option value="Fes">Fès</option>
        <option value="Ifrane">Ifrane</option>
        <option value="Kenitra">Kénitra</option>
        <option value="Marrakech">Marrakech</option>
        <option value="Meknes">Meknès</option>
        <option value="Ouarzazate">Ouarzazate</option>
        <option value="Rabat">Rabat</option>
        <option value="Salé">Salé</option>
        <option value="Tangier">Tanger</option>
        <option value="Taza">Taza</option>
        <option value="Tétouan">Tétouan</option>
        <option value="Taroudant">Taroudant</option>
        <option value="Safi">Safi</option>
        <option value="Nador">Nador</option>
        <option value="Oujda">Oujda</option>
        <option value="Dakhla">Dakhla</option>
        <option value="Laâyoune">Laâyoune</option>
        <option value="Errachidia">Errachidia</option>
        <option value="Guelmim">Guelmim</option>
        <option value="Tinghir">Tinghir</option>
        <option value="El Kelaâ des Sraghna">El Kelaâ des Sraghna</option>
        <option value="Settat">Settat</option>
        <option value="Ksar el-Kébir">Ksar el-Kébir</option>
        <option value="Ouezzane">Ouezzane</option>
        <option value="Berkane">Berkane</option>
        <option value="Midelt">Midelt</option>
        <option value="Figuig">Figuig</option>
        <option value="Tantan">Tan-Tan</option>
        <option value="Sidi Kacem">Sidi Kacem</option>
        <option value="Tiznit">Tiznit</option>
        <option value="Chichaoua">Chichaoua</option>
        <option value="Boudnib">Boudnib</option>
        <option value="Sidi Ifni">Sidi Ifni</option>
        <option value="Benslimane">Benslimane</option>
        <option value="Boujdour">Boujdour</option>
    </select>
</div>
<div class="form-group">
    <label for="university"><i class="zmdi zmdi-graduation-cap"></i></label>
    <select name="university" id="university" onchange="toggleCustomInput('university')">
        <option value="">Sélectionnez une université</option>
        <option value="Université Abdelmalek Essaadi">Université Abdelmalek Essaadi (Tétouan, Tanger)</option>
        <option value="Université Al Akhawayn">Université Al Akhawayn (Ifrane)</option>
        <option value="Université Cadi Ayyad">Université Cadi Ayyad (Marrakech)</option>
        <option value="Université Chouaib Doukkali">Université Chouaib Doukkali (El Jadida)</option>
        <option value="Université Euro-Méditerranéenne de Fès">Université Euro-Méditerranéenne de Fès (Fès)</option>
        <option value="Université Hassan I">Université Hassan I (Settat)</option>
        <option value="Université Hassan II de Casablanca">Université Hassan II de Casablanca (Casablanca)</option>
        <option value="Université Ibn Tofail">Université Ibn Tofail (Kénitra)</option>
        <option value="Université Ibn Zohr">Université Ibn Zohr (Agadir)</option>
        <option value="Université Internationale de Casablanca">Université Internationale de Casablanca (Casablanca)</option>
        <option value="Université Internationale de Rabat">Université Internationale de Rabat (Rabat)</option>
        <option value="Université Mohammed V">Université Mohammed V (Rabat)</option>
        <option value="Université Mohammed VI Polytechnique">Université Mohammed VI Polytechnique (Benguerir)</option>
        <option value="Université Moulay Ismail">Université Moulay Ismail (Meknès)</option>
        <option value="Université Sidi Mohamed Ben Abdellah">Université Sidi Mohamed Ben Abdellah (Fès)</option>
        <option value="Université Al Quaraouiyyin">Université Al Quaraouiyyin (Fès)</option>
        <option value="ENSIAS">ENSIAS (Rabat)</option>
        <option value="EHTP">EHTP (Casablanca)</option>
        <option value="ENSEM">ENSEM (Casablanca)</option>
        <option value="INSEA">INSEA (Rabat)</option>
        <option value="EMI">EMI (Rabat)</option>
        <option value="ISCAE">ISCAE (Casablanca)</option>
        <option value="ENCG">ENCG (Diverses Villes)</option>
        <option value="ENSA">ENSA (Diverses Villes)</option>
        <option value="EST">EST (Diverses Villes)</option>
        <option value="Autre">Autre</option>
    </select>
    <input type="text" name="custom_university" id="custom_university" class="hidden" placeholder="Entrez le nom de l’université">
</div>
                            <div class="form-group">
                                <label for="filiere"><i class="zmdi zmdi-graduation-cap"></i></label>
                                <select name="filiere" id="filiere" onchange="toggleCustomInput('filiere')">
                                    <option value="">Sélectionnez une filière</option>
                                    <option value="Informatique">Informatique</option>
                                    <option value="Génie Civil">Génie Civil</option>
                                    <option value="Génie Électrique">Génie Électrique</option>
                                    <option value="Génie Mécanique">Génie Mécanique</option>
                                    <option value="Génie Industriel">Génie Industriel</option>
                                    <option value="Médecine">Médecine</option>
                                    <option value="Pharmacie">Pharmacie</option>
                                    <option value="Sciences Économiques">Sciences Économiques</option>
                                    <option value="Gestion">Gestion</option>
                                    <option value="Commerce International">Commerce International</option>
                                    <option value="Droit">Droit</option>
                                    <option value="Sciences Politiques">Sciences Politiques</option>
                                    <option value="Mathématiques">Mathématiques</option>
                                    <option value="Physique">Physique</option>
                                    <option value="Chimie">Chimie</option>
                                    <option value="Biologie">Biologie</option>
                                    <option value="Géologie">Géologie</option>
                                    <option value="Architecture">Architecture</option>
                                    <option value="Agriculture">Agriculture</option>
                                    <option value="Sciences de l’Éducation">Sciences de l’Éducation</option>
                                    <option value="Langues et Littératures">Langues et Littératures</option>
                                    <option value="Études Islamiques">Études Islamiques</option>
                                    <option value="Psychologie">Psychologie</option>
                                    <option value="Sociologie">Sociologie</option>
                                    <option value="Tourisme et Hôtellerie">Tourisme et Hôtellerie</option>
                                    <option value="Autre">Autre</option>
                                </select>
                                <input type="text" name="custom_filiere" id="custom_filiere" class="hidden" placeholder="Entrez le nom de la filière">
                            </div>
                            <div class="form-group">
                                <input type="checkbox" name="agree-term" id="agree-term" class="agree-term" required />
                                <label for="agree-term" class="label-agree-term"><span><span></span></span>J'accepte les <a href="#" class="term-service" id="termsLink">conditions d'utilisation</a></label>
                            </div>
                            <input type="hidden" id="device_fingerprint" name="device_fingerprint">
                            <input type="hidden" id="device_name" name="device_name">
                            <input type="hidden" id="latitude" name="latitude">
                            <input type="hidden" id="longitude" name="longitude">
                            <div class="form-group form-button">
                                <input type="submit" name="signup" id="submitBtn" class="form-submit" value="S'inscrire" disabled />
                            </div>
                        </form>
                    </div>
                    <div class="signup-image">
                        <figure><img src="../assets/img/signup-image.jpg" alt="Inscription"></figure>
                        <a href="login.php" class="signup-image-link">Vous avez déjà un compte ? Connectez-vous</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Terms Modal -->
        <div id="termsModal" class="modal">
            <div class="modal-content">
                <span class="modal-close">&times;</span>
                <h3>Conditions d'Utilisation - Zouhair E-Learning</h3>
                <p>En vous inscrivant sur Zouhair E-Learning, vous acceptez les conditions suivantes. Veuillez lire attentivement :</p>
                <ul>
                    <li><strong>Collecte de Données Personnelles</strong>: Nous collectons des informations telles que votre nom complet, adresse e-mail, numéro de téléphone, date de naissance, genre, ville, université, et filière pour créer et gérer votre compte. Ces données sont utilisées pour personnaliser votre expérience d'apprentissage et assurer la sécurité de la plateforme.</li>
                    <li><strong>Suivi de la Localisation</strong>: La plateforme utilise la géolocalisation pour enregistrer votre position (latitude et longitude) lors de l'inscription et de certaines activités (comme les examens). Cela nous aide à vérifier votre identité et à prévenir les fraudes. L'accès à la localisation est obligatoire pour utiliser la plateforme.</li>
                    <li><strong>Empreinte Numérique de l'Appareil</strong>: Nous utilisons FingerprintJS pour générer une empreinte unique de votre appareil (basée sur des caractéristiques comme le navigateur, le système d'exploitation, et autres). Cela permet de sécuriser votre compte et de détecter toute activité suspecte. Les informations sur votre appareil (nom et empreinte) sont stockées de manière sécurisée.</li>
                    <li><strong>Règles de la Plateforme</strong>:
                        <ul>
                            <li>Vous devez avoir au moins 16 ans pour vous inscrire.</li>
                            <li>Il est strictement interdit de partager votre compte ou de tricher lors des examens. Toute violation entraînera une suspension ou une suppression du compte.</li>
                            <li>Vous êtes responsable de maintenir la confidentialité de votre mot de passe et de vos informations de connexion.</li>
                            <li>Le contenu téléchargé (par exemple, réponses aux quiz) doit respecter les lois et ne pas contenir de matériel inapproprié.</li>
                        </ul>
                    </li>
                    <li><strong>Utilisation des Données</strong>: Vos données peuvent être utilisées pour analyser les performances, améliorer la plateforme, et communiquer avec vous (par exemple, via des notifications par e-mail). Nous ne vendons pas vos données à des tiers, mais elles peuvent être partagées avec des partenaires de confiance pour des services comme l'hébergement ou l'analyse.</li>
                    <li><strong>Consentement</strong>: En cochant la case "J'accepte les conditions d'utilisation", vous consentez à la collecte, au stockage, et à l'utilisation de vos données comme décrit ci-dessus. Vous acceptez également de respecter les règles de la plateforme.</li>
                    <li><strong>Modifications des Conditions</strong>: Nous pouvons mettre à jour ces conditions à tout moment. Les utilisateurs seront informés des changements importants via e-mail ou sur la plateforme.</li>
                </ul>
                <p>Pour toute question, contactez-nous à <a href="mailto:support@zouhairelearning.com">support@zouhairelearning.com</a>.</p>
                <button class="close-btn">Fermer</button>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Toggle custom input fields based on dropdown selection
        function toggleCustomInput(field) {
            const select = document.getElementById(field);
            const customInput = document.getElementById(`custom_${field}`);
            if (select.value === 'Autre') {
                customInput.classList.remove('hidden');
                customInput.required = true;
            } else {
                customInput.classList.add('hidden');
                customInput.required = false;
                customInput.value = '';
            }
        }

        // Generate device fingerprint
        Fingerprint2.get(function(components) {
            const values = components.map(c => c.value);
            const fingerprint = Fingerprint2.x64hash128(values.join(''), 31);
            document.getElementById('device_fingerprint').value = fingerprint;
            document.getElementById('device_name').value = navigator.userAgent;
            checkFormReady();
        });

        // Enforce geolocation access
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

        // Enable submit button when all required fields are ready
        function checkFormReady() {
            const fingerprint = document.getElementById('device_fingerprint').value;
            const latitude = document.getElementById('latitude').value;
            const longitude = document.getElementById('longitude').value;
            if (fingerprint && latitude && longitude) {
                document.getElementById('submitBtn').disabled = false;
            }
        }

        // Client-side validation
        $('#registerForm').on('submit', function(e) {
            const password = $('#password').val();
            const confirmPassword = $('#confirm_password').val();
            const phone = $('#phone').val();
            const dob = $('#dob').val();
            const university = $('#university').val();
            const customUniversity = $('#custom_university').val();
            const filiere = $('#filiere').val();
            const customFiliere = $('#custom_filiere').val();

            // Password match check
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas.');
                return;
            }

            // Phone validation
            if (phone && !/^[0-9]{10,15}$/.test(phone)) {
                e.preventDefault();
                alert('Veuillez entrer un numéro de téléphone valide (10-15 chiffres).');
                return;
            }

            // DOB validation (at least 16 years old)
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

            // University validation
            if (university === 'Autre' && (!customUniversity || customUniversity.length > 100)) {
                e.preventDefault();
                alert('Veuillez entrer un nom d’université valide (max 100 caractères).');
                return;
            }
            if (university && university !== 'Autre' && university.length > 100) {
                e.preventDefault();
                alert('Le nom de l’université sélectionnée est trop long.');
                return;
            }

            // Filiere validation
            if (filiere === 'Autre' && (!customFiliere || customFiliere.length > 100)) {
                e.preventDefault();
                alert('Veuillez entrer un nom de filière valide (max 100 caractères).');
                return;
            }
            if (filiere && filiere !== 'Autre' && filiere.length > 100) {
                e.preventDefault();
                alert('Le nom de la filière sélectionnée est trop long.');
                return;
            }
        });

        // Modal Toggle
        $(document).ready(function() {
            const modal = $('#termsModal');
            const termsLink = $('#termsLink');
            const closeBtn = $('.modal-close, .close-btn');

            // Open modal on terms link click
            termsLink.on('click', function(e) {
                e.preventDefault();
                modal.css('display', 'block');
            });

            // Close modal on close button or outside click
            closeBtn.on('click', function() {
                modal.css('display', 'none');
            });

            // Close modal when clicking outside the modal content
            $(window).on('click', function(e) {
                if (e.target === modal[0]) {
                    modal.css('display', 'none');
                }
            });
        });
    </script>
</body>
</html>