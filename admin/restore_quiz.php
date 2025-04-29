<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Restore by setting is_archived = 0
    $stmt = $db->prepare("UPDATE quizzes SET is_archived = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Redirect back to archive_quizzes.php with success message
        header("Location: archive_quizzes.php?success=Examen restauré avec succès");
    } else {
        // Redirect with error message
        header("Location: archive_quizzes.php?error=Erreur lors de la restauration de l'examen");
    }
    
    $stmt->close();
} else {
    // Redirect if no ID is provided
    header("Location: archive_quizzes.php?error=ID d'examen invalide");
}

exit;
?>