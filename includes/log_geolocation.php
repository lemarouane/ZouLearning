<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['latitude'], $_POST['longitude'])) {
    $latitude = (float)$_POST['latitude'];
    $longitude = (float)$_POST['longitude'];
    $_SESSION['last_latitude'] = $latitude;
    $_SESSION['last_longitude'] = $longitude;
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error']);
}
?>