<?php
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];

    $stmt = $db->prepare("INSERT INTO levels (name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $description);
    $stmt->execute();

    // Log the action
    $stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (?, 'admin', 'Added level', ?)");
    $details = "Added level: $name";
    $stmt->bind_param("is", $_SESSION['admin_id'], $details);
    $stmt->execute();

    header("Location: manage_levels.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Level - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-plus-circle"></i> Add New Level</h1>
        <form method="POST" class="form-container">
            <div class="form-group">
                <label><i class="fas fa-layer-group"></i> Level Name</label>
                <input type="text" name="name" placeholder="e.g., Bac+2" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-info-circle"></i> Description</label>
                <textarea name="description" placeholder="Optional description" rows="4"></textarea>
            </div>
            <button type="submit" class="btn-action"><i class="fas fa-save"></i> Add Level</button>
            <a href="manage_levels.php" class="btn-action cancel"><i class="fas fa-times"></i> Cancel</a>
        </form>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>