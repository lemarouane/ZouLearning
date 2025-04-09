<?php
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: manage_students.php");
    exit;
}

$student_id = $_GET['id'];
$student = $db->query("SELECT s.*, l.name AS level_name FROM students s LEFT JOIN levels l ON s.level_id = l.id WHERE s.id = $student_id")->fetch_assoc();
if (!$student) {
    header("Location: manage_students.php");
    exit;
}

$subjects = $db->query("SELECT sub.name FROM student_subjects ss JOIN subjects sub ON ss.subject_id = sub.id WHERE ss.student_id = $student_id");
$courses = $db->query("SELECT c.title, c.difficulty, c.content_type FROM student_courses sc JOIN courses c ON sc.course_id = c.id WHERE sc.student_id = $student_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-user"></i> Student Details</h1>
        <div class="student-details">
            <div class="detail-card">
                <h3><i class="fas fa-id-badge"></i> Profile</h3>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($student['full_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
                <p><strong>Validated:</strong> <?php echo $student['is_validated'] ? 'Yes' : 'No'; ?></p>
                <p><strong>Level:</strong> <?php echo $student['level_name'] ?: 'Not assigned'; ?></p>
                <p><strong>Joined:</strong> <?php echo $student['created_at']; ?></p>
            </div>
            <div class="detail-card">
                <h3><i class="fas fa-book-open"></i> Assigned Subjects</h3>
                <ul>
                    <?php if ($subjects->num_rows > 0): ?>
                        <?php while ($sub = $subjects->fetch_assoc()): ?>
                            <li><?php echo htmlspecialchars($sub['name']); ?> (All Courses)</li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li>No subjects assigned with All Courses option.</li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="detail-card">
                <h3><i class="fas fa-book"></i> Assigned Courses</h3>
                <ul>
                    <?php if ($courses->num_rows > 0): ?>
                        <?php while ($course = $courses->fetch_assoc()): ?>
                            <li><?php echo htmlspecialchars($course['title']) . " (" . $course['difficulty'] . " - " . $course['content_type'] . ")"; ?></li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li>No courses assigned.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <a href="manage_students.php" class="btn-action back"><i class="fas fa-arrow-left"></i> Back to Students</a>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>