<?php
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: manage_subjects.php");
    exit;
}

$subject_id = $_GET['id'];
$subject = $db->query("SELECT name FROM subjects WHERE id = $subject_id")->fetch_assoc();
if (!$subject) {
    header("Location: manage_subjects.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $db->prepare("DELETE FROM subjects WHERE id = ?");
    $stmt->bind_param("i", $subject_id);
    $stmt->execute();

    // Log the action
    $stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (?, 'admin', 'Deleted subject', ?)");
    $details = "Deleted subject ID $subject_id: " . $subject['name'];
    $stmt->bind_param("is", $_SESSION['admin_id'], $details);
    $stmt->execute();

    header("Location: manage_subjects.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Subject - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-trash-alt"></i> Delete Subject</h1>
        <div class="confirmation">
            <p>Are you sure you want to delete <strong><?php echo htmlspecialchars($subject['name']); ?></strong>? This will also delete all associated courses.</p>
            <form method="POST">
                <button type="submit" class="btn-action delete"><i class="fas fa-trash"></i> Yes, Delete</button>
                <a href="manage_subjects.php" class="btn-action cancel"><i class="fas fa-times"></i> Cancel</a>
            </form>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>