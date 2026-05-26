<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

include "../config/database.php";

$pin_id = intval($_GET['pin_id'] ?? 0);

if (!$pin_id) {
    echo json_encode(['success' => false, 'message' => 'No pin_id']);
    exit;
}

$result = mysqli_query($conn,
    "SELECT c.comment_id, c.comment, c.created_at, c.user_id,
            u.name AS user_name, u.profile_image
     FROM pin_comments c
     JOIN users u ON c.user_id = u.user_id
     WHERE c.pin_id = '$pin_id'
     ORDER BY c.created_at ASC"
);
$my_id = intval($_SESSION['user_id']);
while ($row = mysqli_fetch_assoc($result)) {
    $row['is_mine'] = ($row['user_id'] == $my_id) ? 1 : 0;
    $comments[] = $row;
}

echo json_encode(['success' => true, 'comments' => $comments]);