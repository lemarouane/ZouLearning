<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: /login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: /dashboard.php");
    exit;
}

$course_id = (int)$_GET['id'];
$student_id = (int)$_SESSION['student_id'];

// Verify course access
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
    header("Location: /dashboard.php");
    exit;
}

$folders = $db->query("
    SELECT cf.id, cf.name, cf.description, 
           (SELECT COUNT(*) FROM course_contents cc WHERE cc.folder_id = cf.id) AS content_count
    FROM course_folders cf 
    WHERE cf.course_id = $course_id
");

// Log course view
$stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (?, 'student', 'Viewed course', ?)");
$details = "Viewed course ID $course_id: {$course['title']}";
$stmt->bind_param("is", $student_id, $details);
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Voir Cours - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="/assets/img/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script>
        if (typeof jQuery == 'undefined') {
            document.write('<script src="/assets/js/jquery-3.6.0.min.js"><\/script>');
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
            height: 100%;
            margin: 0;
        }
        body.modal-maximized {
            overflow: hidden;
        }
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f8fc;
            color: #2d3748;
            line-height: 1.6;
            font-size: 1rem;
        }
        body.dark-mode {
            background: #1a202c;
            color: #e2e8f0;
        }
        a {
            text-decoration: none;
            color: inherit;
        }
        .dashboard {
            padding: 1.5rem;
            min-height: 100vh;
            position: relative;
        }
        .dashboard h1 {
            font-size: 1.5rem;
            margin-bottom: 1.25rem;
            color: #1e3c72;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        body.dark-mode .dashboard h1 {
            color: #e2e8f0;
        }
        .section-title {
            font-size: 1rem;
            color: #1e3c72;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        body.dark-mode .section-title {
            color: #e2e8f0;
        }
        .course-info {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        body.dark-mode .course-info {
            background: #2d3748;
            border-color: #4a5568;
        }
        .course-info p {
            margin: 0.5rem 0;
        }
        .folder-container {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .folder-view-card {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 0.75rem;
            cursor: pointer;
            transition: background 0.3s ease, box-shadow 0.3s ease;
        }
        body.dark-mode .folder-view-card {
            background: #2d3748;
            border-color: #4a5568;
        }
        .folder-view-card:hover {
            background: #e9f2ff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .folder-view-card.active-folder {
            background: #e9f2ff;
        }
        body.dark-mode .folder-view-card.active-folder {
            background: #4a5568;
        }
        .subfolder-container {
            display: none;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 0.75rem;
            padding: 0.5rem;
            border-left: 3px solid #3182ce;
            background: #fff;
            border-radius: 0 6px 6px 0;
        }
        body.dark-mode .subfolder-container {
            background: #2d3748;
        }
        .subfolder-card {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 0.75rem;
            cursor: pointer;
            transition: background 0.3s ease;
            flex: 1 1 calc(33.333% - 0.5rem);
        }
        body.dark-mode .subfolder-card {
            background: #2d3748;
            border-color: #4a5568;
        }
        .subfolder-card:hover {
            background: #e9f2ff;
        }
        body.dark-mode .subfolder-card:hover {
            background: #4a5568;
        }
        .empty-message {
            color: #718096;
            font-size: 0.9rem;
        }
        .content-view {
            display: none;
            margin-left: 1rem;
            padding: 0.75rem;
            border-left: 3px solid #3182ce;
            background: #fff;
            border-radius: 0 6px 6px 0;
        }
        body.dark-mode .content-view {
            background: #2d3748;
        }
        .content-item {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 0;
        }
        body.dark-mode .content-item {
            background: #4a5568;
            border-color: #718096;
        }
        .pdf-viewer {
            position: relative;
            transition: all 0.3s ease;
            background: #fff;
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        body.dark-mode .pdf-viewer {
            background: #2d3748;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        .pdf-viewer.maximized {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80vw;
            max-width: 1200px;
            height: 98vh;
            z-index: 1000;
            background: linear-gradient(145deg, #ffffff, #f0f4f8);
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow: hidden;
        }
        body.dark-mode .pdf-viewer.maximized {
            background: linear-gradient(145deg, #2d3748, #1a202c);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        }
        .pdf-viewer.maximized::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }
        .pdf-page {
            filter: blur(5px);
            transition: filter 0.3s ease;
            max-width: 100%;
        }
        .pdf-viewer.active .pdf-page {
            filter: none !important;
        }
        .pdf-canvas {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
            overflow-y: auto;
            flex: 1;
        }
        .pdf-viewer.maximized .pdf-canvas {
            max-width: 100%;
            max-height: 88vh;
        }
        .pdf-controls-fixed {
            position: sticky;
            top: 0;
            background: rgba(255, 255, 255, 0.95);
            padding: 0.5rem;
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            z-index: 10;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        body.dark-mode .pdf-controls-fixed {
            background: rgba(45, 55, 72, 0.95);
        }
        .video-viewer {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%;
            overflow: hidden;
            background: #000;
        }
        .video-viewer iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100% !important;
            height: 100% !important;
            border: none;
            display: block;
            object-fit: cover;
        }
        .video-error {
            color: #e74c3c;
            font-size: 0.85rem;
        }
        .error-message {
            color: #e74c3c;
            font-size: 0.85rem;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.85rem;
            border: none;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .btn-action:hover {
            transform: translateY(-2px);
        }
        .zoom-in, .zoom-out, .rotate, .maximize-btn {
            background: #4a5568;
            color: #fff;
        }
        .zoom-in:hover, .zoom-out:hover, .rotate:hover, .maximize-btn:hover {
            background: #2d3748;
        }
        .screenshot-btn {
            background: #dd6b20;
            color: #fff;
        }
        .screenshot-btn:hover {
            background: #c05621;
        }
        .screenshot-info {
            font-size: 0.85rem;
            color: #718096;
            align-self: center;
        }
        .back-btn {
            background: #718096;
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .back-btn:hover {
            background: #5a6779;
            transform: translateY(-2px);
        }
        .unauthorized-warning {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #e53e3e;
            color: #fff;
            padding: 1rem;
            border-radius: 6px;
            z-index: 1000;
            text-align: center;
            font-size: 1rem;
            max-width: 90%;
        }
        .action-warning-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }
        .action-warning-content {
            background: #fff;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        body.dark-mode .action-warning-content {
            background: #2d3748;
            color: #e2e8f0;
        }
        .action-warning-content p {
            margin-bottom: 1rem;
            font-size: 1rem;
        }
        .action-warning-content button {
            background: #3182ce;
            color: #fff;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .action-warning-content button:hover {
            background: #2b6cb0;
        }
        @media (max-width: 768px) {
            .dashboard {
                padding: 1rem;
            }
            .subfolder-card {
                flex: 1 1 100%;
            }
            .video-viewer {
                padding-bottom: 56.25%;
            }
            .action-warning-content {
                width: 95%;
            }
            .pdf-viewer.maximized {
                width: 100vw;
                height: 100vh;
                min-height: 100vh;
                max-height: -webkit-fill-available;
                padding: 0;
                margin: 0;
                top: 0;
                left: 0;
                transform: none;
                border-radius: 0;
                z-index: 1000;
            }
            .pdf-viewer.maximized .pdf-canvas {
                max-width: 100%;
                max-height: calc(100vh - 60px);
            }
            .pdf-controls-fixed {
                padding: 0.3rem;
                gap: 0.3rem;
            }
            .btn-action {
                padding: 0.4rem 0.8rem;
                font-size: 0.75rem;
            }
            .screenshot-info {
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body oncontextmenu="if ($('.content-view:visible .video-viewer').length > 0) { showWarningModal('Right-click on video'); } return false;">
    <?php include '../includes/student_header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-book"></i> Détails du Cours</h1>
        <div class="course-info">
            <h3 class="section-title"><i class="fas fa-info"></i> Informations sur le Cours</h3>
            <p><strong>Titre :</strong> <?php echo htmlspecialchars($course['title']); ?></p>
            <p><strong>Matière :</strong> <?php echo htmlspecialchars($course['subject_name']); ?></p>
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
                                        <div class="pdf-viewer" data-pdf="/student/serve_pdf.php?file=<?php echo urlencode(basename($content['content_path'])); ?>&course_id=<?php echo $course_id; ?>&content_id=<?php echo $content['id']; ?>" id="pdf-<?php echo $content['id']; ?>" tabindex="0">
                                            <div class="pdf-controls-fixed">
                                                <button class="zoom-in btn-action"><i class="fas fa-search-plus"></i> Zoom Avant</button>
                                                <button class="zoom-out btn-action"><i class="fas fa-search-minus"></i> Zoom Arrière</button>
                                                <button class="rotate btn-action"><i class="fas fa-redo"></i> Rotation</button>
                                                <button class="screenshot-btn btn-action" data-content-id="<?php echo $content['id']; ?>"><i class="fas fa-camera"></i> Capture</button>
                                                <button class="maximize-btn btn-action"><i class="fas fa-expand"></i> Plein Écran</button>
                                                <span class="screenshot-info"></span>
                                            </div>
                                            <div class="pdf-canvas"></div>
                                        </div>
                                    <?php else: ?>
                                        <div class="video-viewer">
                                            <iframe src="<?php echo htmlspecialchars($content['content_path']); ?>" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
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
        <a href="courses.php" class="back-btn"><i class="fas fa-arrow-left"></i> Retour aux cours</a>
        <div class="action-warning-modal" id="action-warning-modal">
            <div class="action-warning-content">
                <p>Ce cours est en mode consultation uniquement. Les interactions non autorisées ne sont pas permises.</p>
                <button id="close-warning-modal">Fermer</button>
            </div>
        </div>
        <div class="unauthorized-warning">Action non autorisée !</div>
    </main>
    <?php include '../includes/footer.php'; ?>

    <script>
        // Initialize pdf.js
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';

        // Screenshot limits
        let screenshotCount = parseInt(localStorage.getItem('screenshotCount') || '3');
        let lastReset = parseInt(localStorage.getItem('lastReset') || '0');
        const fifteenMins = 15 * 60 * 1000;

        // Debounce utility
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

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

        // Log unauthorized actions
        function logUnauthorizedAction(action) {
            $.post('/includes/log_unauthorized.php', {
                user_id: '<?php echo $student_id; ?>',
                course_id: '<?php echo $course_id; ?>',
                action: action,
                course_title: '<?php echo addslashes(htmlspecialchars($course['title'])); ?>'
            }, () => {
                console.log(`Logged unauthorized action: ${action}`);
            }).fail(error => {
                console.error('Unauthorized action log error:', error);
            });
        }

        // Show warning modal
        function showWarningModal(action) {
            console.log(`Showing warning modal for: ${action}`);
            $('.action-warning-modal').css('display', 'flex');
            logUnauthorizedAction(action);
        }

        // Hide warning modal
        function closeWarningModal() {
            console.log('Closing warning modal');
            $('.action-warning-modal').css('display', 'none');
        }

        // Blur/unblur PDF
        function toggleBlur(viewer, enable, reason) {
            if (enable) {
                viewer.removeClass('active');
                console.log(`PDF blurred for viewer ID ${viewer.attr('id')} due to: ${reason}`);
            } else {
                viewer.addClass('active');
                console.log(`PDF unblurred for viewer ID ${viewer.attr('id')} due to: ${reason}`);
            }
        }

        // Prevent developer tools and screenshots
        $(document).on('keydown', (e) => {
            if (
                e.key === 'F12' ||
                (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J')) ||
                (e.ctrlKey && e.key === 'U')
            ) {
                e.preventDefault();
                $('.unauthorized-warning').fadeIn(200).delay(2000).fadeOut(200);
                $('.pdf-viewer').each(function() {
                    toggleBlur($(this), true, 'Developer tools attempt');
                });
                logUnauthorizedAction('Attempted to open developer tools');
                return false;
            }
            if (e.key === 'PrintScreen') {
                navigator.clipboard.writeText('');
                $('.unauthorized-warning').fadeIn(200).delay(2000).fadeOut(200);
                $('.pdf-viewer').each(function() {
                    toggleBlur($(this), true, 'PrintScreen attempt');
                });
                logUnauthorizedAction('Attempted PrintScreen');
                return false;
            }
        });

        // Detect DevTools opening
        function detectDevTools() {
            const threshold = 160;
            const widthThreshold = window.outerWidth - window.innerWidth > threshold;
            const heightThreshold = window.outerHeight - window.innerHeight > threshold;
            if (widthThreshold || heightThreshold) {
                $('.pdf-viewer').each(function() {
                    toggleBlur($(this), true, 'DevTools opened');
                });
                $('.unauthorized-warning').fadeIn(200).delay(2000).fadeOut(200);
                logUnauthorizedAction('Opened developer tools');
            }
        }
        setInterval(detectDevTools, 1000);

        // jQuery event handlers
        $(document).ready(function() {
            console.log('jQuery loaded, initializing page');

            // Close warning modal
            $('#close-warning-modal').on('click', function(e) {
                e.stopPropagation();
                closeWarningModal();
            });

            // Prevent modal on video left clicks (handled globally below)
            $('.video-viewer').on('click', function(e) {
                e.stopPropagation();
                console.log('Video viewer clicked, no warning triggered');
            });

            // Keyboard press blurs PDF and shows warning
            $(document).on('keydown', function(e) {
                console.log(`Key pressed: ${e.key}, blurring all PDFs`);
                $('.pdf-viewer').each(function() {
                    toggleBlur($(this), true, `Keypress: ${e.key}`);
                });
                showWarningModal(`Keyboard key pressed: ${e.key}`);
                e.preventDefault();
                return false;
            });

            // Window blur (click outside browser) blurs PDF and shows warning
            const debouncedWindowBlur = debounce(function() {
                console.log('Window lost focus, blurring all PDFs');
                $('.pdf-viewer').each(function() {
                    toggleBlur($(this), true, 'Window blur (click outside browser)');
                });
                if ($('.content-view:visible .video-viewer').length === 0) {
                    showWarningModal('Clicked outside browser window');
                }
            }, 200);

            $(window).on('blur', debouncedWindowBlur);

            // Click on interactive elements to blur PDFs and show modal (exclude video and folder/subfolder)
            const debouncedBlur = debounce(function($viewer, targetDescription) {
                toggleBlur($viewer, true, `Click on ${targetDescription}`);
            }, 200);

            $('.back-btn, h1, .section-title, .course-info').on('click', function(e) {
                const $target = $(this);
                const isPdfViewer = $target.closest('.pdf-viewer').length > 0;
                const isVideoViewer = $target.closest('.video-viewer').length > 0;
                if (!isPdfViewer && !isVideoViewer && $('.content-view:visible .video-viewer').length === 0) {
                    const targetDescription = $target.attr('class') || $target.prop('tagName');
                    $('.pdf-viewer').each(function() {
                        debouncedBlur($(this), targetDescription);
                    });
                    showWarningModal(`Clicked on ${targetDescription}`);
                }
            });

            // Folder click to toggle subfolders
            $('.folder-view-card').on('click', function(e) {
                e.stopPropagation();
                if ($(this).data('busy')) return;
                $(this).data('busy', true);

                const folderId = $(this).data('folder-id');
                const $subfolder = $(`#subfolder-${folderId}`);
                const $card = $(this);

                $('.folder-view-card').not($card).removeClass('active-folder');
                $('.subfolder-container').not($subfolder).slideUp(400);

                $subfolder.slideToggle(400, function() {
                    $card.toggleClass('active-folder', $subfolder.is(':visible'));
                    $card.data('busy', false);
                    console.log(`${$subfolder.is(':visible') ? 'Expanded' : 'Collapsed'} folder ID ${folderId}`);
                });
            });

            // Prevent child elements from triggering folder click
            $('.folder-view-card *').on('click', function(e) {
                e.stopPropagation();
                if ($(this).is('h4') || $(this).parents('h4').length) {
                    $(this).closest('.folder-view-card').trigger('click');
                }
            });

            // Subfolder click to show content
            $('.subfolder-card').on('click', function(e) {
                e.stopPropagation();
                if ($(this).data('busy')) return;
                $(this).data('busy', true);

                const contentId = $(this).data('content-id');
                const $content = $(`#content-${contentId}`);

                $('.content-view').not($content).slideUp(400);
                $content.slideToggle(400, function() {
                    $(this).data('busy', false);
                    console.log(`Toggled content ID ${contentId}`);
                });

                if ($content.is(':visible')) {
                    const $pdfViewer = $content.find('.pdf-viewer');
                    if ($pdfViewer.length && $pdfViewer.find('.pdf-canvas').children().length === 0) {
                        const url = $pdfViewer.data('pdf');
                        const $canvasContainer = $pdfViewer.find('.pdf-canvas');
                        const contentId = $pdfViewer.attr('id').replace('pdf-', '');
                        const courseTitle = '<?php echo addslashes(htmlspecialchars($course['title'])); ?>';
                        let pdfDoc = null;
                        let scale = window.innerWidth <= 768 ? 2.5 : 1.5;
                        let rotation = 0;

                        console.log(`Loading PDF for content ID ${contentId} from: ${url}`);

                        function renderPages(pdf, scale, rotation) {
                            $canvasContainer.empty();
                            for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                                const canvas = document.createElement('canvas');
                                canvas.className = 'pdf-page';
                                canvas.dataset.pageNum = pageNum;
                                $canvasContainer.append(canvas);
                                const ctx = canvas.getContext('2d');

                                pdf.getPage(pageNum).then(page => {
                                    const viewport = page.getViewport({ scale: scale, rotation: rotation });
                                    canvas.height = viewport.height;
                                    canvas.width = viewport.width;
                                    canvas.style.width = '100%';
                                    canvas.style.maxWidth = `${viewport.width}px`;

                                    const renderContext = { canvasContext: ctx, viewport: viewport };
                                    page.render(renderContext);
                                }).catch(error => {
                                    console.error(`Error rendering page ${pageNum}:`, error);
                                    $canvasContainer.append(`<p class="error-message">Erreur de rendu de la page ${pageNum}: ${error.message}</p>`);
                                });
                            }
                        }

                        pdfjsLib.getDocument(url).promise.then(pdf => {
                            pdfDoc = pdf;
                            renderPages(pdf, scale, rotation);
                            updateScreenshotInfo($pdfViewer);
                            toggleBlur($pdfViewer, false, 'PDF loaded');
                        }).catch(error => {
                            $canvasContainer.html(`<p class="error-message">Erreur de chargement du PDF : ${error.message}</p>`);
                            console.error('PDF load error:', error);
                        });

                        $pdfViewer.find('.zoom-in').on('click', () => {
                            if (pdfDoc) {
                                scale += 0.25;
                                renderPages(pdfDoc, scale, rotation);
                            }
                        });

                        $pdfViewer.find('.zoom-out').on('click', () => {
                            if (pdfDoc && scale > 0.5) {
                                scale -= 0.25;
                                renderPages(pdfDoc, scale, rotation);
                            }
                        });

                        $pdfViewer.find('.rotate').on('click', () => {
                            if (pdfDoc) {
                                rotation = (rotation + 90) % 360;
                                renderPages(pdfDoc, scale, rotation);
                            }
                        });

                        $pdfViewer.find('.screenshot-btn').on('click', () => {
                            resetCount();
                            if (screenshotCount <= 0) {
                                alert('Limite de captures atteinte. Veuillez attendre 15 minutes.');
                                return;
                            }

                            if (pdfDoc) {
                                const visiblePage = getVisiblePageNum($pdfViewer[0]);
                                const canvas = $pdfViewer.find(`.pdf-page[data-page-num="${visiblePage}"]`)[0];
                                if (canvas) {
                                    html2canvas(canvas, { scale: window.devicePixelRatio, useCORS: true }).then(imgCanvas => {
                                        const imgData = imgCanvas.toDataURL('image/png');
                                        const link = document.createElement('a');
                                        link.href = imgData;
                                        link.download = `screenshot_page_${visiblePage}.png`;
                                        link.click();

                                        screenshotCount--;
                                        localStorage.setItem('screenshotCount', screenshotCount);
                                        updateScreenshotInfo($pdfViewer);

                                        $.post('/includes/log_screenshot.php', {
                                            user_id: '<?php echo $student_id; ?>',
                                            course_id: '<?php echo $course_id; ?>',
                                            page_num: visiblePage,
                                            course_title: courseTitle
                                        }, () => {
                                            console.log(`Screenshot logged for page ${visiblePage}`);
                                        }).fail(error => {
                                            console.error('Screenshot log error:', error);
                                        });
                                    }).catch(error => {
                                        console.error('Screenshot error:', error);
                                        alert('Erreur lors de la capture.');
                                    });
                                } else {
                                    console.error(`No canvas found for page ${visiblePage}`);
                                    alert('Erreur : page non trouvée pour la capture.');
                                }
                            }
                        });

                        $pdfViewer.find('.maximize-btn').on('click', function(e) {
                            e.stopPropagation();
                            const $viewer = $pdfViewer;
                            const $btn = $(this);
                            const isMaximized = $viewer.hasClass('maximized');

                            if (!isMaximized) {
                                $viewer.addClass('maximized');
                                $btn.html('<i class="fas fa-compress"></i> Réduire');
                                scale = window.innerWidth <= 768 ? 2.5 : Math.max(scale, 2.0);
                                renderPages(pdfDoc, scale, rotation);
                                console.log(`Entered maximized mode for viewer ID ${$viewer.attr('id')}, dimensions: ${$viewer.width()}x${$viewer.height()}`);
                                if (window.innerWidth <= 768) {
                                    $('body').addClass('modal-maximized');
                                    $viewer.css('height', window.innerHeight + 'px');
                                    $viewer.css('min-height', window.innerHeight + 'px');
                                }
                                toggleBlur($viewer, false, 'Entered maximized mode');
                            } else {
                                $viewer.removeClass('maximized');
                                $btn.html('<i class="fas fa-expand"></i> Plein Écran');
                                scale = window.innerWidth <= 768 ? 2.5 : 1.5;
                                renderPages(pdfDoc, scale, rotation);
                                console.log(`Exited maximized mode for viewer ID ${$viewer.attr('id')}`);
                                if (window.innerWidth <= 768) {
                                    $('body').removeClass('modal-maximized');
                                    $viewer.css('height', '');
                                    $viewer.css('min-height', '');
                                }
                                toggleBlur($viewer, false, 'Exited maximized mode');
                            }
                        });

                        $pdfViewer.on('click', function(e) {
                            e.stopPropagation();
                            toggleBlur($pdfViewer, false, 'PDF viewer clicked');
                            console.log(`PDF viewer ID ${$pdfViewer.attr('id')} clicked, unblurred`);
                        });

                        $pdfViewer.find('.pdf-controls-fixed').on('click', function(e) {
                            e.stopPropagation();
                            toggleBlur($pdfViewer, false, 'PDF controls clicked');
                            console.log(`PDF controls clicked for ID ${$pdfViewer.attr('id')}, maintaining unblurred`);
                        });
                    }

                    $content.find('.video-viewer iframe').each(function() {
                        const $iframe = $(this);
                        const $errorMsg = $iframe.siblings('.video-error');
                        $iframe.on('error', () => $errorMsg.show());
                        $.ajax({
                            url: $iframe.attr('src'),
                            type: 'HEAD',
                            success: () => $errorMsg.hide(),
                            error: () => $errorMsg.show()
                        });
                    });
                }
            });

            $('.subfolder-card *').on('click', function(e) {
                e.stopPropagation();
                if ($(this).is('h5') || $(this).parents('h5').length) {
                    $(this).closest('.subfolder-card').trigger('click');
                }
            });

            $(document).on('mousemove', function() {
                $.ajax({
                    url: '/update_activity.php',
                    method: 'POST',
                    data: { student_id: <?php echo isset($_SESSION['student_id']) ? (int)$_SESSION['student_id'] : 0; ?> },
                    error: (xhr, status, error) => {
                        console.error('Activity update error:', status, error);
                    }
                });
            });

            $(window).on('resize', function() {
                if (window.innerWidth <= 768) {
                    $('.pdf-viewer.maximized').each(function() {
                        $(this).css('height', window.innerHeight + 'px');
                        $(this).css('min-height', window.innerHeight + 'px');
                        console.log(`Resized modal ID ${$(this).attr('id')} to height: ${window.innerHeight}px`);
                    });
                }
            });
        });

        function getVisiblePageNum(viewer) {
            const pages = viewer.querySelectorAll('.pdf-page');
            const viewerRect = viewer.getBoundingClientRect();
            let maxVisibleArea = 0;
            let visiblePageNum = 1;

            pages.forEach(page => {
                const pageRect = page.getBoundingClientRect();
                const visibleHeight = Math.min(pageRect.bottom, viewerRect.bottom) - Math.max(pageRect.top, viewerRect.top);
                const visibleWidth = Math.min(pageRect.right, viewerRect.right) - Math.max(pageRect.left, viewerRect.left);
                const visibleArea = visibleHeight > 0 && visibleWidth > 0 ? visibleHeight * visibleWidth : 0;

                if (visibleArea > maxVisibleArea) {
                    maxVisibleArea = visibleArea;
                    visiblePageNum = parseInt(page.dataset.pageNum);
                }
            });

            return visiblePageNum;
        }
    </script>
</body>
</html>
