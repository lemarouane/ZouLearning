<?php
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$logs = $db->query("SELECT al.*, 
                    CASE WHEN al.user_type = 'admin' THEN a.username 
                         WHEN al.user_type = 'student' THEN s.full_name 
                         ELSE 'Unknown' END AS user_name 
                    FROM activity_logs al 
                    LEFT JOIN admins a ON al.user_id = a.id AND al.user_type = 'admin' 
                    LEFT JOIN students s ON al.user_id = s.id AND al.user_type = 'student' 
                    ORDER BY al.timestamp DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-history"></i> Activity Logs</h1>
        <table id="logsTable" class="display">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Type</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($log = $logs->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $log['id']; ?></td>
                        <td><?php echo htmlspecialchars($log['user_name']); ?></td>
                        <td><?php echo ucfirst($log['user_type']); ?></td>
                        <td><?php echo htmlspecialchars($log['action']); ?></td>
                        <td><?php echo htmlspecialchars($log['details'] ?: 'N/A'); ?></td>
                        <td><?php echo $log['timestamp']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
    <?php include '../includes/footer.php'; ?>

    <script>
        $(document).ready(function() {
            $('#logsTable').DataTable({
                pageLength: 10,
                lengthChange: false,
                order: [[5, 'desc']] // Sort by timestamp descending
            });
        });
    </script>
</body>
</html>