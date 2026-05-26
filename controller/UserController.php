<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/mail.php';
require_once __DIR__ . '/../config/captcha.php';

function redirect_with_error(string $path, string $message): void
{
    header('Location: ' . $path . (str_contains($path, '?') ? '&' : '?') . 'error=' . urlencode($message));
    exit;
}

// REGISTER
if (isset($_POST['register'])) {

    $name     = trim($_POST['name']     ?? '');
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $dob      = $_POST['dob']           ?? '';
    $gender   = $_POST['gender']        ?? '';
    $captcha  = trim($_POST['g-recaptcha-response'] ?? '');

    if (!verify_recaptcha_response($captcha)) {
        redirect_with_error('../components/register.php', 'Please fill the CAPTCHA');
    }

    if (empty($name) || empty($email) || empty($password) || empty($dob)) {
        redirect_with_error('../components/register.php', 'All fields are required');
    }

    // Auto-generate username from name if not provided
    if (empty($username)) {
        $username = strtolower(str_replace(' ', '', $name)) . rand(100, 999);
    }

    $name     = mysqli_real_escape_string($conn, $name);
    $username = mysqli_real_escape_string($conn, $username);
    $email    = mysqli_real_escape_string($conn, $email);
    $dob      = mysqli_real_escape_string($conn, $dob);
    $gender   = mysqli_real_escape_string($conn, $gender);
    $password = password_hash($password, PASSWORD_DEFAULT);

    // Check duplicate email
    $check = mysqli_query($conn, "SELECT user_id FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        redirect_with_error('../components/register.php', 'Email already exists');
    }
    // Check duplicate username
    $checkU = mysqli_query($conn, "SELECT user_id FROM users WHERE username='$username'");
    if (mysqli_num_rows($checkU) > 0) {
        $username = $username . rand(10,99); // make unique
    }

    // INSERT matching actual DB columns
    $sql = "INSERT INTO users (name, username, email, password, gender, date_of_birth, account_type, business_status, is_admin)
            VALUES ('$name','$username','$email','$password','$gender','$dob','personal','none',0)";

    if (mysqli_query($conn, $sql)) {
        $newId = mysqli_insert_id($conn);
       $_SESSION['user_email']    = $email;
$_SESSION['user_name']     = $name;
$_SESSION['user_id']       = $newId;
$_SESSION['account_type']  = 'personal';
$_SESSION['profile_image'] = ''; 

        // ── Welcome email to user ──
        sendMail(
            $email,
            'Welcome to Pinterest Clone! 🎉',
            "
            <div style='font-family:Arial,sans-serif;max-width:520px;margin:auto;border:1px solid #eee;border-radius:12px;overflow:hidden'>
              <div style='background:#E60023;padding:24px;text-align:center'>
                <h1 style='color:#fff;margin:0;font-size:28px'>📌 Pinterest</h1>
              </div>
              <div style='padding:28px'>
                <h2 style='color:#111'>Welcome, $name! 🎉</h2>
                <p style='color:#555'>Your account has been created successfully.</p>
                <p style='color:#555'>Start exploring pins, creating boards, and discovering ideas!</p>
                <p style='color:#999;font-size:12px;margin-top:24px'>— Pinterest Clone Team</p>
              </div>
            </div>
            "
        );

        // ── Admin notification ──
        sendMail(
            'ayushisomya1611@gmail.com',
            'New User Registered 👤 — Pinterest',
            "
            <div style='font-family:Arial,sans-serif;max-width:520px;margin:auto;border:1px solid #eee;border-radius:12px;overflow:hidden'>
              <div style='background:#E60023;padding:20px 24px'>
                <h2 style='color:#fff;margin:0'>New User Registered</h2>
              </div>
              <div style='padding:24px'>
                <table style='border-collapse:collapse;width:100%'>
                  <tr><td style='padding:10px 8px;font-weight:bold;color:#444;border-bottom:1px solid #f0f0f0'>Name</td><td style='padding:10px 8px;border-bottom:1px solid #f0f0f0'>$name</td></tr>
                  <tr style='background:#fafafa'><td style='padding:10px 8px;font-weight:bold;color:#444;border-bottom:1px solid #f0f0f0'>Username</td><td style='padding:10px 8px;border-bottom:1px solid #f0f0f0'>@$username</td></tr>
                  <tr><td style='padding:10px 8px;font-weight:bold;color:#444;border-bottom:1px solid #f0f0f0'>Email</td><td style='padding:10px 8px;border-bottom:1px solid #f0f0f0'>$email</td></tr>
                  <tr style='background:#fafafa'><td style='padding:10px 8px;font-weight:bold;color:#444;border-bottom:1px solid #f0f0f0'>Date of Birth</td><td style='padding:10px 8px;border-bottom:1px solid #f0f0f0'>$dob</td></tr>
                  <tr><td style='padding:10px 8px;font-weight:bold;color:#444;border-bottom:1px solid #f0f0f0'>Gender</td><td style='padding:10px 8px;border-bottom:1px solid #f0f0f0'>$gender</td></tr>
                  <tr style='background:#fafafa'><td style='padding:10px 8px;font-weight:bold;color:#444'>Joined</td><td style='padding:10px 8px'>".date('d M Y, h:i A')."</td></tr>
                </table>
                <p style='color:#999;font-size:12px;margin-top:20px'>→ View in <a href='http://localhost/Pinterest_Final/admin/dashboard.php' style='color:#E60023'>Admin Dashboard</a></p>
              </div>
            </div>
            "
        );

        header('Location: ../home.php');
        exit;

    } else {
        redirect_with_error('../components/register.php', mysqli_error($conn));
    }
}



// LOGIN

if (isset($_POST['login'])) {

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $captcha  = trim($_POST['g-recaptcha-response'] ?? '');

    if (!verify_recaptcha_response($captcha)) {
        redirect_with_error('../landing.php?auth=login', 'Please fill the CAPTCHA');
    }

    if (empty($email) || empty($password)) {
        redirect_with_error('../landing.php?auth=login', 'All fields are required');
    }

    $q = mysqli_real_escape_string($conn, $email);
    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$q' LIMIT 1");
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        redirect_with_error('../landing.php?auth=login', 'Email not found');
    }

    if (!password_verify($password, $user['password'])) {
        redirect_with_error('../landing.php?auth=login', 'Invalid password');
    }

    $_SESSION['user_email']      = $user['email'];
$_SESSION['user_name']       = $user['name'];
$_SESSION['user_id']         = $user['user_id'];
$_SESSION['username']        = $user['username'];
$_SESSION['account_type']    = $user['account_type'];
$_SESSION['business_status'] = $user['business_status'];
$_SESSION['profile_image']   = $user['profile_image'] ?? '';

    header('Location: ../home.php');
    exit;
}



header('Location: ../landing.php');
exit;
?>
