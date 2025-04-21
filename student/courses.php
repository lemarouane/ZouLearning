<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = (int)$_SESSION['student_id'];
$courses_query = $db->query("
    SELECT DISTINCT c.id, c.title, s.name AS subject 
    FROM (
        SELECT sc.course_id 
        FROM student_courses sc 
        WHERE sc.student_id = $student_id
        UNION
        SELECT c.id AS course_id 
        FROM student_subjects ss 
        JOIN courses c ON ss.subject_id = c.subject_id 
        WHERE ss.student_id = $student_id AND ss.all_courses = 1
    ) AS unique_courses
    JOIN courses c ON unique_courses.course_id = c.id 
    JOIN subjects s ON c.subject_id = s.id
");
if (!$courses_query) {
    die("Erreur dans la requête des cours : " . $db->error);
}
$courses = $courses_query;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Cours - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">

    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/student_header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-book"></i> Mes Cours</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <p style="color: #4caf50;"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
        <?php endif; ?>
        <table id="coursesTable" class="display">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Matière</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($course = $courses->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course['title']); ?></td>
                        <td><?php echo htmlspecialchars($course['subject']); ?></td>
                        <td>
                            <a href="view_course.php?id=<?php echo $course['id']; ?>" 
                               class="btn-action view" 
                               title="Voir" 
                               onclick="console.log('Clicked View for Course ID: <?php echo $course['id']; ?>');">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
    <?php include '../includes/footer.php'; ?>

    <script>
        $(document).ready(function() {
            $('#coursesTable').DataTable({ pageLength: 10, lengthChange: false });
            $('.btn-action.view').on('click', function(e) {
                var courseId = $(this).attr('href').split('id=')[1];
                console.log('Redirecting to view_course.php?id=' + courseId);
            });
        });
    </script>
</body>
</html>