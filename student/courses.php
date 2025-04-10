<?php
require_once '../includes/db_connect.php';
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

// Fetch all courses
$courses = $db->query("
    SELECT c.id, c.title, c.difficulty, s.name AS subject_name, l.name AS level_name
    FROM courses c
    JOIN subjects s ON c.subject_id = s.id
    JOIN levels l ON s.level_id = l.id
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch enrolled course IDs
$enrolled = $db->query("SELECT course_id FROM enrollments WHERE student_id = $student_id")->fetchAll(PDO::FETCH_COLUMN);
$enrolled_ids = array_column($enrolled, 'course_id');

// Handle enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll'])) {
    $course_id = (int)$_POST['course_id'];
    if (!in_array($course_id, $enrolled_ids)) {
        $stmt = $db->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
        $stmt->execute([$student_id, $course_id]);
        header("Location: courses.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/student_header.php'; ?>
    <main class="main-content dashboard">
        <h1><i class="fas fa-book"></i> Course Catalog</h1>
        <section class="recent-tables">
            <div class="notifications">
                <h2><i class="fas fa-list"></i> Browse Courses</h2>
                <?php if (empty($courses)): ?>
                    <p>No courses available yet.</p>
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
                                        <?php if (in_array($course['id'], $enrolled_ids)): ?>
                                            <a href="view_course.php?id=<?php echo $course['id']; ?>" class="btn-action view">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        <?php else: ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                                <button type="submit" name="enroll" class="btn-action add">
                                                    <i class="fas fa-plus"></i> Enroll
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </section>
        <a href="index.php" class="btn-action back"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>