<?php
session_start();
require_once '../includes/db_connect.php';
if (isset($_SESSION['student_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>En attente d'approbation - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <!-- Font Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            text-decoration: none;
        }
        .form-submit:hover {
            background: linear-gradient(90deg, #1e40af, #3b82f6);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(59, 130, 246, 0.4);
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
        .error {
            color: #dc2626;
            font-size: 18px;
            margin-bottom: 20px;
            font-weight: 500;
            line-height: 1.5;
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
            .error {
                font-size: 16px;
            }
            .form-submit {
                font-size: 16px;
                padding: 15px 40px;
            }
        }
    </style>
</head>
<body>
    <div class="main">
        <!-- Pending approval section -->
        <section class="signin">
            <div class="container">
                <div class="signin-content">
                    <div class="signin-image">
                        <figure><img src="../assets/img/signin-image.jpg" alt="En attente d'approbation"></figure>
                        <a href="login.php" class="signin-image-link">Retour à la connexion</a>
                    </div>
                    <div class="signin-form">
                        <h2 class="form-title"><i class="zmdi zmdi-time"></i> En attente d'approbation</h2>
                        <p class="error">Votre appareil nécessite une approbation de l'administrateur. Veuillez réessayer plus tard ou utiliser votre appareil initial.</p>
                        <div class="form-group form-button">
                            <a href="login.php" class="form-submit"><i class="zmdi zmdi-arrow-left"></i> Retour à la connexion</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>
</html>