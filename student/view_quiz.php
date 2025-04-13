<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: quizzes.php");
    exit;
}

$student_id = (int)$_SESSION['student_id'];
$quiz_id = (int)$_GET['id'];

$quiz = $db->query("
    SELECT q.id, q.title, q.description, q.pdf_path, s.name AS subject_name, l.name AS level_name, 
           qs.response_path, qs.grade, qs.feedback, qs.submitted_at
    FROM quizzes q
    JOIN subjects s ON q.subject_id = s.id
    JOIN levels l ON s.level_id = l.id
    LEFT JOIN quiz_submissions qs ON q.id = qs.quiz_id AND qs.student_id = $student_id
    WHERE q.id = $quiz_id
    AND q.subject_id IN (
        SELECT subject_id FROM student_subjects WHERE student_id = $student_id
        UNION
        SELECT subject_id FROM student_courses sc
        JOIN courses c ON sc.course_id = c.id
        WHERE sc.student_id = $student_id
    )
")->fetch_assoc();

if (!$quiz) {
    header("Location: quizzes.php");
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$quiz['submitted_at']) {
    if (!isset($_FILES['response_pdf']) || $_FILES['response_pdf']['error'] == UPLOAD_ERR_NO_FILE) {
        $errors[] = "Veuillez uploader votre réponse en PDF.";
    } else {
        $file = $_FILES['response_pdf'];
        $allowed_types = ['application/pdf'];
        $max_size = 5 * 1024 * 1024; // 5MB
        if (!in_array($file['type'], $allowed_types) || $file['size'] > $max_size) {
            $errors[] = "Le fichier doit être un PDF de moins de 5 Mo.";
        }
    }

    if (empty($errors)) {
        $upload_dir = '../uploads/quiz_submissions/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $file_name = uniqid() . '_' . basename($file['name']);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $stmt = $db->prepare("INSERT INTO quiz_submissions (quiz_id, student_id, response_path) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $quiz_id, $student_id, $file_path);
            if ($stmt->execute()) {
                $success = "Réponse soumise avec succès.";
                header("Refresh:0");
            } else {
                $errors[] = "Erreur lors de la soumission.";
                unlink($file_path);
            }
        } else {
            $errors[] = "Erreur lors de l'upload du fichier.";
        }
    }
}

// Log quiz view
$stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (?, 'student', 'Viewed quiz', ?)");
$details = "Viewed quiz ID $quiz_id: {$quiz['title']}";
$stmt->bind_param("is", $student_id, $details);
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voir Quiz - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
</head>
<body oncontextmenu="return false;">
    <?php include '../includes/student_header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-question-circle"></i> Détails du Quiz</h1>
        <?php if ($success): ?>
            <p class="success-message"><?php echo $success; ?></p>
        <?php endif; ?>
        <?php foreach ($errors as $error): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endforeach; ?>
        <div class="detail-card">
            <h3><i class="fas fa-info-circle"></i> Informations sur le Quiz</h3>
            <p><strong>Titre :</strong> <?php echo htmlspecialchars($quiz['title']); ?></p>
            <p><strong>Matière :</strong> <?php echo htmlspecialchars($quiz['subject_name']); ?></p>
            <p><strong>Niveau :</strong> <?php echo htmlspecialchars($quiz['level_name']); ?></p>
            <?php if ($quiz['description']): ?>
                <p><strong>Description :</strong> <?php echo htmlspecialchars($quiz['description']); ?></p>
            <?php endif; ?>
            <p><strong>Note :</strong> <?php echo $quiz['grade'] !== null ? number_format($quiz['grade'], 2) . '/20' : 'Non noté'; ?></p>
            <?php if ($quiz['feedback']): ?>
                <p><strong>Commentaires :</strong> <?php echo htmlspecialchars($quiz['feedback']); ?></p>
            <?php endif; ?>
        </div>
        <div class="content-preview">
            <h3><i class="fas fa-file-pdf"></i> Quiz</h3>
            <div class="pdf-viewer" data-pdf="../includes/serve_quiz_pdf.php?quiz_id=<?php echo $quiz['id']; ?>" id="pdf-viewer-<?php echo $quiz['id']; ?>">
                <div class="pdf-controls-fixed">
                    <button class="zoom-in btn-action"><i class="fas fa-search-plus"></i> Zoom Avant</button>
                    <button class="zoom-out btn-action"><i class="fas fa-search-minus"></i> Zoom Arrière</button>
                    <button class="rotate btn-action"><i class="fas fa-redo"></i> Rotation</button>
                </div>
                <div class="pdf-canvas"></div>
            </div>
        </div>
        <?php if ($quiz['submitted_at']): ?>
            <div class="content-preview">
                <p class="success-message">Réponse soumise le <?php echo $quiz['submitted_at']; ?>.</p>
            </div>
        <?php else: ?>
            <div class="form-container">
                <h3><i class="fas fa-upload"></i> Soumettre une Réponse</h3>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="response_pdf"><i class="fas fa-file-pdf"></i> Réponse PDF</label>
                        <input type="file" name="response_pdf" id="response_pdf" accept="application/pdf">
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="save-course-btn"><i class="fas fa-upload"></i> Soumettre</button>
                        <a href="quizzes.php" class="btn-action cancel"><i class="fas fa-times"></i> Annuler</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </main>
    <?php include '../includes/footer.php'; ?>
    <script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';
    $(document).ready(function() {
        const pdfViewer = $('.pdf-viewer');
        const url = pdfViewer.data('pdf');
        const canvasContainer = pdfViewer.find('.pdf-canvas');
        let pdfDoc = null;
        let scale = 1.5;
        let rotation = 0;

        function renderPages(pdf, scale, rotation) {
            canvasContainer.empty();
            for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                const canvas = document.createElement('canvas');
                canvas.className = 'pdf-page';
                canvas.dataset.pageNum = pageNum;
                canvasContainer.append(canvas);
                const ctx = canvas.getContext('2d');

                pdf.getPage(pageNum).then(page => {
                    const viewport = page.getViewport({ scale: scale, rotation: rotation });
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    canvas.style.width = '100%';
                    canvas.style.maxWidth = `${viewport.width}px`;
                    canvas.style.margin = '0 auto'; // Center canvas
                    canvas.style.display = 'block'; // Ensure block display

                    const renderContext = { canvasContext: ctx, viewport: viewport };
                    page.render(renderContext);
                });
            }
        }

        pdfjsLib.getDocument(url).promise.then(pdf => {
            pdfDoc = pdf;
            renderPages(pdf, scale, rotation);
        }).catch(error => {
            canvasContainer.html('<p class="error-message">Erreur de chargement du PDF : ' + error.message + '</p>');
        });

        pdfViewer.find('.zoom-in').click(() => {
            if (pdfDoc) {
                scale += 0.25;
                renderPages(pdfDoc, scale, rotation);
            }
        });

        pdfViewer.find('.zoom-out').click(() => {
            if (pdfDoc && scale > 0.5) {
                scale -= 0.25;
                renderPages(pdfDoc, scale, rotation);
            }
        });

        pdfViewer.find('.rotate').click(() => {
            if (pdfDoc) {
                rotation = (rotation + 90) % 360;
                renderPages(pdfDoc, scale, rotation);
            }
        });

        // Blur handling
        let isFocused = true;
        function applyBlur() {
            if (!isFocused) {
                pdfViewer.addClass('blurred');
            } else {
                pdfViewer.removeClass('blurred');
            }
        }

        $(window).on('focus', () => {
            isFocused = true;
            applyBlur();
        });

        $(window).on('blur', () => {
            isFocused = false;
            applyBlur();
        });

        // Prevent screenshot via PrintScreen
        $(document).on('keydown', (e) => {
            if (e.key === 'PrintScreen') {
                navigator.clipboard.writeText('');
                alert('Les captures d’écran via PrintScreen sont désactivées.');
            }
        });
    });
</script>
</body>
</html>