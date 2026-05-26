<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

include "../config/database.php";
$user_id  = $_SESSION['user_id'];
$board_id = intval($_POST['board_id'] ?? 0);

if (!$board_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid board']);
    exit;
}

// Make sure board belongs to this user
$check = mysqli_query($conn, "SELECT board_id FROM boards WHERE board_id='$board_id' AND user_id='$user_id'");
if (!$check || mysqli_num_rows($check) === 0) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

// Remove pins from junction table only (pins themselves stay)
mysqli_query($conn, "DELETE FROM board_pins WHERE board_id='$board_id'");

// Delete the board
$deleted = mysqli_query($conn, "DELETE FROM boards WHERE board_id='$board_id' AND user_id='$user_id'");

echo json_encode(['success' => (bool)$deleted]);