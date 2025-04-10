<?php
require_once '../includes/db_connect.php';

if (isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $stmt = $db->prepare("SELECT id FROM students WHERE email = ? AND password = ?");
    $stmt->execute([$email, $password]);
    $student = $stmt->fetch();
    if ($student) {
        $_SESSION['student_id'] = $student['id'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="login-page">
        <div class="login-container">
            <h2><i class="fas fa-sign-in-alt"></i> Student Login</h2>
            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn-action add"><i class="fas fa-sign-in-alt"></i> Login</button>
            </form>
            <p>Not registered? <a href="register.php">Register here</a></p>
        </div>
    </div>
</body>
</html>