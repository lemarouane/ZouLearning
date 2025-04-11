<?php
session_start();
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
$student_query = $db->query("SELECT * FROM students WHERE id = $student_id");
if (!$student_query || $student_query->num_rows == 0) {
    header("Location: manage_students.php");
    exit;
}
$student = $student_query->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $db->real_escape_string($_POST['full_name']);
    $email = $db->real_escape_string($_POST['email']);
    $status = isset($_POST['status']) && $_POST['status'] == 'approved' ? 'approved' : 'pending';
    $level_id = (int)$_POST['level_id'];

    $stmt = $db->prepare("UPDATE students SET full_name = ?, email = ?, status = ?, level_id = ? WHERE id = ?");
    $stmt->bind_param("sssii", $full_name, $email, $status, $level_id, $student_id);
    $stmt->execute();

    // Clear existing assignments
    $db->query("DELETE FROM student_courses WHERE student_id = $student_id");
    $db->query("DELETE FROM student_subjects WHERE student_id = $student_id");

    if (isset($_POST['subject_ids']) && isset($_POST['all_courses'])) {
        foreach ($_POST['subject_ids'] as $subject_id) {
            $subject_id = (int)$subject_id;
            $stmt = $db->prepare("INSERT INTO student_subjects (student_id, subject_id, all_courses) VALUES (?, ?, 1)");
            $stmt->bind_param("ii", $student_id, $subject_id);
            $stmt->execute();

            $stmt = $db->prepare("INSERT IGNORE INTO student_courses (student_id, course_id) SELECT ?, id FROM courses WHERE subject_id = ?");
            $stmt->bind_param("ii", $student_id, $subject_id);
            $stmt->execute();
        }
    } elseif (isset($_POST['subject_ids']) && isset($_POST['course_ids'])) {
        foreach ($_POST['subject_ids'] as $subject_id) {
            $subject_id = (int)$subject_id;
            $stmt = $db->prepare("INSERT INTO student_subjects (student_id, subject_id, all_courses) VALUES (?, ?, 0)");
            $stmt->bind_param("ii", $student_id, $subject_id);
            $stmt->execute();
        }
        foreach ($_POST['course_ids'] as $course_id) {
            $course_id = (int)$course_id;
            $stmt = $db->prepare("INSERT IGNORE INTO student_courses (student_id, course_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $student_id, $course_id);
            $stmt->execute();
        }
    }

    $_SESSION['message'] = "Modifications enregistrées avec succès !";
    header("Location: view_student.php?id=$student_id");
    exit;
}

// Fetch levels
$levels = $db->query("SELECT * FROM levels");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Étudiant - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-user-edit"></i> Modifier Étudiant</h1>
        <form method="POST" class="edit-form">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Nom Complet</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($student['full_name']); ?>" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-check"></i> Statut</label>
                <select name="status" required>
                    <option value="pending" <?php echo $student['status'] == 'pending' ? 'selected' : ''; ?>>En attente</option>
                    <option value="approved" <?php echo $student['status'] == 'approved' ? 'selected' : ''; ?>>Approuvé</option>
                </select>
            </div>
            <div class="form-group">
                <label><i class="fas fa-layer-group"></i> Niveau</label>
                <select name="level_id" id="levelId" required>
                    <option value="">Choisir un niveau</option>
                    <?php while ($level = $levels->fetch_assoc()): ?>
                        <option value="<?php echo $level['id']; ?>" <?php echo $student['level_id'] == $level['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($level['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label><i class="fas fa-book-open"></i> Matières</label>
                <select name="subject_ids[]" id="subjectIds" multiple required>
                    <option value="">Choisir un niveau d'abord</option>
                </select>
            </div>
            <div class="form-group">
                <label><i class="fas fa-book"></i> Cours</label>
                <select name="course_ids[]" id="courseIds" multiple>
                    <option value="">Choisir des matières d'abord</option>
                </select>
                <div class="checkbox-group">
                    <input type="checkbox" name="all_courses" id="allCourses" value="1">
                    <label for="allCourses">Tous les cours des matières sélectionnées</label>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-action"><i class="fas fa-save"></i> Enregistrer</button>
                <a href="view_student.php?id=<?php echo $student_id; ?>" class="btn-action back"><i class="fas fa-arrow-left"></i> Retour</a>
            </div>
        </form>
    </main>
    <?php include '../includes/footer.php'; ?>

    <script>
        $(document).ready(function() {
            const levelId = $('#levelId');
            const subjectIds = $('#subjectIds');
            const courseIds = $('#courseIds');
            const allCourses = $('#allCourses');

            // Load subjects on page load if level is selected
            if (levelId.val()) {
                $.get('ajax/fetch_subjects.php?level_id=' + levelId.val(), function(data) {
                    subjectIds.html(data);
                });
            }

            levelId.on('change', function() {
                const level_id = $(this).val();
                if (level_id) {
                    $.get('ajax/fetch_subjects.php?level_id=' + level_id, function(data) {
                        subjectIds.html(data);
                        courseIds.html('<option value="">Choisir des matières d\'abord</option>');
                    });
                } else {
                    subjectIds.html('<option value="">Choisir un niveau d\'abord</option>');
                    courseIds.html('<option value="">Choisir des matières d\'abord</option>');
                }
            });

            subjectIds.on('change', function() {
                const subject_ids = $(this).val();
                if (subject_ids && subject_ids.length > 0) {
                    $.post('ajax/fetch_courses.php', { subject_ids: subject_ids }, function(data) {
                        courseIds.html(data);
                    });
                } else {
                    courseIds.html('<option value="">Choisir des matières d\'abord</option>');
                }
            });

            allCourses.on('change', function() {
                if ($(this).is(':checked')) {
                    courseIds.prop('disabled', true).val([]);
                } else {
                    courseIds.prop('disabled', false);
                }
            });
        });
    </script>
</body>
</html>