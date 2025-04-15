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

    $attempt = $db->query("SELECT student_id, device_fingerprint, device_info, ip_address, latitude, longitude FROM device_attempts WHERE id = $attempt_id AND status = 'pending'")->fetch_assoc();

    if ($attempt) {
        if ($action === 'approve') {
            $stmt = $db->prepare("INSERT INTO student_devices (student_id, device_fingerprint, device_info, ip_address, latitude, longitude, created_at, status) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'approved')");
            $stmt->bind_param("isssdd", $attempt['student_id'], $attempt['device_fingerprint'], $attempt['device_info'], $attempt['ip_address'], $attempt['latitude'], $attempt['longitude']);
            $stmt->execute();

            $db->query("UPDATE device_attempts SET status = 'approved' WHERE id = $attempt_id");
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Device Requests - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-mobile-alt"></i> Pending Device Requests</h1>
        <?php if ($attempts->num_rows > 0): ?>
            <table class="display">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Location</th>
                        <th>Device</th>
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
                                <a href="?action=approve&id=<?php echo $attempt['id']; ?>" class="btn-action view"><i class="fas fa-check"></i> Approve</a>
                                <a href="?action=deny&id=<?php echo $attempt['id']; ?>" class="btn-action delete"><i class="fas fa-times"></i> Deny</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="error">No pending device requests.</p>
        <?php endif; ?>
        <a href="dashboard.php" class="btn-action"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>