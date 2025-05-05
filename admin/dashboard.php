<?php
session_start();
require_once '../includes/db_connect.php';

// Check admin session
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch stats with is_archived filter
$stmt = $db->prepare("SELECT COUNT(*) FROM students WHERE is_archived = 0");
$stmt->execute();
$total_students = $stmt->get_result()->fetch_row()[0] ?? 0;
$stmt->close();

$stmt = $db->prepare("SELECT COUNT(*) FROM students WHERE status = 'approved' AND is_archived = 0");
$stmt->execute();
$validated_students = $stmt->get_result()->fetch_row()[0] ?? 0;
$stmt->close();

$stmt = $db->prepare("SELECT COUNT(*) FROM students WHERE status = 'pending' AND is_archived = 0");
$stmt->execute();
$pending_students = $stmt->get_result()->fetch_row()[0] ?? 0;
$stmt->close();

$stmt = $db->prepare("SELECT COUNT(*) FROM courses WHERE is_archived = 0");
$stmt->execute();
$total_courses = $stmt->get_result()->fetch_row()[0] ?? 0;
$stmt->close();

$stmt = $db->prepare("SELECT COUNT(*) FROM levels WHERE is_archived = 0");
$stmt->execute();
$total_levels = $stmt->get_result()->fetch_row()[0] ?? 0;
$stmt->close();

$stmt = $db->prepare("SELECT COUNT(*) FROM activity_logs WHERE timestamp > NOW() - INTERVAL 1 DAY");
$stmt->execute();
$notifications = $stmt->get_result()->fetch_row()[0] ?? 0;
$stmt->close();

$stmt = $db->prepare("SELECT COUNT(*) AS count FROM quiz_submissions WHERE grade IS NULL");
$stmt->execute();
$ungraded_submissions = $stmt->get_result()->fetch_assoc()['count'] ?? 0;
$stmt->close();

$stmt = $db->prepare("SELECT COUNT(*) FROM device_attempts WHERE status = 'pending'");
$stmt->execute();
$pending_devices = $stmt->get_result()->fetch_row()[0] ?? 0;
$stmt->close();

// Gender distribution
$gender_chart = [];
$stmt = $db->prepare("SELECT gender, COUNT(*) as count FROM students WHERE gender IS NOT NULL AND is_archived = 0 GROUP BY gender");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $gender_chart[$row['gender']] = $row['count'];
}
$stmt->close();

// University distribution (top 5 + Other)
$university_chart = [];
$other_university_count = 0;
$predefined_universities = [
    'Abdelmalek Essaadi University', 'Al Akhawayn University', 'Cadi Ayyad University', 'Chouaib Doukkali University',
    'Euro-Mediterranean University of Fes', 'Hassan I University', 'Hassan II University of Casablanca', 'Ibn Tofail University',
    'Ibn Zohr University', 'International University of Casablanca', 'International University of Rabat', 'Mohammed V University',
    'Mohammed VI Polytechnic University', 'Moulay Ismail University', 'Sidi Mohamed Ben Abdellah University', 'University of Al Quaraouiyyin',
    'ENSIAS', 'EHTP', 'ENSEM', 'INSEA', 'EMI', 'ISCAE', 'ENCG', 'ENSA', 'EST'
];
$stmt = $db->prepare("SELECT university, COUNT(*) as count FROM students WHERE university IS NOT NULL AND is_archived = 0 GROUP BY university ORDER BY count DESC");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    if (count($university_chart) < 5 && in_array($row['university'], $predefined_universities)) {
        $university_chart[$row['university']] = $row['count'];
    } else {
        $other_university_count += $row['count'];
    }
}
$stmt->close();
if ($other_university_count > 0) {
    $university_chart['Other'] = $other_university_count;
}

// Filiere distribution (top 5 + Other)
$filiere_chart = [];
$other_filiere_count = 0;
$predefined_filieres = [
    'Informatique', 'Génie Civil', 'Génie Électrique', 'Génie Mécanique', 'Génie Industriel', 'Médecine', 'Pharmacie',
    'Sciences Économiques', 'Gestion', 'Commerce International', 'Droit', 'Sciences Politiques', 'Mathématiques', 'Physique',
    'Chimie', 'Biologie', 'Géologie', 'Architecture', 'Agriculture', 'Sciences de l’Éducation', 'Langues et Littératures',
    'Études Islamiques', 'Psychologie', 'Sociologie', 'Tourisme et Hôtellerie'
];
$stmt = $db->prepare("SELECT filiere, COUNT(*) as count FROM students WHERE filiere IS NOT NULL AND is_archived = 0 GROUP BY filiere ORDER BY count DESC");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    if (count($filiere_chart) < 5 && in_array($row['filiere'], $predefined_filieres)) {
        $filiere_chart[$row['filiere']] = $row['count'];
    } else {
        $other_filiere_count += $row['count'];
    }
}
$stmt->close();
if ($other_filiere_count > 0) {
    $filiere_chart['Other'] = $other_filiere_count;
}

// City distribution (top 5 + Other)
$city_chart = [];
$other_city_count = 0;
$stmt = $db->prepare("SELECT city, COUNT(*) as count FROM students WHERE city IS NOT NULL AND is_archived = 0 GROUP BY city ORDER BY count DESC");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    if (count($city_chart) < 5) {
        $city_chart[$row['city']] = $row['count'];
    } else {
        $other_city_count += $row['count'];
    }
}
$stmt->close();
if ($other_city_count > 0) {
    $city_chart['Other'] = $other_city_count;
}

// Recent data
$stmt = $db->prepare("SELECT * FROM students WHERE is_archived = 0 ORDER BY created_at DESC LIMIT 5");
$stmt->execute();
$recent_students = $stmt->get_result();
$stmt->close();

$stmt = $db->prepare("SELECT c.*, s.name AS subject FROM courses c LEFT JOIN subjects s ON c.subject_id = s.id WHERE c.is_archived = 0 AND (s.is_archived = 0 OR s.is_archived IS NULL) ORDER BY c.created_at DESC LIMIT 5");
$stmt->execute();
$recent_courses = $stmt->get_result();
$stmt->close();

$stmt = $db->prepare("SELECT * FROM activity_logs ORDER BY timestamp DESC LIMIT 5");
$stmt->execute();
$notifs = $stmt->get_result();
$stmt->close();

// Subjects chart data
$subjects_chart = [];
$stmt = $db->prepare("SELECT s.name, COUNT(c.id) as count FROM subjects s LEFT JOIN courses c ON s.id = c.subject_id WHERE s.is_archived = 0 AND (c.is_archived = 0 OR c.is_archived IS NULL) GROUP BY s.id, s.name");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $subjects_chart[$row['name']] = $row['count'];
}
$stmt->close();

// Activity trend data (last 7 days)
$activity_trend = [];
$stmt = $db->prepare("SELECT DATE(timestamp) AS day, COUNT(*) AS count FROM activity_logs WHERE timestamp > NOW() - INTERVAL 7 DAY GROUP BY day ORDER BY day ASC");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $activity_trend[$row['day']] = $row['count'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-tachometer-alt"></i> Tableau de Bord Admin</h1>
        
        <!-- Section des Statistiques -->
        <section class="stats">
            <div class="stat-card">
                <h3><i class="fas fa-users"></i> Étudiants</h3>
                <p><?php echo $total_students; ?></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-check-circle"></i> Validés</h3>
                <p><?php echo $validated_students; ?></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-hourglass-half"></i> En Attente</h3>
                <p><a href="manage_students.php"><?php echo $pending_students; ?></a></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-book"></i> Total des Cours</h3>
                <p><a href="manage_courses.php"><?php echo $total_courses; ?></a></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-layer-group"></i> Niveaux</h3>
                <p><a href="manage_levels.php"><?php echo $total_levels; ?></a></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-tasks"></i> À Noter</h3>
                <p><a href="grade_quizzes.php"><?php echo $ungraded_submissions; ?></a></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-mobile-alt"></i> En Attente</h3>
                <p><a href="device_requests.php"><?php echo $pending_devices; ?></a></p>
            </div>
        </section>

        <!-- Section des Graphiques -->
        <section class="charts">
            <div class="chart-container">
                <h2><i class="fas fa-users"></i> Statut des Étudiants</h2>
                <canvas id="studentChart"></canvas>
            </div>
            <div class="chart-container">
                <h2><i class="fas fa-venus-mars"></i> Distribution par Genre</h2>
                <canvas id="genderChart"></canvas>
            </div>
            <div class="chart-container">
                <h2><i class="fas fa-university"></i> Universités/Écoles</h2>
                <canvas id="universityChart"></canvas>
            </div>
            <div class="chart-container">
                <h2><i class="fas fa-graduation-cap"></i> Filières</h2>
                <canvas id="filiereChart"></canvas>
            </div>
            <div class="chart-container">
                <h2><i class="fas fa-book"></i> Cours par Matière</h2>
                <canvas id="subjectChart"></canvas>
            </div>
            <div class="chart-container">
                <h2><i class="fas fa-chart-line"></i> Tendance d'Activité</h2>
                <canvas id="activityChart"></canvas>
            </div>
            <div class="chart-container">
                <h2><i class="fas fa-city"></i> Distribution par Ville</h2>
                <canvas id="cityChart"></canvas>
            </div>
        </section>

        <!-- Tableaux Récents -->
        <section class="recent-tables">
            <div class="notifications">
                <h2><i class="fas fa-user-plus"></i> Étudiants Récents</h2>
                <table class="display">
                    <thead>
                        <tr><th>Nom</th><th>Email</th><th>Date d'Inscription</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($student = $recent_students->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td><?php echo htmlspecialchars($student['created_at']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <div class="notifications">
                <h2><i class="fas fa-book-open"></i> Cours Récents</h2>
                <table class="display">
                    <thead>
                        <tr><th>Titre</th><th>Matière</th><th>Date d'Ajout</th></tr>
                    </thead>
                    <tbody>
                        <?php if ($recent_courses): while ($course = $recent_courses->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($course['title'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($course['subject'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($course['created_at'] ?? 'N/A'); ?></td>
                            </tr>
                        <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    
    <?php include '../includes/footer.php'; ?>

    <script>
        new Chart(document.getElementById('studentChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Validés', 'En Attente'],
                datasets: [{
                    label: 'Étudiants',
                    data: [<?php echo $validated_students; ?>, <?php echo $pending_students; ?>],
                    backgroundColor: ['#4CAF50', '#FF9800'],
                    borderColor: ['#388E3C', '#F57C00'],
                    borderWidth: 1
                }]
            },
            options: {
                scales: { y: { beginAtZero: true, ticks: { color: '#666' } }, x: { ticks: { color: '#666' } } },
                animation: { duration: 1000, easing: 'easeInOutQuad' },
                plugins: { legend: { labels: { color: '#666' } } }
            }
        });

        new Chart(document.getElementById('genderChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: [<?php echo "'" . implode("','", array_keys($gender_chart)) . "'"; ?>],
                datasets: [{
                    data: [<?php echo implode(',', array_values($gender_chart)); ?>],
                    backgroundColor: ['#2196F3', '#FF5722', '#9C27B0'],
                    borderColor: ['#1976D2', '#E64A19', '#7B1FA2'],
                    borderWidth: 1
                }]
            },
            options: {
                plugins: { legend: { position: 'right', labels: { color: '#666' } } },
                animation: { animateRotate: true, animateScale: true, duration: 1500 }
            }
        });

        new Chart(document.getElementById('universityChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: [<?php echo "'" . implode("','", array_keys($university_chart)) . "'"; ?>],
                datasets: [{
                    label: 'Étudiants',
                    data: [<?php echo implode(',', array_values($university_chart)); ?>],
                    backgroundColor: '#4CAF50',
                    borderColor: '#388E3C',
                    borderWidth: 1
                }]
            },
            options: {
                scales: { y: { beginAtZero: true, ticks: { color: '#666' } }, x: { ticks: { color: '#666' } } },
                animation: { duration: 1000, easing: 'easeInOutQuad' },
                plugins: { legend: { labels: { color: '#666' } } }
            }
        });

        new Chart(document.getElementById('filiereChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: [<?php echo "'" . implode("','", array_keys($filiere_chart)) . "'"; ?>],
                datasets: [{
                    label: 'Étudiants',
                    data: [<?php echo implode(',', array_values($filiere_chart)); ?>],
                    backgroundColor: '#FF9800',
                    borderColor: '#F57C00',
                    borderWidth: 1
                }]
            },
            options: {
                scales: { y: { beginAtZero: true, ticks: { color: '#666' } }, x: { ticks: { color: '#666' } } },
                animation: { duration: 1000, easing: 'easeInOutQuad' },
                plugins: { legend: { labels: { color: '#666' } } }
            }
        });

        new Chart(document.getElementById('subjectChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: [<?php echo "'" . implode("','", array_keys($subjects_chart)) . "'"; ?>],
                datasets: [{
                    data: [<?php echo implode(',', array_values($subjects_chart)); ?>],
                    backgroundColor: ['#4CAF50', '#2196F3', '#FF5722', '#FFC107', '#9C27B0'],
                    borderColor: ['#388E3C', '#1976D2', '#E64A19', '#FFB300', '#7B1FA2'],
                    borderWidth: 1
                }]
            },
            options: {
                plugins: { legend: { position: 'right', labels: { color: '#666' } } },
                animation: { animateRotate: true, animateScale: true, duration: 1500 }
            }
        });

        new Chart(document.getElementById('activityChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: [<?php echo "'" . implode("','", array_keys($activity_trend)) . "'"; ?>],
                datasets: [{
                    label: 'Activité',
                    data: [<?php echo implode(',', array_values($activity_trend)); ?>],
                    borderColor: '#2196F3',
                    backgroundColor: 'rgba(33, 150, 243, 0.2)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#2196F3',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                scales: { y: { beginAtZero: true, ticks: { color: '#666' } }, x: { ticks: { color: '#666' } } },
                animation: { duration: 1000 },
                plugins: { legend: { labels: { color: '#666' } } }
            }
        });

        new Chart(document.getElementById('cityChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: [<?php echo "'" . implode("','", array_keys($city_chart)) . "'"; ?>],
                datasets: [{
                    label: 'Étudiants',
                    data: [<?php echo implode(',', array_values($city_chart)); ?>],
                    backgroundColor: '#9C27B0',
                    borderColor: '#7B1FA2',
                    borderWidth: 1
                }]
            },
            options: {
                scales: { y: { beginAtZero: true, ticks: { color: '#666' } }, x: { ticks: { color: '#666' } } },
                animation: { duration: 1000, easing: 'easeInOutQuad' },
                plugins: { legend: { labels: { color: '#666' } } }
            }
        });

        $(document).ready(function() {
            $('.stat-card').each(function(i) { 
                $(this).delay(i * 200).fadeIn(500); 
            });
            $('.display').DataTable({ searching: false, paging: false, info: false });
            $('.charts, .recent-tables, .activity-log').addClass('animate-in');
        });
    </script>
</body>
</html>