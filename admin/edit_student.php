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
$student = $db->query("SELECT * FROM students WHERE id = $student_id")->fetch_assoc();
if (!$student) {
    header("Location: manage_students.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $db->prepare("UPDATE students SET full_name = ?, email = ?, password = ? WHERE id = ?");
    $stmt->bind_param("sssi", $full_name, $email, $password, $student_id);
    $stmt->execute();

    // Log the action
    $stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (?, 'admin', 'Edited student', ?)");
    $details = "Edited student ID $student_id";
    $stmt->bind_param("is", $_SESSION['admin_id'], $details);
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
    <title>Edit Student - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-edit"></i> Edit Student</h1>
        <form method="POST" class="form-container">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Full Name</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($student['full_name']); ?>" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="text" name="password" value="<?php echo htmlspecialchars($student['password']); ?>" required>
            </div>
            <button type="submit" class="btn-action"><i class="fas fa-save"></i> Save Changes</button>
            <a href="manage_students.php" class="btn-action cancel"><i class="fas fa-times"></i> Cancel</a>
        </form>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>