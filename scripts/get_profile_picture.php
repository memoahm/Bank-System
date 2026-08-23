<?php
session_start();
require('../../configs/db.php');

$defaultImage = '../../assets/img/pp_default.jpg';

if (!isset($_GET['accno']) || empty($_GET['accno'])) {
    // Return default image
    header('Content-Type: image/jpeg');
    readfile($defaultImage);
    exit;
}

$accNo = $_GET['accno'];

// Fetch profile picture from database
$stmt = $conn->prepare("SELECT profile_picture FROM userinfo WHERE AccNo = ?");
$stmt->bind_param('s', $accNo);
$stmt->execute();
$stmt->bind_result($imageData);
$stmt->fetch();

if ($imageData) {
    // Determine image type (assuming common formats)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->buffer($imageData);
    header('Content-Type: ' . $mimeType);
    echo $imageData;
} else {
    // Return default image
    header('Content-Type: image/jpeg');
    readfile($defaultImage);
}

$stmt->close();
$conn->close();
exit;
?>