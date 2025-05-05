<?php
session_start();
ini_set('display_errors', 1); // Show errors for debugging
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/db_connect.php';
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = (int)$_SESSION['student_id'];

// Fetch student details (only non-archived)
$student_query = $db->query("SELECT * FROM students WHERE id = $student_id AND is_archived = 0");
if (!$student_query || $student_query->num_rows == 0) {
    die("Erreur : Étudiant introuvable ou compte archivé.");
}
$student = $student_query->fetch_assoc();

// Stats
$total_courses_query = $db->query("
    SELECT COUNT(DISTINCT course_id) 
    FROM (
        SELECT sc.course_id 
        FROM student_courses sc 
        JOIN courses c ON sc.course_id = c.id
        WHERE sc.student_id = $student_id AND c.is_archived = 0
        UNION
        SELECT c.id AS course_id 
        FROM student_subjects ss 
        JOIN courses c ON ss.subject_id = c.subject_id 
        JOIN subjects s ON c.subject_id = s.id
        WHERE ss.student_id = $student_id AND ss.all_courses = 1 AND c.is_archived = 0 AND s.is_archived = 0
    ) AS unique_courses
");
$total_courses = $total_courses_query->fetch_row()[0] ?? 0;

$total_subjects = $db->query("
    SELECT COUNT(*) 
    FROM student_subjects ss 
    JOIN subjects s ON ss.subject_id = s.id 
    WHERE ss.student_id = $student_id AND s.is_archived = 0
")->fetch_row()[0] ?? 0;

$level_query = $db->query("
    SELECT name 
    FROM levels 
    WHERE id = " . (int)$student['level_id'] . " AND is_archived = 0
");
$level = $level_query && $level_query->num_rows > 0 ? $level_query->fetch_assoc()['name'] : 'N/A';

// Recent courses
$courses_query = $db->query("
    SELECT DISTINCT c.title, s.name AS subject 
    FROM (
        SELECT sc.course_id 
        FROM student_courses sc 
        JOIN courses c ON sc.course_id = c.id
        WHERE sc.student_id = $student_id AND c.is_archived = 0
        UNION
        SELECT c.id AS course_id 
        FROM student_subjects ss 
        JOIN courses c ON ss.subject_id = c.subject_id 
        JOIN subjects s ON c.subject_id = s.id
        WHERE ss.student_id = $student_id AND ss.all_courses = 1 AND c.is_archived = 0 AND s.is_archived = 0
    ) AS unique_courses
    JOIN courses c ON unique_courses.course_id = c.id 
    JOIN subjects s ON c.subject_id = s.id 
    WHERE s.is_archived = 0
    LIMIT 5
");
if (!$courses_query) {
    die("Erreur dans la requête des cours : " . $db->error);
}
$courses = $courses_query;

// Subjects chart data
$subjects_chart = [];
$result = $db->query("
    SELECT s.name, COUNT(DISTINCT c.id) as count 
    FROM student_subjects ss 
    JOIN subjects s ON ss.subject_id = s.id 
    LEFT JOIN courses c ON s.id = c.subject_id 
    LEFT JOIN student_courses sc ON c.id = sc.course_id AND sc.student_id = $student_id
    WHERE ss.student_id = $student_id 
    AND s.is_archived = 0 
    AND (ss.all_courses = 1 OR sc.course_id IS NOT NULL)
    AND (c.id IS NULL OR c.is_archived = 0)
    GROUP BY s.id, s.name
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $subjects_chart[$row['name']] = $row['count'];
    }
} else {
    echo "Erreur dans la requête des sujets : " . $db->error;
}

// New and graded quizzes
$new_quizzes = $db->query("
    SELECT COUNT(*) AS count
    FROM quizzes q
    JOIN subjects s ON q.subject_id = s.id
    WHERE q.is_archived = 0 
    AND s.is_archived = 0
    AND q.subject_id IN (
        SELECT subject_id FROM student_subjects WHERE student_id = $student_id
        UNION
        SELECT subject_id FROM student_courses sc
        JOIN courses c ON sc.course_id = c.id
        WHERE sc.student_id = $student_id AND c.is_archived = 0
    )
    AND q.id NOT IN (
        SELECT quiz_id FROM quiz_submissions WHERE student_id = $student_id
    )
")->fetch_assoc()['count'];

$graded_quizzes = $db->query("
    SELECT COUNT(*) AS count
    FROM quiz_submissions qs
    JOIN quizzes q ON qs.quiz_id = q.id
    WHERE qs.student_id = $student_id 
    AND qs.grade IS NOT NULL 
    AND q.is_archived = 0
")->fetch_assoc()['count'];
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
    <?php include '../includes/student_header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-tachometer-alt"></i> Tableau de Bord Étudiant</h1>
        
        <!-- Stats Section -->
        <section class="stats">
            <div class="stat-card">
                <h3><i class="fas fa-book"></i> Cours Total</h3>
                <p><?php echo $total_courses; ?></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-folder"></i> Matières</h3>
                <p><?php echo $total_subjects; ?></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-question-circle"></i> Notifications Examens</h3>
                <p><a href="quizzes.php"><?php echo $new_quizzes; ?> Examens</a></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-question-circle"></i> Notifications Examens</h3>
                <p><a href="quizzes.php"><?php echo $graded_quizzes; ?> Examens notés</a></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-layer-group"></i> Niveau</h3>
                <p><?php echo htmlspecialchars($level); ?></p>
            </div>
        </section>

        <!-- Charts Section -->
        <section class="charts">
            <div class="chart-container">
                <h2><i class="fas fa-book"></i> Répartition des Cours par Sujet</h2>
                <canvas id="subjectChart"></canvas>
            </div>
        </section>

        <!-- Recent Courses -->
        <section class="recent-tables">
            <div class="notifications">
                <h2><i class="fas fa-book-open"></i> Cours Récent</h2>
                <table class="display">
                    <thead>
                        <tr><th>Titre</th><th>Sujet</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($course = $courses->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($course['title']); ?></td>
                                <td><?php echo htmlspecialchars($course['subject']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    <?php include '../includes/footer.php'; ?>

    <script>
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

        $(document).ready(function() {
            $('.stat-card').each(function(i) { 
                $(this).delay(i * 200).fadeIn(500); 
            });
            $('.display').DataTable({ searching: false, paging: false, info: false });
            $('.charts, .recent-tables').addClass('animate-in');

            // Inactivity and tab switch detection
            let inactivityTimer;
            const INACTIVITY_TIMEOUT = 1800000; // 30 minutes in milliseconds

            // Reset timer on activity
            function resetInactivityTimer() {
                clearTimeout(inactivityTimer);
                inactivityTimer = setTimeout(logout, INACTIVITY_TIMEOUT);
            }

            // Logout function
            function logout() {
                window.location.href = 'logout.php';
            }

            // Track activity on dashboard
            $(document).on('mousemove keydown', function() {
                resetInactivityTimer();
            });

            // Track tab visibility
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    // Tab is hidden, start checking for activity in other tabs
                    startActivityCheck();
                } else {
                    // Tab is visible, reset timer and stop activity check
                    resetInactivityTimer();
                    stopActivityCheck();
                }
            });

            let activityCheckInterval;
            function startActivityCheck() {
                activityCheckInterval = setInterval(function() {
                    $.ajax({
                        url: 'check_activity.php',
                        method: 'POST',
                        data: { student_id: <?php echo $student_id; ?> },
                        success: function(response) {
                            if (response.isActive) {
                                // Activity detected in another tab, start logout timer
                                clearTimeout(inactivityTimer);
                                inactivityTimer = setTimeout(logout, INACTIVITY_TIMEOUT);
                            }
                        },
                        error: function() {
                            console.error('Error checking activity');
                        }
                    });
                }, 5000); // Check every 5 seconds
            }

            function stopActivityCheck() {
                clearInterval(activityCheckInterval);
            }

            // Initialize timer
            resetInactivityTimer();
        });
    </script>
</body>
</html>2