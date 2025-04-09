<?php
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
$course = $db->query("SELECT c.*, s.name AS subject_name, l.name AS level_name FROM courses c JOIN subjects s ON c.subject_id = s.id JOIN levels l ON s.level_id = l.id WHERE c.id = $course_id")->fetch_assoc();
if (!$course) {
    header("Location: manage_courses.php");
    exit;
}

$pdf_url = "serve_pdf.php?file=" . urlencode(basename($course['content_path'])) . "&course_id=" . $course_id;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Course - Zouhair E-Learning</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { user-select: none; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; }
        .pdf-viewer { pointer-events: none; }
        .pdf-controls { pointer-events: auto; }
    </style>
</head>
<body oncontextmenu="return false;">
    <?php include '../includes/header.php'; ?>
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
                <div class="pdf-viewer" id="pdfViewer"></div>
                <div class="pdf-controls">
                    <button id="screenshotBtn" class="btn-action"><i class="fas fa-camera"></i> Screenshot</button>
                    <span id="screenshotInfo">3 shots left</span>
                </div>
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
        <a href="manage_courses.php" class="btn-action back"><i class="fas fa-arrow-left"></i> Back to Courses</a>
    </main>
    <?php include '../includes/footer.php'; ?>

    <?php if ($course['content_type'] === 'PDF'): ?>
    <script type="module">
        import * as pdfjsLib from '../assets/js/pdf.mjs';

        pdfjsLib.GlobalWorkerOptions.workerSrc = '../assets/js/pdf.worker.mjs';

        const url = '<?php echo $pdf_url; ?>';
        const viewer = document.getElementById('pdfViewer');

        pdfjsLib.getDocument(url).promise.then(pdf => {
            console.log('PDF loaded, pages:', pdf.numPages);
            const numPages = pdf.numPages;
            for (let pageNum = 1; pageNum <= numPages; pageNum++) {
                const canvas = document.createElement('canvas');
                canvas.className = 'pdf-page';
                viewer.appendChild(canvas);
                const ctx = canvas.getContext('2d');

                pdf.getPage(pageNum).then(page => {
                    const viewport = page.getViewport({ scale: 1 });
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    const renderContext = {
                        canvasContext: ctx,
                        viewport: viewport
                    };
                    page.render(renderContext).promise.then(() => {
                        console.log(`Page ${pageNum} rendered`);
                    }).catch(err => console.error(`Render error page ${pageNum}:`, err));
                });
            }
        }).catch(error => {
            console.error('PDF.js error:', error);
            viewer.innerHTML = '<p>Error loading PDF: ' + error.message + '. Check console.</p>';
        });

        // Screenshot logic
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

        document.getElementById('screenshotBtn').addEventListener('click', () => {
            resetCount();
            if (screenshotCount > 0) {
                const pageToCapture = document.querySelector('.pdf-page');
                html2canvas(pageToCapture).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    const link = document.createElement('a');
                    link.href = imgData;
                    link.download = 'screenshot.png';
                    link.click();
                    screenshotCount--;
                    localStorage.setItem('screenshotCount', screenshotCount);
                    updateScreenshotInfo();
                });
            } else {
                alert('Screenshot limit reached! Wait 15 minutes.');
            }
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <?php endif; ?>
</body>
</html>