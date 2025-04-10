<?php
require_once '../includes/db_connect.php';

if (isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if email exists
    $stmt = $db->prepare("SELECT id FROM students WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $error = "Email already registered";
    } else {
        $stmt = $db->prepare("INSERT INTO students (email, password) VALUES (?, ?)");
        if ($stmt->execute([$email, $password])) {
            $success = "Registration successful! <a href='login.php'>Login here</a>";
        } else {
            $error = "Registration failed";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Register - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="login-page">
        <div class="login-container">
            <h2><i class="fas fa-user-plus"></i> Student Registration</h2>
            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php elseif ($success): ?>
                <p style="color: #4caf50;"><?php echo $success; ?></p>
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
                <button type="submit" class="btn-action add"><i class="fas fa-user-plus"></i> Register</button>
            </form>
            <p>Already registered? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>