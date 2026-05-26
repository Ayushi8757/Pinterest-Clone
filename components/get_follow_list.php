<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

include "../config/database.php";
$user_id = $_SESSION['user_id'];
$type    = $_GET['type'] ?? 'following'; // 'following' or 'followers'

if ($type === 'following') {
    // People I follow
    $sql = "
        SELECT u.user_id, u.name, u.username, u.profile_image
        FROM users u
        INNER JOIN followers f ON u.user_id = f.following_user_id
        WHERE f.user_id = '$user_id'
        ORDER BY f.created_at DESC
    ";
} else {
    // People who follow me
    $sql = "
        SELECT u.user_id, u.name, u.username, u.profile_image,
               (SELECT COUNT(*) FROM followers WHERE user_id='$user_id' AND following_user_id=u.user_id) as i_follow_them
        FROM users u
        INNER JOIN followers f ON u.user_id = f.user_id
        WHERE f.following_user_id = '$user_id'
        ORDER BY f.created_at DESC
    ";
}

$result = mysqli_query($conn, $sql);
$list   = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) $list[] = $row;
}

echo json_encode(['success' => true, 'list' => $list, 'type' => $type]);