<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

include "../config/database.php";
include "notify.php";   //  same folder (components/)

$user_id = intval($_SESSION['user_id']);
$pin_id  = intval($_POST['pin_id']  ?? 0);
$comment = trim($_POST['comment'] ?? '');

if (!$pin_id || $comment === '') {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$comment_esc = mysqli_real_escape_string($conn, $comment);

$insert = mysqli_query($conn,
    "INSERT INTO pin_comments (pin_id, user_id, comment)
     VALUES ('$pin_id', '$user_id', '$comment_esc')"
);

if (!$insert) {
    echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    exit;
}

//  Notify pin owner
$pinRes = mysqli_query($conn, "SELECT user_id FROM pins WHERE pin_id='$pin_id'");
$pinRow = $pinRes ? mysqli_fetch_assoc($pinRes) : null;
if ($pinRow && $pinRow['user_id'] != $user_id) {
    notify($conn, $pinRow['user_id'], $user_id, 'comment', $pin_id, $comment);
}

// Return new comment with user info
$new_id = mysqli_insert_id($conn);
$row = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT c.comment_id, c.comment, c.created_at,
            u.name AS user_name, u.profile_image
     FROM pin_comments c
     JOIN users u ON c.user_id = u.user_id
     WHERE c.comment_id = '$new_id'"
));

echo json_encode(['success' => true, 'comment' => $row]);