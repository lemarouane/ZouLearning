<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch stats
$total_students = $db->query("SELECT COUNT(*) FROM students")->fetch_row()[0];
$validated_students = $db->query("SELECT COUNT(*) FROM students WHERE status = 'approved'")->fetch_row()[0];
$pending_students = $db->query("SELECT COUNT(*) FROM students WHERE status = 'pending'")->fetch_row()[0];
$total_courses = $db->query("SELECT COUNT(*) FROM courses")->fetch_row()[0] ?? 0;
$total_levels = $db->query("SELECT COUNT(*) FROM levels")->fetch_row()[0] ?? 0;
$notifications = $db->query("SELECT COUNT(*) FROM activity_logs WHERE timestamp > NOW() - INTERVAL 1 DAY")->fetch_row()[0] ?? 0;
$ungraded_submissions = $db->query("SELECT COUNT(*) AS count FROM quiz_submissions WHERE grade IS NULL")->fetch_assoc()['count'];
$pending_devices = $db->query("SELECT COUNT(*) FROM device_attempts WHERE status = 'pending'")->fetch_row()[0];

// Recent data
$recent_students = $db->query("SELECT * FROM students ORDER BY created_at DESC LIMIT 5");
$recent_courses = $db->query("SELECT c.*, s.name AS subject FROM courses c LEFT JOIN subjects s ON c.subject_id = s.id ORDER BY c.created_at DESC LIMIT 5") ?? [];
$notifs = $db->query("SELECT * FROM activity_logs ORDER BY timestamp DESC LIMIT 5") ?? [];

// Subjects chart data
$subjects_chart = [];
$result = $db->query("SELECT s.name, COUNT(c.id) as count FROM subjects s LEFT JOIN courses c ON s.id = c.subject_id GROUP BY s.id, s.name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $subjects_chart[$row['name']] = $row['count'];
    }
}

// Activity trend data (last 7 days)
$activity_trend = [];
$result = $db->query("SELECT DATE(timestamp) AS day, COUNT(*) AS count FROM activity_logs WHERE timestamp > NOW() - INTERVAL 7 DAY GROUP BY day ORDER BY day ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $activity_trend[$row['day']] = $row['count'];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Zouhair E-Learning</title>
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
                <h3><i class="fas fa-users"></i> Total des Étudiants</h3>
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
                <h3><i class="fas fa-layer-group"></i> Total des Niveaux</h3>
                <p><a href="manage_levels.php"><?php echo $total_levels; ?></a></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-tasks"></i> À Noter</h3>
                <p><a href="grade_quizzes.php"><?php echo $ungraded_submissions; ?></a></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-mobile-alt"></i> Appareils en Attente</h3>
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
                <h2><i class="fas fa-book"></i> Cours par Matière</h2>
                <canvas id="subjectChart"></canvas>
            </div>
            <div class="chart-container">
                <h2><i class="fas fa-chart-line"></i> Tendance d'Activité</h2>
                <canvas id="activityChart"></canvas>
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
                                <td><?php echo $student['created_at']; ?></td>
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
                                <td><?php echo $course['created_at'] ?? 'N/A'; ?></td>
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
                labels: ['Validated', 'Pending'],
                datasets: [{
                    label: 'Students',
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
                    label: 'Activity',
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