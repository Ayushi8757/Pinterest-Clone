<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

include "../config/database.php";
include "notify.php";   // ← same folder (components/)

$user_id   = $_SESSION['user_id'];
$target_id = intval($_POST['target_id'] ?? 0);
$action    = $_POST['action'] ?? '';

if (!$target_id || $target_id == $user_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid user']);
    exit;
}

if ($action === 'follow') {
    $check = mysqli_query($conn, "SELECT follower_id FROM followers 
        WHERE user_id='$user_id' AND following_user_id='$target_id'");
    if (mysqli_num_rows($check) === 0) {
        mysqli_query($conn, "INSERT INTO followers (user_id, following_user_id) 
            VALUES ('$user_id', '$target_id')");

        //  Notify
        notify($conn, $target_id, $user_id, 'follow');
    }
} elseif ($action === 'unfollow') {
    mysqli_query($conn, "DELETE FROM followers 
        WHERE user_id='$user_id' AND following_user_id='$target_id'");
}

$followerRes   = mysqli_query($conn, "SELECT COUNT(*) as c FROM followers 
    WHERE following_user_id='$target_id'");
$followingRes  = mysqli_query($conn, "SELECT COUNT(*) as c FROM followers 
    WHERE user_id='$user_id'");
$myFollowerRes = mysqli_query($conn, "SELECT COUNT(*) as c FROM followers 
    WHERE following_user_id='$user_id'");

echo json_encode([
    'success'          => true,
    'action'           => $action,
    'follower_count'   => mysqli_fetch_assoc($myFollowerRes)['c'],
    'following_count'  => mysqli_fetch_assoc($followingRes)['c'],
    'target_followers' => mysqli_fetch_assoc($followerRes)['c'],
]);