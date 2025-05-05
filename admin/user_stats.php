<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/device_utils.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Get student_id from URL
$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
if ($student_id <= 0) {
    header("Location: sessions.php");
    exit;
}

// Get student info
$stmt = $db->prepare("SELECT full_name, email FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$student) {
    header("Location: sessions.php");
    exit;
}

// Initialize date range (default: last 30 days)
$end_date = date('Y-m-d');
$start_date = date('Y-m-d', strtotime('-30 days'));
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = $_POST['start_date'] ?? $start_date;
    $end_date = $_POST['end_date'] ?? $end_date;
    // Validate dates
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
        $error = "Format de date invalide.";
    } elseif (strtotime($start_date) > strtotime($end_date)) {
        $error = "La date de début doit être antérieure à la date de fin.";
    }
}

// Query sessions
$stmt = $db->prepare("
    SELECT us.id, us.login_time, us.logout_time, us.latitude, us.longitude, us.device_info, us.ip_address, sd.device_name
    FROM user_sessions us
    LEFT JOIN student_devices sd ON us.student_id = sd.student_id AND us.device_info = sd.device_info AND sd.status = 'approved'
    WHERE us.student_id = ? AND us.login_time BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)
    ORDER BY us.login_time DESC
");
$stmt->bind_param("iss", $student_id, $start_date, $end_date);
$stmt->execute();
$sessions = $stmt->get_result();
$stmt->close();

// Calculate total duration and login count
$total_duration = 0;
$login_count = $sessions->num_rows;
while ($session = $sessions->fetch_assoc()) {
    if ($session['logout_time']) {
        $login = new DateTime($session['login_time']);
        $logout = new DateTime($session['logout_time']);
        $interval = $login->diff($logout);
        $total_duration += ($interval->days * 86400) + ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
    }
}
$sessions->data_seek(0); // Reset result pointer
$total_duration_str = sprintf('%02d:%02d:%02d', ($total_duration / 3600), ($total_duration % 3600 / 60), ($total_duration % 60));

// Query QCM submissions
$stmt = $db->prepare("
    SELECT qs.id, qs.qcm_id, qs.score, qs.submitted_at, q.title
    FROM qcm_submissions qs
    JOIN qcm q ON qs.qcm_id = q.id
    WHERE qs.student_id = ? AND qs.submitted_at BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)
    ORDER BY qs.submitted_at DESC
");
$stmt->bind_param("iss", $student_id, $start_date, $end_date);
$stmt->execute();
$qcm_submissions = $stmt->get_result();
$qcm_count = $qcm_submissions->num_rows;
$stmt->close();

// Query course views from activity_logs
$stmt = $db->prepare("
    SELECT al.id, al.details, al.timestamp, c.id as course_id, c.title
    FROM activity_logs al
    JOIN courses c ON al.details LIKE CONCAT('%', c.id, '%')
    WHERE al.user_id = ? AND al.user_type = 'student' AND al.action = 'Viewed course'
    AND al.timestamp BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)
    ORDER BY al.timestamp DESC
");
$stmt->bind_param("iss", $student_id, $start_date, $end_date);
$stmt->execute();
$course_views = $stmt->get_result();
$view_count = $course_views->num_rows;
$stmt->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques Étudiant - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <style>
        .dashboard { padding: 20px; }
        h1 { color: #1e40af; font-family: Poppins; }
        .form-group { margin-bottom: 20px; }
        label { font-weight: 600; color: #1e40af; margin-right: 10px; }
        input[type="date"] { 
            padding: 8px; 
            border: 2px solid #d1d5db; 
            border-radius: 5px; 
            font-family: Poppins; 
        }
        .btn-submit { 
            background: linear-gradient(90deg, #3b82f6, #1e40af); 
            color: #fff; 
            padding: 10px 20px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
        }
        .btn-submit:hover { 
            background: linear-gradient(90deg, #1e40af, #3b82f6); 
        }
        .stats-summary { 
            background: #f1f5f9; 
            padding: 20px; 
            border-radius: 10px; 
            margin-bottom: 20px; 
        }
        .stats-summary p { 
            margin: 10px 0; 
            font-size: 16px; 
        }
        .error { 
            color: #dc2626; 
            font-size: 16px; 
            margin-bottom: 20px; 
        }
        table.display { width: 100%; margin-bottom: 20px; }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-chart-line"></i> Statistiques de <?php echo htmlspecialchars($student['full_name']); ?></h1>
        <p>Email: <?php echo htmlspecialchars($student['email']); ?></p>
        
        <!-- Date Range Form -->
        <form method="POST" class="form-group">
            <label for="start_date">Date de début:</label>
            <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" required>
            <label for="end_date">Date de fin:</label>
            <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" required>
            <button type="submit" class="btn-submit">Filtrer</button>
        </form>
        
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        
        <!-- Summary Stats -->
        <div class="stats-summary">
            <h2>Résumé</h2>
            <p><strong>Durée totale:</strong> <?php echo $total_duration_str; ?></p>
            <p><strong>Nombre de connexions:</strong> <?php echo $login_count; ?></p>
            <p><strong>Tentatives de QCM:</strong> <?php echo $qcm_count; ?></p>
            <p><strong>Vues de cours:</strong> <?php echo $view_count; ?></p>
        </div>
        
        <!-- Sessions Table -->
        <h2>Sessions</h2>
        <?php if ($sessions->num_rows > 0): ?>
            <table id="sessionsTable" class="display">
                <thead>
                    <tr>
                        <th>Heure de Connexion</th>
                        <th>Heure de Déconnexion</th>
                        <th>Durée</th>
                        <th>Localisation</th>
                        <th>Appareil</th>
                        <th>Adresse IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($session = $sessions->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($session['login_time']); ?></td>
                            <td><?php echo htmlspecialchars($session['logout_time'] ?? 'Actif'); ?></td>
                            <td>
                                <?php
                                if ($session['logout_time']) {
                                    $login = new DateTime($session['login_time']);
                                    $logout = new DateTime($session['logout_time']);
                                    $interval = $login->diff($logout);
                                    echo htmlspecialchars($interval->format('%h:%i:%s'));
                                } else {
                                    echo 'Actif';
                                }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars(getLocationName($session['latitude'], $session['longitude'])); ?></td>
                            <td><?php echo htmlspecialchars($session['device_name'] ?? simplifyDeviceInfo($session['device_info'])); ?></td>
                            <td><?php echo htmlspecialchars($session['ip_address']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="error">Aucune session trouvée pour cette période.</p>
        <?php endif; ?>
        
        <!-- QCM Submissions Table -->
        <h2>Tentatives de QCM</h2>
        <?php if ($qcm_submissions->num_rows > 0): ?>
            <table id="qcmTable" class="display">
                <thead>
                    <tr>
                        <th>QCM ID</th>
                        <th>Titre</th>
                        <th>Score</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($qcm = $qcm_submissions->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($qcm['qcm_id']); ?></td>
                            <td><?php echo htmlspecialchars($qcm['title']); ?></td>
                            <td><?php echo htmlspecialchars($qcm['score']); ?></td>
                            <td><?php echo htmlspecialchars($qcm['submitted_at']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="error">Aucune tentative de QCM trouvée pour cette période.</p>
        <?php endif; ?>
        
        <!-- Course Views Table -->
        <h2>Vues de Cours</h2>
        <?php if ($course_views->num_rows > 0): ?>
            <table id="courseTable" class="display">
                <thead>
                    <tr>
                        <th>Cours ID</th>
                        <th>Titre</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($view = $course_views->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($view['course_id']); ?></td>
                            <td><?php echo htmlspecialchars($view['title']); ?></td>
                            <td><?php echo htmlspecialchars($view['timestamp']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="error">Aucune vue de cours trouvée pour cette période.</p>
        <?php endif; ?>
        
        <a href="sessions.php" class="btn-action"><i class="fas fa-arrow-left"></i> Retour aux Sessions</a>
    </main>
    <?php include '../includes/footer.php'; ?>

    <script>
        $(document).ready(function() {
            $('#sessionsTable').DataTable({
                pageLength: 10,
                lengthChange: false,
                order: [[0, 'desc']],
                columnDefs: [
                    { orderable: false, targets: [3, 4] } // Disable sorting on Localisation, Appareil
                ]
            });
            $('#qcmTable').DataTable({
                pageLength: 10,
                lengthChange: false,
                order: [[3, 'desc']]
            });
            $('#courseTable').DataTable({
                pageLength: 10,
                lengthChange: false,
                order: [[2, 'desc']]
            });
        });
    </script>
</body>
</html>