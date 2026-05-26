<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', $_ENV['DB_PASSWORD']);
define('DB_NAME', 'pinterest');

define('WS_HOST', '0.0.0.0');
define('WS_PORT', 8080);
define('MAX_CLIENTS', 100);

function db(): mysqli {
    static $conn;
    if (!$conn) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) die("DB Error: " . $conn->connect_error . "\n");
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}

function handshake(string $data, Socket $socket): bool {
    if (preg_match('/Sec-WebSocket-Key:\s*(.+)\r\n/i', $data, $m)) {
        $key = base64_encode(sha1(trim($m[1]) . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
        $response = "HTTP/1.1 101 Switching Protocols\r\n"
                  . "Upgrade: websocket\r\n"
                  . "Connection: Upgrade\r\n"
                  . "Sec-WebSocket-Accept: $key\r\n\r\n";
        socket_write($socket, $response, strlen($response));
        return true;
    }
    return false;
}

function ws_decode(string $data): string {
    if (strlen($data) < 6) return '';
    $len = ord($data[1]) & 127;
    $mask_start = 2;
    if ($len === 126) $mask_start = 4;
    elseif ($len === 127) $mask_start = 10;
    $masks   = substr($data, $mask_start, 4);
    $payload = '';
    for ($i = $mask_start + 4, $j = 0; $i < strlen($data); $i++, $j++) {
        $payload .= $data[$i] ^ $masks[$j % 4];
    }
    return $payload;
}

function ws_encode(string $text): string {
    $len = strlen($text);
    if ($len <= 125)     return "\x81" . chr($len) . $text;
    elseif ($len <= 65535) return "\x81\x7E" . pack('n', $len) . $text;
    else                 return "\x81\x7F" . pack('J', $len) . $text;
}

function send_to(Socket $socket, array $data): void {
    $json = json_encode($data, JSON_UNESCAPED_UNICODE);
    @socket_write($socket, ws_encode($json));
}

function save_message(int $from, int $to, string $msg): int {
    $db  = db();
    $msg = $db->real_escape_string($msg);
    $db->query("INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES ($from, $to, '$msg')");
    return (int)$db->insert_id;
}

function unread_count(int $user_id): int {
    $row = db()->query(
        "SELECT COUNT(*) as c FROM chat_messages WHERE receiver_id = $user_id AND is_read = 0"
    )->fetch_assoc();
    return (int)$row['c'];
}

function mark_read(int $viewer, int $other): void {
    db()->query(
        "UPDATE chat_messages SET is_read = 1
         WHERE sender_id = $other AND receiver_id = $viewer AND is_read = 0"
    );
}

function set_online(int $user_id, string $socket_id): void {
    $db = db();
    $socket_id = $db->real_escape_string($socket_id);
    $db->query(
        "INSERT INTO chat_online (user_id, socket_id) VALUES ($user_id, '$socket_id')
         ON DUPLICATE KEY UPDATE socket_id='$socket_id', last_seen=NOW()"
    );
}

function set_offline(int $user_id): void {
    db()->query("DELETE FROM chat_online WHERE user_id = $user_id");
}

function broadcast_status(int $uid, bool $online, array &$clients): void {
    $payload = ws_encode(json_encode([
        'type'    => 'user_status',
        'user_id' => $uid,
        'online'  => $online,
    ]));
    foreach ($clients as $c) {
        if ($c['handshake'] && $c['user_id'] !== $uid) {
            @socket_write($c['socket'], $payload);
        }
    }
}

// ── Create server socket 
$server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($server, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($server, WS_HOST, WS_PORT);
socket_listen($server, 10);
socket_set_nonblock($server);

// KEY CHANGE: use spl_object_id() as array key — never cast Socket to int
$clients        = []; // spl_object_id => ['socket'=>Socket, ...]
$user_to_socket = []; // user_id => Socket

echo "[WebSocket Server] Listening on ws://" . WS_HOST . ":" . WS_PORT . "\n";

while (true) {
    // Build $read with only real Socket objects
    $read = [$server];
    foreach ($clients as $client) {
        $read[] = $client['socket'];
    }

    $write  = null;
    $except = null;

    // socket_select() needs the array re-indexed cleanly
    $read = array_values($read);

    if (socket_select($read, $write, $except, 0, 200000) < 1) {
        continue;
    }

    // ── New incoming connection 
    if (in_array($server, $read, true)) {
        $new = socket_accept($server);
        if ($new instanceof Socket) {
            $id = spl_object_id($new);           // stable unique int key
            $clients[$id] = [
                'socket'    => $new,
                'handshake' => false,
                'user_id'   => 0,
                'id'        => $id,
            ];
            echo "[+] New connection ($id)\n";
        }
    }

    // ── Handle data from each client
    foreach ($clients as $key => $client) {
        $sock = $client['socket'];
        if (!in_array($sock, $read, true)) continue;

        $data = @socket_read($sock, 4096);

        if ($data === false || $data === '') {
            $uid = $client['user_id'];
            if ($uid) {
                set_offline($uid);
                unset($user_to_socket[$uid]);
                broadcast_status($uid, false, $clients);
            }
            socket_close($sock);
            unset($clients[$key]);
            echo "[-] Disconnected ({$client['id']})\n";
            continue;
        }

        if (!$client['handshake']) {
            if (handshake($data, $sock)) {
                $clients[$key]['handshake'] = true;
                echo "[H] Handshake complete ({$client['id']})\n";
            }
            continue;
        }

        $json = ws_decode($data);
        if (empty($json)) continue;
        $msg = json_decode($json, true);
        if (!$msg || !isset($msg['type'])) continue;

        echo "[MSG] type={$msg['type']} from user={$client['user_id']}\n";

        switch ($msg['type']) {

            case 'auth':
                $uid = (int)($msg['user_id'] ?? 0);
                if ($uid < 1) break;
                $clients[$key]['user_id'] = $uid;
                $user_to_socket[$uid]     = $sock;
                set_online($uid, (string)$client['id']);
                send_to($sock, ['type' => 'unread_count', 'count' => unread_count($uid)]);
                echo "[A] User $uid authenticated\n";
                break;

            case 'send_message':
                $from = $client['user_id'];
                $to   = (int)($msg['to'] ?? 0);
                $text = trim($msg['message'] ?? '');
                if (!$from || !$to || !$text) break;

                $msg_id = save_message($from, $to, $text);
                $row = db()->query(
                    "SELECT name, profile_image FROM users WHERE user_id = $from"
                )->fetch_assoc();

                $payload = [
                    'type'        => 'new_message',
                    'id'          => $msg_id,
                    'from'        => $from,
                    'to'          => $to,
                    'message'     => $text,
                    'sender_name' => $row['name'] ?? '',
                    'sender_pic'  => $row['profile_image'] ?? '',
                    'time'        => date('g:i A'),
                ];

                if (isset($user_to_socket[$to])) {
                    send_to($user_to_socket[$to], $payload);
                    send_to($user_to_socket[$to], [
                        'type'  => 'unread_count',
                        'count' => unread_count($to),
                    ]);
                }
                send_to($sock, $payload);
                break;

            case 'mark_read':
                $viewer = $client['user_id'];
                $other  = (int)($msg['other_user'] ?? 0);
                if ($viewer && $other) {
                    mark_read($viewer, $other);
                    send_to($sock, ['type' => 'unread_count', 'count' => unread_count($viewer)]);
                }
                break;

            case 'typing':
                $from = $client['user_id'];
                $to   = (int)($msg['to'] ?? 0);
                if ($from && $to && isset($user_to_socket[$to])) {
                    send_to($user_to_socket[$to], ['type' => 'typing', 'from' => $from]);
                }
                break;
        }
    }
}