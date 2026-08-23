<?php
require('notifications.php');

markNotificationsAsRead();

header('Content-Type: application/json');
echo json_encode(['status' => 'success']);
?>