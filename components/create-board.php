<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

include "../config/database.php";
$user_id = $_SESSION['user_id'];

$board_name  = trim($_POST['board_name'] ?? '');
$privacy     = ($_POST['privacy'] ?? 'public') === 'private' ? 'private' : 'public';
$description = trim($_POST['description'] ?? '');

if (empty($board_name)) {
    echo json_encode(['success' => false, 'message' => 'Board name required']);
    exit;
}

$nameSafe = mysqli_real_escape_string($conn, $board_name);
$descSafe = mysqli_real_escape_string($conn, $description);

// Check duplicate
$exists = mysqli_query($conn, "SELECT board_id FROM boards WHERE board_name='$nameSafe' AND user_id='$user_id'");
if ($exists && mysqli_num_rows($exists) > 0) {
    echo json_encode(['success' => false, 'message' => 'Board already exists']);
    exit;
}

$result = mysqli_query($conn, "
    INSERT INTO boards (user_id, board_name, description, privacy, created_at)
    VALUES ('$user_id', '$nameSafe', '$descSafe', '$privacy', NOW())
");

if ($result) {
    echo json_encode([
        'success'    => true,
        'board_id'   => mysqli_insert_id($conn),
        'board_name' => $board_name
    ]);
} else {
    echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
}