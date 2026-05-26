<?php
// get_notif_count.php
session_start();
header('Content-Type: application/json');
 
if (empty($_SESSION['user_id'])) {
    echo json_encode(['count' => 0]);
    exit;
}
 
include "../config/database.php";
$uid = intval($_SESSION['user_id']);
 
// Uses your column: user_id, is_read
$res   = mysqli_query($conn, "SELECT COUNT(*) as c FROM notifications WHERE user_id=$uid AND is_read=0");
$count = $res ? (int)(mysqli_fetch_assoc($res)['c']) : 0;
 
echo json_encode(['count' => $count]);