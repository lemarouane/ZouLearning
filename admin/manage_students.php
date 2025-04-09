<?php
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$students = $db->query("SELECT * FROM students ORDER BY created_at DESC");

if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $db->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_students.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-users"></i> Manage Students</h1>
        <table id="studentsTable" class="display">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Validated</th>
                    <th>Level</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($student = $students->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $student['id']; ?></td>
                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                        <td><?php echo $student['is_validated'] ? 'Yes' : 'No'; ?></td>
                        <td><?php 
                            if ($student['level_id']) {
                                $level = $db->query("SELECT name FROM levels WHERE id = " . $student['level_id'])->fetch_assoc();
                                echo htmlspecialchars($level['name']);
                            } else {
                                echo 'N/A';
                            }
                        ?></td>
                        <td><?php echo $student['created_at']; ?></td>
                        <td>
                            <a href="view_student.php?id=<?php echo $student['id']; ?>" class="btn-action view" title="View"><i class="fas fa-eye"></i></a>
                            <a href="edit_student.php?id=<?php echo $student['id']; ?>" class="btn-action edit" title="Edit"><i class="fas fa-edit"></i></a>
                            <?php if (!$student['is_validated']): ?>
                                <a href="#" class="btn-action validate" data-id="<?php echo $student['id']; ?>" title="Validate"><i class="fas fa-check"></i></a>
                            <?php endif; ?>
                            <a href="?delete=1&id=<?php echo $student['id']; ?>" class="btn-action delete" onclick="return confirm('Are you sure?');" title="Delete"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Validation Modal -->
        <div class="modal" id="validateModal" style="display: none;">
            <div class="modal-content">
                <span class="close" onclick="$('#validateModal').hide();">×</span>
                <h2><i class="fas fa-check-circle"></i> Validate Student</h2>
                <form method="POST" action="validate_student.php" id="validateForm">
                    <input type="hidden" name="student_id" id="studentId">
                    <div class="form-group">
                        <label>Level</label>
                        <select name="level_id" id="levelId" required>
                            <?php
                            $levels = $db->query("SELECT * FROM levels");
                            while ($level = $levels->fetch_assoc()) {
                                echo "<option value='{$level['id']}'>" . htmlspecialchars($level['name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Subjects</label>
                        <select name="subject_ids[]" id="subjectIds" multiple required></select>
                    </div>
                    <div class="form-group">
                        <label>Courses</label>
                        <select name="course_ids[]" id="courseIds" multiple></select>
                        <div class="checkbox-group">
                            <input type="checkbox" name="all_courses" id="allCourses" value="1">
                            <label for="allCourses">All Courses in Selected Subjects</label>
                        </div>
                    </div>
                    <button type="submit" class="btn-action"><i class="fas fa-save"></i> Validate</button>
                </form>
            </div>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>

    <script>
        $(document).ready(function() {
            $('#studentsTable').DataTable({ pageLength: 10, lengthChange: false });

            $('.validate').click(function(e) {
                e.preventDefault();
                $('#studentId').val($(this).data('id'));
                $('#levelId').val('');
                $('#subjectIds').html('<option value="">Select a level first</option>');
                $('#courseIds').html('<option value="">Select subjects first</option>');
                $('#allCourses').prop('checked', false);
                $('#validateModal').show();
            });

            $('#levelId').change(function() {
                let level_id = $(this).val();
                $.get('ajax/fetch_subjects.php?level_id=' + level_id, function(data) {
                    $('#subjectIds').html(data);
                    $('#courseIds').html('<option value="">Select subjects first</option>');
                });
            });

            $('#subjectIds').change(function() {
                let subject_ids = $(this).val();
                if (subject_ids && subject_ids.length > 0) {
                    $.post('ajax/fetch_courses.php', { subject_ids: subject_ids }, function(data) {
                        $('#courseIds').html(data);
                    });
                } else {
                    $('#courseIds').html('<option value="">Select subjects first</option>');
                }
            });
        });
    </script>
</body>
</html>