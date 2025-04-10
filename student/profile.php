<?php
require_once '../includes/db_connect.php';
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$stmt = $db->prepare("SELECT name, email FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if email is taken by another user
    $stmt = $db->prepare("SELECT id FROM students WHERE email = ? AND id != ?");
    $stmt->execute([$email, $student_id]);
    if ($stmt->fetch()) {
        $error = "Email already in use by another account";
    } else {
        $stmt = $db->prepare("UPDATE students SET name = ?, email = ?, password = ? WHERE id = ?");
        if ($stmt->execute([$name, $email, $password, $student_id])) {
            $success = "Profile updated successfully!";
        } else {
            $error = "Update failed";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/student_header.php'; ?>
    <main class="main-content dashboard">
        <h1><i class="fas fa-user"></i> My Profile</h1>
        <div class="form-container">
            <h2><i class="fas fa-edit"></i> Edit Profile</h2>
            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php elseif ($success): ?>
                <p style="color: #4caf50;"><?php echo $success; ?></p>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="name"><i class="fas fa-user"></i> Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($student['password']); ?>" required>
                </div>
                <button type="submit" class="btn-action edit"><i class="fas fa-save"></i> Save Changes</button>
                <a href="index.php" class="btn-action back"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
            </form>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>