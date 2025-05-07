<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zouhair E-Learning - Bienvenue</title>
    <link rel="icon" type="image/png" href="assets/img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            overflow-x: hidden;
            background: linear-gradient(135deg, #1c2526, #9b59b6);
            color: #fff;
            min-height: 100vh;
            position: relative;
        }

        /* Canvas for Cursor-Following Elements */
        canvas#cursor-canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            z-index: 2;
            padding: 20px;
        }

        .hero h1 {
            font-size: 4.5rem;
            font-weight: 700;
            background: linear-gradient(90deg, #ff2e63, #00f4ff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 0 20px rgba(0, 244, 255, 0.7);
            margin-bottom: 20px;
            animation: neon-glow 1.5s ease-in-out infinite alternate;
        }

        @keyframes neon-glow {
            from { text-shadow: 0 0 10px rgba(0, 244, 255, 0.7); }
            to { text-shadow: 0 0 25px rgba(255, 46, 99, 0.9); }
        }

        .hero p {
            font-size: 1.2rem;
            font-weight: 300;
            margin-bottom: 40px;
            max-width: 600px;
            opacity: 0.9;
        }

        /* Buttons */
        .btn-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            color: #fff;
            background: linear-gradient(90deg, #ff2e63, #9b59b6);
            border-radius: 50px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s, background 0.3s;
        }

        .btn:hover {
            transform: scale(1.15) rotate(3deg);
            box-shadow: 0 0 30px rgba(0, 244, 255, 0.8);
            background: linear-gradient(90deg, #00f4ff, #9b59b6);
        }

        /* Ripple Effect */
        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        /* Floating Hexagons */
        .hexagon {
            position: absolute;
            width: 50px;
            height: 50px;
            background: rgba(255, 46, 99, 0.2);
            clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
            box-shadow: 0 0 20px rgba(0, 244, 255, 0.5);
            animation: spin 10s linear infinite;
            z-index: 1;
            pointer-events: none;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 20px;
            background: rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 2;
            font-size: 0.9rem;
            font-weight: 300;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 { font-size: 2.5rem; }
            .hero p { font-size: 1rem; }
            .btn-container { flex-direction: column; }
            .btn { padding: 12px 25px; font-size: 1rem; }
            .hexagon { width: 30px; height: 30px; }
        }

        @media (max-width: 480px) {
            .hero h1 { font-size: 2rem; }
            .hero p { font-size: 0.9rem; }
        }
    </style>
</head>
<body>
    <!-- Canvas for Cursor-Following Orbs -->
    <canvas id="cursor-canvas"></canvas>

    <!-- Hero Section -->
    <section class="hero" data-aos="fade-up">
        <h1>Zouhair E-Learning</h1>
        <p>Plongez dans un univers d'apprentissage vibrant et interactif. Connectez-vous pour dominer vos cours !</p>
        <div class="btn-container">
            <a href="admin/login.php" class="btn" data-aos="zoom-in" data-aos-delay="200">
                <i class="fas fa-user-shield"></i> Connexion Admin
            </a>
            <a href="student/login.php" class="btn" data-aos="zoom-in" data-aos-delay="400">
                <i class="fas fa-user-graduate"></i> Connexion Étudiant
            </a>
        </div>
    </section>

    <!-- Floating Hexagons (Positioned by JS) -->
    <div class="hexagon" id="hex1"></div>
    <div class="hexagon" id="hex2"></div>
    <div class="hexagon" id="hex3"></div>

    <!-- Footer -->
    <footer>
        © 2025 Zouhair E-Learning. Tous droits réservés.
    </footer>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        // AOS Initialization
        AOS.init({
            duration: 1000,
            once: true
        });

        // Cursor-Following Orbs and Hexagons
        const canvas = document.getElementById('cursor-canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });

        const orbs = [];
        let mouseX = 0;
        let mouseY = 0;

        // Track Mouse
        document.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
            // Spawn new orb
            orbs.push({
                x: mouseX,
                y: mouseY,
                radius: Math.random() * 10 + 5,
                color: ['#ff2e63', '#00f4ff', '#9b59b6'][Math.floor(Math.random() * 3)],
                alpha: 1,
                life: 100
            });
        });

        // Animate Orbs
        function animateOrbs() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            orbs.forEach((orb, index) => {
                if (orb.alpha <= 0) {
                    orbs.splice(index, 1);
                    return;
                }
                ctx.beginPath();
                ctx.arc(orb.x, orb.y, orb.radius, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(${parseInt(orb.color.slice(1, 3), 16)}, ${parseInt(orb.color.slice(3, 5), 16)}, ${parseInt(orb.color.slice(5, 7), 16)}, ${orb.alpha})`;
                ctx.shadowBlur = 20;
                ctx.shadowColor = orb.color;
                ctx.fill();
                orb.alpha -= 0.02;
                orb.life--;
                orb.x += (mouseX - orb.x) * 0.05;
                orb.y += (mouseY - orb.y) * 0.05;
            });
            requestAnimationFrame(animateOrbs);
        }

        animateOrbs();

        // Hexagon Movement
        const hexagons = [
            document.getElementById('hex1'),
            document.getElementById('hex2'),
            document.getElementById('hex3')
        ];

        hexagons.forEach((hex, i) => {
            let offsetX = (Math.random() - 0.5) * 200;
            let offsetY = (Math.random() - 0.5) * 200;
            function moveHexagon() {
                const dx = mouseX - (parseFloat(hex.style.left) || 0) - offsetX;
                const dy = mouseY - (parseFloat(hex.style.top) || 0) - offsetY;
                hex.style.left = `${parseFloat(hex.style.left || 0) + dx * 0.03}px`;
                hex.style.top = `${parseFloat(hex.style.top || 0) + dy * 0.03}px`;
                requestAnimationFrame(moveHexagon);
            }
            hex.style.background = ['#ff2e63', '#00f4ff', '#9b59b6'][i];
            moveHexagon();
        });

        // Button Hover Particle Burst
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(button => {
            button.addEventListener('mouseenter', () => {
                const rect = button.getBoundingClientRect();
                for (let i = 0; i < 10; i++) {
                    orbs.push({
                        x: rect.left + rect.width / 2,
                        y: rect.top + rect.height / 2,
                        radius: Math.random() * 5 + 3,
                        color: '#00f4ff',
                        alpha: 1,
                        life: 50,
                        vx: (Math.random() - 0.5) * 10,
                        vy: (Math.random() - 0.5) * 10
                    });
                }
            });
        });
    </script>
</body>
</html>