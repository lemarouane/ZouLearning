<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Soft delete by setting is_archived = 1
    $stmt = $db->prepare("UPDATE courses SET is_archived = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Redirect back to manage_courses.php with success message
        header("Location: manage_courses.php?success=Cours archivé avec succès");
    } else {
        // Redirect with error message
        header("Location: manage_courses.php?error=Erreur lors de l'archivage du cours");
    }
    
    $stmt->close();
} else {
    // Redirect if no ID is provided
    header("Location: manage_courses.php?error=ID de cours invalide");
}

exit;
?>