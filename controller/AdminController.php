<?php
session_start();
require_once __DIR__ . '/../config/database.php';
 
// ADMIN CREDENTIALS
define('ADMIN_EMAIL',    $_ENV['ADMIN_EMAIL']);
define('ADMIN_PASSWORD', $_ENV['ADMIN_PASSWORD']);
define('ADMIN_NAME',     'Admin');
 
// ADMIN LOGIN
if (isset($_POST['adminLogin'])) {
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
 
    if ($email === ADMIN_EMAIL && $password === ADMIN_PASSWORD) {
        $_SESSION['role']      = 'admin';
        $_SESSION['user_name'] = ADMIN_NAME;
        header('Location: ../admin/dashboard.php');
        exit;
    }
    header('Location: ../admin/login.php?error=1');
    exit;
}
 
// LOGOUT
if (isset($_GET['action']) && $_GET['action'] === 'adminLogout') {
    session_destroy();
    header('Location: ../admin/login.php');
    exit;
}
 
// AJAX API — admin only, JSON responses
$action = $_GET['action'] ?? $_POST['action'] ?? '';
 
if ($action) {
    if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    header('Content-Type: application/json');
 
    //  DASHBOARD STATS
    if ($action === 'getDashboardStats') {
        $totalUsers     = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users"))['c'];
        $businessAccts  = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE account_type='business'"))['c'];
        $suspendedUsers = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE business_status='suspended'"))['c'];
        $activeUsers    = $totalUsers - $suspendedUsers;
        $pendingBiz     = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE business_status='pending'"))['c'];
        $newToday       = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE DATE(created_at) = CURDATE()"))['c'];
 
        $totalPins = 0;
        $pr = mysqli_query($conn, "SELECT COUNT(*) as c FROM pins");
        if ($pr) $totalPins = (int)mysqli_fetch_assoc($pr)['c'];
 
        $totalBoards = 0;
        $br = mysqli_query($conn, "SELECT COUNT(*) as c FROM boards");
        if ($br) $totalBoards = (int)mysqli_fetch_assoc($br)['c'];
 
        echo json_encode(['success' => true, 'data' => [
            'totalUsers'     => $totalUsers,
            'activeUsers'    => $activeUsers,
            'businessAccts'  => $businessAccts,
            'pendingBiz'     => $pendingBiz,
            'newUsersToday'  => $newToday,
            'totalPins'      => $totalPins,
            'totalBoards'    => $totalBoards,
            'suspendedUsers' => $suspendedUsers,
        ]]);
        exit;
    }
 
    //  ALL USERS
    if ($action === 'getAllUsers') {
        $page   = max(1, intval($_GET['page'] ?? 1));
        $limit  = 20;
        $offset = ($page - 1) * $limit;
        $total  = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users"))['c'];
 
        $result = mysqli_query($conn,
            "SELECT user_id, name, username, email, account_type, business_status, created_at
             FROM users
             ORDER BY user_id DESC
             LIMIT $limit OFFSET $offset"
        );
        $users = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $status = 'active';
            if ($row['business_status'] === 'suspended') $status = 'suspended';
 
            $users[] = [
                'id'           => $row['user_id'],
                'name'         => $row['name'],
                'username'     => $row['username'] ?? '',
                'email'        => $row['email'],
                'account_type' => $row['account_type'],
                'status'       => $status,
                'biz_status'   => $row['business_status'],
                'created_at'   => $row['created_at'],
            ];
        }
        echo json_encode([
            'success' => true,
            'data'    => $users,
            'meta'    => ['page' => $page, 'pages' => max(1, ceil($total / $limit)), 'total' => $total]
        ]);
        exit;
    }

    //  SEARCH USERS
    if ($action === 'searchUsers') {
        $q  = '%' . mysqli_real_escape_string($conn, $_GET['q'] ?? '') . '%';
        $rs = mysqli_query($conn,
            "SELECT user_id, name, username, email, account_type, business_status, created_at
             FROM users
             WHERE name LIKE '$q' OR email LIKE '$q' OR username LIKE '$q'
             LIMIT 50"
        );
        $users = [];
        while ($r = mysqli_fetch_assoc($rs)) {
            $status = ($r['business_status'] === 'suspended') ? 'suspended' : 'active';
            $users[] = [
                'id'           => $r['user_id'],
                'name'         => $r['name'],
                'username'     => $r['username'] ?? '',
                'email'        => $r['email'],
                'account_type' => $r['account_type'],
                'status'       => $status,
                'created_at'   => $r['created_at'],
            ];
        }
        echo json_encode(['success' => true, 'data' => $users]);
        exit;
    }

    //  GET USER DETAILS  
    if ($action === 'getUserDetails') {
        $id = intval($_GET['user_id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            exit;
        }

        $result = mysqli_query($conn,
            "SELECT user_id, name, username, email, account_type, business_status,
                    profile_image, bio, website, gender, date_of_birth, is_admin, created_at
             FROM users WHERE user_id = $id LIMIT 1"
        );

        if (!$result || mysqli_num_rows($result) === 0) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }

        $u = mysqli_fetch_assoc($result);

        // follower_id (PK), user_id, following_user_id, created_at
        $pins = 0;
        $pr = mysqli_query($conn, "SELECT COUNT(*) as c FROM pins WHERE user_id = $id");
        if ($pr) $pins = (int)mysqli_fetch_assoc($pr)['c'];

        $boards = 0;
        $bdr = mysqli_query($conn, "SELECT COUNT(*) as c FROM boards WHERE user_id = $id");
        if ($bdr) $boards = (int)mysqli_fetch_assoc($bdr)['c'];

        // People following this user  → following_user_id = $id
        $followers = 0;
        $flr = mysqli_query($conn, "SELECT COUNT(*) as c FROM follows WHERE following_user_id = $id");
        if ($flr) $followers = (int)mysqli_fetch_assoc($flr)['c'];

        // People this user follows → user_id = $id
        $following = 0;
        $fng = mysqli_query($conn, "SELECT COUNT(*) as c FROM follows WHERE user_id = $id");
        if ($fng) $following = (int)mysqli_fetch_assoc($fng)['c'];

        echo json_encode([
            'success' => true,
            'data'    => [
                'user_id'         => $u['user_id'],
                'name'            => $u['name'],
                'username'        => $u['username'] ?? '',
                'email'           => $u['email'],
                'account_type'    => $u['account_type'],
                'business_status' => $u['business_status'],
                'profile_image'   => $u['profile_image'] ?? '',
                'bio'             => $u['bio'] ?? '',
                'website'         => $u['website'] ?? '',
                'gender'          => $u['gender'] ?? '',
                'date_of_birth'   => $u['date_of_birth'] ?? '',
                'is_admin'        => $u['is_admin'] ?? 0,
                'created_at'      => $u['created_at'],
                'status'          => ($u['business_status'] === 'suspended') ? 'suspended' : 'active',
                'stats'           => [
                    'pins'      => $pins,
                    'boards'    => $boards,
                    'followers' => $followers,
                    'following' => $following,
                ],
            ]
        ]);
        exit;
    }

    //  BUSINESS REQUESTS
    if ($action === 'getBusinessRequests') {
        $status = mysqli_real_escape_string($conn, $_GET['status'] ?? 'pending');
        $result = mysqli_query($conn,
            "SELECT user_id, name, username, email, account_type, business_status, created_at
             FROM users
             WHERE business_status = '$status'
             ORDER BY created_at DESC"
        );
        $requests = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $requests[] = [
                'id'            => $row['user_id'],
                'user_id'       => $row['user_id'],
                'user_name'     => $row['name'],
                'user_email'    => $row['email'],
                'business_name' => $row['name'],
                'business_type' => $row['account_type'] === 'business' ? 'Business' : 'Personal → Business',
                'status'        => $row['business_status'],
                'admin_note'    => '',
                'created_at'    => $row['created_at'],
            ];
        }
        echo json_encode(['success' => true, 'data' => $requests]);
        exit;
    }
    //  APPROVE BUSINESS

    if ($action === 'approveBusinessRequest') {
        require_once __DIR__ . '/../config/mail.php';
        $id = intval($_POST['request_id'] ?? 0);
 
        mysqli_query($conn, "UPDATE users SET account_type='business', business_status='approved' WHERE user_id=$id");
 
        $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name, email FROM users WHERE user_id=$id"));
        if ($user) {
            sendMail(
                $user['email'],
                'Business Account Approved  — Pinterest',
                "
                <div style='font-family:Arial,sans-serif;max-width:520px;margin:auto;border:1px solid #eee;border-radius:12px;overflow:hidden'>
                  <div style='background:#E60023;padding:20px 24px'><h2 style='color:#fff;margin:0'>Business Account Approved ✅</h2></div>
                  <div style='padding:24px'>
                    <p>Hello <strong>{$user['name']}</strong>,</p>
                    <p>Your business account request has been <strong style='color:green'>approved</strong>!</p>
                    <p>You now have access to all Pinterest Business features.</p>
                    <p style='color:#999;font-size:12px;margin-top:20px'>— Pinterest Admin Team</p>
                  </div>
                </div>
                "
            );
        }
        echo json_encode(['success' => true, 'message' => 'Business account approved & email sent']);
        exit;
    }

    //  REJECT BUSINESS
   
    if ($action === 'rejectBusinessRequest') {
        require_once __DIR__ . '/../config/mail.php';
        $id     = intval($_POST['request_id'] ?? 0);
        $reason = mysqli_real_escape_string($conn, $_POST['reason'] ?? '');
 
        mysqli_query($conn, "UPDATE users SET business_status='rejected' WHERE user_id=$id");
 
        $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name, email FROM users WHERE user_id=$id"));
        if ($user) {
            $reasonLine = $reason ? "<p><strong>Reason:</strong> $reason</p>" : '';
            sendMail(
                $user['email'],
                'Business Account Request Update — Pinterest',
                "
                <div style='font-family:Arial,sans-serif;max-width:520px;margin:auto;border:1px solid #eee;border-radius:12px;overflow:hidden'>
                  <div style='background:#E60023;padding:20px 24px'><h2 style='color:#fff;margin:0'>Business Request Update</h2></div>
                  <div style='padding:24px'>
                    <p>Hello <strong>{$user['name']}</strong>,</p>
                    <p>Your business account request has been <strong style='color:#c62828'>rejected</strong>.</p>
                    $reasonLine
                    <p>Please contact support if you have any questions.</p>
                    <p style='color:#999;font-size:12px;margin-top:20px'>— Pinterest Admin Team</p>
                  </div>
                </div>
                "
            );
        }
        echo json_encode(['success' => true, 'message' => 'Request rejected & email sent']);
        exit;
    }
 

    //  SUSPEND USER

    if ($action === 'suspendUser') {
        $id = intval($_POST['user_id'] ?? 0);
        mysqli_query($conn, "UPDATE users SET business_status='suspended' WHERE user_id=$id");
        echo json_encode(['success' => true, 'message' => 'User suspended']);
        exit;
    }

    //  ACTIVATE USER
    if ($action === 'activateUser') {
        $id = intval($_POST['user_id'] ?? 0);
        mysqli_query($conn, "UPDATE users SET business_status='none' WHERE user_id=$id");
        echo json_encode(['success' => true, 'message' => 'User activated']);
        exit;
    }

    //  DELETE USER

    if ($action === 'deleteUser') {
        $id = intval($_POST['user_id'] ?? 0);
        mysqli_query($conn, "DELETE FROM follows     WHERE user_id = $id OR following_user_id = $id");
        mysqli_query($conn, "DELETE FROM saved_pins  WHERE user_id = $id");
        mysqli_query($conn, "DELETE FROM comments    WHERE user_id = $id");
        mysqli_query($conn, "DELETE FROM pins        WHERE user_id = $id");
        mysqli_query($conn, "DELETE FROM boards      WHERE user_id = $id");
        mysqli_query($conn, "DELETE FROM users       WHERE user_id = $id");
        echo json_encode(['success' => true, 'message' => 'User deleted']);
        exit;
    }
 
    //  ALL PINS
    if ($action === 'getAllPins') {
        $page   = max(1, intval($_GET['page'] ?? 1));
        $limit  = 20;
        $offset = ($page - 1) * $limit;
 
        $total = 0;
        $tr = mysqli_query($conn, "SELECT COUNT(*) as c FROM pins");
        if ($tr) $total = (int)mysqli_fetch_assoc($tr)['c'];
 
        $result = mysqli_query($conn,
            "SELECT p.pin_id,
                    p.title,
                    p.category,
                    p.created_at,
                    u.name  AS creator_name,
                    (SELECT COUNT(*) FROM saved_pins sp WHERE sp.pin_id = p.pin_id) AS save_count,
                    (SELECT COUNT(*) FROM comments  c2 WHERE c2.pin_id  = p.pin_id) AS comment_count
             FROM pins p
             LEFT JOIN users u ON p.user_id = u.user_id
             ORDER BY p.created_at DESC
             LIMIT $limit OFFSET $offset"
        );
 
        $pins = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $pins[] = [
                    'id'            => $row['pin_id'],
                    'title'         => $row['title'] ?: 'Untitled',
                    'creator_name'  => $row['creator_name'] ?? '—',
                    'board_name'    => $row['category'] ?: '—',
                    'save_count'    => (int)$row['save_count'],
                    'comment_count' => (int)$row['comment_count'],
                    'created_at'    => $row['created_at'],
                ];
            }
        }
        echo json_encode([
            'success' => true,
            'data'    => $pins,
            'meta'    => ['page' => $page, 'pages' => max(1, ceil($total / $limit)), 'total' => $total]
        ]);
        exit;
    }
 
    //  DELETE PIN
   
    if ($action === 'deletePin') {
        $id = intval($_POST['pin_id'] ?? 0);
        mysqli_query($conn, "DELETE FROM saved_pins WHERE pin_id = $id");
        mysqli_query($conn, "DELETE FROM comments   WHERE pin_id = $id");
        mysqli_query($conn, "DELETE FROM pins        WHERE pin_id = $id");
        echo json_encode(['success' => true, 'message' => 'Pin deleted']);
        exit;
    }

    //  ALL BOARDS

    if ($action === 'getAllBoards') {
        $result = mysqli_query($conn,
            "SELECT b.board_id,
                    b.board_name,
                    b.privacy,
                    b.created_at,
                    u.name AS owner_name,
                    (SELECT COUNT(*) FROM pins p WHERE p.user_id = b.user_id) AS pin_count
             FROM boards b
             LEFT JOIN users u ON b.user_id = u.user_id
             ORDER BY b.created_at DESC"
        );
 
        if (!$result) {
            echo json_encode(['success' => false, 'message' => 'Query error: ' . mysqli_error($conn)]);
            exit;
        }
 
        $boards = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $boards[] = [
                'id'         => $row['board_id'],
                'name'       => $row['board_name'],
                'owner_name' => $row['owner_name'] ?? '—',
                'pin_count'  => (int)$row['pin_count'],
                'is_public'  => ($row['privacy'] === 'public'),
                'created_at' => $row['created_at'],
            ];
        }
        echo json_encode(['success' => true, 'data' => $boards]);
        exit;
    }
 
    //  DELETE BOARD

    if ($action === 'deleteBoard') {
        $id = intval($_POST['board_id'] ?? 0);
        mysqli_query($conn, "DELETE FROM boards WHERE board_id = $id");
        echo json_encode(['success' => true, 'message' => 'Board deleted']);
        exit;
    }

    //  CATEGORIES 


    if ($action === 'getCategories') {
        $result = mysqli_query($conn,
            "SELECT category AS name, COUNT(*) AS pin_count
             FROM pins
             WHERE category IS NOT NULL AND category != ''
             GROUP BY category
             ORDER BY pin_count DESC"
        );
        $cats = [];
        $i = 1;
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $cats[] = [
                    'id'          => $i++,
                    'name'        => $row['name'],
                    'description' => '',
                    'pin_count'   => (int)$row['pin_count'],
                ];
            }
        }
        echo json_encode(['success' => true, 'data' => $cats]);
        exit;
    }
 
    if ($action === 'addCategory' || $action === 'updateCategory' || $action === 'deleteCategory') {
        echo json_encode([
            'success' => false,
            'message' => 'Categories are stored as text on pins. Create a categories table to enable full CRUD.'
        ]);
        exit;
    }
 
    //  REPORTS
    if ($action === 'getReports') {
        $result = mysqli_query($conn,
            "SELECT r.*, u.name AS reporter_name
             FROM reports r
             LEFT JOIN users u ON r.user_id = u.user_id
             ORDER BY r.created_at DESC
             LIMIT 100"
        );
        $reports = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) $reports[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $reports]);
        exit;
    }
 
    echo json_encode(['success' => false, 'message' => 'Unknown action: ' . $action]);
    exit;
}
 
header('Location: ../admin/login.php');
exit;
?>