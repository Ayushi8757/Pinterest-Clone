<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

include "../config/database.php";

$user_id    = intval($_SESSION['user_id']);
$comment_id = intval($_POST['comment_id'] ?? 0);

if (!$comment_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid comment_id']);
    exit;
}

// Sirf apna comment delete kar sakta hai
$check = mysqli_query($conn, "SELECT comment_id FROM pin_comments WHERE comment_id='$comment_id' AND user_id='$user_id'");
if (mysqli_num_rows($check) === 0) {
    echo json_encode(['success' => false, 'message' => 'Not allowed']);
    exit;
}

mysqli_query($conn, "DELETE FROM pin_comments WHERE comment_id='$comment_id' AND user_id='$user_id'");
echo json_encode(['success' => true]);