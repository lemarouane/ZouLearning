<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$subjects = $db->query("SELECT s.id, s.name, l.name AS level_name FROM subjects s JOIN levels l ON s.level_id = l.id WHERE s.is_archived = 0 ORDER BY s.name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $db->real_escape_string(trim($_POST['title']));
    $subject_id = (int)$_POST['subject_id'];

    // Validate course inputs
    if (empty($title) || !$subject_id) {
        $_SESSION['error'] = "Veuillez remplir tous les champs du cours correctement.";
        header("Location: add_course.php");
        exit;
    }

    $db->begin_transaction();
    try {
        // Insert course with difficulty set to 'N/A'
        $difficulty = 'N/A';
        $stmt = $db->prepare("INSERT INTO courses (title, subject_id, difficulty) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $title, $subject_id, $difficulty);
        if (!$stmt->execute()) {
            throw new Exception("Erreur lors de la création du cours.");
        }
        $course_id = $db->insert_id;

        // Handle subfolders
        $folder_names = $_POST['folder_names'] ?? [];
        $folder_descs = $_POST['folder_descriptions'] ?? [];
        $errors = [];

        foreach ($folder_names as $i => $folder_name) {
            $folder_name = trim($folder_name);
            if (empty($folder_name)) {
                $errors[] = "Nom du dossier vide à l'index $i.";
                continue;
            }

            $folder_name = $db->real_escape_string($folder_name);
            $folder_desc = !empty($folder_descs[$i]) ? $db->real_escape_string(trim($folder_descs[$i])) : null;

            // Insert subfolder
            $stmt = $db->prepare("INSERT INTO course_folders (course_id, name, description) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $course_id, $folder_name, $folder_desc);
            if (!$stmt->execute()) {
                $errors[] = "Erreur lors de l'ajout du dossier '$folder_name'.";
                continue;
            }
        }

        // Assign course to students with all_courses = 1
        $students_query = $db->query("
            SELECT st.id
            FROM students st
            JOIN student_subjects ss ON st.id = ss.student_id
            WHERE ss.subject_id = $subject_id AND ss.all_courses = 1
        ");
        if ($students_query && $students_query->num_rows > 0) {
            $stmt = $db->prepare("INSERT INTO student_courses (student_id, course_id) VALUES (?, ?)");
            while ($student = $students_query->fetch_assoc()) {
                $student_id = $student['id'];
                $stmt->bind_param("ii", $student_id, $course_id);
                if (!$stmt->execute()) {
                    error_log("Failed to assign course $course_id to student $student_id");
                }
            }
        }

        // Log action
        $stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (?, 'admin', 'Added course', ?)");
        $details = "Added course: $title for subject ID $subject_id";
        $stmt->bind_param("is", $_SESSION['admin_id'], $details);
        $stmt->execute();

        // Send email notifications to students with all_courses = 1
        $students_query = $db->query("
            SELECT st.id, st.full_name, st.email
            FROM students st
            JOIN student_subjects ss ON st.id = ss.student_id
            WHERE ss.subject_id = $subject_id AND ss.all_courses = 1
        ");
        if ($students_query && $students_query->num_rows > 0) {
            // Check if course has a QCM
            $qcm_query = $db->query("SELECT title FROM qcm WHERE course_after_id = $course_id AND is_archived = 0");
            $course_detail = $qcm_query->num_rows > 0 ? $qcm_query->fetch_assoc()['title'] : $title;

            $webhook_url = 'https://script.google.com/macros/s/AKfycbzmsPSqVPc_rFsZC4ig9fWPaSO5I3d1B7YJ5hY2NQYUU1wZvZ1KOkt-n7mqS4Usy_M4/exec';
            while ($student = $students_query->fetch_assoc()) {
                $post_data = json_encode([
                    'event' => 'course_added',
                    'full_name' => $student['full_name'],
                    'email' => $student['email'],
                    'details' => ['course' => $course_detail]
                ]);

                $ch = curl_init($webhook_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($http_code != 200) {
                    error_log("Failed to send course_added email for student {$student['id']}: HTTP $http_code, Response: $response");
                } else {
                    error_log("Course_added email sent to {$student['email']} for course/QCM: $course_detail");
                }
            }
        }

        if (!empty($errors)) {
            throw new Exception(implode("<br>", $errors));
        }

        $db->commit();
        $_SESSION['message'] = "Cours ajouté avec succès ! Ajoutez du contenu via Modifier le Cours.";
        header("Location: manage_courses.php");
        exit;
    } catch (Exception $e) {
        $db->rollback();
        $_SESSION['error'] = $e->getMessage();
        header("Location: add_course.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Cours - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-plus-circle"></i> Ajouter un Nouveau Cours</h1>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error-message"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form method="POST" class="course-form">
            <div class="course-input-group">
                <label class="course-label"><i class="fas fa-book"></i> Titre du Cours</label>
                <input type="text" name="title" class="course-input" placeholder="ex., Algèbre" required>
            </div>
            <div class="course-input-group">
                <label class="course-label"><i class="fas fa-book-open"></i> Matière</label>
                <select name="subject_id" class="course-select" required>
                    <option value="">Sélectionnez une matière</option>
                    <?php
                    $subjects->data_seek(0);
                    while ($sub = $subjects->fetch_assoc()): ?>
                        <option value="<?php echo $sub['id']; ?>"><?php echo htmlspecialchars($sub['name'] . " (" . $sub['level_name'] . ")"); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="subfolder-section-test">
                <h3 class="section-title"><i class="fas fa-folder"></i> Dossiers</h3>
                <button type="button" id="addFolder" class="add-folder-btn"><i class="fas fa-plus"></i> Ajouter un Dossier</button>
                <div id="folder-list"></div>
            </div>
            <div class="form-controls">
                <button type="submit" class="save-course-btn"><i class="fas fa-save"></i> Ajouter le Cours</button>
                <a href="manage_courses.php" class="cancel-btn"><i class="fas fa-times"></i> Annuler</a>
            </div>
        </form>
    </main>
    <?php include '../includes/footer.php'; ?>

    <script>
        $(document).ready(function() {
            let folderCount = 0;

            function addFolder() {
                folderCount++;
                const folderHtml = `
                    <div class="subfolder-card-test" id="folder-${folderCount}">
                        <div class="course-input-group">
                            <label class="course-label"><i class="fas fa-folder"></i> Nom du Dossier</label>
                            <input type="text" name="folder_names[]" class="course-input" placeholder="ex., Les Matrices" required>
                        </div>
                        <div class="course-input-group">
                            <label class="course-label"><i class="fas fa-info-circle"></i> Description (facultatif)</label>
                            <textarea name="folder_descriptions[]" class="course-textarea" placeholder="Description du dossier"></textarea>
                        </div>
                        <button type="button" class="remove-folder-btn" data-folder-id="${folderCount}"><i class="fas fa-trash"></i> Supprimer Dossier</button>
                    </div>`;
                $('#folder-list').append(folderHtml).find(`#folder-${folderCount}`).hide().slideDown(300);
            }

            $('#addFolder').click(addFolder);

            $(document).on('click', '.remove-folder-btn', function() {
                const folderId = $(this).data('folder-id');
                $(`#folder-${folderId}`).slideUp(300, function() {
                    $(this).remove();
                });
            });

            // Add one folder by default
            addFolder();
        });
    </script>
</body>
</html>