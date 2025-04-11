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

// Fetch assigned subjects and their status
$assigned_subjects = [];
$result = $db->query("SELECT subject_id, all_courses FROM student_subjects WHERE student_id = $student_id");
while ($row = $result->fetch_assoc()) {
    $subject_id = $row['subject_id'];
    $assigned_subjects[$subject_id] = ['all_courses' => $row['all_courses'], 'courses' => []];
}

// Fetch all courses per subject
$all_subjects = [];
$result = $db->query("SELECT s.id, s.name, c.id AS course_id, c.title FROM subjects s LEFT JOIN courses c ON s.id = c.subject_id");
while ($row = $result->fetch_assoc()) {
    $subject_id = $row['id'];
    if (!isset($all_subjects[$subject_id])) {
        $all_subjects[$subject_id] = ['name' => $row['name'], 'courses' => []];
    }
    $all_subjects[$subject_id]['courses'][$row['course_id']] = $row['title'];
}

// Fetch explicitly assigned courses (for Specific Courses)
$result = $db->query("SELECT c.id AS course_id, c.title, c.subject_id FROM student_courses sc JOIN courses c ON sc.course_id = c.id WHERE sc.student_id = $student_id");
while ($row = $result->fetch_assoc()) {
    $subject_id = $row['subject_id'];
    if (isset($assigned_subjects[$subject_id]) && !$assigned_subjects[$subject_id]['all_courses']) {
        $assigned_subjects[$subject_id]['courses'][$row['course_id']] = $row['title'];
    }
}

// Merge "All Courses" subjects with current courses
foreach ($assigned_subjects as $subject_id => &$subject) {
    $subject['name'] = $all_subjects[$subject_id]['name'];
    if ($subject['all_courses']) {
        $subject['courses'] = $all_subjects[$subject_id]['courses']; // Include all current courses
    }
}


function getLocationName($lat, $lon) {
    if (!$lat || !$lon) return 'N/A';
    $url = "https://nominatim.openstreetmap.org/reverse?lat=$lat&lon=$lon&format=json&zoom=16&addressdetails=1";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'ZouhairElearning/1.0');
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Log for debugging
    file_put_contents('geo_debug.log', "Coords: $lat, $lon | HTTP: $httpCode | Response: $response\n", FILE_APPEND);

    $data = json_decode($response, true);
    if (isset($data['address'])) {
        $address = $data['address'];
        $neighborhood = $address['suburb'] ?? $address['neighbourhood'] ?? '';
        $city = $address['city'] ?? $address['town'] ?? $address['village'] ?? '';
        if ($neighborhood && $city) {
            return "$neighborhood, $city";
        } elseif ($city) {
            return $city;
        } elseif ($address['country']) {
            return $address['country'];
        }
    }
    return 'Unknown Location';
}
unset($subject);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-user"></i> Student Details</h1>
        <div class="detail-card">
            <h3><i class="fas fa-info-circle"></i> Information</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($student['full_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
            <p><strong>Validated:</strong> <?php echo $student['is_validated'] ? 'Yes' : 'No'; ?></p>
            <p><strong>Created:</strong> <?php echo $student['created_at']; ?></p>
            <p><strong>Device ID:</strong> <?php echo htmlspecialchars($student['device_id']); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars(getLocationName($student['latitude'], $student['longitude'])); ?></p>
            <p><strong>Last Device:</strong> <?php echo htmlspecialchars($student['device_name']); ?></p>
 
            
        </div>

        <div class="detail-card">
            <h3><i class="fas fa-book"></i> Assigned Subjects</h3>
            <?php if (empty($assigned_subjects)): ?>
                <p>No subjects assigned.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($assigned_subjects as $subject_id => $subject): ?>
                        <li>
                            <?php echo htmlspecialchars($subject['name']); ?> 
                            (<?php echo $subject['all_courses'] ? 'All Courses' : 'Specific Courses'; ?>)
                            <ul>
                                <?php foreach ($subject['courses'] as $course_id => $course_title): ?>
                                    <li><?php echo htmlspecialchars($course_title); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <a href="edit_student.php?id=<?php echo $student_id; ?>" class="btn-action"><i class="fas fa-edit"></i> Edit</a>
        <a href="manage_students.php" class="btn-action back"><i class="fas fa-arrow-left"></i> Back</a>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>