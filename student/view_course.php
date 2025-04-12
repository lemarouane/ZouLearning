<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$course_id = (int)$_GET['id'];
$student_id = (int)$_SESSION['student_id'];

// Verify access
$course = $db->query("
    SELECT c.*, s.name AS subject_name, l.name AS level_name
    FROM (
        SELECT sc.course_id, c.title, c.subject_id, c.difficulty, c.created_at
        FROM student_courses sc
        JOIN courses c ON sc.course_id = c.id
        WHERE sc.student_id = $student_id
        UNION
        SELECT c.id AS course_id, c.title, c.subject_id, c.difficulty, c.created_at
        FROM student_subjects ss
        JOIN courses c ON ss.subject_id = c.subject_id
        WHERE ss.student_id = $student_id AND ss.all_courses = 1
    ) AS unique_courses
    JOIN courses c ON c.id = unique_courses.course_id
    JOIN subjects s ON c.subject_id = s.id
    JOIN levels l ON s.level_id = l.id
    WHERE c.id = $course_id
")->fetch_assoc();

if (!$course) {
    header("Location: dashboard.php");
    exit;
}

$folders = $db->query("
    SELECT cf.id, cf.name, cf.description, 
           (SELECT COUNT(*) FROM course_contents cc WHERE cc.folder_id = cf.id) AS content_count
    FROM course_folders cf 
    WHERE cf.course_id = $course_id
");

// Log view
$stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (?, 'student', 'Viewed course', ?)");
$details = "Viewed course ID $course_id: {$course['title']}";
$stmt->bind_param("is", $student_id, $details);
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voir Cours - Zouhair E-Learning</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body oncontextmenu="return false;">
    <?php include '../includes/student_header.php'; ?>
    <main class="container mt-4">
        <h1 class="dashboard mb-4"><i class="fas fa-book"></i> Détails du Cours</h1>
        <div class="detail-card shadow-sm">
            <h3><i class="fas fa-info-circle"></i> Informations sur le Cours</h3>
            <p><strong>Titre :</strong> <?php echo htmlspecialchars($course['title']); ?></p>
            <p><strong>Matière :</strong> <?php echo htmlspecialchars($course['subject_name']); ?></p>
            <p><strong>Niveau :</strong> <?php echo htmlspecialchars($course['level_name']); ?></p>
            <p><strong>Difficulté :</strong> <?php echo $course['difficulty']; ?></p>
            <p><strong>Créé :</strong> <?php echo $course['created_at']; ?></p>
        </div>
        <div class="content-preview detail-card shadow-sm">
            <h3><i class="fas fa-folder"></i> Dossiers du Cours</h3>
            <?php if ($folders->num_rows > 0): ?>
                <?php while ($folder = $folders->fetch_assoc()): ?>
                    <div class="folder-view-card" data-folder-id="<?php echo $folder['id']; ?>">
                        <h4><i class="fas fa-folder"></i> <?php echo htmlspecialchars($folder['name']); ?> (<?php echo $folder['content_count']; ?> fichiers)</h4>
                        <?php if ($folder['description']): ?>
                            <p><?php echo htmlspecialchars($folder['description']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="content-view" id="content-<?php echo $folder['id']; ?>">
                        <?php
                        $contents = $db->query("SELECT id, content_type, content_name, content_path FROM course_contents WHERE folder_id = {$folder['id']}");
                        if ($contents->num_rows > 0):
                        ?>
                            <?php while ($content = $contents->fetch_assoc()): ?>
                                <div class="content-item">
                                    <h5><i class="fas fa-<?php echo $content['content_type'] == 'PDF' ? 'file-pdf' : 'video'; ?>"></i> <?php echo htmlspecialchars($content['content_name']); ?></h5>
                                    <?php if ($content['content_type'] == 'PDF'): ?>
                                        <div class="pdf-viewer" data-pdf="serve_pdf.php?file=<?php echo urlencode(basename($content['content_path'])); ?>&course_id=<?php echo $course_id; ?>&content_id=<?php echo $content['id']; ?>" id="pdf-<?php echo $content['id']; ?>">
                                            <div class="pdf-controls-fixed">
                                                <button class="zoom-in btn-action"><i class="fas fa-search-plus"></i> Zoom Avant</button>
                                                <button class="zoom-out btn-action"><i class="fas fa-search-minus"></i> Zoom Arrière</button>
                                                <button class="rotate btn-action"><i class="fas fa-redo"></i> Rotation</button>
                                                <button class="screenshot btn-action"><i class="fas fa-camera"></i> Capture</button>
                                                <span class="screenshot-info"></span>
                                            </div>
                                            <div class="pdf-canvas"></div>
                                        </div>
                                    <?php else: ?>
                                        <div class="video-viewer">
                                            <iframe src="<?php echo strpos($content['content_path'], 'youtube.com') !== false ? str_replace('watch?v=', 'embed/', $content['content_path']) : $content['content_path']; ?>" frameborder="0" allowfullscreen></iframe>
                                            <div class="video-error"></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="empty-message">Aucun contenu dans ce dossier.</p>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="empty-message">Aucun dossier créé pour ce cours.</p>
            <?php endif; ?>
        </div>
        <a href="dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Retour au Tableau de Bord</a>
    </main>
    <?php include '../includes/footer.php'; ?>

    <script type="module">
        import * as pdfjsLib from '../assets/js/pdf.mjs';
        pdfjsLib.GlobalWorkerOptions.workerSrc = '../assets/js/pdf.worker.mjs';

        // Folder toggle
        $('.folder-view-card').on('click', function() {
            const folderId = $(this).data('folder-id');
            const contentSection = $(`#content-${folderId}`);
            contentSection.slideToggle(300);

            if (contentSection.is(':visible')) {
                contentSection.find('.pdf-viewer').each(function() {
                    const pdfViewer = $(this);
                    if (pdfViewer.find('.pdf-canvas').children().length > 0) return;

                    const url = pdfViewer.data('pdf');
                    const canvasContainer = pdfViewer.find('.pdf-canvas');
                    let pdfDoc = null;
                    let scale = 1.5;
                    let rotation = 0;

                    function renderPages(pdf, scale, rotation) {
                        canvasContainer.empty();
                        const numPages = pdf.numPages;
                        for (let pageNum = 1; pageNum <= numPages; pageNum++) {
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

                    let screenshotCount = parseInt(localStorage.getItem('screenshotCount') || '3');
                    let lastReset = parseInt(localStorage.getItem('lastReset') || '0');
                    const fifteenMins = 15 * 60 * 1000;

                    function updateScreenshotInfo() {
                        pdfViewer.find('.screenshot-info').text(`${screenshotCount} captures restantes`);
                    }

                    function resetCount() {
                        const now = Date.now();
                        if (now - lastReset >= fifteenMins) {
                            screenshotCount = 3;
                            lastReset = now;
                            localStorage.setItem('screenshotCount', screenshotCount);
                            localStorage.setItem('lastReset', lastReset);
                        }
                    }

                    updateScreenshotInfo();
                    resetCount();

                    function getVisiblePageNum() {
                        const pages = pdfViewer.find('.pdf-page');
                        const viewerRect = canvasContainer[0].getBoundingClientRect();
                        for (let page of pages) {
                            const pageRect = page.getBoundingClientRect();
                            if (pageRect.top >= viewerRect.top && pageRect.top < viewerRect.bottom) {
                                return page.dataset.pageNum;
                            }
                        }
                        return 1;
                    }

                    pdfViewer.find('.screenshot').click(() => {
                        resetCount();
                        if (screenshotCount <= 0) {
                            alert('Limite de captures atteinte. Veuillez attendre 15 minutes.');
                            return;
                        }

                        import('https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js').then(() => {
                            html2canvas(canvasContainer[0], { scale: window.devicePixelRatio, useCORS: true }).then(canvas => {
                                const imgData = canvas.toDataURL('image/png');
                                const link = document.createElement('a');
                                link.href = imgData;
                                link.download = 'screenshot.png';
                                link.click();

                                screenshotCount--;
                                localStorage.setItem('screenshotCount', screenshotCount);
                                updateScreenshotInfo();

                                const xhr = new XMLHttpRequest();
                                xhr.open('POST', '../includes/log_screenshot.php', true);
                                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                                const userId = '<?php echo $student_id; ?>';
                                const pageNum = getVisiblePageNum();
                                const courseTitle = '<?php echo addslashes($course['title']); ?>';
                                const data = `user_id=${userId}&course_id=<?php echo $course_id; ?>&page=${pageNum}&course_title=${encodeURIComponent(courseTitle)}`;
                                xhr.send(data);
                            });
                        });
                    });
                });
            }
        });

        // Blur on window focus loss
        let isFocused = true;
        function applyBlur() {
            $('.pdf-viewer').each(function() {
                if (!isFocused) {
                    $(this).addClass('blurred');
                } else {
                    $(this).removeClass('blurred');
                }
            });
        }

        $(window).on('focus', () => {
            isFocused = true;
            applyBlur();
        });

        $(window).on('blur', () => {
            isFocused = false;
            applyBlur();
        });

        // Prevent screenshot via PrintScreen (basic deterrence)
        $(document).on('keydown', (e) => {
            if (e.key === 'PrintScreen') {
                navigator.clipboard.writeText('');
                alert('Les captures d’écran via PrintScreen sont désactivées.');
            }
        });
    </script>
</body>
</html>