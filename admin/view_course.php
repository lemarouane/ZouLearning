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
$course = $db->query("
    SELECT c.*, s.name AS subject_name, l.name AS level_name 
    FROM courses c 
    JOIN subjects s ON c.subject_id = s.id 
    JOIN levels l ON s.level_id = l.id 
    WHERE c.id = $course_id
")->fetch_assoc();
if (!$course) {
    header("Location: manage_courses.php");
    exit;
}

$folders = $db->query("
    SELECT cf.id, cf.name, cf.description, 
           (SELECT COUNT(*) FROM course_contents cc WHERE cc.folder_id = cf.id) AS content_count
    FROM course_folders cf 
    WHERE cf.course_id = $course_id
");
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
    <?php include '../includes/header.php'; ?>
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
                                        <div class="pdf-viewer" data-pdf="serve_pdf.php?file=<?php echo urlencode(basename($content['content_path'])); ?>&course_id=<?php echo $course_id; ?>" id="pdf-<?php echo $content['id']; ?>" tabindex="0">
                                            <div class="pdf-controls-fixed">
                                                <button class="zoom-in btn-action"><i class="fas fa-search-plus"></i> Zoom Avant</button>
                                                <button class="zoom-out btn-action"><i class="fas fa-search-minus"></i> Zoom Arrière</button>
                                                <button class="rotate btn-action"><i class="fas fa-redo"></i> Rotation</button>
                                                <button class="screenshot-btn" data-content-id="<?php echo $content['id']; ?>"><i class="fas fa-camera"></i> Capture</button>
                                                <span class="screenshot-info">Captures illimitées (Admin)</span>
                                            </div>
                                            <div class="pdf-canvas"></div>
                                        </div>
                                    <?php else: ?>
                                        <div class="video-viewer">
                                            <iframe src="<?php echo htmlspecialchars($content['content_path']); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                            <p class="video-error" style="display: none; color: #e74c3c;">Erreur de chargement de la vidéo. Vérifiez l'URL ou les restrictions YouTube.</p>
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
        <a href="manage_courses.php" class="back-btn"><i class="fas fa-arrow-left"></i> Retour aux Cours</a>
    </main>
    <?php include '../includes/footer.php'; ?>

    <script type="module">
        import * as pdfjsLib from '../assets/js/pdf.mjs';
        pdfjsLib.GlobalWorkerOptions.workerSrc = '../assets/js/pdf.worker.mjs';

        $('.folder-view-card').click(function() {
            const folderId = $(this).data('folder-id');
            const contentSection = $(`#content-${folderId}`);
            contentSection.slideToggle(300);

            if (contentSection.is(':visible')) {
                contentSection.find('.pdf-viewer').each(function() {
                    const pdfViewer = $(this);
                    if (pdfViewer.find('.pdf-canvas').children().length > 0) return;

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

                    pdfViewer.find('.screenshot-btn').click(() => {
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

                                    // Log screenshot
                                    $.post('../includes/log_screenshot.php', {
                                        user_id: '<?php echo $_SESSION['admin_id']; ?>',
                                        course_id: '<?php echo $course_id; ?>',
                                        page_num: visiblePage,
                                        course_title: courseTitle
                                    });
                                });
                            }
                        }
                    });

                    // Blur handling
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

                    pdfViewer.click(function() {
                        toggleBlur(pdfViewer, false);
                        pdfViewer.focus();
                    });
                });

                // Check video loading
                contentSection.find('.video-viewer iframe').each(function() {
                    const iframe = $(this);
                    const errorMsg = iframe.siblings('.video-error');
                    iframe.on('error', function() {
                        errorMsg.show();
                    });
                    // Test load
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
    </script>
</body>
</html>