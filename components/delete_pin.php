<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

include "../config/database.php";
$user_id = $_SESSION['user_id'];
$pin_id  = intval($_POST['pin_id'] ?? 0);

if (!$pin_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid pin']);
    exit;
}

// Make sure the pin belongs to this user
$check = mysqli_query($conn, "SELECT image FROM pins WHERE pin_id='$pin_id' AND user_id='$user_id'");
if (!$check || mysqli_num_rows($check) === 0) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

$row = mysqli_fetch_assoc($check);

// Delete from board_pins junction table first
mysqli_query($conn, "DELETE FROM board_pins WHERE pin_id='$pin_id'");

// Delete from saved_pins if exists
mysqli_query($conn, "DELETE FROM saved_pins WHERE pin_id='$pin_id'");

// Delete from pin_likes if exists
mysqli_query($conn, "DELETE FROM pin_likes WHERE pin_id='$pin_id'");

// Delete actual pin
$deleted = mysqli_query($conn, "DELETE FROM pins WHERE pin_id='$pin_id' AND user_id='$user_id'");

// Delete image file from server
if ($deleted && !empty($row['image'])) {
    $filePath = __DIR__ . '/../' . $row['image'];
    if (file_exists($filePath)) unlink($filePath);
}

echo json_encode(['success' => (bool)$deleted]);