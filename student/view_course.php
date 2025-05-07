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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body oncontextmenu="return false;">
    <?php include '../includes/student_header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-book"></i> Détails du Cours</h1>
        <div class="course-info">
            <h3 class="section-title"><i class="fas fa-info"></i> Informations sur le Cours</h3>
            <p><strong>Titre :</strong> <?php echo htmlspecialchars($course['title']); ?></p>
            <p><strong>Matière :</strong> <?php echo htmlspecialchars($course['subject_name']); ?></p>
            <p><strong>Niveau :</strong> <?php echo htmlspecialchars($course['level_name']); ?></p>
            <p><strong>Difficulté :</strong> <?php echo $course['difficulty']; ?></p>
            <p><strong>Créé :</strong> <?php echo $course['created_at']; ?></p>
        </div>
        <div class="subfolder-section">
            <h3 class="section-title"><i class="fas fa-folder"></i> Dossiers du Cours</h3>
            <?php if ($folders->num_rows > 0): ?>
                <div class="folder-container">
                    <?php while ($folder = $folders->fetch_assoc()): ?>
                        <div class="folder-view-card" data-folder-id="<?php echo $folder['id']; ?>">
                            <h4><i class="fas fa-folder"></i> <?php echo htmlspecialchars($folder['name']); ?> (<?php echo $folder['content_count']; ?> fichiers)</h4>
                            <?php if ($folder['description']): ?>
                                <p><?php echo htmlspecialchars($folder['description']); ?></p>
                            <?php endif; ?>
                            <div class="subfolder-container" id="subfolder-<?php echo $folder['id']; ?>">
                                <?php
                                $contents = $db->query("SELECT id, content_type, content_name, content_path FROM course_contents WHERE folder_id = {$folder['id']}");
                                if ($contents->num_rows > 0):
                                    while ($content = $contents->fetch_assoc()):
                                ?>
                                        <div class="subfolder-card" data-content-id="<?php echo $content['id']; ?>">
                                            <h5><i class="fas fa-<?php echo $content['content_type'] == 'PDF' ? 'file-pdf' : 'video'; ?>"></i> <?php echo htmlspecialchars($content['content_name']); ?></h5>
                                        </div>
                                <?php
                                    endwhile;
                                else:
                                ?>
                                    <p class="empty-message">Aucun contenu dans ce dossier.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                <!-- Content View Container for All Files -->
                <div class="content-view-container">
                    <?php
                    $folders->data_seek(0);
                    while ($folder = $folders->fetch_assoc()):
                        $contents = $db->query("SELECT id, content_type, content_name, content_path FROM course_contents WHERE folder_id = {$folder['id']}");
                        while ($content = $contents->fetch_assoc()):
                    ?>
                            <div class="content-view" id="content-<?php echo $content['id']; ?>" style="display: none;">
                                <div class="content-item">
                                    <?php if ($content['content_type'] == 'PDF'): ?>
                                        <div class="pdf-viewer" data-pdf="serve_pdf.php?file=<?php echo urlencode(basename($content['content_path'])); ?>&course_id=<?php echo $course_id; ?>&content_id=<?php echo $content['id']; ?>&student_id=<?php echo $student_id; ?>" id="pdf-<?php echo $content['id']; ?>" tabindex="0">
                                            <div class="pdf-controls-fixed">
                                                <button class="zoom-in btn-action"><i class="fas fa-search-plus"></i> Zoom Avant</button>
                                                <button class="zoom-out btn-action"><i class="fas fa-search-minus"></i> Zoom Arrière</button>
                                                <button class="rotate btn-action"><i class="fas fa-redo"></i> Rotation</button>
                                                <button class="screenshot-btn btn-action" data-content-id="<?php echo $content['id']; ?>"><i class="fas fa-camera"></i> Capture</button>
                                                <span class="screenshot-info"></span>
                                            </div>
                                            <div class="pdf-canvas"></div>
                                        </div>
                                    <?php else: ?>
                                        <div class="video-viewer">
                                            <iframe src="<?php echo htmlspecialchars($content['content_path']); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                            <p class="video-error" style="display: none; color: #e53e3e;">Erreur de chargement de la vidéo. Vérifiez l'URL ou les restrictions YouTube.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                    <?php
                        endwhile;
                    endwhile;
                    ?>
                </div>
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

        // Initialize screenshot limits
        let screenshotCount = parseInt(localStorage.getItem('screenshotCount') || '3');
        let lastReset = parseInt(localStorage.getItem('lastReset') || '0');
        const fifteenMins = 15 * 60 * 1000;

        function updateScreenshotInfo(viewer) {
            viewer.find('.screenshot-info').text(`${screenshotCount} captures restantes`);
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

        // Improved folder toggle functionality - fixes hosting issues
        $(document).ready(function() {
            // First, make sure all subfolders are hidden initially
            $('.subfolder-container').hide();
            
            // Toggle subfolder visibility on folder click
            $('.folder-view-card').on('click', function(e) {
                e.stopPropagation();
                const folderId = $(this).data('folder-id');
                const subfolderSection = $(`#subfolder-${folderId}`);
                const parentCard = $(this);
    
                console.log(`Clicked on folder ID: ${folderId}`);
    
                // Remove active class from all folder cards
                $('.folder-view-card').removeClass('active-folder');
    
                // Hide all other subfolder containers
                $('.subfolder-container').not(subfolderSection).each(function() {
                    $(this).hide();
                    const parent = $(this).parent('.folder-view-card');
                    parent.css({
                        'height': '80px',
                        'min-height': '0'
                    });
                });
    
                // Toggle the clicked folder's subfolder container
                if (subfolderSection.is(':visible')) {
                    subfolderSection.hide();
                    parentCard.removeClass('active-folder');
                    parentCard.css({
                        'height': '80px',
                        'min-height': '0'
                    });
                    console.log(`Collapsed folder ${parentCard.find('h4').text()}`);
                } else {
                    subfolderSection.show();
                    parentCard.addClass('active-folder');
                    parentCard.css({
                        'height': 'auto',
                        'min-height': '80px'
                    });
                    console.log(`Expanded folder ${parentCard.find('h4').text()}`);
                }
            });
    
            // Toggle content visibility on subfolder click
            $('.subfolder-card').on('click', function(e) {
                e.stopPropagation();
                const contentId = $(this).data('content-id');
                const contentSection = $(`#content-${contentId}`);
                
                console.log(`Clicked on content ID: ${contentId}`);
    
                // Hide all other content views
                $('.content-view').not(contentSection).hide();
                
                // Toggle visibility of clicked content
                if (contentSection.is(':visible')) {
                    contentSection.hide();
                } else {
                    contentSection.show();
                    loadPdfIfNeeded(contentSection);
                }
            });
        });

        // Function to load PDF if needed
        function loadPdfIfNeeded(contentSection) {
            const pdfViewer = contentSection.find('.pdf-viewer');
            if (pdfViewer.length && pdfViewer.find('.pdf-canvas').children().length === 0) {
                const url = pdfViewer.data('pdf');
                const canvasContainer = pdfViewer.find('.pdf-canvas');
                const contentId = pdfViewer.attr('id').replace('pdf-', '');
                const courseTitle = '<?php echo addslashes(htmlspecialchars($course['title'])); ?>';
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

                            const renderContext = { canvasContext: ctx, viewport: viewport };
                            page.render(renderContext);
                        });
                    }
                }

                pdfjsLib.getDocument(url).promise.then(pdf => {
                    pdfDoc = pdf;
                    renderPages(pdf, scale, rotation);
                    updateScreenshotInfo(pdfViewer);
                }).catch(error => {
                    canvasContainer.html('<p class="error-message">Erreur de chargement du PDF : ' + error.message + '</p>');
                    console.error('PDF load error:', error);
                });

                pdfViewer.find('.zoom-in').on('click', function() {
                    if (pdfDoc) {
                        scale += 0.25;
                        renderPages(pdfDoc, scale, rotation);
                    }
                });

                pdfViewer.find('.zoom-out').on('click', function() {
                    if (pdfDoc && scale > 0.5) {
                        scale -= 0.25;
                        renderPages(pdfDoc, scale, rotation);
                    }
                });

                pdfViewer.find('.rotate').on('click', function() {
                    if (pdfDoc) {
                        rotation = (rotation + 90) % 360;
                        renderPages(pdfDoc, scale, rotation);
                    }
                });

                pdfViewer.find('.screenshot-btn').on('click', function() {
                    resetCount();
                    if (screenshotCount <= 0) {
                        alert('Limite de captures atteinte. Veuillez attendre 15 minutes.');
                        return;
                    }

                    if (pdfDoc) {
                        const visiblePage = getVisiblePageNum(pdfViewer[0]);
                        const canvas = pdfViewer.find(`.pdf-page[data-page-num="${visiblePage}"]`)[0];
                        if (canvas) {
                            html2canvas(canvas, { scale: window.devicePixelRatio, useCORS: true }).then(imgCanvas => {
                                const imgData = imgCanvas.toDataURL('image/png');
                                const link = document.createElement('a');
                                link.href = imgData;
                                link.download = `screenshot_page_${visiblePage}.png`;
                                link.click();

                                screenshotCount--;
                                localStorage.setItem('screenshotCount', screenshotCount);
                                updateScreenshotInfo(pdfViewer);

                                $.post('../includes/log_screenshot.php', {
                                    user_id: '<?php echo $student_id; ?>',
                                    course_id: '<?php echo $course_id; ?>',
                                    page_num: visiblePage,
                                    course_title: courseTitle
                                }, () => {
                                    console.log('Screenshot logged:', visiblePage);
                                });
                            }).catch(error => {
                                console.error('Screenshot error:', error);
                                alert('Erreur lors de la capture.');
                            });
                        }
                    }
                });

                function toggleBlur(viewer, enable) {
                    const canvases = viewer.find('.pdf-page');
                    canvases.each(function() {
                        $(this).css('filter', enable ? 'blur(5px)' : 'none');
                    });
                }

                window.addEventListener('blur', () => {
                    toggleBlur(pdfViewer, true);
                });

                window.addEventListener('focus', () => {
                    toggleBlur(pdfViewer, false);
                    pdfViewer.focus();
                });

                pdfViewer.on('click', function() {
                    toggleBlur(pdfViewer, false);
                    pdfViewer.focus();
                });
            }

            contentSection.find('.video-viewer iframe').each(function() {
                const iframe = $(this);
                const errorMsg = iframe.siblings('.video-error');
                iframe.on('error', function() {
                    errorMsg.show();
                });
                $.ajax({
                    url: iframe.attr('src'),
                    type: 'HEAD',
                    success: function() {
                        errorMsg.hide();
                    },
                    error: function() {
                        errorMsg.show();
                    }
                });
            });
        }

        // Prevent PrintScreen
        $(document).on('keydown', function(e) {
            if (e.key === 'PrintScreen') {
                navigator.clipboard.writeText('');
                alert('Les captures d'écran via PrintScreen sont désactivées.');
            }
        });

        function getVisiblePageNum(viewer) {
            const pages = viewer.querySelectorAll('.pdf-page');
            const viewerRect = viewer.getBoundingClientRect();
            for (let page of pages) {
                const pageRect = page.getBoundingClientRect();
                if (pageRect.top >= viewerRect.top && pageRect.top < viewerRect.bottom) {
                    return page.dataset.pageNum;
                }
            }
            return 1;
        }

        // Activity tracking for session
        $(document).on('mousemove keydown', function() {
            $.ajax({
                url: 'update_activity.php',
                method: 'POST',
                data: { student_id: <?php echo isset($_SESSION['student_id']) ? (int)$_SESSION['student_id'] : 0; ?> },
                error: function() {
                    console.error('Error updating activity');
                }
            });
        });
    </script>
</body>
</html>