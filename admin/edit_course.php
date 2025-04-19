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

$subjects = $db->query("SELECT s.id, s.name, l.name AS level_name FROM subjects s JOIN levels l ON s.level_id = l.id ORDER BY s.name ASC");
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

    // Validate inputs
    if (empty($title) || !$subject_id || !in_array($difficulty, ['Easy', 'Medium', 'Hard'])) {
        $_SESSION['error'] = "Veuillez remplir tous les champs correctement.";
        header("Location: edit_course.php?id=$course_id");
        exit;
    }

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

                // Sanitize filename (preserve Arabic, replace spaces/special chars)
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .course-form .card-header {
            background-color: #1e3c72;
        }
        .btn-danger {
            background-color: #e53e3e;
            border-color: #e53e3e;
        }
        .btn-danger:hover {
            background-color: #c53030;
            border-color: #c53030;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="container mt-4">
        <h1 class="mb-4 text-primary"><i class="fas fa-edit"></i> Modifier Cours</h1>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="course-form">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-book"></i> Informations du Cours</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-book"></i> Titre du Cours</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($course['title']); ?>" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label"><i class="fas fa-book-open"></i> Matière</label>
                            <select name="subject_id" class="form-select" required>
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
                        <div class="col-md-3 mb-3">
                            <label class="form-label"><i class="fas fa-tachometer-alt"></i> Difficulté</label>
                            <select name="difficulty" class="form-select" required>
                                <option value="">Sélectionnez une difficulté</option>
                                <option value="Easy" <?php echo $course['difficulty'] == 'Easy' ? 'selected' : ''; ?>>Facile</option>
                                <option value="Medium" <?php echo $course['difficulty'] == 'Medium' ? 'selected' : ''; ?>>Moyen</option>
                                <option value="Hard" <?php echo $course['difficulty'] == 'Hard' ? 'selected' : ''; ?>>Difficile</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-folder"></i> Dossiers Existants</h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="folder-accordion">
                        <?php
                        $folders->data_seek(0);
                        while ($folder = $folders->fetch_assoc()):
                            $folder_id = $folder['id'];
                            $contents = $db->query("SELECT id, content_type, content_name, content_path FROM course_contents WHERE folder_id = $folder_id");
                        ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-<?php echo $folder_id; ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $folder_id; ?>" aria-expanded="false" aria-controls="collapse-<?php echo $folder_id; ?>">
                                        <i class="fas fa-folder me-2"></i> <?php echo htmlspecialchars($folder['name']); ?>
                                    </button>
                                </h2>
                                <div id="collapse-<?php echo $folder_id; ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?php echo $folder_id; ?>" data-bs-parent="#folder-accordion">
                                    <div class="accordion-body">
                                        <input type="hidden" name="folder_ids[]" value="<?php echo $folder_id; ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Nom du Dossier</label>
                                            <input type="text" name="folder_names[]" class="form-control" value="<?php echo htmlspecialchars($folder['name']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Description (facultatif)</label>
                                            <textarea name="folder_descriptions[]" class="form-control"><?php echo htmlspecialchars($folder['description'] ?? ''); ?></textarea>
                                        </div>
                                        <ul class="nav nav-tabs mb-3" id="content-tabs-<?php echo $folder_id; ?>" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="pdf-tab-<?php echo $folder_id; ?>" data-bs-toggle="tab" data-bs-target="#pdf-content-<?php echo $folder_id; ?>" type="button" role="tab" aria-controls="pdf-content-<?php echo $folder_id; ?>" aria-selected="true"><i class="fas fa-file-pdf"></i> PDFs</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="video-tab-<?php echo $folder_id; ?>" data-bs-toggle="tab" data-bs-target="#video-content-<?php echo $folder_id; ?>" type="button" role="tab" aria-controls="video-content-<?php echo $folder_id; ?>" aria-selected="false"><i class="fas fa-video"></i> Vidéos</button>
                                            </li>
                                        </ul>
                                        <div class="tab-content" id="content-tab-content-<?php echo $folder_id; ?>">
                                            <div class="tab-pane fade show active" id="pdf-content-<?php echo $folder_id; ?>" role="tabpanel" aria-labelledby="pdf-tab-<?php echo $folder_id; ?>">
                                                <h6>Contenu Existant</h6>
                                                <div class="row">
                                                    <?php
                                                    $contents->data_seek(0);
                                                    $has_pdfs = false;
                                                    while ($content = $contents->fetch_assoc()):
                                                        if ($content['content_type'] == 'PDF'):
                                                            $has_pdfs = true;
                                                    ?>
                                                            <div class="col-md-6 col-lg-4 mb-3">
                                                                <div class="content-item card h-100">
                                                                    <div class="card-body d-flex justify-content-between align-items-center">
                                                                        <span><i class="fas fa-file-pdf me-2"></i> <?php echo htmlspecialchars($content['content_name']); ?></span>
                                                                        <button type="button" class="btn btn-danger btn-sm delete-content-btn" data-content-id="<?php echo $content['id']; ?>" data-content-name="<?php echo htmlspecialchars($content['content_name']); ?>"><i class="fas fa-trash"></i></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                    <?php
                                                        endif;
                                                    endwhile;
                                                    if (!$has_pdfs):
                                                    ?>
                                                        <p class="text-muted">Aucun PDF dans ce dossier.</p>
                                                    <?php endif; ?>
                                                </div>
                                                <h6>Ajouter des PDFs</h6>
                                                <button type="button" class="btn btn-outline-primary add-pdf-btn mb-3" data-folder="<?php echo $folder_id; ?>"><i class="fas fa-plus"></i> Ajouter PDF</button>
                                                <div class="pdf-list"></div>
                                            </div>
                                            <div class="tab-pane fade" id="video-content-<?php echo $folder_id; ?>" role="tabpanel" aria-labelledby="video-tab-<?php echo $folder_id; ?>">
                                                <h6>Contenu Existant</h6>
                                                <div class="row">
                                                    <?php
                                                    $contents->data_seek(0);
                                                    $has_videos = false;
                                                    while ($content = $contents->fetch_assoc()):
                                                        if ($content['content_type'] == 'Video'):
                                                            $has_videos = true;
                                                    ?>
                                                            <div class="col-md-6 col-lg-4 mb-3">
                                                                <div class="content-item card h-100">
                                                                    <div class="card-body d-flex justify-content-between align-items-center">
                                                                        <span><i class="fas fa-video me-2"></i> <?php echo htmlspecialchars($content['content_name']); ?></span>
                                                                        <button type="button" class="btn btn-danger btn-sm delete-content-btn" data-content-id="<?php echo $content['id']; ?>" data-content-name="<?php echo htmlspecialchars($content['content_name']); ?>"><i class="fas fa-trash"></i></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                    <?php
                                                        endif;
                                                    endwhile;
                                                    if (!$has_videos):
                                                    ?>
                                                        <p class="text-muted">Aucune vidéo dans ce dossier.</p>
                                                    <?php endif; ?>
                                                </div>
                                                <h6>Ajouter des Vidéos</h6>
                                                <button type="button" class="btn btn-outline-primary add-video-btn mb-3" data-folder="<?php echo $folder_id; ?>"><i class="fas fa-plus"></i> Ajouter Vidéo</button>
                                                <div class="video-list"></div>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <label class="form-check-label">
                                                <input type="checkbox" name="delete_folders[]" value="<?php echo $folder_id; ?>" class="form-check-input">
                                                Supprimer ce dossier
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-folder-plus"></i> Nouveaux Dossiers</h5>
                </div>
                <div class="card-body">
                    <button type="button" id="addFolder" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Ajouter un Dossier</button>
                    <div id="new-folder-list"></div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success me-2"><i class="fas fa-save"></i> Enregistrer</button>
                <a href="manage_courses.php" class="btn btn-secondary"><i class="fas fa-times"></i> Annuler</a>
            </div>
        </form>
    </main>

    <div class="modal fade" id="deleteContentModal" tabindex="-1" aria-labelledby="deleteContentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteContentModalLabel">Confirmer la Suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer "<span id="delete-content-name"></span>" ? Cette action est irréversible.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete-btn">Supprimer</button>
                </div>
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
                    <div class="subfolder-card mb-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-folder"></i> Nom du Dossier</label>
                                    <input type="text" name="new_folder_names[]" class="form-control" placeholder="ex., Les Matrices">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-info-circle"></i> Description (facultatif)</label>
                                    <textarea name="new_folder_descriptions[]" class="form-control" placeholder="Description du dossier"></textarea>
                                </div>
                                <ul class="nav nav-tabs mb-3" id="new-content-tabs-${folderCount}" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="new-pdf-tab-${folderCount}" data-bs-toggle="tab" data-bs-target="#new-pdf-content-${folderCount}" type="button" role="tab" aria-controls="new-pdf-content-${folderCount}" aria-selected="true"><i class="fas fa-file-pdf"></i> PDFs</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="new-video-tab-${folderCount}" data-bs-toggle="tab" data-bs-target="#new-video-content-${folderCount}" type="button" role="tab" aria-controls="new-video-content-${folderCount}" aria-selected="false"><i class="fas fa-video"></i> Vidéos</button>
                                    </li>
                                </ul>
                                <div class="tab-content" id="new-content-tab-content-${folderCount}">
                                    <div class="tab-pane fade show active" id="new-pdf-content-${folderCount}" role="tabpanel" aria-labelledby="new-pdf-tab-${folderCount}">
                                        <button type="button" class="btn btn-outline-primary add-pdf-btn mb-3" data-folder="${folderCount}"><i class="fas fa-plus"></i> Ajouter PDF</button>
                                        <div class="pdf-list"></div>
                                    </div>
                                    <div class="tab-pane fade" id="new-video-content-${folderCount}" role="tabpanel" aria-labelledby="new-video-tab-${folderCount}">
                                        <button type="button" class="btn btn-outline-primary add-video-btn mb-3" data-folder="${folderCount}"><i class="fas fa-plus"></i> Ajouter Vidéo</button>
                                        <div class="video-list"></div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-danger remove-folder-btn mt-3"><i class="fas fa-trash"></i> Supprimer Dossier</button>
                            </div>
                        </div>
                    </div>`;
                $('#new-folder-list').append(folderHtml);
            }

            $('#addFolder').click(addFolder);

            $(document).on('click', '.add-pdf-btn', function() {
                const folderId = $(this).data('folder');
                const namePrefix = $(this).closest('.subfolder-card').length ? 'new_pdf_names' : 'pdf_names';
                const filePrefix = $(this).closest('.subfolder-card').length ? 'new_pdf_files' : 'pdf_files';
                const pdfHtml = `
                    <div class="content-item card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nom du PDF</label>
                                    <input type="text" name="${namePrefix}[${folderId}][]" class="form-control" placeholder="ex., Introduction aux Matrices">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fichier PDF</label>
                                    <input type="file" name="${filePrefix}[${folderId}][]" class="form-control" accept=".pdf">
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-danger btn-sm remove-content-btn"><i class="fas fa-times"></i> Supprimer</button>
                        </div>
                    </div>`;
                $(this).siblings('.pdf-list').append(pdfHtml);
            });

            $(document).on('click', '.add-video-btn', function() {
                const folderId = $(this).data('folder');
                const namePrefix = $(this).closest('.subfolder-card').length ? 'new_video_names' : 'video_names';
                const urlPrefix = $(this).closest('.subfolder-card').length ? 'new_video_urls' : 'video_urls';
                const videoHtml = `
                    <div class="content-item card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nom de la Vidéo</label>
                                    <input type="text" name="${namePrefix}[${folderId}][]" class="form-control" placeholder="ex., Vidéo d'Introduction">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">URL YouTube</label>
                                    <input type="url" name="${urlPrefix}[${folderId}][]" class="form-control" placeholder="ex., https://www.youtube.com/watch?v=xyz">
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-danger btn-sm remove-content-btn"><i class="fas fa-times"></i> Supprimer</button>
                        </div>
                    </div>`;
                $(this).siblings('.video-list').append(videoHtml);
            });

            $(document).on('click', '.remove-folder-btn, .remove-content-btn', function() {
                $(this).closest('.subfolder-card, .content-item').remove();
            });

            let deleteContentId = null;
            $(document).on('click', '.delete-content-btn', function() {
                deleteContentId = $(this).data('content-id');
                const contentName = $(this).data('content-name');
                $('#delete-content-name').text(contentName);
                $('#deleteContentModal').modal('show');
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
                                $(`.delete-content-btn[data-content-id="${deleteContentId}"]`).closest('.content-item').remove();
                                $('#deleteContentModal').modal('hide');
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