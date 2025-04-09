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
$subject = $db->query("SELECT s.*, l.name AS level_name FROM subjects s JOIN levels l ON s.level_id = l.id WHERE s.id = $subject_id")->fetch_assoc();
if (!$subject) {
    header("Location: manage_subjects.php");
    exit;
}

$courses = $db->query("SELECT title, difficulty, content_type FROM courses WHERE subject_id = $subject_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Subject - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-book-open"></i> Subject Details</h1>
        <div class="detail-card">
            <h3><i class="fas fa-info"></i> Subject Information</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($subject['name']); ?></p>
            <p><strong>Level:</strong> <?php echo htmlspecialchars($subject['level_name']); ?></p>
            <p><strong>Created:</strong> <?php echo $subject['created_at']; ?></p>
        </div>
        <div class="detail-card">
            <h3><i class="fas fa-book"></i> Associated Courses</h3>
            <ul>
                <?php if ($courses->num_rows > 0): ?>
                    <?php while ($course = $courses->fetch_assoc()): ?>
                        <li><?php echo htmlspecialchars($course['title']) . " (" . $course['difficulty'] . " - " . $course['content_type'] . ")"; ?></li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>No courses assigned to this subject.</li>
                <?php endif; ?>
            </ul>
        </div>
        <a href="manage_subjects.php" class="btn-action back"><i class="fas fa-arrow-left"></i> Back to Subjects</a>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>