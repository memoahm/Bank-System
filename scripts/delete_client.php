<?php
header('Content-Type: application/json');
require('../configs/db.php');
$data = json_decode(file_get_contents('php://input'), true);
$accNo = mysqli_real_escape_string($conn, $data['accNo']);

$sql = "DELETE FROM userinfo WHERE AccNo='$accNo'";
if (mysqli_query($conn, $sql)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete client']);
}
?>