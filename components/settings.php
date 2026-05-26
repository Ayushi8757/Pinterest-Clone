<?php
session_start();

//  DANGER ZONE: handle deactivate / delete 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['danger_action'])) {

  $dsn    = 'mysql:host=localhost;dbname=pinterest;charset=utf8mb4';
$dbUser = 'root';
$dbPass = 'root@123#';

    try {
        $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    } catch (PDOException $e) {
        die(json_encode(['success' => false, 'message' => 'DB connection failed.']));
    }

    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        die(json_encode(['success' => false, 'message' => 'Not authenticated.']));
    }

    $action = $_POST['danger_action'];

    if ($action === 'deactivate') {
        // Find real primary key
        $pkCol = 'id';
        $cols  = $pdo->query("DESCRIBE users")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cols as $col) {
            if ($col['Key'] === 'PRI') { $pkCol = $col['Field']; break; }
        }
        $stmt = $pdo->prepare("UPDATE users SET is_active = 0, deactivated_at = NOW() WHERE {$pkCol} = ?");
        $stmt->execute([$userId]);
        session_destroy();
        echo json_encode(['success' => true, 'redirect' => '../index.php?msg=deactivated']);
        exit;

    } elseif ($action === 'delete') {

        // ── Step 1: Find the real primary key column name of users table ──
        $pkCol = 'id'; // default
        $cols  = $pdo->query("DESCRIBE users")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cols as $col) {
            if ($col['Key'] === 'PRI') { $pkCol = $col['Field']; break; }
        }

        // ── Step 2: Find all column names that exist in users table
        $existingCols = array_column($cols, 'Field');
        function colExists($name, $list) { return in_array($name, $list); }

        $pdo->beginTransaction();
        try {
            // Build UPDATE dynamically — only set columns that exist
            $sets = [];
            $vals = [];
            $sets[] = "name = 'Deleted User'";
            if (colExists('email',         $existingCols)) { $sets[] = "email = CONCAT('deleted_', {$pkCol}, '@pinterest.invalid')"; }
            if (colExists('password',      $existingCols)) { $sets[] = "password = ''"; }
            if (colExists('password_hash', $existingCols)) { $sets[] = "password_hash = ''"; }
            if (colExists('bio',           $existingCols)) { $sets[] = "bio = NULL"; }
            if (colExists('website',       $existingCols)) { $sets[] = "website = NULL"; }
            if (colExists('location',      $existingCols)) { $sets[] = "location = NULL"; }
            if (colExists('profile_image', $existingCols)) { $sets[] = "profile_image = NULL"; }
            if (colExists('profile_pic',   $existingCols)) { $sets[] = "profile_pic = NULL"; }
            if (colExists('avatar',        $existingCols)) { $sets[] = "avatar = NULL"; }
            if (colExists('is_active',     $existingCols)) { $sets[] = "is_active = 0"; }
            if (colExists('is_deleted',    $existingCols)) { $sets[] = "is_deleted = 1"; }
            if (colExists('deleted_at',    $existingCols)) { $sets[] = "deleted_at = NOW()"; }

            $sql = "UPDATE users SET " . implode(', ', $sets) . " WHERE {$pkCol} = ?";
            $pdo->prepare($sql)->execute([$userId]);

            // followers: columns are user_id and following_user_id
            $pdo->prepare("DELETE FROM followers WHERE user_id = ? OR following_user_id = ?")->execute([$userId, $userId]);

            // comments
            $pdo->prepare("DELETE FROM comments WHERE user_id = ?")->execute([$userId]);

            // likes
            $pdo->prepare("DELETE FROM likes WHERE user_id = ?")->execute([$userId]);

            // saved_pins
            $pdo->prepare("DELETE FROM saved_pins WHERE user_id = ?")->execute([$userId]);

            // notifications: only has user_id 
            $pdo->prepare("DELETE FROM notifications WHERE user_id = ?")->execute([$userId]);

            // messages: sender_id and receiver_id
            $pdo->prepare("DELETE FROM messages WHERE sender_id = ? OR receiver_id = ?")->execute([$userId, $userId]);

            // reports
            $pdo->prepare("DELETE FROM reports WHERE user_id = ?")->execute([$userId]);

            // board_pins first (FK), then boards
            $pdo->prepare("DELETE bp FROM board_pins bp INNER JOIN boards b ON bp.board_id = b.board_id WHERE b.user_id = ?")->execute([$userId]);
            $pdo->prepare("DELETE FROM boards WHERE user_id = ?")->execute([$userId]);

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Deletion failed: ' . $e->getMessage()]);
            exit;
        }
        session_destroy();
        echo json_encode(['success' => true, 'redirect' => '../index.php?msg=deleted']);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

// ── AVATAR UPLOAD 
if (isset($_FILES['avatar_upload']) && $_FILES['avatar_upload']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../uploads/profiles/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);
    $ext      = pathinfo($_FILES['avatar_upload']['name'], PATHINFO_EXTENSION);
    $fileName = 'user_' . ($_SESSION['user_id'] ?? 'x') . '_' . time() . '.' . $ext;
    $dest     = $uploadDir . $fileName;
    if (move_uploaded_file($_FILES['avatar_upload']['tmp_name'], $dest)) {
        $_SESSION['profile_image'] = '../uploads/profiles/' . $fileName;
    }
    header('Location: settings.php#profile');
    exit;
}

$profileImg   = $_SESSION['profile_image'] ?? '';
$profileName  = htmlspecialchars($_SESSION['user_name']  ?? 'User');
$profileEmail = htmlspecialchars($_SESSION['user_email'] ?? '');
$firstLetter  = strtoupper(mb_substr($profileName, 0, 1));
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pinterest – Settings</title>

  <!-- ① Theme init — MUST be first script to avoid flash -->
  <script>
  (function () {
      const saved      = localStorage.getItem('pb_theme') || 'light';
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      const resolved   = saved === 'system' ? (prefersDark ? 'dark' : 'light') : saved;
      document.documentElement.setAttribute('data-theme', resolved);
      // Font size
      const fs = localStorage.getItem('pb_fontSize');
      if (fs) document.documentElement.style.fontSize = fs + 'px';
  })();
  </script>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<?php require_once __DIR__ . "/app_shell.php"; ?>
  <link rel="stylesheet" href="../css/settings.css" />
  <link rel="stylesheet" href="../css/app-shell.css" />
</head>
<body>

<?php render_app_shell([
    'scope' => 'components',
    'active' => 'settings',
]); ?>

<div class="main">
  <!-- Sidebar -->
  <div class="settings-sidebar">
    <div class="page-title">Settings</div>
    <nav>
      <a href="edit_profile.php"><i class="bi bi-person"></i> Profile</a>
      <a href="#account"       onclick="showSection('account',this)"><i class="bi bi-shield-lock"></i> Account</a>
      <a href="#notifications" onclick="showSection('notifications',this)"><i class="bi bi-bell"></i> Notifications</a>
      <a href="#privacy"       onclick="showSection('privacy',this)"><i class="bi bi-eye-slash"></i> Privacy</a>
      <a href="#danger"        onclick="showSection('danger',this)" style="color:#E60023;margin-top:12px;"><i class="bi bi-exclamation-triangle"></i> Danger zone</a>
    </nav>
  </div>

  <!-- Content -->
  <div class="settings-content">

    <!-- PROFILE -->
    <div id="section-profile" class="settings-section" style="display:none">
      <div class="settings-card">
        <h5><i class="bi bi-person-circle me-2"></i>Edit Profile</h5>
        <div class="text-center mb-4">
          <?php if ($profileImg): ?>
            <img src="<?= htmlspecialchars($profileImg) ?>" alt="Avatar" id="settingsAvatar" class="rounded-circle" style="width:80px;height:80px;object-fit:cover;"/>
          <?php else: ?>
            <div id="settingsAvatar" class="rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width:80px;height:80px;background:#E60023;color:#fff;font-size:2rem;font-weight:700;"><?= $firstLetter ?></div>
          <?php endif; ?>
          <div class="mt-2">
            <form id="avatarSettingsForm" action="settings.php" method="POST" enctype="multipart/form-data" style="display:inline;">
              <input type="file" id="avatarPick" name="avatar_upload" accept="image/*" style="display:none" onchange="document.getElementById('avatarSettingsForm').submit()"/>
            </form>
            <button class="btn btn-sm btn-outline-secondary rounded-pill" onclick="document.getElementById('avatarPick').click()"><i class="bi bi-camera"></i> Change photo</button>
          </div>
        </div>
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label fw-500">First name</label><input type="text" class="form-control" value="<?= htmlspecialchars(explode(' ', $profileName)[0]) ?>"/></div>
          <div class="col-md-6"><label class="form-label fw-500">Last name</label><input type="text" class="form-control" value="<?= htmlspecialchars(implode(' ', array_slice(explode(' ', $profileName), 1))) ?>"/></div>
          <div class="col-12"><label class="form-label fw-500">Username</label><div class="input-group"><span class="input-group-text rounded-start-pill">@</span><input type="text" class="form-control rounded-end-pill" value="aaravsharma"/></div></div>
          <div class="col-12"><label class="form-label fw-500">Bio</label><textarea class="form-control" rows="3">✨ Design lover · Food explorer · Based in Agra, India</textarea></div>
          <div class="col-md-6"><label class="form-label fw-500">Website</label><input type="url" class="form-control" placeholder="https://yoursite.com"/></div>
          <div class="col-md-6"><label class="form-label fw-500">Location</label><input type="text" class="form-control" value="Agra, India"/></div>
        </div>
        <div class="mt-4"><button class="btn-pin" onclick="saveSuccess()">Save changes</button></div>
      </div>
    </div>

    <!-- ACCOUNT -->
    <div id="section-account" class="settings-section" style="display:none">
      <div class="settings-card">
        <h5><i class="bi bi-envelope me-2"></i>Email & Password</h5>
        <div class="mb-3"><label class="form-label fw-500">Email address</label><input type="email" class="form-control" value="<?= $profileEmail ?>"/></div>
        <div class="mb-3"><label class="form-label fw-500">Current password</label><input type="password" class="form-control" placeholder="••••••••"/></div>
        <div class="mb-3"><label class="form-label fw-500">New password</label><input type="password" class="form-control" placeholder="Min 8 characters"/></div>
        <div class="mb-4"><label class="form-label fw-500">Confirm new password</label><input type="password" class="form-control" placeholder="Repeat new password"/></div>
        <button class="btn-pin" onclick="saveSuccess()">Update account</button>
      </div>
    </div>

    <!-- NOTIFICATIONS -->
    <div id="section-notifications" class="settings-section" style="display:none">
      <div class="settings-card">
        <h5><i class="bi bi-bell me-2"></i>Notification Preferences</h5>
        <div class="toggle-row"><div><div class="label">New followers</div><div class="desc">When someone follows you</div></div><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" checked/></div></div>
        <div class="toggle-row"><div><div class="label">Pin saves</div><div class="desc">When someone saves your pin</div></div><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" checked/></div></div>
        <div class="toggle-row"><div><div class="label">Comments</div><div class="desc">When someone comments on your pin</div></div><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" checked/></div></div>
        <div class="toggle-row"><div><div class="label">Mentions</div><div class="desc">When someone mentions you</div></div><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox"/></div></div>
        <div class="toggle-row"><div><div class="label">Weekly digest email</div><div class="desc">Top ideas from your interests</div></div><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" checked/></div></div>
        <div class="toggle-row"><div><div class="label">Marketing emails</div><div class="desc">Promotions and new features</div></div><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox"/></div></div>
        <div class="mt-3"><button class="btn-pin" onclick="saveSuccess()">Save preferences</button></div>
      </div>
    </div>

    <!-- PRIVACY -->
    <div id="section-privacy" class="settings-section" style="display:none">
      <div class="settings-card">
        <h5><i class="bi bi-eye-slash me-2"></i>Privacy & Data</h5>
        <div class="toggle-row"><div><div class="label">Private profile</div><div class="desc">Only approved followers see your pins</div></div><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox"/></div></div>
        <div class="toggle-row"><div><div class="label">Personalised ads</div><div class="desc">Use my activity to show relevant ads</div></div><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" checked/></div></div>
        <div class="toggle-row"><div><div class="label">Search engine indexing</div><div class="desc">Allow search engines to show your profile</div></div><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" checked/></div></div>
        <div class="toggle-row"><div><div class="label">Show active status</div><div class="desc">Let others see when you were last active</div></div><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" checked/></div></div>
        <div class="mt-3"><button class="btn btn-outline-secondary rounded-pill me-2">Download my data</button><button class="btn-pin" onclick="saveSuccess()">Save</button></div>
      </div>
    </div>

    
    <!-- DANGER ZONE -->
    <div id="section-danger" class="settings-section" style="display:none">
      <div class="danger-zone">
        <h5><i class="bi bi-exclamation-triangle me-2"></i>Danger Zone</h5>
        <p class="text-muted">These actions are serious. Please read carefully before proceeding.</p>

        <!-- <div class="d-flex justify-content-between align-items-center p-3 dz-row rounded-3 mb-3">
          <div>
            <div class="fw-600">Deactivate account</div>
            <small class="text-muted">Temporarily hides your profile &amp; pins.<br>Reactivates automatically when you log back in.</small>
          </div>
          <button class="btn btn-outline-warning rounded-pill px-4" onclick="openDeactivateModal()">Deactivate</button>
        </div> -->

        <div class="d-flex justify-content-between align-items-center p-3 dz-row rounded-3">
          <div>
            <div class="fw-600 text-danger">Delete account</div>
            <small class="text-muted">Permanently removes your personal info.<br>Your pins stay in feeds, posted anonymously.</small>
          </div>
          <button class="btn btn-outline-danger rounded-pill px-4" onclick="openDeleteModal()">Delete</button>
        </div>
      </div>
    </div>

  </div><!-- /settings-content -->
</div><!-- /main -->

<!-- ── DEACTIVATE MODAL ─────────────────────────────────────────────────── -->
<div class="danger-modal-overlay" id="deactivateOverlay" onclick="closeModal('deactivateOverlay')">
  <div class="danger-modal" onclick="event.stopPropagation()">
    <h4>⏸ Deactivate your account?</h4>
    <p>Your profile, boards, and pins will be hidden from everyone. Come back anytime — just log in and everything will be restored automatically.</p>
    <div class="modal-actions">
      <button class="modal-cancel" onclick="closeModal('deactivateOverlay')">Cancel</button>
      <button class="modal-confirm-deact" id="deactivateBtn" onclick="submitDangerAction('deactivate')">Yes, deactivate</button>
    </div>
  </div>
</div>

<!-- ── DELETE MODAL ────────────────────────────────────────────────────── -->
<div class="danger-modal-overlay" id="deleteOverlay" onclick="closeModal('deleteOverlay')">
  <div class="danger-modal" onclick="event.stopPropagation()">
    <h4 style="color:var(--pin-red)">🗑 Permanently delete account?</h4>
    <p>This will <strong>erase your personal information</strong> (name, email, bio, profile photo) and remove you from all follower lists.<br><br>
       Your pins will remain visible in feeds — they'll just appear as posted by a deleted user.</p>
    <div class="confirm-input">
      <label>Type <strong>DELETE</strong> to confirm</label>
      <input type="text" id="deleteConfirmInput" placeholder="DELETE" oninput="checkDeleteWord(this.value)">
    </div>
    <div class="modal-actions">
      <button class="modal-cancel" onclick="closeModal('deleteOverlay')">Cancel</button>
      <button class="modal-confirm-del" id="deleteBtn" disabled onclick="submitDangerAction('delete')">Delete my account</button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  /* ── Section switcher ───────────────────────────────────────── */
  function showSection(id, link) {
      document.querySelectorAll('.settings-section').forEach(s => s.style.display = 'none');
      document.querySelectorAll('.settings-sidebar nav a').forEach(a => a.classList.remove('active'));
      document.getElementById('section-' + id).style.display = 'block';
      link.classList.add('active');
      if (id === 'appearance') initAppearanceSection();
  }

  /* ── Theme ──────────────────────────────────────────────────── */
  function setTheme(theme) {
      localStorage.setItem('pb_theme', theme);
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      const resolved    = theme === 'system' ? (prefersDark ? 'dark' : 'light') : theme;
      document.documentElement.setAttribute('data-theme', resolved);
      // Update active button styles
      ['light','dark','system'].forEach(function(t) {
          var btn = document.getElementById('theme-' + t);
          if (!btn) return;
          if (t === theme) {
              btn.style.borderColor = '#E60023';
              btn.style.boxShadow   = '0 0 0 3px rgba(230,0,35,.12)';
              btn.style.background  = 'var(--surface)';
          } else {
              btn.style.borderColor = 'var(--border)';
              btn.style.boxShadow   = 'none';
              btn.style.background  = 'var(--surface-2)';
          }
      });
  }

  /* ── Font size ──────────────────────────────────────────────── */
  function previewFontSize(val) {
      document.documentElement.style.fontSize = val + 'px';
      document.getElementById('fontSizeLabel').textContent = val + 'px';
  }

  /* ── Init appearance section (restore saved prefs) ──────────── */
  function initAppearanceSection() {
      // Theme
      const saved = localStorage.getItem('pb_theme') || 'light';
      setTheme(saved); // this also highlights the right button

      // Font size
      const fs    = localStorage.getItem('pb_fontSize') || '15';
      const range = document.getElementById('fontRange');
      const label = document.getElementById('fontSizeLabel');
      if (range) range.value = fs;
      if (label) label.textContent = fs + 'px';

      // Language
      const lang = localStorage.getItem('pb_lang') || 'en';
      const sel  = document.getElementById('langSelect');
      if (sel) sel.value = lang;
  }

  /* ── Save all appearance*/
  function saveAppearance() {
      const fs   = document.getElementById('fontRange').value;
      const lang = document.getElementById('langSelect').value;
      localStorage.setItem('pb_fontSize', fs);
      localStorage.setItem('pb_lang', lang);
      document.documentElement.style.fontSize = fs + 'px';
      const saved = document.getElementById('appearanceSaved');
      saved.style.display = 'inline';
      setTimeout(() => saved.style.display = 'none', 2500);
  }

  /* Misc */
  function saveSuccess() { alert(' Changes saved successfully!'); }

  /* Danger zone modals  */
  
  function openDeleteModal() {
      document.getElementById('deleteOverlay').classList.add('show');
      document.getElementById('deleteConfirmInput').value = '';
      document.getElementById('deleteBtn').disabled = true;
  }
  function closeModal(id) {
      document.getElementById(id).classList.remove('show');
  }
  function checkDeleteWord(val) {
      document.getElementById('deleteBtn').disabled = val.trim().toUpperCase() !== 'DELETE';
  }

  async function submitDangerAction(action) {
      const btnId = action === 'deactivate' ? 'deactivateBtn' : 'deleteBtn';
      const btn   = document.getElementById(btnId);
      btn.disabled    = true;
      btn.textContent = 'Please wait…';

      try {
          const res  = await fetch('settings.php', {
              method:  'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body:    `danger_action=${action}`
          });
          const data = await res.json();
          if (data.success) {
              window.location.href = data.redirect;
          } else {
              alert('Error: ' + (data.message || 'Something went wrong.'));
              btn.disabled    = false;
              btn.textContent = action === 'deactivate' ? 'Yes, deactivate' : 'Delete my account';
          }
      } catch (e) {
          alert('Network error. Please try again.');
          btn.disabled    = false;
          btn.textContent = action === 'deactivate' ? 'Yes, deactivate' : 'Delete my account';
      }
  }
</script>
</body>
<?php include "footer.php"; ?>


