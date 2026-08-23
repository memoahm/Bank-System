<?php
// Initialize notifications array if not set
if (!isset($_SESSION['notifications'])) {
    $_SESSION['notifications'] = [];
}

// Function to add a notification
function addNotification($type, $message) {
    $_SESSION['notifications'][] = [
        'type' => $type, // 'success' or 'error'
        'message' => $message,
        'read' => false,
        'timestamp' => date('Y-m-d H:i:s')
    ];
}

// Function to get unread notifications count
function getUnreadNotificationsCount() {
    $count = 0;
    foreach ($_SESSION['notifications'] as $notification) {
        if (!$notification['read']) {
            $count++;
        }
    }
    return $count;
}

// Function to mark all notifications as read
function markNotificationsAsRead() {
    foreach ($_SESSION['notifications'] as &$notification) {
        $notification['read'] = true;
    }
}
?>