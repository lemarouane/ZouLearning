<?php
// Database credentials
define('DB_HOST', 'localhost'); // Localhost
define('DB_USER', 'root');       // Default XAMPP MySQL user
define('DB_PASS', '');           // Default XAMPP has no password; change if yours is different
define('DB_NAME', 'zouhair_elearning'); // Your database name

// Create connection
$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Start session (if not already started elsewhere)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>