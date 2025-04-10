<?php
require_once '../includes/db_connect.php';
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: courses.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$course_id = (int)$_GET['id'];
$course = $db->query("SELECT c.*, s.name AS subject_name, l.name AS level_name FROM courses c JOIN subjects s ON c.subject_id = s.id JOIN levels l ON s.level_id = l.id WHERE c.id = $course_id")->fetch_assoc();
if (!$course) {
    header("Location: courses.php");
    exit;
}

// Check enrollment (assuming an enrollments table exists)
$enrolled = $db->query("SELECT * FROM enrollments WHERE student_id = $student_id AND course_id = $course_id")->num_rows > 0;
if (!$enrolled) {
    header("Location: courses.php");
    exit;
}

$pdf_url = "../admin/serve_pdf.php?file=" . urlencode(basename($course['content_path'])) . "&course_id=" . $course_id;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
 @media name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Course - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css"> <!-- Reuse admin.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { user-select: none; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; }
        .pdf-controls { pointer-events: auto; }
    </style>
</head>
<body oncontextmenu="return false;">
    <?php include '../includes/student_header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-book"></i> Course Details</h1>
        <div class="detail-card">
            <h3><i class="fas fa-info"></i> Course Information</h3>
            <p><strong>Title:</strong> <?php echo htmlspecialchars($course['title']); ?></p>
            <p><strong>Subject:</strong> <?php echo htmlspecialchars($course['subject_name']); ?></p>
            <p><strong>Level:</strong> <?php echo htmlspecialchars($course['level_name']); ?></p>
            <p><strong>Difficulty:</strong> <?php echo $course['difficulty']; ?></p>
            <p><strong>Type:</strong> <?php echo $course['content_type']; ?></p>
            <p><strong>Created:</strong> <?php echo $course['created_at']; ?></p>
        </div>
        <div class="detail-card content-preview">
            <h3><i class="fas fa-eye"></i> Content Preview</h3>
            <?php if ($course['content_type'] === 'PDF'): ?>
                <div class="pdf-controls">
                    <button id="zoomInBtn" class="btn-action"><i class="fas fa-search-plus"></i> Zoom In</button>
                    <button id="zoomOutBtn" class="btn-action"><i class="fas fa-search-minus"></i> Zoom Out</button>
                    <button id="rotateBtn" class="btn-action"><i class="fas fa-redo"></i> Rotate</button>
                    <button id="screenshotBtn" class="btn-action"><i class="fas fa-camera"></i> Screenshot</button>
                    <span id="screenshotInfo">3 shots left</span>
                </div>
                <div class="pdf-viewer" id="pdfViewer" tabindex="0"></div>
            <?php elseif ($course['content_type'] === 'Video'): ?>
                <div class="video-viewer">
                    <?php
                    $video_url = $course['content_path'];
                    if (strpos($video_url, 'youtube.com/watch?v=') !== false) {
                        $video_id = explode('v=', $video_url)[1];
                        $video_url = "https://www.youtube.com/embed/" . $video_id;
                    }
                    ?>
                    <iframe id="videoFrame" src="<?php echo htmlspecialchars($video_url); ?>" frameborder="0" allowfullscreen></iframe>
                </div>
            <?php endif; ?>
        </div>
        <a href="courses.php" class="btn-action back"><i class="fas fa-arrow-left"></i> Back to Courses</a>
    </main>
    <?php include '../includes/footer.php'; ?>

    <?php if ($course['content_type'] === 'PDF'): ?>
    <script type="module">
        import * as pdfjsLib from '../assets/js/pdf.mjs';

        pdfjsLib.GlobalWorkerOptions.workerSrc = '../assets/js/pdf.worker.mjs';

        const url = '<?php echo $pdf_url; ?>';
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

                    const renderContext = {
                        canvasContext: ctx,
                        viewport: viewport
                    };
                    page.render(renderContext).promise.then(() => {
                        console.log(`Page ${pageNum} rendered`);
                    });
                });
            }
        }

        pdfjsLib.getDocument(url).promise.then(pdf => {
            pdfDoc = pdf;
            renderPages(pdf, scale, rotation);
        }).catch(error => {
            viewer.innerHTML = '<p>Error loading PDF: ' + error.message + '</p>';
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

        let screenshotCount = parseInt(localStorage.getItem('screenshotCount') || '3');
        let lastReset = parseInt(localStorage.getItem('lastReset') || '0');
        const fifteenMins = 15 * 60 * 1000;

        function updateScreenshotInfo() {
            document.getElementById('screenshotInfo').textContent = `${screenshotCount} shots left`;
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

        document.getElementById('screenshotBtn').addEventListener('click', () => {
            resetCount();
            if (screenshotCount > 0) {
                html2canvas(viewer, { scale: window.devicePixelRatio, useCORS: true }).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    const link = document.createElement('a');
                    link.href = imgData;
                    link.download = 'screenshot.png';
                    link.click();

                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '../includes/log_screenshot.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    const userId = '<?php echo $student_id; ?>';
                    const pageNum = getVisiblePageNum();
                    const data = `user_id=${userId}&course_id=${<?php echo $course_id; ?>}&page_num=${pageNum}&course_title=${encodeURIComponent(courseTitle)}`;
                    xhr.send(data);

                    screenshotCount--;
                    localStorage.setItem('screenshotCount', screenshotCount);
                    updateScreenshotInfo();
                });
            } else {
                alert('Screenshot limit reached! Wait 15 minutes.');
            }
        });

        viewer.focus();
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <?php endif; ?>
</body>
</html>