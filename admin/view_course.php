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

$contents_query = $db->query("SELECT content_type, content_path FROM course_contents WHERE course_id = $course_id");
$contents = [];
while ($row = $contents_query->fetch_assoc()) {
    if ($row['content_type'] === 'PDF') {
        $contents['PDF'] = "serve_pdf.php?file=" . urlencode(basename($row['content_path'])) . "&course_id=" . $course_id;
    } elseif ($row['content_type'] === 'Video') {
        $contents['Video'] = $row['content_path'];
        if (strpos($contents['Video'], 'youtube.com/watch?v=') !== false) {
            $video_id = explode('v=', $contents['Video'])[1];
            $contents['Video'] = "https://www.youtube.com/embed/" . $video_id;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voir Cours - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { user-select: none; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; }
        .pdf-controls { pointer-events: auto; margin-bottom: 10px; }
        .content-section { margin-bottom: 20px; }
        .pdf-viewer, .video-viewer { border: 1px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body oncontextmenu="return false;">
    <?php include '../includes/header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-book"></i> Détails du Cours</h1>
        <div class="detail-card">
            <h3><i class="fas fa-info"></i> Informations sur le Cours</h3>
            <p><strong>Titre :</strong> <?php echo htmlspecialchars($course['title']); ?></p>
            <p><strong>Matière :</strong> <?php echo htmlspecialchars($course['subject_name']); ?></p>
            <p><strong>Niveau :</strong> <?php echo htmlspecialchars($course['level_name']); ?></p>
            <p><strong>Difficulté :</strong> <?php echo $course['difficulty']; ?></p>
            <p><strong>Créé :</strong> <?php echo $course['created_at']; ?></p>
        </div>
        <div class="detail-card content-preview">
            <h3><i class="fas fa-eye"></i> Aperçu du Contenu</h3>
            <?php if (isset($contents['PDF'])): ?>
                <div class="content-section">
                    <h4><i class="fas fa-file-pdf"></i> Document PDF</h4>
                    <div class="pdf-controls">
                        <button id="zoomInBtn" class="btn-action"><i class="fas fa-search-plus"></i> Zoom Avant</button>
                        <button id="zoomOutBtn" class="btn-action"><i class="fas fa-search-minus"></i> Zoom Arrière</button>
                        <button id="rotateBtn" class="btn-action"><i class="fas fa-redo"></i> Rotation</button>
                        <button id="screenshotBtn" class="btn-action"><i class="fas fa-camera"></i> Capture</button>
                        <span id="screenshotInfo">Captures illimitées (Admin)</span>
                    </div>
                    <div class="pdf-viewer" id="pdfViewer" tabindex="0"></div>
                </div>
            <?php endif; ?>
            <?php if (isset($contents['Video'])): ?>
                <div class="content-section">
                    <h4><i class="fas fa-video"></i> Vidéo</h4>
                    <div class="video-viewer">
                        <iframe id="videoFrame" src="<?php echo htmlspecialchars($contents['Video']); ?>" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (empty($contents)): ?>
                <p>Aucun contenu disponible pour ce cours.</p>
            <?php endif; ?>
        </div>
        <a href="manage_courses.php" class="btn-action back"><i class="fas fa-arrow-left"></i> Retour aux Cours</a>
    </main>
    <?php include '../includes/footer.php'; ?>

    <?php if (isset($contents['PDF'])): ?>
    <script type="module">
        import * as pdfjsLib from '../assets/js/pdf.mjs';
        pdfjsLib.GlobalWorkerOptions.workerSrc = '../assets/js/pdf.worker.mjs';

        const url = '<?php echo $contents['PDF']; ?>';
        const viewer = document.getElementById('pdfViewer');
        let pdfDoc = null;
        let scale = 1.5;
        let rotation = 0;
        const courseTitle = '<?php echo addslashes(htmlspecialchars($course['title'])); ?>';

        function renderPages(pdf, scale, rotation) {
            viewer.innerHTML = '';
            const numPages = pdf.numPages;
            for (let pageNum = 1; pageNum <= numPages; pageNum++) {
                const canvas = document.createElement('canvas');
                canvas.className = 'pdf-page';
                canvas.dataset.pageNum = pageNum;
                viewer.appendChild(canvas);
                const ctx = canvas.getContext('2d');

                pdf.getPage(pageNum).then(page => {
                    const viewport = page.getViewport({ scale: scale, rotation: rotation });
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    canvas.style.width = '100%';
                    canvas.style.maxWidth = `${viewport.width}px`;

                    const renderContext = { canvasContext: ctx, viewport: viewport };
                    page.render(renderContext).promise.then(() => {
                        console.log(`Page ${pageNum} rendered`);
                    }).catch(err => console.error(`Render error page ${pageNum}:`, err));
                }).catch(err => console.error(`Page load error ${pageNum}:`, err));
            }
        }

        pdfjsLib.getDocument(url).promise.then(pdf => {
            console.log('PDF loaded, pages:', pdf.numPages);
            pdfDoc = pdf;
            renderPages(pdf, scale, rotation);
        }).catch(error => {
            console.error('PDF.js error:', error);
            viewer.innerHTML = '<p>Erreur de chargement du PDF : ' + error.message + '. Consultez la console.</p>';
        });

        document.getElementById('zoomInBtn').addEventListener('click', () => {
            if (pdfDoc) {
                scale += 0.25;
                renderPages(pdfDoc, scale, rotation);
            }
        });

        document.getElementById('zoomOutBtn').addEventListener('click', () => {
            if (pdfDoc && scale > 0.5) {
                scale -= 0.25;
                renderPages(pdfDoc, scale, rotation);
            }
        });

        document.getElementById('rotateBtn').addEventListener('click', () => {
            if (pdfDoc) {
                rotation = (rotation + 90) % 360;
                renderPages(pdfDoc, scale, rotation);
            }
        });

        window.addEventListener('blur', () => viewer.classList.add('blurred'));
        window.addEventListener('focus', () => viewer.classList.remove('blurred'));
        document.addEventListener('click', (e) => {
            if (!viewer.contains(e.target) && !document.querySelector('.pdf-controls').contains(e.target)) {
                viewer.classList.add('blurred');
            }
        });
        viewer.addEventListener('click', () => {
            viewer.classList.remove('blurred');
            viewer.focus();
        });

        document.getElementById('screenshotBtn').addEventListener('click', () => {
            html2canvas(viewer, { scale: window.devicePixelRatio, useCORS: true }).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const link = document.createElement('a');
                link.href = imgData;
                link.download = 'screenshot.png';
                link.click();

                // Log screenshot (no limit for admin)
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '../includes/log_screenshot.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                const userId = '<?php echo $_SESSION['admin_id']; ?>';
                const pageNum = getVisiblePageNum();
                const data = `user_id=${userId}&course_id=${<?php echo $course_id; ?>}&page_num=${pageNum}&course_title=${encodeURIComponent(courseTitle)}`;
                xhr.send(data);
            }).catch(err => console.error('Screenshot error:', err));
        });

        function getVisiblePageNum() {
            const pages = document.querySelectorAll('.pdf-page');
            const viewerRect = viewer.getBoundingClientRect();
            for (let page of pages) {
                const pageRect = page.getBoundingClientRect();
                if (pageRect.top >= viewerRect.top && pageRect.top < viewerRect.bottom) {
                    return page.dataset.pageNum;
                }
            }
            return 1;
        }

        viewer.focus();
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <?php endif; ?>
</body>
</html>