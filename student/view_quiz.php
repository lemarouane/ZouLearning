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
    SELECT q.id, q.title, q.description, q.pdf_path, q.start_datetime, q.duration_hours,
           s.name AS subject_name, l.name AS level_name
    FROM quizzes q
    JOIN subjects s ON q.subject_id = s.id
    JOIN levels l ON s.level_id = l.id
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

$submissions = $db->query("
    SELECT response_path, grade, feedback, submitted_at
    FROM quiz_submissions
    WHERE quiz_id = $quiz_id AND student_id = $student_id
    ORDER BY submitted_at DESC
");

$errors = [];
$success = '';
$now = new DateTime('now', new DateTimeZone('Africa/Casablanca'));
$start_datetime = new DateTime($quiz['start_datetime'], new DateTimeZone('Africa/Casablanca'));
$deadline = clone $start_datetime;
$deadline->modify("+{$quiz['duration_hours']} hours");
$is_before_start = $now < $start_datetime;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$is_before_start) {
    if (!isset($_FILES['response_pdf']) || $_FILES['response_pdf']['error'] == UPLOAD_ERR_NO_FILE) {
        $errors[] = "Veuillez uploader votre réponse en PDF.";
    } else {
        $file = $_FILES['response_pdf'];
        $allowed_types = ['application/pdf'];
        $max_size = 5 * 1024 * 1024;
        if (!in_array($file['type'], $allowed_types) || $file['size'] > $max_size) {
            $errors[] = "Le fichier doit être un PDF de moins de 5 Mo.";
        }
    }

    if (empty($errors)) {
        $upload_dir = '../Uploads/quiz_submissions/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $student = $db->query("SELECT full_name FROM students WHERE id = $student_id")->fetch_assoc();
        $safe_full_name = preg_replace('/[^A-Za-z0-9 ]/', '', $student['full_name']);
        $safe_quiz_title = preg_replace('/[^A-Za-z0-9 ]/', '', $quiz['title']);
        $submission_count = $submissions->num_rows + 1;
        $file_name = $safe_full_name . ' - ' . $safe_quiz_title . ' v' . $submission_count . '.pdf';
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $stmt = $db->prepare("INSERT INTO quiz_submissions (quiz_id, student_id, response_path, submitted_at) VALUES (?, ?, ?, NOW())");
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
    <link rel="icon" type="image/png" href="../assets/img/logo.png">

    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
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
            <p><strong>Début :</strong> <?php echo htmlspecialchars($quiz['start_datetime']); ?> (GMT+1)</p>
            <p><strong>Durée :</strong> <?php echo number_format($quiz['duration_hours'], 2); ?> heures</p>
            <?php if ($quiz['description']): ?>
                <p><strong>Description :</strong> <?php echo htmlspecialchars($quiz['description']); ?></p>
            <?php endif; ?>
            <?php if ($submissions->num_rows > 0): ?>
                <h4>Soumissions</h4>
                <?php $submissions->data_seek(0); while ($submission = $submissions->fetch_assoc()): ?>
                    <p>
                        <strong>Soumis le :</strong> <?php echo htmlspecialchars($submission['submitted_at']); ?><br>
                        <strong>Note :</strong> <?php echo $submission['grade'] !== null ? number_format($submission['grade'], 2) . '/20' : 'Non noté'; ?><br>
                        <strong>Commentaires :</strong> <?php echo $submission['feedback'] ? htmlspecialchars($submission['feedback']) : '-'; ?>
                    </p>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
        <div class="content-preview" id="quiz-content">
            <?php if ($is_before_start): ?>
                <div class="countdown-container start-countdown">
                    <h3><i class="fas fa-clock"></i> En attente du début</h3>
                    <p>Le quiz commencera le <?php echo htmlspecialchars($quiz['start_datetime']); ?> (GMT+1).</p>
                    <div class="countdown-timer">
                        <span id="countdown-start" class="countdown-text"></span>
                    </div>
                </div>
            <?php else: ?>
                <h3><i class="fas fa-file-pdf"></i> Quiz</h3>
                <div class="pdf-viewer" data-pdf="../includes/serve_quiz_pdf.php?quiz_id=<?php echo $quiz['id']; ?>" id="pdf-viewer-<?php echo $quiz['id']; ?>">
                    <div class="pdf-controls-fixed">
                        <button class="zoom-in btn-action"><i class="fas fa-search-plus"></i> Zoom Avant</button>
                        <button class="zoom-out btn-action"><i class="fas fa-search-minus"></i> Zoom Arrière</button>
                        <button class="rotate btn-action"><i class="fas fa-redo"></i> Rotation</button>
                    </div>
                    <div class="pdf-canvas"></div>
                </div>
            <?php endif; ?>
        </div>
        <?php if (!$is_before_start): ?>
            <div class="form-container" id="upload-form">
                <h3><i class="fas fa-upload"></i> Soumettre une Réponse</h3>
                <p class="info-message">Vous pouvez soumettre plusieurs fois. Chaque soumission est enregistrée séparément.</p>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="response_pdf"><i class="fas fa-file-pdf"></i> Réponse PDF</label>
                        <input type="file" name="response_pdf" id="response_pdf" accept="application/pdf" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="save-course-btn"><i class="fas fa-upload"></i> Soumettre</button>
                        <a href="quizzes.php" class="btn-action cancel"><i class="fas fa-times"></i> Annuler</a>
                    </div>
                </form>
            </div>
        </div>
            <?php if (!$is_before_start): ?>
                <div class="countdown-bar duration-countdown" id="countdown-message">
                    <div class="countdown-content">
                        <i class="fas fa-hourglass-half"></i>
                        <span><strong>Temps restant :</strong></span>
                        <div class="countdown-timer">
                            <span id="countdown-duration" class="countdown-text"></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>
    <?php include '../includes/footer.php'; ?>
    <script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';
    $(document).ready(function() {
        const startTime = new Date('<?php echo $quiz['start_datetime']; ?>').getTime();
        const durationMs = <?php echo $quiz['duration_hours'] * 3600000; ?>;
        const deadlineTime = startTime + durationMs;

        function formatTime(ms) {
            const hours = Math.floor(ms / 3600000);
            const minutes = Math.floor((ms % 3600000) / 60000);
            const seconds = Math.floor((ms % 60000) / 1000);
            return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }

        function loadPDF() {
            const pdfViewer = $('#pdf-viewer-<?php echo $quiz['id']; ?>');
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
                        canvas.style.margin = '0 auto';
                        canvas.style.display = 'block';

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

            $(document).on('keydown', (e) => {
                if (e.key === 'PrintScreen') {
                    navigator.clipboard.writeText('');
                    alert('Les captures d’écran via PrintScreen sont désactivées.');
                }
            });
        }

        function updateCountdown() {
            const currentTime = new Date().getTime();
            const quizContent = $('#quiz-content');
            const uploadForm = $('#upload-form');

            if (currentTime < startTime) {
                const timeLeft = startTime - currentTime;
                $('#countdown-start').text(formatTime(timeLeft));
                if (timeLeft <= 0) {
                    quizContent.html(`
                        <h3><i class="fas fa-file-pdf"></i> Quiz</h3>
                        <div class="pdf-viewer" data-pdf="../includes/serve_quiz_pdf.php?quiz_id=<?php echo $quiz['id']; ?>" id="pdf-viewer-<?php echo $quiz['id']; ?>">
                            <div class="pdf-controls-fixed">
                                <button class="zoom-in btn-action"><i class="fas fa-search-plus"></i> Zoom Avant</button>
                                <button class="zoom-out btn-action"><i class="fas fa-search-minus"></i> Zoom Arrière</button>
                                <button class="rotate btn-action"><i class="fas fa-redo"></i> Rotation</button>
                            </div>
                            <div class="pdf-canvas"></div>
                        </div>
                    `);
                    if (!uploadForm.length) {
                        $('main.dashboard').append(`
                            <div class="form-container" id="upload-form">
                                <h3><i class="fas fa-upload"></i> Soumettre une Réponse</h3>
                                <p class="info-message">Vous pouvez soumettre plusieurs fois. Chaque soumission est enregistrée séparément.</p>
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="response_pdf"><i class="fas fa-file-pdf"></i> Réponse PDF</label>
                                        <input type="file" name="response_pdf" id="response_pdf" accept="application/pdf" required>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" class="save-course-btn"><i class="fas fa-upload"></i> Soumettre</button>
                                        <a href="quizzes.php" class="btn-action cancel"><i class="fas fa-times"></i> Annuler</a>
                                    </div>
                                </form>
                            </div>
                        `);
                    }
                    loadPDF();
                }
            } else if (currentTime < deadlineTime) {
                const timeLeft = deadlineTime - currentTime;
                $('#countdown-duration').text(formatTime(timeLeft));
                $('#countdown-message').show();
                if (timeLeft <= 0) {
                    $('#countdown-message').hide();
                    quizContent.append('<p class="warning-message">La période de soumission recommandée est terminée. Les soumissions tardives seront marquées comme telles.</p>');
                }
            } else {
                $('#countdown-message').hide();
                if (!quizContent.find('.warning-message').length) {
                    quizContent.append('<p class="warning-message">La période de soumission recommandée est terminée. Les soumissions tardives seront marquées comme telles.</p>');
                }
            }
        }

        if (startTime <= new Date().getTime()) {
            loadPDF();
            if (deadlineTime <= new Date().getTime()) {
                $('#countdown-message').hide();
                if (!$('#quiz-content').find('.warning-message').length) {
                    $('#quiz-content').append('<p class="warning-message">La période de soumission recommandée est terminée. Les soumissions tardives seront marquées comme telles.</p>');
                }
            }
        }

        if (startTime > new Date().getTime() || (startTime <= new Date().getTime() && new Date().getTime() <= deadlineTime)) {
            setInterval(updateCountdown, 1000);
            updateCountdown();
        }
    });
    </script>
</body>
</html>