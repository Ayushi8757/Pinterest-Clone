<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: ../components/login.php');
    exit;
}

include "../config/database.php";

$user_id     = intval($_SESSION['user_id']);
$profileImg  = $_SESSION['profile_image'] ?? '';
$profileName = $_SESSION['user_name']  ?? 'User';
$userEmail   = $_SESSION['user_email'] ?? '';
$username    = $_SESSION['username']   ?? strtolower(str_replace(' ', '', $profileName));
$userBio     = $_SESSION['user_bio']   ?? '';
$firstLetter = strtoupper(mb_substr($profileName, 0, 1));

// Split name
$nameParts = explode(' ', trim($profileName), 2);
$firstName = $nameParts[0] ?? '';
$lastName  = $nameParts[1] ?? '';

// Avatar upload handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar_upload']) && $_FILES['avatar_upload']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../uploads/profiles/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);
    $ext      = strtolower(pathinfo($_FILES['avatar_upload']['name'], PATHINFO_EXTENSION));
    $allowed  = ['jpg','jpeg','png','gif','webp'];
    if (in_array($ext, $allowed)) {
        $fileName = 'user_' . $user_id . '_' . time() . '.' . $ext;
        $dest     = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['avatar_upload']['tmp_name'], $dest)) {
            $fileVal = 'uploads/profiles/' . $fileName;
            $fileEsc = mysqli_real_escape_string($conn, $fileVal);
            mysqli_query($conn, "UPDATE users SET profile_image='$fileEsc' WHERE user_id='$user_id'");
            $_SESSION['profile_image'] = $fileVal;
            $profileImg = $fileVal;
        }
    }
    header('Location: edit_profile.php?saved=avatar');
    exit;
}

// Profile save handler (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_profile') {
    header('Content-Type: application/json');
    $first    = trim($_POST['first_name']  ?? '');
    $last     = trim($_POST['last_name']   ?? '');
    $uname    = trim($_POST['username']    ?? '');
    $bio      = trim($_POST['bio']         ?? '');
    $website  = trim($_POST['website']     ?? '');
    $location = trim($_POST['location']    ?? '');
    $fullName = trim("$first $last");
    if ($fullName === '') { echo json_encode(['success'=>false,'message'=>'Name cannot be empty']); exit; }

    $nameEsc  = mysqli_real_escape_string($conn, $fullName);
    $unameEsc = mysqli_real_escape_string($conn, $uname);
    $bioEsc   = mysqli_real_escape_string($conn, $bio);
    $webEsc   = mysqli_real_escape_string($conn, $website);
    $locEsc   = mysqli_real_escape_string($conn, $location);

    mysqli_query($conn, "UPDATE users SET 
        name='$nameEsc', username='$unameEsc', bio='$bioEsc', 
        website='$webEsc', location='$locEsc' 
        WHERE user_id='$user_id'");

    $_SESSION['user_name'] = $fullName;
    $_SESSION['username']  = $uname;
    $_SESSION['user_bio']  = $bio;
    echo json_encode(['success'=>true]);
    exit;
}

// Account save handler (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_account') {
    header('Content-Type: application/json');
    $email       = trim($_POST['email']        ?? '');
    $currentPass = trim($_POST['current_pass'] ?? '');
    $newPass     = trim($_POST['new_pass']     ?? '');
    if ($email === '') { echo json_encode(['success'=>false,'message'=>'Email cannot be empty']); exit; }

    $row = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT password, email FROM users WHERE user_id='$user_id'"));

    if ($newPass !== '') {
        if ($currentPass === '') { echo json_encode(['success'=>false,'message'=>'Enter your current password']); exit; }
        $validPass = password_verify($currentPass, $row['password']) || ($currentPass === $row['password']);
        if (!$validPass) { echo json_encode(['success'=>false,'message'=>'Current password is incorrect']); exit; }
        if (strlen($newPass) < 8) { echo json_encode(['success'=>false,'message'=>'Password must be at least 8 characters']); exit; }
        $hash    = password_hash($newPass, PASSWORD_BCRYPT);
        $hashEsc = mysqli_real_escape_string($conn, $hash);
        mysqli_query($conn, "UPDATE users SET password='$hashEsc' WHERE user_id='$user_id'");
    }

    $emailEsc = mysqli_real_escape_string($conn, $email);
    if ($email !== $row['email']) {
        $check = mysqli_query($conn, "SELECT user_id FROM users WHERE email='$emailEsc' AND user_id!='$user_id' LIMIT 1");
        if ($check && mysqli_num_rows($check) > 0) { echo json_encode(['success'=>false,'message'=>'Email already in use']); exit; }
        mysqli_query($conn, "UPDATE users SET email='$emailEsc' WHERE user_id='$user_id'");
        $_SESSION['user_email'] = $email;
    }
    echo json_encode(['success'=>true]);
    exit;
}

$profileImgSrc = $profileImg ? '../' . $profileImg : '';
$savedMsg = $_GET['saved'] ?? '';

// Fetch extra fields from DB — safely handle missing columns
$website  = '';
$location = '';

// Try fetching all fields; fall back gracefully if columns don't exist yet
$colCheck = mysqli_query($conn, "SHOW COLUMNS FROM users");
$existingCols = [];
if ($colCheck) {
    while ($col = mysqli_fetch_assoc($colCheck)) {
        $existingCols[] = $col['Field'];
    }
}

// Add missing columns automatically
if (!in_array('website', $existingCols)) {
    mysqli_query($conn, "ALTER TABLE users ADD COLUMN website VARCHAR(255) DEFAULT '' AFTER bio");
}
if (!in_array('location', $existingCols)) {
    mysqli_query($conn, "ALTER TABLE users ADD COLUMN location VARCHAR(255) DEFAULT '' AFTER website");
}
if (!in_array('username', $existingCols)) {
    mysqli_query($conn, "ALTER TABLE users ADD COLUMN username VARCHAR(100) DEFAULT '' AFTER name");
}

// Now fetch safely
$userRes = mysqli_query($conn, "SELECT * FROM users WHERE user_id='$user_id'");
$userData = $userRes ? mysqli_fetch_assoc($userRes) : [];
$website  = htmlspecialchars($userData['website']  ?? '');
$location = htmlspecialchars($userData['location'] ?? '');
$username = htmlspecialchars($userData['username'] ?? $username);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Profile – Pinterest</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

  <style>
    :root {
      --pin-red: #E60023;
      --pin-red-dark: #ad081b;
      --bg: #f8f7f5;
      --nav-h: 64px;
      --border: #e0e0e0;
      --muted: #767676;
      --radius: 16px;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: #111; min-height: 100vh; }

    /* ── NAV ── */
    .pnav {
      position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
      height: var(--nav-h); background: #fff;
      box-shadow: 0 2px 12px rgba(0,0,0,.07);
      display: flex; align-items: center; gap: 12px; padding: 0 24px;
    }
    .pnav .logo { font-family: 'Playfair Display', serif; font-size: 32px; color: var(--pin-red); text-decoration: none; }
    .pnav .back-btn {
      display: flex; align-items: center; gap: 6px;
      background: none; border: none; font-size: .95rem;
      font-weight: 600; color: #111; cursor: pointer;
      padding: 8px 14px; border-radius: 24px;
      transition: background .2s; font-family: 'DM Sans', sans-serif;
    }
    .pnav .back-btn:hover { background: #f0eeeb; }
    .pnav .page-title-nav {
      font-family: 'Playfair Display', serif;
      font-size: 1.2rem; color: #111;
    }
    .pnav .nav-right { margin-left: auto; display: flex; gap: 8px; align-items: center; }

    /* ── MAIN LAYOUT ── */
    .main {
      max-width: 780px; margin: 0 auto;
      padding: calc(var(--nav-h) + 36px) 20px 60px;
    }

    /* ── TAB PILLS ── */
    .tab-pills {
      display: flex; gap: 8px; margin-bottom: 28px;
      background: #fff; padding: 6px; border-radius: 50px;
      box-shadow: 0 2px 10px rgba(0,0,0,.06); width: fit-content;
    }
    .tab-pill {
      background: none; border: none; border-radius: 50px;
      padding: 9px 22px; font-size: .9rem; font-weight: 600;
      color: var(--muted); cursor: pointer;
      transition: background .2s, color .2s;
      font-family: 'DM Sans', sans-serif;
    }
    .tab-pill.active { background: var(--pin-red); color: #fff; }

    /* ── CARD ── */
    .card-section {
      background: #fff; border-radius: 24px; padding: 32px;
      box-shadow: 0 2px 16px rgba(0,0,0,.06);
      display: none;
    }
    .card-section.active { display: block; }

    /* ── AVATAR ── */
    .avatar-zone {
      display: flex; align-items: center; gap: 24px;
      padding: 24px; background: var(--bg);
      border-radius: 16px; margin-bottom: 28px;
    }
    .avatar-circle {
      width: 90px; height: 90px; border-radius: 50%;
      object-fit: cover; flex-shrink: 0;
      border: 3px solid #fff;
      box-shadow: 0 4px 16px rgba(0,0,0,.12);
    }
    .avatar-initials {
      width: 90px; height: 90px; border-radius: 50%;
      background: var(--pin-red); color: #fff;
      display: flex; align-items: center; justify-content: center;
      font-size: 2.2rem; font-weight: 700; flex-shrink: 0;
      border: 3px solid #fff; box-shadow: 0 4px 16px rgba(0,0,0,.12);
    }
    .avatar-info h6 { font-weight: 700; font-size: 1rem; margin-bottom: 4px; }
    .avatar-info p { font-size: .82rem; color: var(--muted); margin-bottom: 12px; line-height: 1.5; }
    .btn-change-photo {
      background: #111; color: #fff; border: none;
      border-radius: 24px; padding: 9px 20px;
      font-size: .85rem; font-weight: 600; cursor: pointer;
      transition: background .2s; font-family: 'DM Sans', sans-serif;
      display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-change-photo:hover { background: #333; }

    /* ── FORM ── */
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 18px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 18px; }
    .form-group:last-child { margin-bottom: 0; }
    .form-label {
      font-size: .8rem; font-weight: 700; color: #111;
      text-transform: uppercase; letter-spacing: .04em;
    }
    .form-control {
      border: 2px solid var(--border); border-radius: 12px;
      padding: 12px 15px; font-size: .95rem;
      font-family: 'DM Sans', sans-serif; color: #111;
      outline: none; transition: border-color .2s, box-shadow .2s;
      background: #fff; width: 100%;
    }
    .form-control:focus {
      border-color: var(--pin-red);
      box-shadow: 0 0 0 3px rgba(230,0,35,.1);
    }
    textarea.form-control { resize: vertical; min-height: 96px; }
    .input-group-custom {
      display: flex; border: 2px solid var(--border);
      border-radius: 12px; overflow: hidden;
      transition: border-color .2s, box-shadow .2s;
    }
    .input-group-custom:focus-within {
      border-color: var(--pin-red);
      box-shadow: 0 0 0 3px rgba(230,0,35,.1);
    }
    .input-group-prefix {
      padding: 12px 14px; background: #f8f7f5;
      color: var(--muted); font-weight: 600; font-size: .9rem;
      border-right: 2px solid var(--border); white-space: nowrap;
    }
    .input-group-custom .form-control {
      border: none; border-radius: 0; box-shadow: none;
    }
    .input-group-custom .form-control:focus { box-shadow: none; }
    .char-count { font-size: .75rem; color: var(--muted); text-align: right; }

    /* ── DIVIDER ── */
    .section-divider {
      border: none; border-top: 1px solid #f0eeeb; margin: 24px 0;
    }
    .section-sub {
      font-family: 'Playfair Display', serif;
      font-size: 1.05rem; margin-bottom: 18px; color: #111;
    }

    /* ── PASSWORD ── */
    .pass-input-wrap { position: relative; }
    .pass-input-wrap .form-control { padding-right: 44px; }
    .pass-toggle {
      position: absolute; right: 14px; top: 50%;
      transform: translateY(-50%); background: none;
      border: none; color: var(--muted); cursor: pointer;
      font-size: 1rem; padding: 0;
    }
    .pass-strength { margin-top: 6px; }
    .strength-bar {
      height: 4px; border-radius: 2px;
      background: #e0e0e0; overflow: hidden;
    }
    .strength-fill {
      height: 100%; border-radius: 2px;
      transition: width .3s, background .3s;
      width: 0%;
    }
    .strength-label { font-size: .75rem; margin-top: 4px; }

    /* ── ACTIONS ── */
    .form-actions {
      display: flex; align-items: center; gap: 14px;
      margin-top: 28px; padding-top: 24px;
      border-top: 1px solid #f0eeeb;
    }
    .btn-save {
      background: var(--pin-red); color: #fff; border: none;
      border-radius: 24px; padding: 12px 32px;
      font-weight: 700; font-size: .95rem; cursor: pointer;
      transition: background .2s, transform .1s;
      font-family: 'DM Sans', sans-serif;
      display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-save:hover { background: var(--pin-red-dark); }
    .btn-save:active { transform: scale(.97); }
    .btn-save:disabled { background: #ccc; cursor: not-allowed; }
    .btn-cancel {
      background: none; border: 2px solid #ddd;
      border-radius: 24px; padding: 11px 24px;
      font-weight: 600; font-size: .9rem; cursor: pointer;
      color: #111; transition: border-color .2s, background .2s;
      font-family: 'DM Sans', sans-serif;
    }
    .btn-cancel:hover { border-color: #111; background: #f8f7f5; }

    /* ── TOAST ── */
    .toast-bar {
      position: fixed; bottom: 28px; left: 50%;
      transform: translateX(-50%) translateY(80px);
      background: #111; color: #fff; padding: 12px 24px;
      border-radius: 50px; font-weight: 600; font-size: .9rem;
      z-index: 9999; transition: transform .3s ease;
      white-space: nowrap; pointer-events: none;
      display: flex; align-items: center; gap: 8px;
    }
    .toast-bar.show { transform: translateX(-50%) translateY(0); }
    .toast-bar.error { background: var(--pin-red); }

    /* ── SUCCESS BANNER ── */
    .success-banner {
      display: flex; align-items: center; gap: 10px;
      background: #f0fdf4; border: 1.5px solid #86efac;
      border-radius: 12px; padding: 12px 18px;
      margin-bottom: 20px; font-size: .9rem; color: #166534;
      font-weight: 600;
    }

    @media (max-width: 560px) {
      .form-row { grid-template-columns: 1fr; }
      .avatar-zone { flex-direction: column; text-align: center; }
      .card-section { padding: 20px 16px; }
    }
  </style>
</head>
<body>

<!-- NAV -->
<nav class="pnav">
  <a href="../home.php" class="logo"><i class="fa-brands fa-pinterest"></i></a>
  <button class="back-btn" onclick="history.back()">
    <i class="bi bi-arrow-left"></i> Back
  </button>
  <span class="page-title-nav">Edit Profile</span>
</nav>

<div class="main">

  <?php if ($savedMsg === 'avatar'): ?>
  <div class="success-banner">
    <i class="bi bi-check-circle-fill"></i> Profile photo updated successfully!
  </div>
  <?php endif; ?>

  <!-- TAB PILLS -->
  <div class="tab-pills">
    <button class="tab-pill active" onclick="switchTab('profile', this)">
      <i class="bi bi-person me-1"></i> Profile
    </button>
    <button class="tab-pill" onclick="switchTab('account', this)">
      <i class="bi bi-shield-lock me-1"></i> Account
    </button>
  </div>

  <!--  PROFILE TAB  -->
  <div class="card-section active" id="tab-profile">

    <!-- Avatar -->
    <div class="avatar-zone">
      <form id="avatarForm" action="edit_profile.php" method="POST" enctype="multipart/form-data">
        <input type="file" id="avatarInput" name="avatar_upload" accept="image/*" style="display:none"
               onchange="this.form.submit()"/>
      </form>
      <?php if ($profileImgSrc): ?>
        <img src="<?= htmlspecialchars($profileImgSrc) ?>" alt="Avatar"
             class="avatar-circle" id="avatarPreview"/>
      <?php else: ?>
        <div class="avatar-initials" id="avatarPreview"><?= $firstLetter ?></div>
      <?php endif; ?>
      <div class="avatar-info">
        <h6>Profile photo</h6>
        <p>A great photo helps people recognise you.<br>JPG, PNG or GIF, max 5 MB.</p>
        <button class="btn-change-photo" onclick="document.getElementById('avatarInput').click()">
          <i class="bi bi-camera-fill"></i> Change photo
        </button>
      </div>
    </div>

    <!-- Profile form -->
    <form id="profileForm">
      <div class="form-row">
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label">First name</label>
          <input type="text" class="form-control" name="first_name"
                 value="<?= htmlspecialchars($firstName) ?>" placeholder="First name" required/>
        </div>
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label">Last name</label>
          <input type="text" class="form-control" name="last_name"
                 value="<?= htmlspecialchars($lastName) ?>" placeholder="Last name"/>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Username</label>
        <div class="input-group-custom">
          <span class="input-group-prefix">@</span>
          <input type="text" class="form-control" name="username"
                 value="<?= $username ?>" placeholder="yourname"/>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Bio</label>
        <textarea class="form-control" name="bio" maxlength="160"
                  id="bioInput" oninput="updateCharCount()"
                  placeholder="Tell people about yourself..."><?= htmlspecialchars($userBio) ?></textarea>
        <div class="char-count"><span id="charCount"><?= mb_strlen($userBio) ?></span>/160</div>
      </div>

      <div class="form-row">
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label">Website</label>
          <input type="url" class="form-control" name="website"
                 value="<?= $website ?>" placeholder="https://yoursite.com"/>
        </div>
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label">Location</label>
          <input type="text" class="form-control" name="location"
                 value="<?= $location ?>" placeholder="City, Country"/>
        </div>
      </div>

      <div class="form-actions">
        <button type="button" class="btn-save" id="saveProfileBtn" onclick="saveProfile()">
          <i class="bi bi-check-lg"></i> Save changes
        </button>
        <button type="button" class="btn-cancel" onclick="history.back()">Cancel</button>
        <span id="profileStatus" style="font-size:.85rem;color:var(--muted);"></span>
      </div>
    </form>
  </div>

  <!--  ACCOUNT TAB  -->
  <div class="card-section" id="tab-account">

    <h4 class="section-sub"><i class="bi bi-envelope me-2"></i>Email address</h4>
    <div class="form-group">
      <label class="form-label">Current email</label>
      <input type="email" class="form-control" id="emailInput"
             value="<?= htmlspecialchars($userEmail) ?>" placeholder="you@example.com"/>
    </div>

    <hr class="section-divider"/>

    <h4 class="section-sub"><i class="bi bi-lock me-2"></i>Change password</h4>
    <p style="font-size:.85rem;color:var(--muted);margin-bottom:18px;">Leave blank if you don't want to change your password.</p>

    <div class="form-group">
      <label class="form-label">Current password</label>
      <div class="pass-input-wrap">
        <input type="password" class="form-control" id="currentPass" placeholder="Enter current password"/>
        <button class="pass-toggle" onclick="togglePass('currentPass', this)" type="button">
          <i class="bi bi-eye"></i>
        </button>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">New password</label>
      <div class="pass-input-wrap">
        <input type="password" class="form-control" id="newPass"
               placeholder="Min 8 characters" oninput="checkStrength(this.value)"/>
        <button class="pass-toggle" onclick="togglePass('newPass', this)" type="button">
          <i class="bi bi-eye"></i>
        </button>
      </div>
      <div class="pass-strength" id="strengthWrap" style="display:none;">
        <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
        <div class="strength-label" id="strengthLabel"></div>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">Confirm new password</label>
      <div class="pass-input-wrap">
        <input type="password" class="form-control" id="confirmPass" placeholder="Repeat new password"/>
        <button class="pass-toggle" onclick="togglePass('confirmPass', this)" type="button">
          <i class="bi bi-eye"></i>
        </button>
      </div>
    </div>

    <div class="form-actions">
      <button type="button" class="btn-save" id="saveAccountBtn" onclick="saveAccount()">
        <i class="bi bi-check-lg"></i> Update account
      </button>
      <button type="button" class="btn-cancel" onclick="history.back()">Cancel</button>
      <span id="accountStatus" style="font-size:.85rem;color:var(--muted);"></span>
    </div>

  </div>
</div>

<!-- TOAST -->
<div class="toast-bar" id="toastBar"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  //  Tab switch 
  function switchTab(name, btn) {
    document.querySelectorAll('.tab-pill').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.card-section').forEach(s => s.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('tab-' + name).classList.add('active');
  }

  // ── Character counter ──
  function updateCharCount() {
    document.getElementById('charCount').textContent =
      document.getElementById('bioInput').value.length;
  }

  // ── Password toggle 
  function togglePass(id, btn) {
    var input = document.getElementById(id);
    var show  = input.type === 'password';
    input.type = show ? 'text' : 'password';
    btn.innerHTML = show ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
  }

  // ── Password strength ──
  function checkStrength(val) {
    var wrap  = document.getElementById('strengthWrap');
    var fill  = document.getElementById('strengthFill');
    var label = document.getElementById('strengthLabel');
    if (!val) { wrap.style.display = 'none'; return; }
    wrap.style.display = 'block';
    var score = 0;
    if (val.length >= 8)           score++;
    if (/[A-Z]/.test(val))         score++;
    if (/[0-9]/.test(val))         score++;
    if (/[^A-Za-z0-9]/.test(val))  score++;
    var levels = [
      { pct:'25%', color:'#ef4444', text:'Weak' },
      { pct:'50%', color:'#f97316', text:'Fair' },
      { pct:'75%', color:'#eab308', text:'Good' },
      { pct:'100%', color:'#22c55e', text:'Strong' },
    ];
    var l = levels[score - 1] || levels[0];
    fill.style.width     = l.pct;
    fill.style.background = l.color;
    label.textContent    = l.text;
    label.style.color    = l.color;
  }

  // ── Toast ──
  function showToast(msg, isError) {
    var t = document.getElementById('toastBar');
    t.textContent = msg;
    t.className   = 'toast-bar' + (isError ? ' error' : '');
    t.classList.add('show');
    setTimeout(function() { t.classList.remove('show'); }, 3000);
  }

  // ── Save profile ──
  function saveProfile() {
    var btn = document.getElementById('saveProfileBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Saving...';

    var form = document.getElementById('profileForm');
    var data = new FormData(form);
    data.append('action', 'save_profile');

    fetch('edit_profile.php', { method: 'POST', body: data })
      .then(r => r.json())
      .then(function(res) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg"></i> Save changes';
        if (res.success) {
          showToast(' Profile updated!');
        } else {
          showToast(res.message || 'Something went wrong', true);
        }
      })
      .catch(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg"></i> Save changes';
        showToast('Network error. Try again.', true);
      });
  }

  // ── Save account ──
  function saveAccount() {
    var newPass     = document.getElementById('newPass').value;
    var confirmPass = document.getElementById('confirmPass').value;
    if (newPass && newPass !== confirmPass) {
      showToast('Passwords do not match', true);
      return;
    }

    var btn = document.getElementById('saveAccountBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Saving...';

    var data = new FormData();
    data.append('action',       'save_account');
    data.append('email',        document.getElementById('emailInput').value);
    data.append('current_pass', document.getElementById('currentPass').value);
    data.append('new_pass',     newPass);

    fetch('edit_profile.php', { method: 'POST', body: data })
      .then(r => r.json())
      .then(function(res) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg"></i> Update account';
        if (res.success) {
          showToast(' Account updated!');
          document.getElementById('currentPass').value = '';
          document.getElementById('newPass').value     = '';
          document.getElementById('confirmPass').value = '';
          document.getElementById('strengthWrap').style.display = 'none';
        } else {
          showToast(res.message || 'Something went wrong', true);
        }
      })
      .catch(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg"></i> Update account';
        showToast('Network error. Try again.', true);
      });
  }
</script>
</body>
</html>