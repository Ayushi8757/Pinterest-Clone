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
mysqli_query($conn, "UPDATE users SET business_status='rejected' WHERE user_id=$id");

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name, email FROM users WHERE user_id=$id"));

if ($user) {
    sendMail($user['email'], 'Business Account Request Update — Pinterest',
        "<div style='font-family:Arial,sans-serif;max-width:520px;margin:auto'>
           <h2 style='color:#E60023'>Business Request Update</h2>
           <p>Hello <strong>{$user['name']}</strong>,</p>
           <p>Your business account request has been <strong style='color:#c62828'>rejected</strong>.</p>
           <p>Please contact support for more information.</p>
           <p style='color:#999;font-size:12px'>— Pinterest Admin Team</p>
         </div>");
}
?>
<!DOCTYPE html><html><head><title>Rejected</title>
<meta http-equiv="refresh" content="2;url=dashboard.php">
<style>body{font-family:Arial;display:flex;justify-content:center;align-items:center;height:100vh;background:#f5f5f5}.box{background:white;padding:40px;border-radius:12px;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,.1);width:400px}h2{color:#c62828}</style>
</head><body><div class="box"><h2>❌ Rejected</h2><p>Request rejected and email sent.</p><p style="color:#999;font-size:13px">Redirecting to dashboard…</p><a href="dashboard.php" style="color:#E60023">Go now</a></div></body></html>
