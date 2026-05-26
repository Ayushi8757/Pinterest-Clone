<?php
session_start();
require_once 'config/database.php';
require_once 'config/mail.php';

if (empty($_SESSION['user_id'])) {
    header('Location: components/login.php');
    exit;
}

$user_id = intval($_SESSION['user_id']);

// Set business_status='pending' (enum value in DB)
mysqli_query($conn, "UPDATE users SET business_status='pending' WHERE user_id=$user_id");

// Update session
$_SESSION['business_status'] = 'pending';

// Get user details
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$user_id"));

// ── Confirmation email to user ──
sendMail(
    $user['email'],
    'Business Account Request Submitted 📋 — Pinterest',
    "
    <div style='font-family:Arial,sans-serif;max-width:520px;margin:auto;border:1px solid #eee;border-radius:12px;overflow:hidden'>
      <div style='background:#E60023;padding:20px 24px'><h2 style='color:#fff;margin:0'>Request Submitted!</h2></div>
      <div style='padding:24px'>
        <p>Hello <strong>{$user['name']}</strong>,</p>
        <p>Your business account request is now <strong>pending review</strong> by the admin.</p>
        <p>You will receive an email once a decision has been made.</p>
        <p style='color:#999;font-size:12px;margin-top:20px'>— Pinterest Clone Team</p>
      </div>
    </div>
    "
);

// ── Admin alert email ──
sendMail(
    'ayushisomya1611@gmail.com',
    'New Business Account Request 🏢 — Pinterest',
    "
    <div style='font-family:Arial,sans-serif;max-width:520px;margin:auto;border:1px solid #eee;border-radius:12px;overflow:hidden'>
      <div style='background:#E60023;padding:20px 24px'><h2 style='color:#fff;margin:0'>New Business Request</h2></div>
      <div style='padding:24px'>
        <table style='border-collapse:collapse;width:100%'>
          <tr><td style='padding:10px 8px;font-weight:bold;color:#444;border-bottom:1px solid #f0f0f0'>Name</td><td style='padding:10px 8px;border-bottom:1px solid #f0f0f0'>{$user['name']}</td></tr>
          <tr style='background:#fafafa'><td style='padding:10px 8px;font-weight:bold;color:#444;border-bottom:1px solid #f0f0f0'>Email</td><td style='padding:10px 8px;border-bottom:1px solid #f0f0f0'>{$user['email']}</td></tr>
          <tr><td style='padding:10px 8px;font-weight:bold;color:#444;border-bottom:1px solid #f0f0f0'>User ID</td><td style='padding:10px 8px;border-bottom:1px solid #f0f0f0'>$user_id</td></tr>
          <tr style='background:#fafafa'><td style='padding:10px 8px;font-weight:bold;color:#444'>Requested On</td><td style='padding:10px 8px'>".date('d M Y, h:i A')."</td></tr>
        </table>
        <p style='margin-top:20px'>
          <a href='http://localhost/Pinterest_Final/admin/dashboard.php' style='background:#E60023;color:white;padding:10px 20px;text-decoration:none;border-radius:6px;font-weight:bold'>Review in Admin Panel →</a>
        </p>
      </div>
    </div>
    "
);

header('Location: home.php?msg=business_request_submitted');
exit;
?>
