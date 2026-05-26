<?php
session_start();
include "../config/database.php";
include "notify.php";   

if (!isset($_SESSION['user_id'])) {
    exit("login required");
}

$user_id = $_SESSION['user_id'];
$pin_id  = intval($_POST['pin_id']);

$check = mysqli_query($conn, "
    SELECT saved_pin_id
    FROM saved_pins
    WHERE user_id = '$user_id' AND pin_id = '$pin_id'
");

if (mysqli_num_rows($check) > 0) {
    mysqli_query($conn, "
        DELETE FROM saved_pins
        WHERE user_id = '$user_id' AND pin_id = '$pin_id'
    ");
    echo "removed";
} else {
    mysqli_query($conn, "
        INSERT INTO saved_pins (user_id, pin_id)
        VALUES ('$user_id', '$pin_id')
    ");

    // Notify pin owner
    $pinRes = mysqli_query($conn, "SELECT user_id FROM pins WHERE pin_id='$pin_id'");
    $pinRow = $pinRes ? mysqli_fetch_assoc($pinRes) : null;
    if ($pinRow && $pinRow['user_id'] != $user_id) {
        notify($conn, $pinRow['user_id'], $user_id, 'save', $pin_id);
    }

    echo "saved";
}