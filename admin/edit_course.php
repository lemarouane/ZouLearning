<?php
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: manage_courses.php");
    exit;
}

$course_id = $_GET['id'];
$course = $db->query("SELECT * FROM courses WHERE id = $course_id")->fetch_assoc();
if (!$course) {
    header("Location: manage_courses.php");
    exit;
}

$subjects = $db->query("SELECT s.id, s.name, l.name AS level_name FROM subjects s JOIN levels l ON s.level_id = l.id ORDER BY s.name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $subject_id = $_POST['subject_id'];
    $difficulty = $_POST['difficulty'];
    $content_type = $_POST['content_type'];
    $content_path = $course['content_path'];

    if ($content_type === 'PDF' && isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/pdfs/';
        $content_path = $upload_dir . basename($_FILES['pdf_file']['name']);
        move_uploaded_file($_FILES['pdf_file']['tmp_name'], $content_path);
    } elseif ($content_type === 'Video' && !empty($_POST['video_url'])) {
        $content_path = $_POST['video_url'];
    }

    $stmt = $db->prepare("UPDATE courses SET title = ?, subject_id = ?, difficulty = ?, content_type = ?, content_path = ? WHERE id = ?");
    $stmt->bind_param("sisssi", $title, $subject_id, $difficulty, $content_type, $content_path, $course_id);
    $stmt->execute();

    // Log the action
    $stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (?, 'admin', 'Edited course', ?)");
    $details = "Edited course ID $course_id: $title";
    $stmt->bind_param("is", $_SESSION['admin_id'], $details);
    $stmt->execute();

    header("Location: manage_courses.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-edit"></i> Edit Course</h1>
        <form method="POST" class="form-container" enctype="multipart/form-data">
            <div class="form-group">
                <label><i class="fas fa-book"></i> Course Title</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($course['title']); ?>" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-book-open"></i> Subject</label>
                <select name="subject_id" required>
                    <?php while ($sub = $subjects->fetch_assoc()): ?>
                        <option value="<?php echo $sub['id']; ?>" <?php echo $sub['id'] == $course['subject_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($sub['name'] . " (" . $sub['level_name'] . ")"); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label><i class="fas fa-tachometer-alt"></i> Difficulty</label>
                <select name="difficulty" required>
                    <?php foreach (['Easy', 'Medium', 'Hard'] as $diff): ?>
                        <option value="<?php echo $diff; ?>" <?php echo $course['difficulty'] == $diff ? 'selected' : ''; ?>><?php echo $diff; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label><i class="fas fa-file-alt"></i> Content Type</label>
                <select name="content_type" id="contentType" required>
                    <option value="PDF" <?php echo $course['content_type'] == 'PDF' ? 'selected' : ''; ?>>PDF</option>
                    <option value="Video" <?php echo $course['content_type'] == 'Video' ? 'selected' : ''; ?>>Video</option>
                </select>
            </div>
            <div class="form-group" id="pdfField" <?php echo $course['content_type'] == 'Video' ? 'style="display: none;"' : ''; ?>>
                <label><i class="fas fa-upload"></i> Upload New PDF (optional)</label>
                <input type="file" name="pdf_file" accept=".pdf">
                <p>Current: <?php echo basename($course['content_path']); ?></p>
            </div>
            <div class="form-group" id="videoField" <?php echo $course['content_type'] == 'PDF' ? 'style="display: none;"' : ''; ?>>
                <label><i class="fas fa-video"></i> Video URL</label>
                <input type="url" name="video_url" value="<?php echo $course['content_type'] == 'Video' ? htmlspecialchars($course['content_path']) : ''; ?>" placeholder="e.g., https://youtube.com/watch?v=xyz">
            </div>
            <button type="submit" class="btn-action"><i class="fas fa-save"></i> Save Changes</button>
            <a href="manage_courses.php" class="btn-action cancel"><i class="fas fa-times"></i> Cancel</a>
        </form>
    </main>
    <?php include '../includes/footer.php'; ?>

    <script>
        $(document).ready(function() {
            $('#contentType').change(function() {
                if ($(this).val() === 'PDF') {
                    $('#pdfField').show();
                    $('#videoField').hide();
                    $('#videoField input').prop('required', false);
                } else {
                    $('#pdfField').hide();
                    $('#videoField').show();
                    $('#videoField input').prop('required', true);
                }
            });
        });
    </script>
</body>
</html>