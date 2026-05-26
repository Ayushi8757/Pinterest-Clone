<?php
// mark_notif_read.php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

include "../config/database.php";
$uid      = intval($_SESSION['user_id']);
$notif_id = isset($_POST['notif_id']) ? intval($_POST['notif_id']) : 0;

// Uses your columns: notification_id, user_id, is_read
if ($notif_id) {
    mysqli_query($conn,
        "UPDATE notifications SET is_read=1
         WHERE notification_id=$notif_id AND user_id=$uid");
} else {
    // Mark ALL as read
    mysqli_query($conn,
        "UPDATE notifications SET is_read=1 WHERE user_id=$uid");
}

echo json_encode(['success' => true]);