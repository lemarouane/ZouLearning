<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/device_utils.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Use prepared statement for better security and performance
$stmt = $db->prepare("
    SELECT us.id, us.student_id, us.login_time, us.logout_time, us.latitude, us.longitude, us.device_info, us.ip_address,
           s.full_name, s.email, sd.device_name
    FROM user_sessions us
    JOIN students s ON us.student_id = s.id
    LEFT JOIN student_devices sd ON us.student_id = sd.student_id AND us.device_info = sd.device_fingerprint AND sd.status = 'approved'
    ORDER BY us.login_time DESC
");
$stmt->execute();
$sessions = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sessions Utilisateurs - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <style>
        .device-active { color: #28a745; font-weight: bold; }
        .device-active::after { content: ' (Actif)'; font-size: 0.8em; color: #28a745; }
        .device-inactive { color: #6b7280; }
        .btn-action { 
            display: inline-block; 
            padding: 8px 12px; 
            margin: 0 5px; 
            border-radius: 5px; 
            text-decoration: none; 
            font-size: 14px; 
            transition: all 0.3s ease; 
        }
        .logout-btn { 
            background: #dc2626; 
            color: #fff; 
        }
        .logout-btn:hover { 
            background: #b91c1c; 
        }
        .stats-btn { 
            background: #3b82f6; 
            color: #fff; 
        }
        .stats-btn:hover { 
            background: #1e40af; 
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-clock"></i> Sessions Utilisateurs</h1>
        <?php if ($sessions->num_rows > 0): ?>
            <table id="sessionsTable" class="display">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Heure de Connexion</th>
                        <th>Localisation</th>
                        <th>Appareil</th>
                        <th>Adresse IP</th>
                        <th>Durée</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($session = $sessions->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($session['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($session['email']); ?></td>
                            <td><?php echo htmlspecialchars($session['login_time']); ?></td>
                            <td><?php echo htmlspecialchars(getLocationName($session['latitude'], $session['longitude'])); ?></td>
                            <td class="<?php echo $session['logout_time'] ? 'device-inactive' : 'device-active'; ?>">
                                <?php
                                // Prioritize device_name, fall back to simplified device_info
                                $device_display = $session['device_name'] ?? simplifyDeviceInfo($session['device_info']);
                                echo htmlspecialchars($device_display);
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($session['ip_address']); ?></td>
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
                            <td><?php echo $session['logout_time'] ? 'Terminé' : 'Actif'; ?></td>
                            <td>
                                <a href="user_stats.php?student_id=<?php echo $session['student_id']; ?>" class="btn-action stats-btn">
                                    <i class="fas fa-chart-line"></i> Stats
                                </a>
                                <?php if (!$session['logout_time']): ?>
                                    <button class="btn-action logout-btn" data-session-id="<?php echo $session['id']; ?>">
                                        <i class="fas fa-sign-out-alt"></i> Déconnecter
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="error">Aucune session utilisateur trouvée.</p>
        <?php endif; ?>
        <a href="dashboard.php" class="btn-action"><i class="fas fa-arrow-left"></i> Retour au Tableau de Bord</a>
    </main>
    <?php include '../includes/footer.php'; ?>

    <script>
        $(document).ready(function() {
            $('#sessionsTable').DataTable({
                pageLength: 10,
                lengthChange: false,
                order: [[2, 'desc']],
                columnDefs: [
                    { orderable: false, targets: [4, 8] } // Disable sorting on Appareil and Actions
                ]
            });

            $('.logout-btn').click(function() {
                const sessionId = $(this).data('session-id');
                const button = $(this);
                
                $.ajax({
                    url: 'logout_student_session.php',
                    method: 'POST',
                    data: { session_id: sessionId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const row = button.closest('tr');
                            row.find('td:eq(6)').text('Terminé');
                            row.find('td:eq(7)').text('Terminé');
                            row.find('td:eq(4)').removeClass('device-active').addClass('device-inactive').text(row.find('td:eq(4)').text().replace(' (Actif)', ''));
                            button.remove();
                            alert('L\'utilisateur a été déconnecté avec succès.');
                        } else {
                            alert('Erreur lors de la déconnexion: ' + response.error);
                        }
                    },
                    error: function() {
                        alert('Une erreur est survenue lors de la déconnexion.');
                    }
                });
            });
        });
    </script>
</body>
</html>