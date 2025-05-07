<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: manage_courses.php");
    exit;
}

$course_id = (int)$_GET['id'];
$course = $db->query("SELECT c.*, s.id AS subject_id, s.name AS subject_name, l.name AS level_name FROM courses c JOIN subjects s ON c.subject_id = s.id JOIN levels l ON s.level_id = l.id WHERE c.id = $course_id")->fetch_assoc();
if (!$course) {
    header("Location: manage_courses.php");
    exit;
}

$subjects = $db->query("SELECT s.id, s.name, l.name AS level_name FROM subjects s JOIN levels l ON s.level_id = l.id WHERE s.is_archived = 0 ORDER BY s.name ASC");
$folders = $db->query("SELECT id, name, description FROM course_folders WHERE course_id = $course_id");

// Ensure upload directory exists
$upload_dir = "../Uploads/pdfs/";
if (!is_dir($upload_dir)) {
    if (!mkdir($upload_dir, 0777, true)) {
        error_log("Failed to create directory: $upload_dir");
        $_SESSION['error'] = "Erreur serveur : Impossible de créer le dossier des uploads.";
        header("Location: edit_course.php?id=$course_id");
        exit;
    }
}
if (!is_writable($upload_dir)) {
    error_log("Directory not writable: $upload_dir");
    $_SESSION['error'] = "Erreur serveur : Le dossier des uploads n'est pas accessible en écriture.";
    header("Location: edit_course.php?id=$course_id");
    exit;
}

// Handle AJAX delete content
if (isset($_POST['action']) && $_POST['action'] === 'delete_content') {
    $content_id = (int)$_POST['content_id'];
    $stmt = $db->prepare("SELECT content_path, content_type, content_name FROM course_contents WHERE id = ? AND course_id = ?");
    $stmt->bind_param("ii", $content_id, $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($content = $result->fetch_assoc()) {
        if ($content['content_type'] == 'PDF' && file_exists($content['content_path'])) {
            unlink($content['content_path']);
        }
        $stmt = $db->prepare("DELETE FROM course_contents WHERE id = ?");
        $stmt->bind_param("i", $content_id);
        if ($stmt->execute()) {
            $stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (?, 'admin', 'Deleted content', ?)");
            $details = "Deleted {$content['content_type']} '{$content['content_name']}' from course ID $course_id";
            $stmt->bind_param("is", $_SESSION['admin_id'], $details);
            $stmt->execute();
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Contenu introuvable']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action'])) {
    $title = $db->real_escape_string(trim($_POST['title']));
    $subject_id = (int)$_POST['subject_id'];
    $difficulty = $db->real_escape_string($_POST['difficulty']);



    // Update course
    $stmt = $db->prepare("UPDATE courses SET title = ?, subject_id = ?, difficulty = ? WHERE id = ?");
    $stmt->bind_param("sisi", $title, $subject_id, $difficulty, $course_id);
    $stmt->execute();

    // Debug: Log POST and FILES
    error_log("Edit Course POST: " . print_r($_POST, true));
    error_log("Edit Course FILES: " . print_r($_FILES, true));

    // Handle existing subfolders
    $folder_ids = $_POST['folder_ids'] ?? [];
    $folder_names = $_POST['folder_names'] ?? [];
    $folder_descs = $_POST['folder_descriptions'] ?? [];
    $delete_folders = $_POST['delete_folders'] ?? [];
    $pdf_names = $_POST['pdf_names'] ?? [];
    $video_urls = $_POST['video_urls'] ?? [];
    $video_names = $_POST['video_names'] ?? [];

    foreach ($folder_ids as $i => $folder_id) {
        if (in_array($folder_id, $delete_folders)) {
            $stmt = $db->prepare("DELETE FROM course_contents WHERE folder_id = ?");
            $stmt->bind_param("i", $folder_id);
            $stmt->execute();
            $stmt = $db->prepare("DELETE FROM course_folders WHERE id = ?");
            $stmt->bind_param("i", $folder_id);
            $stmt->execute();
            $stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (?, 'admin', 'Deleted folder', ?)");
            $details = "Deleted folder ID $folder_id from course ID $course_id";
            $stmt->bind_param("is", $_SESSION['admin_id'], $details);
            $stmt->execute();
            continue;
        }

        $folder_name = $db->real_escape_string(trim($folder_names[$i]));
        $folder_desc = !empty($folder_descs[$i]) ? $db->real_escape_string(trim($folder_descs[$i])) : null;
        if (empty($folder_name)) continue;

        $stmt = $db->prepare("UPDATE course_folders SET name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $folder_name, $folder_desc, $folder_id);
        $stmt->execute();

        // Existing folder PDFs
        if (!empty($_FILES['pdf_files']['name'][$folder_id])) {
            foreach ($_FILES['pdf_files']['name'][$folder_id] as $j => $pdf_name) {
                if ($_FILES['pdf_files']['error'][$folder_id][$j] !== UPLOAD_ERR_OK) {
                    $error_codes = [
                        UPLOAD_ERR_INI_SIZE => "Le fichier '$pdf_name' dépasse la taille maximale autorisée.",
                        UPLOAD_ERR_FORM_SIZE => "Le fichier '$pdf_name' dépasse la taille du formulaire.",
                        UPLOAD_ERR_PARTIAL => "Le fichier '$pdf_name' n'a pas été entièrement téléchargé.",
                        UPLOAD_ERR_NO_FILE => "Aucun fichier sélectionné pour '$pdf_name'.",
                        UPLOAD_ERR_NO_TMP_DIR => "Dossier temporaire manquant pour '$pdf_name'.",
                        UPLOAD_ERR_CANT_WRITE => "Impossible d'écrire le fichier '$pdf_name' sur le disque.",
                        UPLOAD_ERR_EXTENSION => "Une extension PHP a bloqué le téléchargement de '$pdf_name'."
                    ];
                    $_SESSION['error'] = $error_codes[$_FILES['pdf_files']['error'][$folder_id][$j]] ?? "Erreur inconnue lors du téléchargement de '$pdf_name'.";
                    error_log("Upload error for '$pdf_name': " . $_FILES['pdf_files']['error'][$folder_id][$j]);
                    continue;
                }

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $_FILES['pdf_files']['tmp_name'][$folder_id][$j]);
                finfo_close($finfo);
                if ($mime !== 'application/pdf') {
                    $_SESSION['error'] = "Le fichier '$pdf_name' n'est pas un PDF valide.";
                    error_log("Invalid MIME type for '$pdf_name': $mime");
                    continue;
                }

                // Sanitize filename
                $clean_name = preg_replace('/[^\p{L}\p{N}\-.() ]/u', '', basename($pdf_name));
                $clean_name = str_replace(' ', '_', $clean_name);
                $pdf_file_name = time() . '_' . $clean_name;
                $pdf_path = $upload_dir . $pdf_file_name;

                if (!move_uploaded_file($_FILES['pdf_files']['tmp_name'][$folder_id][$j], $pdf_path)) {
                    $_SESSION['error'] = "Erreur lors du déplacement du PDF '$pdf_name'. Vérifiez les permissions du dossier.";
                    error_log("Failed to move file '$pdf_name' to '$pdf_path'");
                    continue;
                }

                $content_name = !empty($pdf_names[$folder_id][$j]) ? $db->real_escape_string(trim($pdf_names[$folder_id][$j])) : $clean_name;
                $stmt = $db->prepare("INSERT INTO course_contents (course_id, folder_id, content_type, content_name, content_path) VALUES (?, ?, 'PDF', ?, ?)");
                $stmt->bind_param("iiss", $course_id, $folder_id, $content_name, $pdf_path);
                $stmt->execute();
            }
        }

        // Existing folder videos
        if (!empty($video_urls[$folder_id])) {
            foreach ($video_urls[$folder_id] as $j => $video_url) {
                $video_url = trim($video_url);
                if (empty($video_url)) continue;

                if (preg_match('/^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)([\w-]{11})/', $video_url, $matches)) {
                    $video_id = $matches[4];
                    $video_url = "https://www.youtube.com/embed/$video_id";
                } else {
                    $_SESSION['error'] = "URL YouTube invalide pour '$folder_name' : $video_url";
                    continue;
                }

                $video_url = $db->real_escape_string($video_url);
                $content_name = !empty($video_names[$folder_id][$j]) ? $db->real_escape_string(trim($video_names[$folder_id][$j])) : "Vidéo " . ($j + 1);
                $stmt = $db->prepare("INSERT INTO course_contents (course_id, folder_id, content_type, content_name, content_path) VALUES (?, ?, 'Video', ?, ?)");
                $stmt->bind_param("iiss", $course_id, $folder_id, $content_name, $video_url);
                $stmt->execute();
            }
        }
    }

    // Handle new subfolders
    $new_folder_names = $_POST['new_folder_names'] ?? [];
    $new_folder_descs = $_POST['new_folder_descriptions'] ?? [];
    $new_pdf_names = $_POST['new_pdf_names'] ?? [];
    $new_video_urls = $_POST['new_video_urls'] ?? [];
    $new_video_names = $_POST['new_video_names'] ?? [];

    foreach ($new_folder_names as $i => $folder_name) {
        $folder_name = trim($folder_name);
        if (empty($folder_name)) continue;

        $folder_name = $db->real_escape_string($folder_name);
        $folder_desc = !empty($new_folder_descs[$i]) ? $db->real_escape_string(trim($new_folder_descs[$i])) : null;
        $stmt = $db->prepare("INSERT INTO course_folders (course_id, name, description) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $course_id, $folder_name, $folder_desc);
        $stmt->execute();
        $folder_id = $db->insert_id;

        // New folder PDFs
        if (!empty($_FILES['new_pdf_files']['name'][$i])) {
            foreach ($_FILES['new_pdf_files']['name'][$i] as $j => $pdf_name) {
                if ($_FILES['new_pdf_files']['error'][$i][$j] !== UPLOAD_ERR_OK) {
                    $error_codes = [
                        UPLOAD_ERR_INI_SIZE => "Le fichier '$pdf_name' dépasse la taille maximale autorisée.",
                        UPLOAD_ERR_FORM_SIZE => "Le fichier '$pdf_name' dépasse la taille du formulaire.",
                        UPLOAD_ERR_PARTIAL => "Le fichier '$pdf_name' n'a pas été entièrement téléchargé.",
                        UPLOAD_ERR_NO_FILE => "Aucun fichier sélectionné pour '$pdf_name'.",
                        UPLOAD_ERR_NO_TMP_DIR => "Dossier temporaire manquant pour '$pdf_name'.",
                        UPLOAD_ERR_CANT_WRITE => "Impossible d'écrire le fichier '$pdf_name' sur le disque.",
                        UPLOAD_ERR_EXTENSION => "Une extension PHP a bloqué le téléchargement de '$pdf_name'."
                    ];
                    $_SESSION['error'] = $error_codes[$_FILES['new_pdf_files']['error'][$i][$j]] ?? "Erreur inconnue lors du téléchargement de '$pdf_name'.";
                    error_log("Upload error for '$pdf_name': " . $_FILES['new_pdf_files']['error'][$i][$j]);
                    continue;
                }

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $_FILES['new_pdf_files']['tmp_name'][$i][$j]);
                finfo_close($finfo);
                if ($mime !== 'application/pdf') {
                    $_SESSION['error'] = "Le fichier '$pdf_name' n'est pas un PDF valide.";
                    error_log("Invalid MIME type for '$pdf_name': $mime");
                    continue;
                }

                // Sanitize filename
                $clean_name = preg_replace('/[^\p{L}\p{N}\-.() ]/u', '', basename($pdf_name));
                $clean_name = str_replace(' ', '_', $clean_name);
                $pdf_file_name = time() . '_' . $clean_name;
                $pdf_path = $upload_dir . $pdf_file_name;

                if (!move_uploaded_file($_FILES['new_pdf_files']['tmp_name'][$i][$j], $pdf_path)) {
                    $_SESSION['error'] = "Erreur lors du déplacement du PDF '$pdf_name'. Vérifiez les permissions du dossier.";
                    error_log("Failed to move file '$pdf_name' to '$pdf_path'");
                    continue;
                }

                $content_name = !empty($new_pdf_names[$i][$j]) ? $db->real_escape_string(trim($new_pdf_names[$i][$j])) : $clean_name;
                $stmt = $db->prepare("INSERT INTO course_contents (course_id, folder_id, content_type, content_name, content_path) VALUES (?, ?, 'PDF', ?, ?)");
                $stmt->bind_param("iiss", $course_id, $folder_id, $content_name, $pdf_path);
                $stmt->execute();
            }
        }

        // New folder videos
        if (!empty($new_video_urls[$i])) {
            foreach ($new_video_urls[$i] as $j => $video_url) {
                $video_url = trim($video_url);
                if (empty($video_url)) continue;

                if (preg_match('/^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)([\w-]{11})/', $video_url, $matches)) {
                    $video_id = $matches[4];
                    $video_url = "https://www.youtube.com/embed/$video_id";
                } else {
                    $_SESSION['error'] = "URL YouTube invalide pour '$folder_name' : $video_url";
                    continue;
                }

                $video_url = $db->real_escape_string($video_url);
                $content_name = !empty($new_video_names[$i][$j]) ? $db->real_escape_string(trim($new_video_names[$i][$j])) : "Vidéo " . ($j + 1);
                $stmt = $db->prepare("INSERT INTO course_contents (course_id, folder_id, content_type, content_name, content_path) VALUES (?, ?, 'Video', ?, ?)");
                $stmt->bind_param("iiss", $course_id, $folder_id, $content_name, $video_url);
                $stmt->execute();
            }
        }
    }

    $stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (?, 'admin', 'Edited course', ?)");
    $details = "Edited course ID $course_id: $title";
    $stmt->bind_param("is", $_SESSION['admin_id'], $details);
    $stmt->execute();

    if (!isset($_SESSION['error'])) {
        $_SESSION['message'] = "Cours modifié avec succès !";
    }
    header("Location: manage_courses.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Cours - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .content-item {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f9f9f9;
        }
        .form-check-label {
            margin-left: 10px;
            color: #333;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        .modal-content {
            background: #fff;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .modal-header, .modal-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-header h5 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .modal-body {
            margin: 20px 0;
            font-size: 16px;
        }
        .btn-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #333;
        }
        .btn-close:hover {
            color: #000;
        }
        /* Button Styles */
        .sparkle-add-folder, .glow-add-pdf, .shine-add-video, .victory-save-course, .retreat-cancel-course, .fire-remove-folder, .storm-remove-content, .blaze-delete-content, .calm-modal-cancel, .inferno-modal-delete {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .sparkle-add-folder, .glow-add-pdf, .shine-add-video {
            background: linear-gradient(135deg, #28a745, #218838);
            color: #fff;
        }
        .sparkle-add-folder:hover, .glow-add-pdf:hover, .shine-add-video:hover {
            background: linear-gradient(135deg, #218838, #1e7e34);
            transform: scale(1.05);
            filter: brightness(1.1);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .victory-save-course {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: #fff;
        }
        .victory-save-course:hover {
            background: linear-gradient(135deg, #0056b3, #004085);
            transform: scale(1.05);
            filter: brightness(1.1);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .retreat-cancel-course, .calm-modal-cancel {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: #fff;
        }
        .retreat-cancel-course:hover, .calm-modal-cancel:hover {
            background: linear-gradient(135deg, #5a6268, #545b62);
            transform: scale(1.05);
            filter: brightness(1.1);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .fire-remove-folder, .storm-remove-content, .blaze-delete-content, .inferno-modal-delete {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: #fff;
        }
        .fire-remove-folder:hover, .storm-remove-content:hover, .blaze-delete-content:hover, .inferno-modal-delete:hover {
            background: linear-gradient(135deg, #c82333, #bd2130);
            transform: scale(1.05);
            filter: brightness(1.1);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .sparkle-add-folder i, .glow-add-pdf i, .shine-add-video i, .victory-save-course i, .retreat-cancel-course i, .fire-remove-folder i, .storm-remove-content i, .blaze-delete-content i, .calm-modal-cancel i, .inferno-modal-delete i {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-edit"></i> Modifier Cours</h1>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error-message"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="course-form">
            <div class="course-input-group">
                <label class="course-label"><i class="fas fa-book"></i> Titre du Cours</label>
                <input type="text" name="title" class="course-input" value="<?php echo htmlspecialchars($course['title']); ?>" required>
            </div>
            <div class="course-input-group">
                <label class="course-label"><i class="fas fa-book-open"></i> Matière</label>
                <select name="subject_id" class="course-select" required>
                    <option value="">Sélectionnez une matière</option>
                    <?php
                    $subjects->data_seek(0);
                    while ($sub = $subjects->fetch_assoc()): ?>
                        <option value="<?php echo $sub['id']; ?>" <?php echo $sub['id'] == $course['subject_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($sub['name'] . " (" . $sub['level_name'] . ")"); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
 

            <div class="subfolder-section-test">
                <h3 class="section-title"><i class="fas fa-folder"></i> Dossiers Existants</h3>
                <div id="existing-folder-list">
                    <?php
                    $folders->data_seek(0);
                    while ($folder = $folders->fetch_assoc()):
                        $folder_id = $folder['id'];
                        $contents = $db->query("SELECT id, content_type, content_name, content_path FROM course_contents WHERE folder_id = $folder_id");
                    ?>
                        <div class="subfolder-card-test">
                            <input type="hidden" name="folder_ids[]" value="<?php echo $folder_id; ?>">
                            <div class="course-input-group">
                                <label class="course-label"><i class="fas fa-folder"></i> Nom du Dossier</label>
                                <input type="text" name="folder_names[]" class="course-input" value="<?php echo htmlspecialchars($folder['name']); ?>" required>
                            </div>
                            <div class="course-input-group">
                                <label class="course-label"><i class="fas fa-info-circle"></i> Description (facultatif)</label>
                                <textarea name="folder_descriptions[]" class="course-textarea"><?php echo htmlspecialchars($folder['description'] ?? ''); ?></textarea>
                            </div>
                            <div class="course-input-group">
                                <label class="course-label"><i class="fas fa-file-pdf"></i> Contenu Existant (PDFs)</label>
                                <div class="content-list">
                                    <?php
                                    $contents->data_seek(0);
                                    $has_pdfs = false;
                                    while ($content = $contents->fetch_assoc()):
                                        if ($content['content_type'] == 'PDF'):
                                            $has_pdfs = true;
                                    ?>
                                            <div class="content-item">
                                                <span><i class="fas fa-file-pdf"></i> <?php echo htmlspecialchars($content['content_name']); ?></span>
                                                <button type="button" class="blaze-delete-content" data-content-id="<?php echo $content['id']; ?>" data-content-name="<?php echo htmlspecialchars($content['content_name']); ?>"><i class="fas fa-trash"></i></button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endwhile; ?>
                                    <?php if (!$has_pdfs): ?>
                                        <p class="text-muted">Aucun PDF dans ce dossier.</p>
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="glow-add-pdf" data-folder="<?php echo $folder_id; ?>"><i class="fas fa-plus"></i> Ajouter PDF</button>
                                <div class="pdf-list"></div>
                            </div>
                            <div class="course-input-group">
                                <label class="course-label"><i class="fas fa-video"></i> Contenu Existant (Vidéos)</label>
                                <div class="content-list">
                                    <?php
                                    $contents->data_seek(0);
                                    $has_videos = false;
                                    while ($content = $contents->fetch_assoc()):
                                        if ($content['content_type'] == 'Video'):
                                            $has_videos = true;
                                    ?>
                                            <div class="content-item">
                                                <span><i class="fas fa-video"></i> <?php echo htmlspecialchars($content['content_name']); ?></span>
                                                <button type="button" class="blaze-delete-content" data-content-id="<?php echo $content['id']; ?>" data-content-name="<?php echo htmlspecialchars($content['content_name']); ?>"><i class="fas fa-trash"></i></button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endwhile; ?>
                                    <?php if (!$has_videos): ?>
                                        <p class="text-muted">Aucune vidéo dans ce dossier.</p>
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="shine-add-video" data-folder="<?php echo $folder_id; ?>"><i class="fas fa-plus"></i> Ajouter Vidéo</button>
                                <div class="video-list"></div>
                            </div>
                            <div class="course-input-group">
                                <label class="form-check-label">
                                    <input type="checkbox" name="delete_folders[]" value="<?php echo $folder_id; ?>" class="form-check-input">
                                    Supprimer ce dossier
                                </label>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="subfolder-section-test">
                <h3 class="section-title"><i class="fas fa-folder-plus"></i> Nouveaux Dossiers</h3>
                <button type="button" id="addFolder" class="sparkle-add-folder"><i class="fas fa-plus"></i> Ajouter un Dossier</button>
                <div id="new-folder-list"></div>
            </div>

            <div class="form-controls">
                <button type="submit" class="victory-save-course"><i class="fas fa-save"></i> Enregistrer</button>
                <a href="manage_courses.php" class="retreat-cancel-course"><i class="fas fa-times"></i> Annuler</a>
            </div>
        </form>
    </main>

    <div class="modal" id="deleteContentModal">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Confirmer la Suppression</h5>
                <button type="button" class="btn-close" data-modal-close>×</button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer "<span id="delete-content-name"></span>" ? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="calm-modal-cancel" data-modal-close><i class="fas fa-times"></i> Annuler</button>
                <button type="button" class="inferno-modal-delete" id="confirm-delete-btn"><i class="fas fa-trash"></i> Supprimer</button>
            </div>
        </div>
    </div>

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
                            <input type="text" name="new_folder_names[]" class="course-input" placeholder="ex., Les Matrices">
                        </div>
                        <div class="course-input-group">
                            <label class="course-label"><i class="fas fa-info-circle"></i> Description (facultatif)</label>
                            <textarea name="new_folder_descriptions[]" class="course-textarea" placeholder="Description du dossier"></textarea>
                        </div>
                        <div class="course-input-group">
                            <label class="course-label"><i class="fas fa-file-pdf"></i> PDFs</label>
                            <button type="button" class="glow-add-pdf" data-folder="${folderCount}"><i class="fas fa-plus"></i> Ajouter PDF</button>
                            <div class="pdf-list"></div>
                        </div>
                        <div class="course-input-group">
                            <label class="course-label"><i class="fas fa-video"></i> Vidéos</label>
                            <button type="button" class="shine-add-video" data-folder="${folderCount}"><i class="fas fa-plus"></i> Ajouter Vidéo</button>
                            <div class="video-list"></div>
                        </div>
                        <button type="button" class="fire-remove-folder"><i class="fas fa-trash"></i> Supprimer Dossier</button>
                    </div>`;
                $('#new-folder-list').append(folderHtml).find(`#folder-${folderCount}`).hide().slideDown(300);
            }

            $('#addFolder').click(addFolder);

            $(document).on('click', '.glow-add-pdf', function() {
                const folderId = $(this).data('folder');
                const namePrefix = $(this).closest('.subfolder-card-test').parent().attr('id') === 'new-folder-list' ? 'new_pdf_names' : 'pdf_names';
                const filePrefix = $(this).closest('.subfolder-card-test').parent().attr('id') === 'new-folder-list' ? 'new_pdf_files' : 'pdf_files';
                const pdfHtml = `
                    <div class="content-item">
                        <div class="course-input-group">
                            <label class="course-label">Nom du PDF</label>
                            <input type="text" name="${namePrefix}[${folderId}][]" class="course-input" placeholder="ex., Introduction aux Matrices">
                        </div>
                        <div class="course-input-group">
                            <label class="course-label">Fichier PDF</label>
                            <input type="file" name="${filePrefix}[${folderId}][]" class="course-input" accept=".pdf">
                        </div>
                        <button type="button" class="storm-remove-content"><i class="fas fa-times"></i> Supprimer</button>
                    </div>`;
                $(this).siblings('.pdf-list').append(pdfHtml).find('.content-item').last().hide().slideDown(300);
            });

            $(document).on('click', '.shine-add-video', function() {
                const folderId = $(this).data('folder');
                const namePrefix = $(this).closest('.subfolder-card-test').parent().attr('id') === 'new-folder-list' ? 'new_video_names' : 'video_names';
                const urlPrefix = $(this).closest('.subfolder-card-test').parent().attr('id') === 'new-folder-list' ? 'new_video_urls' : 'video_urls';
                const videoHtml = `
                    <div class="content-item">
                        <div class="course-input-group">
                            <label class="course-label">Nom de la Vidéo</label>
                            <input type="text" name="${namePrefix}[${folderId}][]" class="course-input" placeholder="ex., Vidéo d'Introduction">
                        </div>
                        <div class="course-input-group">
                            <label class="course-label">URL YouTube</label>
                            <input type="url" name="${urlPrefix}[${folderId}][]" class="course-input" placeholder="ex., https://www.youtube.com/watch?v=xyz">
                        </div>
                        <button type="button" class="storm-remove-content"><i class="fas fa-times"></i> Supprimer</button>
                    </div>`;
                $(this).siblings('.video-list').append(videoHtml).find('.content-item').last().hide().slideDown(300);
            });

            $(document).on('click', '.fire-remove-folder, .storm-remove-content', function() {
                $(this).closest('.subfolder-card-test, .content-item').slideUp(300, function() {
                    $(this).remove();
                });
            });

            let deleteContentId = null;
            $(document).on('click', '.blaze-delete-content', function() {
                deleteContentId = $(this).data('content-id');
                const contentName = $(this).data('content-name');
                $('#delete-content-name').text(contentName);
                $('#deleteContentModal').show();
            });

            $(document).on('click', '[data-modal-close]', function() {
                $('#deleteContentModal').hide();
            });

            $('#confirm-delete-btn').click(function() {
                if (deleteContentId) {
                    $.ajax({
                        url: 'edit_course.php?id=<?php echo $course_id; ?>',
                        type: 'POST',
                        data: { action: 'delete_content', content_id: deleteContentId },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                $(`.blaze-delete-content[data-content-id="${deleteContentId}"]`).closest('.content-item').slideUp(300, function() {
                                    $(this).remove();
                                });
                                $('#deleteContentModal').hide();
                            } else {
                                alert(response.error || 'Erreur lors de la suppression');
                            }
                        },
                        error: function() {
                            alert('Erreur serveur lors de la suppression');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>