<?php
// components/notify.php
// Include: include "notify.php"; (same folder se)

function notify($conn, $to_user_id, $from_user_id, $type, $pin_id = null, $comment_text = null) {
    if ((int)$to_user_id === (int)$from_user_id) return;

    $to      = intval($to_user_id);
    $from    = intval($from_user_id);
    $type_es = mysqli_real_escape_string($conn, $type);

    // Get sender's name
    $userRes    = mysqli_query($conn, "SELECT name, username FROM users WHERE user_id = $from");
    $userRow    = $userRes ? mysqli_fetch_assoc($userRes) : null;
    $senderName = $userRow['name'] ?? $userRow['username'] ?? 'Someone';
    $senderName = mysqli_real_escape_string($conn, $senderName);

    // Get pin title if needed
    $pinTitle = '';
    if ($pin_id) {
        $pinRes   = mysqli_query($conn, "SELECT title FROM pins WHERE pin_id = " . intval($pin_id));
        $pinRow   = $pinRes ? mysqli_fetch_assoc($pinRes) : null;
        $pinTitle = $pinRow['title'] ?? '';
    }

    // Build message
    switch ($type) {
        case 'follow':
            $message = "$senderName started following you";
            break;
        case 'like':
            $message = $pinTitle
                ? "$senderName liked your pin \"$pinTitle\""
                : "$senderName liked your pin";
            break;
        case 'save':
            $message = $pinTitle
                ? "$senderName saved your pin \"$pinTitle\""
                : "$senderName saved your pin";
            break;
        case 'comment':
            $preview = $comment_text ? '"' . mb_substr($comment_text, 0, 60) . '"' : '';
            $message = $pinTitle
                ? "$senderName commented on your pin \"$pinTitle\": $preview"
                : "$senderName commented on your pin: $preview";
            break;
        default:
            $message = "$senderName interacted with you";
    }

    $message_es = mysqli_real_escape_string($conn, $message);

    // Avoid duplicate follow notifications
    if ($type === 'follow') {
        $exists = mysqli_query($conn,
            "SELECT notification_id FROM notifications
             WHERE user_id=$to AND notification_type='follow'
             AND message LIKE '$senderName%' LIMIT 1");
        if ($exists && mysqli_num_rows($exists) > 0) return;
    }

    mysqli_query($conn,
        "INSERT INTO notifications (user_id, message, notification_type, is_read, created_at)
         VALUES ($to, '$message_es', '$type_es', 0, NOW())"
    );
}
?>