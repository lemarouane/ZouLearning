<?php
require_once '../includes/db_connect.php';
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

// Fetch enrolled courses
$stmt = $db->prepare("
    SELECT c.id, c.title, c.difficulty, s.name AS subject_name, l.name AS level_name
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    JOIN subjects s ON c.subject_id = s.id
    JOIN levels l ON s.level_id = l.id
    WHERE e.student_id = ?
");
$stmt->execute([$student_id]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/student_header.php'; ?>
    <main class="main-content dashboard">
        <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
        <section class="stats">
            <div class="stat-card">
                <h3><i class="fas fa-book"></i> Enrolled Courses</h3>
                <p><?php echo count($courses); ?></p>
            </div>
            <!-- Add more stats if needed, e.g., completed courses -->
        </section>
        <section class="recent-tables">
            <div class="notifications">
                <h2><i class="fas fa-book-open"></i> My Courses</h2>
                <?php if (empty($courses)): ?>
                    <p>No courses enrolled yet. <a href="courses.php">Browse Courses</a></p>
                <?php else: ?>
                    <table class="display">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Subject</th>
                                <th>Level</th>
                                <th>Difficulty</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($course['title']); ?></td>
                                    <td><?php echo htmlspecialchars($course['subject_name']); ?></td>
                                    <td><?php echo htmlspecialchars($course['level_name']); ?></td>
                                    <td><?php echo htmlspecialchars($course['difficulty']); ?></td>
                                    <td>
                                        <a href="view_course.php?id=<?php echo $course['id']; ?>" class="btn-action view">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </section>
        <a href="courses.php" class="btn-action add"><i class="fas fa-plus"></i> Browse More Courses</a>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>