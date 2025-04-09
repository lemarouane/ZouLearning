<?php
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$courses = $db->query("SELECT c.*, s.name AS subject_name, l.name AS level_name FROM courses c JOIN subjects s ON c.subject_id = s.id JOIN levels l ON s.level_id = l.id ORDER BY c.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-book"></i> Manage Courses</h1>
        <a href="add_course.php" class="btn-action add"><i class="fas fa-plus"></i> Add New Course</a>
        <table id="coursesTable" class="display">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Subject</th>
                    <th>Level</th>
                    <th>Difficulty</th>
                    <th>Type</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($course = $courses->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $course['id']; ?></td>
                        <td><?php echo htmlspecialchars($course['title']); ?></td>
                        <td><?php echo htmlspecialchars($course['subject_name']); ?></td>
                        <td><?php echo htmlspecialchars($course['level_name']); ?></td>
                        <td><?php echo $course['difficulty']; ?></td>
                        <td><?php echo $course['content_type']; ?></td>
                        <td><?php echo $course['created_at']; ?></td>
                        <td>
                            <a href="view_course.php?id=<?php echo $course['id']; ?>" class="btn-action view" title="View"><i class="fas fa-eye"></i></a>
                            <a href="edit_course.php?id=<?php echo $course['id']; ?>" class="btn-action edit" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="delete_course.php?id=<?php echo $course['id']; ?>" class="btn-action delete" onclick="return confirm('Are you sure?');" title="Delete"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
    <?php include '../includes/footer.php'; ?>

    <script>
        $(document).ready(function() {
            $('#coursesTable').DataTable({
                pageLength: 10,
                lengthChange: false,
                order: [[6, 'desc']] // Sort by created_at descending
            });
        });
    </script>
</body>
</html>