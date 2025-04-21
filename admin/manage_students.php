<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

function getLocationName($lat, $lon) {
    if (!$lat || !$lon) return 'N/A';
    $url = "https://nominatim.openstreetmap.org/reverse?lat=$lat&lon=$lon&format=json&zoom=16&addressdetails=1";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'ZouhairElearning/1.0');
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    if (isset($data['address'])) {
        $address = $data['address'];
        $neighborhood = $address['suburb'] ?? $address['neighbourhood'] ?? '';
        $city = $address['city'] ?? $address['town'] ?? $address['village'] ?? '';
        if ($neighborhood && $city) return "$neighborhood, $city";
        elseif ($city) return $city;
        elseif ($address['country']) return $address['country'];
    }
    return 'Unknown Location';
}

$students = $db->query("SELECT s.*, l.name AS level_name FROM students s LEFT JOIN levels l ON s.level_id = l.id ORDER BY s.created_at DESC");

if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $db->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $_SESSION['message'] = "Étudiant supprimé avec succès !";
    header("Location: manage_students.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les Étudiants - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">

    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-users"></i> Gérer les Étudiants</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <p style="color: #4caf50;"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <table id="studentsTable" class="display">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Statut</th>
                    <th>Niveau</th>
                     <th>Localisation</th>
                    <th>Inscrit</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($student = $students->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $student['id']; ?></td>
                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                        <td><?php echo ucfirst($student['status']); ?></td>
                        <td><?php echo $student['level_name'] ? htmlspecialchars($student['level_name']) : 'N/A'; ?></td>
                         <td><?php echo htmlspecialchars(getLocationName($student['latitude'], $student['longitude'])); ?></td>
                        <td><?php echo $student['created_at']; ?></td>
                        <td>
                            <a href="view_student.php?id=<?php echo $student['id']; ?>" class="btn-action view" title="Voir"><i class="fas fa-eye"></i></a>
                            <a href="edit_student.php?id=<?php echo $student['id']; ?>" class="btn-action edit" title="Modifier"><i class="fas fa-edit"></i></a>
                            <a href="?delete=1&id=<?php echo $student['id']; ?>" class="btn-action delete" onclick="return confirm('Êtes-vous sûr ?');" title="Supprimer"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
    <?php include '../includes/footer.php'; ?>

    <script>
        $(document).ready(function() {
            $('#studentsTable').DataTable({ pageLength: 10, lengthChange: false });
        });
    </script>
</body>
</html>