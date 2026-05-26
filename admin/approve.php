<?php
session_start();
require_once '../config/database.php';
require_once '../config/mail.php';

if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
if (!isset($_GET['id'])) { die('Invalid Request'); }

$id = intval($_GET['id']);

// Update: set account_type='business', business_status='approved'
mysqli_query($conn, "UPDATE users SET account_type='business', business_status='approved' WHERE user_id=$id");

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name, email FROM users WHERE user_id=$id"));

if ($user) {
    sendMail($user['email'], 'Business Account Approved ✅ — Pinterest',
        "<div style='font-family:Arial,sans-serif;max-width:520px;margin:auto'>
           <h2 style='color:#E60023'>Business Account Approved! ✅</h2>
           <p>Hello <strong>{$user['name']}</strong>,</p>
           <p>Your business account request has been <strong style='color:green'>approved</strong>.</p>
           <p>You now have access to all Pinterest Business features.</p>
           <p style='color:#999;font-size:12px'>— Pinterest Admin Team</p>
         </div>");
}
?>
<!DOCTYPE html><html><head><title>Approved</title>
<meta http-equiv="refresh" content="2;url=dashboard.php">
<style>body{font-family:Arial;display:flex;justify-content:center;align-items:center;height:100vh;background:#f5f5f5}.box{background:white;padding:40px;border-radius:12px;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,.1);width:400px}h2{color:green}</style>
</head><body><div class="box"><h2>✅ Approved!</h2><p>User approved and email sent.</p><p style="color:#999;font-size:13px">Redirecting to dashboard…</p><a href="dashboard.php" style="color:#E60023">Go now</a></div></body></html>
