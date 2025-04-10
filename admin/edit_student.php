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

$student_id = (int)$_GET['id'];
$student = $db->query("SELECT * FROM students WHERE id = $student_id")->fetch_assoc();
if (!$student) {
    header("Location: manage_students.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $db->real_escape_string($_POST['full_name']);
    $email = $db->real_escape_string($_POST['email']);
    $is_validated = isset($_POST['is_validated']) ? 1 : 0;

    $db->query("UPDATE students SET full_name = '$full_name', email = '$email', is_validated = $is_validated WHERE id = $student_id");

    // Clear existing assignments
    $db->query("DELETE FROM student_courses WHERE student_id = $student_id");
    $db->query("DELETE FROM student_subjects WHERE student_id = $student_id");

    if (isset($_POST['subjects'])) {
        foreach ($_POST['subjects'] as $subject_id => $courses) {
            if ($courses === 'all') {
                $db->query("INSERT INTO student_subjects (student_id, subject_id, all_courses) VALUES ($student_id, $subject_id, 1)");
                $result = $db->query("SELECT id FROM courses WHERE subject_id = $subject_id");
                while ($course = $result->fetch_assoc()) {
                    $course_id = $course['id'];
                    $db->query("INSERT INTO student_courses (student_id, course_id) VALUES ($student_id, $course_id)");
                }
            } elseif (is_array($courses)) {
                $db->query("INSERT INTO student_subjects (student_id, subject_id, all_courses) VALUES ($student_id, $subject_id, 0)");
                foreach ($courses as $course_id) {
                    $db->query("INSERT INTO student_courses (student_id, course_id) VALUES ($student_id, $course_id)");
                }
            }
        }
    }

    header("Location: view_student.php?id=$student_id");
    exit;
}

// Fetch all subjects and their courses
$subjects = [];
$result = $db->query("SELECT s.id, s.name FROM subjects s");
while ($row = $result->fetch_assoc()) {
    $subject_id = $row['id'];
    $subjects[$subject_id] = ['name' => $row['name'], 'courses' => []];
    $course_result = $db->query("SELECT id, title FROM courses WHERE subject_id = $subject_id");
    while ($course = $course_result->fetch_assoc()) {
        $subjects[$subject_id]['courses'][$course['id']] = $course['title'];
    }
}

// Fetch current assignments
$assigned_courses = [];
$result = $db->query("SELECT course_id FROM student_courses WHERE student_id = $student_id");
while ($row = $result->fetch_assoc()) {
    $assigned_courses[] = $row['course_id'];
}

$assigned_subjects = [];
$result = $db->query("SELECT subject_id, all_courses FROM student_subjects WHERE student_id = $student_id");
while ($row = $result->fetch_assoc()) {
    $assigned_subjects[$row['subject_id']] = $row['all_courses'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-user-edit"></i> Edit Student</h1>
        <form method="POST" class="edit-form">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Full Name</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($student['full_name']); ?>" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-check"></i> Validated</label>
                <input type="checkbox" name="is_validated" <?php echo $student['is_validated'] ? 'checked' : ''; ?>>
            </div>

            <div class="form-group">
                <label><i class="fas fa-book"></i> Assign Subjects & Courses</label>
                <div class="subjects-container">
                    <?php foreach ($subjects as $subject_id => $subject): ?>
                        <div class="subject-block">
                            <h3><?php echo htmlspecialchars($subject['name']); ?></h3>
                            <div class="selection-options">
                                <label class="option-none">
                                    <input type="radio" name="subjects[<?php echo $subject_id; ?>]" value="none" 
                                        <?php echo !isset($assigned_subjects[$subject_id]) ? 'checked' : ''; ?>> 
                                    None
                                </label>
                                <label class="option-all">
                                    <input type="radio" name="subjects[<?php echo $subject_id; ?>]" value="all" 
                                        <?php echo isset($assigned_subjects[$subject_id]) && $assigned_subjects[$subject_id] ? 'checked' : ''; ?>> 
                                    All Courses <span class="hint">(Includes future courses)</span>
                                </label>
                                <label class="option-specific">
                                    <input type="radio" name="subjects[<?php echo $subject_id; ?>]" value="specific" 
                                        <?php echo isset($assigned_subjects[$subject_id]) && !$assigned_subjects[$subject_id] ? 'checked' : ''; ?>> 
                                    Specific Courses
                                </label>
                            </div>
                            <div class="course-list" <?php echo (!isset($assigned_subjects[$subject_id]) || $assigned_subjects[$subject_id]) ? 'style="display: none;"' : ''; ?>>
                                <?php foreach ($subject['courses'] as $course_id => $course_title): ?>
                                    <label>
                                        <input type="checkbox" name="subjects[<?php echo $subject_id; ?>][]" value="<?php echo $course_id; ?>" 
                                            <?php echo in_array($course_id, $assigned_courses) && (!isset($assigned_subjects[$subject_id]) || !$assigned_subjects[$subject_id]) ? 'checked' : ''; ?>> 
                                        <?php echo htmlspecialchars($course_title); ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-action"><i class="fas fa-save"></i> Save Changes</button>
                <a href="view_student.php?id=<?php echo $student_id; ?>" class="btn-action back"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
        </form>
    </main>
    <?php include '../includes/footer.php'; ?>

    <script>
        document.querySelectorAll('.subject-block input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const subjectBlock = this.closest('.subject-block');
                const courseList = subjectBlock.querySelector('.course-list');
                if (this.value === 'specific') {
                    courseList.style.display = 'block';
                } else {
                    courseList.style.display = 'none';
                    courseList.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
                }
            });
        });
    </script>
</body>
</html>