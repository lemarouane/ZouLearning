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
    <link rel="icon" type="image/png" href="../assets/img/logo.png">

    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <style>
        .dashboard {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1.5rem;
            font-family: 'Inter', sans-serif;
            background: #f5f8fc;
            color: #2d3748;
        }
        h1 {
            font-size: 1.5rem;
            color: #1e3c72;
            margin-bottom: 1.25rem;
        }
        .detail-card, .folder-view-card, .content-item {
            background: #fff;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .pdf-viewer {
            position: relative;
            margin: 1rem 0;
        }
        .pdf-canvas {
            max-height: 80vh;
            overflow-y: auto;
            scroll-behavior: smooth;
        }
        .pdf-page {
            margin-bottom: 10px;
        }
        .pdf-controls-fixed {
            margin-bottom: 0.5rem;
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .btn-action {
            background: #1e3c72;
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
        }
        .btn-action:hover {
            background: #152a55;
        }
        .screenshot-info {
            font-size: 0.9rem;
            color: #e53e3e;
        }
        .blurred {
            filter: blur(5px);
            pointer-events: none;
        }
        .empty-message {
            color: #666;
            text-align: center;
            margin: 2rem 0;
        }
        .back-btn {
            display: inline-block;
            background: #1e3c72;
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            text-decoration: none;
            margin-top: 1rem;
        }
        .back-btn:hover {
            background: #152a55;
        }
    </style>
</head>
<body oncontextmenu="return false;">
    <?php include '../includes/student_header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-book"></i> Détails du Cours</h1>
        <div class="detail-card">
            <h3><i class="fas fa-info-circle"></i> Informations sur le Cours</h3>
            <p><strong>Titre :</strong> <?php echo htmlspecialchars($course['title']); ?></p>
            <p><strong>Matière :</strong> <?php echo htmlspecialchars($course['subject_name']); ?></p>
            <p><strong>Niveau :</strong> <?php echo htmlspecialchars($course['level_name']); ?></p>
            <p><strong>Difficulté :</strong> <?php echo $course['difficulty']; ?></p>
            <p><strong>Créé :</strong> <?php echo $course['created_at']; ?></p>
        </div>
        <div class="content-preview">
            <h3><i class="fas fa-folder"></i> Dossiers du Cours</h3>
            <?php if ($folders->num_rows > 0): ?>
                <?php while ($folder = $folders->fetch_assoc()): ?>
                    <div class="folder-view-card" data-folder-id="<?php echo $folder['id']; ?>">
                        <h4><i class="fas fa-folder"></i> <?php echo htmlspecialchars($folder['name']); ?> (<?php echo $folder['content_count']; ?> fichiers)</h4>
                        <?php if ($folder['description']): ?>
                            <p><?php echo htmlspecialchars($folder['description']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="content-view" id="content-<?php echo $folder['id']; ?>" style="display: none;">
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
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';

        $(document).ready(function() {
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
                            const containerRect = canvasContainer[0].getBoundingClientRect();
                            let maxVisibleArea = 0;
                            let visiblePageNum = 1;

                            pages.each(function() {
                                const page = $(this);
                                const pageRect = this.getBoundingClientRect();
                                const visibleHeight = Math.min(pageRect.bottom, containerRect.bottom) - Math.max(pageRect.top, containerRect.top);
                                const visibleArea = visibleHeight > 0 ? visibleHeight * pageRect.width : 0;

                                if (visibleArea > maxVisibleArea) {
                                    maxVisibleArea = visibleArea;
                                    visiblePageNum = parseInt(page.data('pageNum'));
                                }
                            });

                            return visiblePageNum;
                        }

                        pdfViewer.find('.screenshot').click(() => {
                            resetCount();
                            if (screenshotCount <= 0) {
                                alert('Limite de captures atteinte. Veuillez attendre 15 minutes.');
                                return;
                            }

                            const pageNum = getVisiblePageNum();
                            const visibleCanvas = pdfViewer.find(`.pdf-page[data-page-num="${pageNum}"]`)[0];
                            if (!visibleCanvas) {
                                alert('Erreur : Page visible non trouvée.');
                                return;
                            }

                            import('https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js').then(() => {
                                html2canvas(visibleCanvas, { 
                                    scale: window.devicePixelRatio, 
                                    useCORS: true,
                                    width: visibleCanvas.width,
                                    height: visibleCanvas.height
                                }).then(canvas => {
                                    const imgData = canvas.toDataURL('image/png');
                                    const link = document.createElement('a');
                                    link.href = imgData;
                                    link.download = `screenshot_page_${pageNum}.png`;
                                    link.click();

                                    screenshotCount--;
                                    localStorage.setItem('screenshotCount', screenshotCount);
                                    updateScreenshotInfo();

                                    const xhr = new XMLHttpRequest();
                                    xhr.open('POST', '../includes/log_screenshot.php', true);
                                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                                    const userId = '<?php echo $student_id; ?>';
                                    const courseTitle = '<?php echo addslashes($course['title']); ?>';
                                    const data = `user_id=${userId}&course_id=<?php echo $course_id; ?>&page=${pageNum}&course_title=${encodeURIComponent(courseTitle)}`;
                                    xhr.send(data);
                                }).catch(error => {
                                    console.error('Screenshot error:', error);
                                    alert('Erreur lors de la capture : ' + error.message);
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