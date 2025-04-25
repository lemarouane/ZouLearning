<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/device_utils.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $attempt_id = (int)$_GET['id'];
    $action = $_GET['action'];

    $attempt = $db->query("SELECT da.student_id, da.device_fingerprint, da.device_info, da.ip_address, da.latitude, da.longitude, s.full_name, s.email
                           FROM device_attempts da
                           JOIN students s ON da.student_id = s.id
                           WHERE da.id = $attempt_id AND da.status = 'pending'")->fetch_assoc();

    if ($attempt) {
        if ($action === 'approve') {
            // Check current approved device count
            $device_count = $db->query("SELECT COUNT(*) FROM student_devices WHERE student_id = {$attempt['student_id']} AND status = 'approved'")->fetch_row()[0];
            $device_name = $device_count == 0 ? 'Device 1' : 'Device 2';

            $stmt = $db->prepare("INSERT INTO student_devices (student_id, device_fingerprint, device_name, device_info, ip_address, latitude, longitude, created_at, status) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 'approved')");
            $stmt->bind_param("issssdd", $attempt['student_id'], $attempt['device_fingerprint'], $device_name, 
                             $attempt['device_info'], $attempt['ip_address'], $attempt['latitude'], $attempt['longitude']);
            $stmt->execute();
            $stmt->close();

            $db->query("UPDATE device_attempts SET status = 'approved' WHERE id = $attempt_id");

            // Send approval email
            $webhook_url = 'https://script.google.com/macros/s/-WORClZ90-vf4V36NlqJyNj6ZYMS0t06Ng_I0zf/exec';
            $post_data = json_encode([
                'event' => 'approval',
                'full_name' => $attempt['full_name'],
                'email' => $attempt['email'],
                'details' => ['deviceId' => $device_name]
            ]);

            $ch = curl_init($webhook_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code != 200) {
                error_log("Failed to send approval email: HTTP $http_code, Response: $response");
            } else {
                error_log("Approval email sent successfully for device: $device_name");
            }
        } elseif ($action === 'deny') {
            $db->query("UPDATE device_attempts SET status = 'denied' WHERE id = $attempt_id");
        }
        header("Location: device_requests.php");
        exit;
    }
}

$attempts = $db->query("
    SELECT da.id, da.student_id, da.device_fingerprint, da.device_info, da.ip_address, da.latitude, da.longitude, da.attempted_at, s.full_name, s.email
    FROM device_attempts da
    JOIN students s ON da.student_id = s.id
    WHERE da.status = 'pending'
    ORDER BY da.attempted_at DESC
");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les Demandes d'Appareils - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-mobile-alt"></i> Demandes d'Appareils en Attente</h1>
        <?php if ($attempts->num_rows > 0): ?>
            <table class="display">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Localisation</th>
                        <th>Appareil</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($attempt = $attempts->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($attempt['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($attempt['email']); ?></td>
                            <td><?php echo htmlspecialchars(getLocationName($attempt['latitude'], $attempt['longitude'])); ?></td>
                            <td><?php echo htmlspecialchars(simplifyDeviceInfo($attempt['device_info'])); ?></td>
                            <td>
                                <a href="?action=approve&id=<?php echo $attempt['id']; ?>" class="btn-action view"><i class="fas fa-check"></i> Approuver</a>
                                <a href="?action=deny&id=<?php echo $attempt['id']; ?>" class="btn-action delete"><i class="fas fa-times"></i> Refuser</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="error">Aucune demande d'appareil en attente.</p>
        <?php endif; ?>
        <a href="dashboard.php" class="btn-action"><i class="fas fa-arrow-left"></i> Retour au Tableau de Bord</a>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>