<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$db = new mysqli('localhost', 'root', 'root@123#', 'pinterest');
if ($db->connect_error) {
    echo json_encode(['error' => 'DB connection failed: ' . $db->connect_error]);
    exit;
}
$db->set_charset('utf8mb4');

$me = (int)($_SESSION['user_id'] ?? 0);
if (!$me) {
    echo json_encode(['error' => 'Not logged in', 'session' => $_SESSION]);
    exit;
}

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'get_users':
        $res = $db->query(
            "SELECT user_id, name, profile_image,
                    (SELECT COUNT(*) FROM chat_online WHERE user_id = u.user_id) AS is_online
             FROM users u
             WHERE u.user_id != $me
             ORDER BY name ASC
             LIMIT 100"
        );
        //  Error check
        if (!$res) {
            echo json_encode(['error' => 'get_users query failed: ' . $db->error, 'users' => []]);
            break;
        }
        $users = [];
        while ($r = $res->fetch_assoc()) {
            $users[] = [
                'id'        => (int)$r['user_id'],
                'name'      => $r['name'],
                'pic'       => $r['profile_image'],
                'is_online' => (bool)$r['is_online'],
            ];
        }
        echo json_encode(['users' => $users]);
        break;

    case 'search':
        $q = '%' . $db->real_escape_string($_GET['q'] ?? '') . '%';
        $res = $db->query(
            "SELECT user_id, name, profile_image,
                    (SELECT COUNT(*) FROM chat_online WHERE user_id = u.user_id) AS is_online
             FROM users u
             WHERE u.user_id != $me AND name LIKE '$q'
             LIMIT 20"
        );
        if (!$res) {
            echo json_encode(['error' => 'search query failed: ' . $db->error, 'users' => []]);
            break;
        }
        $users = [];
        while ($r = $res->fetch_assoc()) {
            $users[] = [
                'id'        => (int)$r['user_id'],
                'name'      => $r['name'],
                'pic'       => $r['profile_image'],
                'is_online' => (bool)$r['is_online'],
            ];
        }
        echo json_encode(['users' => $users]);
        break;

    case 'history':
        $other = (int)($_GET['uid'] ?? 0);
        if (!$other) { echo json_encode(['messages' => []]); break; }

        $db->query(
            "UPDATE chat_messages SET is_read = 1
             WHERE sender_id = $other AND receiver_id = $me"
        );

        $res = $db->query(
            "SELECT m.id, m.sender_id, m.receiver_id, m.message,
                    DATE_FORMAT(m.created_at, '%h:%i %p') AS time
             FROM chat_messages m
             WHERE (sender_id = $me AND receiver_id = $other)
                OR (sender_id = $other AND receiver_id = $me)
             ORDER BY m.created_at ASC
             LIMIT 100"
        );
        if (!$res) {
            echo json_encode(['error' => 'history query failed: ' . $db->error, 'messages' => []]);
            break;
        }
        $msgs = [];
        while ($r = $res->fetch_assoc()) {
            $msgs[] = [
                'id'      => (int)$r['id'],
                'from'    => (int)$r['sender_id'],
                'to'      => (int)$r['receiver_id'],
                'message' => $r['message'],
                'time'    => $r['time'],
                'is_mine' => ((int)$r['sender_id'] === $me),
            ];
        }
        echo json_encode(['messages' => $msgs]);
        break;

    case 'conversations':
    $res = $db->query(
        "SELECT
            u.user_id AS id, u.name, u.profile_image,
            (SELECT message FROM chat_messages
             WHERE (sender_id = $me AND receiver_id = u.user_id)
                OR (sender_id = u.user_id AND receiver_id = $me)
             ORDER BY created_at DESC LIMIT 1) AS last_msg,
            (SELECT DATE_FORMAT(created_at, '%h:%i %p') FROM chat_messages
             WHERE (sender_id = $me AND receiver_id = u.user_id)
                OR (sender_id = u.user_id AND receiver_id = $me)
             ORDER BY created_at DESC LIMIT 1) AS last_time,
            (SELECT MAX(created_at) FROM chat_messages
             WHERE (sender_id = $me AND receiver_id = u.user_id)
                OR (sender_id = u.user_id AND receiver_id = $me)
             ) AS last_at,
            (SELECT COUNT(*) FROM chat_messages
             WHERE sender_id = u.user_id AND receiver_id = $me AND is_read = 0) AS unread,
            (SELECT COUNT(*) FROM chat_online
             WHERE user_id = u.user_id) AS is_online
         FROM users u
         WHERE u.user_id != $me
           AND EXISTS (
               SELECT 1 FROM chat_messages
               WHERE (sender_id = $me AND receiver_id = u.user_id)
                  OR (sender_id = u.user_id AND receiver_id = $me)
           )
         ORDER BY last_at DESC
         LIMIT 30"
    );
    if (!$res) {
        echo json_encode(['error' => 'conversations query failed: ' . $db->error, 'conversations' => []]);
        break;
    }
    $convs = [];
    while ($r = $res->fetch_assoc()) {
        $convs[] = [
            'id'        => (int)$r['id'],
            'name'      => $r['name'],
            'pic'       => $r['profile_image'],
            'last_msg'  => $r['last_msg'],
            'last_time' => $r['last_time'],
            'unread'    => (int)$r['unread'],
            'is_online' => (bool)$r['is_online'],
        ];
    }
    echo json_encode(['conversations' => $convs]);
    break;
    default:
        echo json_encode(['error' => 'Unknown action: ' . $action]);
}
$db->close();