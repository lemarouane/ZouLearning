<?php
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Restore by setting is_archived = 0
    $stmt = $db->prepare("UPDATE courses SET is_archived = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Redirect back to archive_courses.php with success message
        header("Location: archive_courses.php?success=Niveau restauré avec succès");
    } else {
        // Redirect with error message
        header("Location: archive_courses.php?error=Erreur lors de la restauration du niveau");
    }
    
    $stmt->close();
} else {
    // Redirect if no ID is provided
    header("Location: archive_courses.php?error=ID de niveau invalide");
}

exit;
?>