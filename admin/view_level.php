<?php
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: manage_levels.php");
    exit;
}

$level_id = $_GET['id'];
$level = $db->query("SELECT * FROM levels WHERE id = $level_id")->fetch_assoc();
if (!$level) {
    header("Location: manage_levels.php");
    exit;
}

$subjects = $db->query("SELECT name FROM subjects WHERE level_id = $level_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Level - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-layer-group"></i> Level Details</h1>
        <div class="detail-card">
            <h3><i class="fas fa-info"></i> Level Information</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($level['name']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($level['description'] ?: 'N/A'); ?></p>
            <p><strong>Created:</strong> <?php echo $level['created_at']; ?></p>
        </div>
        <div class="detail-card">
            <h3><i class="fas fa-book-open"></i> Associated Subjects</h3>
            <ul>
                <?php if ($subjects->num_rows > 0): ?>
                    <?php while ($sub = $subjects->fetch_assoc()): ?>
                        <li><?php echo htmlspecialchars($sub['name']); ?></li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>No subjects assigned to this level.</li>
                <?php endif; ?>
            </ul>
        </div>
        <a href="manage_levels.php" class="btn-action back"><i class="fas fa-arrow-left"></i> Back to Levels</a>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>