<?php
/* ============================================================
   settings.php  –  Appearance & Danger Zone sections
   Drop these blocks in place of the existing ones.
   Also paste the <style> additions, the theme-init <script>,
   and the bottom <script> block into your page.
   ============================================================ */

/* ── PHP at top of file (add after session_start) ─────────── */
?>

<?php
// ── DANGER ZONE: handle deactivate / delete ────────────────────────────────
// Place this block near the top of settings.php, after session_start()

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['danger_action'])) {

    // Minimal DB helper — adjust DSN / credentials to match your project
    $dsn = 'mysql:host=localhost;dbname=YOUR_DB;charset=utf8mb4';
    $dbUser = 'YOUR_DB_USER';
    $dbPass = 'YOUR_DB_PASS';

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
        /*
         * Sets is_active = 0 on the users table.
         * The user can reactivate by logging in again (handle in login.php).
         * Pins are untouched — they remain visible to others.
         */
        $stmt = $pdo->prepare('UPDATE users SET is_active = 0, deactivated_at = NOW() WHERE id = ?');
        $stmt->execute([$userId]);

        session_destroy();
        echo json_encode(['success' => true, 'redirect' => '../index.php?msg=deactivated']);
        exit;

    } elseif ($action === 'delete') {
        /*
         * GDPR-style soft anonymisation:
         *   1. Nullify / anonymise PII in users table.
         *   2. Keep the pins row intact; just set user_id = NULL (or a
         *      dedicated "deleted_user" placeholder id = 0).
         *   3. Hard-delete sessions, followers, comments referencing the user.
         *
         * Adjust table / column names to your schema.
         */

        $pdo->beginTransaction();
        try {
            // 1. Anonymise the user row (keeps id so FK references don't break)
            $pdo->prepare('
                UPDATE users SET
                    name          = "Deleted User",
                    email         = CONCAT("deleted_", id, "@pinboard.invalid"),
                    password_hash = "",
                    bio           = NULL,
                    website       = NULL,
                    location      = NULL,
                    profile_image = NULL,
                    is_active     = 0,
                    is_deleted    = 1,
                    deleted_at    = NOW()
                WHERE id = ?
            ')->execute([$userId]);

            // 2. Pins stay; just detach from the user (optional: set to ghost id)
            // If you want pins completely anonymous uncomment the next line:
            // $pdo->prepare('UPDATE pins SET user_id = NULL WHERE user_id = ?')->execute([$userId]);

            // 3. Remove social graph & comments
            $pdo->prepare('DELETE FROM followers  WHERE follower_id = ? OR following_id = ?')->execute([$userId, $userId]);
            $pdo->prepare('DELETE FROM comments   WHERE user_id = ?')->execute([$userId]);
            $pdo->prepare('DELETE FROM pin_saves  WHERE user_id = ?')->execute([$userId]);
            $pdo->prepare('DELETE FROM user_sessions WHERE user_id = ?')->execute([$userId]);

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
?>

<!-- ════════════════════════════════════════════════════════════
     THEME INIT — paste this as the VERY FIRST <script> in <head>
     (before any CSS loads) to prevent flash of wrong theme
     ═══════════════════════════════════════════════════════════ -->
<script>
(function () {
    const saved = localStorage.getItem('pb_theme') || 'light';
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const resolved = saved === 'system' ? (prefersDark ? 'dark' : 'light') : saved;
    document.documentElement.setAttribute('data-theme', resolved);
})();
</script>

<!-- ════════════════════════════════════════════════════════════
     EXTRA CSS — add inside your existing <style> block
     ═══════════════════════════════════════════════════════════ -->
<style>
/* ── Global theme tokens ───────────────────────────────────── */
:root,
[data-theme="light"] {
    --bg:           #f8f7f5;
    --surface:      #ffffff;
    --surface-2:    #f0eeeb;
    --text-primary: #111111;
    --text-muted:   #767676;
    --border:       #e0e0e0;
    --shadow:       rgba(0,0,0,.07);
    --nav-bg:       #ffffff;
    --card-bg:      #ffffff;
    --input-bg:     #ffffff;
    --toggle-track: #e0e0e0;
}
[data-theme="dark"] {
    --bg:           #18181b;
    --surface:      #27272a;
    --surface-2:    #3f3f46;
    --text-primary: #fafafa;
    --text-muted:   #a1a1aa;
    --border:       #3f3f46;
    --shadow:       rgba(0,0,0,.4);
    --nav-bg:       #1c1c1f;
    --card-bg:      #27272a;
    --input-bg:     #3f3f46;
    --toggle-track: #52525b;
}

/* Wire the existing components to tokens */
body                { background: var(--bg); color: var(--text-primary); transition: background .3s, color .3s; }
.pnav               { background: var(--nav-bg) !important; box-shadow: 0 2px 12px var(--shadow); }
.pnav .nav-actions a{ color: var(--text-primary) !important; }
.pnav .nav-actions a:hover { background: var(--surface-2) !important; }
.settings-sidebar nav a { color: var(--text-primary); }
.settings-sidebar nav a:hover,
.settings-sidebar nav a.active { background: var(--surface) !important; box-shadow: 0 2px 8px var(--shadow); }
.settings-card      { background: var(--card-bg) !important; box-shadow: 0 2px 12px var(--shadow); }
.settings-card h5   { border-color: var(--border) !important; }
.form-control,
select.form-control { background: var(--input-bg) !important; border-color: var(--border) !important; color: var(--text-primary) !important; }
.form-control::placeholder { color: var(--text-muted); }
.toggle-row         { border-color: var(--surface-2) !important; }
.toggle-row .desc   { color: var(--text-muted); }
.input-group-text   { background: var(--surface-2); border-color: var(--border); color: var(--text-primary); }
.text-muted         { color: var(--text-muted) !important; }
.danger-zone        { background: var(--surface) !important; border-color: #ffd0d6 !important; }
.danger-zone .bg-white { background: var(--surface-2) !important; }

/* ── Appearance theme-card picker ─────────────────────────── */
.theme-card {
    flex: 1;
    cursor: pointer;
    border-radius: 16px;
    padding: 20px 10px 16px;
    text-align: center;
    border: 2px solid var(--border);
    background: var(--surface-2);
    transition: border-color .2s, transform .15s, box-shadow .2s;
    user-select: none;
}
.theme-card:hover  { transform: translateY(-2px); box-shadow: 0 6px 18px var(--shadow); }
.theme-card.active { border-color: var(--pin-red) !important; background: var(--surface); box-shadow: 0 0 0 3px rgba(230,0,35,.12); }
.theme-card i      { font-size: 1.7rem; display: block; margin-bottom: 8px; }
.theme-card small  { font-weight: 600; font-size: .85rem; }

/* ── Danger zone modal overlay ─────────────────────────────── */
.danger-modal-overlay {
    display: none;
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,.55);
    backdrop-filter: blur(4px);
    align-items: center; justify-content: center;
}
.danger-modal-overlay.show { display: flex; animation: fadeIn .2s ease; }
.danger-modal {
    background: var(--card-bg);
    border-radius: 20px;
    padding: 32px 28px;
    max-width: 420px; width: 90%;
    box-shadow: 0 24px 60px rgba(0,0,0,.25);
    animation: slideUp .25s ease;
}
.danger-modal h4 { font-family:'Playfair Display',serif; font-size:1.3rem; margin-bottom:8px; }
.danger-modal p  { color: var(--text-muted); font-size:.92rem; line-height:1.6; }
.confirm-input   { margin-top:16px; }
.confirm-input label { font-size:.85rem; font-weight:600; margin-bottom:6px; display:block; color:var(--text-muted); }
.confirm-input input { width:100%; border:1.5px solid var(--border); border-radius:10px; padding:10px 14px; background:var(--input-bg); color:var(--text-primary); font-size:.95rem; outline:none; transition:border-color .2s; }
.confirm-input input:focus { border-color: var(--pin-red); }
.modal-actions { display:flex; gap:10px; margin-top:20px; }
.modal-actions button { flex:1; padding:11px; border-radius:24px; font-weight:700; font-size:.92rem; border:none; cursor:pointer; transition:background .2s, opacity .2s; }
.modal-cancel  { background: var(--surface-2); color: var(--text-primary); }
.modal-cancel:hover { opacity:.8; }
.modal-confirm-deact { background: #ff8c00; color: #fff; }
.modal-confirm-deact:hover { background: #e07800; }
.modal-confirm-del  { background: var(--pin-red); color: #fff; }
.modal-confirm-del:hover  { background: var(--pin-red-dark); }
.modal-confirm-del:disabled,
.modal-confirm-deact:disabled { opacity:.4; cursor:not-allowed; }
@keyframes fadeIn  { from{opacity:0} to{opacity:1} }
@keyframes slideUp { from{transform:translateY(30px);opacity:0} to{transform:translateY(0);opacity:1} }
</style>

<!-- ════════════════════════════════════════════════════════════
     APPEARANCE SECTION  (replace existing #section-appearance)
     ═══════════════════════════════════════════════════════════ -->
<div id="section-appearance" class="settings-section" style="display:none">
  <div class="settings-card">
    <h5><i class="bi bi-palette me-2"></i>Appearance</h5>

    <!-- Theme picker -->
    <div class="mb-4">
      <label class="form-label fw-500">Theme</label>
      <div class="d-flex gap-3 mt-2" id="themePicker">
        <div class="theme-card" data-theme="light" onclick="setTheme('light',this)">
          <i class="bi bi-sun"></i>
          <small>Light</small>
        </div>
        <div class="theme-card" data-theme="dark" onclick="setTheme('dark',this)">
          <i class="bi bi-moon-stars"></i>
          <small>Dark</small>
        </div>
        <div class="theme-card" data-theme="system" onclick="setTheme('system',this)">
          <i class="bi bi-circle-half"></i>
          <small>System</small>
        </div>
      </div>
    </div>

    <!-- Language -->
    <div class="mb-4">
      <label class="form-label fw-500">Language</label>
      <select class="form-control" id="langSelect" onchange="saveLanguage(this.value)">
        <option value="en">English</option>
        <option value="hi">हिंदी (Hindi)</option>
        <option value="es">Español</option>
        <option value="fr">Français</option>
        <option value="de">Deutsch</option>
        <option value="ja">日本語</option>
        <option value="pt">Português</option>
      </select>
    </div>

    <!-- Font size -->
    <div class="mb-4">
      <label class="form-label fw-500">Text size</label>
      <div class="d-flex align-items-center gap-3 mt-1">
        <span style="font-size:.8rem;color:var(--text-muted)">A</span>
        <input type="range" class="form-range flex-1" id="fontRange" min="13" max="18" step="1" value="15"
               style="accent-color:var(--pin-red);" oninput="setFontSize(this.value)">
        <span style="font-size:1.2rem;color:var(--text-muted)">A</span>
        <span id="fontSizeLabel" style="min-width:28px;text-align:right;font-size:.85rem;color:var(--text-muted)">15px</span>
      </div>
    </div>

    <button class="btn-pin" onclick="saveAppearance()">Save appearance</button>
    <span id="appearanceSaved" style="display:none;margin-left:12px;color:green;font-size:.9rem;"><i class="bi bi-check-circle"></i> Saved</span>
  </div>
</div>

<!-- DANGER ZONE SECTION  -->
<div id="section-danger" class="settings-section" style="display:none">
  <div class="danger-zone">
    <h5><i class="bi bi-exclamation-triangle me-2"></i>Danger Zone</h5>
    <p class="text-muted">These actions are permanent and cannot be undone. Please read carefully before proceeding.</p>

    <!-- Deactivate -->
    <div class="d-flex justify-content-between align-items-center p-3 rounded-3 mb-3" style="background:var(--surface-2);">
      <div>
        <div class="fw-600">Deactivate account</div>
        <small class="text-muted">Temporarily hides your profile &amp; pins from others.<br>You can reactivate anytime by logging back in.</small>
      </div>
      <button class="btn btn-outline-warning rounded-pill px-4" onclick="openDeactivateModal()">Deactivate</button>
    </div>

    <!-- Delete -->
    <div class="d-flex justify-content-between align-items-center p-3 rounded-3" style="background:var(--surface-2);">
      <div>
        <div class="fw-600 text-danger">Delete account</div>
        <small class="text-muted">Permanently removes your personal info.<br>Your created pins will remain visible (anonymously) in others' feeds.</small>
      </div>
      <button class="btn btn-outline-danger rounded-pill px-4" onclick="openDeleteModal()">Delete</button>
    </div>
  </div>
</div>

<!-- DEACTIVATE MODAL-->
<div class="danger-modal-overlay" id="deactivateOverlay" onclick="closeModal('deactivateOverlay')">
  <div class="danger-modal" onclick="event.stopPropagation()">
    <h4>⏸ Deactivate your account?</h4>
    <p>Your profile, boards, and pins will be hidden from everyone. You can come back anytime — just log in and your account will be restored automatically.</p>
    <div class="modal-actions">
      <button class="modal-cancel" onclick="closeModal('deactivateOverlay')">Cancel</button>
      <button class="modal-confirm-deact" id="deactivateBtn" onclick="submitDangerAction('deactivate')">Yes, deactivate</button>
    </div>
  </div>
</div>

<!-- DELETE MODAL-->
<div class="danger-modal-overlay" id="deleteOverlay" onclick="closeModal('deleteOverlay')">
  <div class="danger-modal" onclick="event.stopPropagation()">
    <h4 style="color:var(--pin-red)">🗑 Permanently delete account?</h4>
    <p>This will <strong>erase your personal information</strong> (name, email, bio, profile photo) and remove you from all follower lists. <br><br>
    Your pins will stay visible in feeds — they'll just appear as posted by a deleted user.</p>
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

<!-- JAVASCRIPT — append to your existing <script> block -->
<script>
/* ── Theme  */
function setTheme(theme, el) {
    localStorage.setItem('pb_theme', theme);
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const resolved    = theme === 'system' ? (prefersDark ? 'dark' : 'light') : theme;
    document.documentElement.setAttribute('data-theme', resolved);

    // Update picker active state
    document.querySelectorAll('.theme-card').forEach(c => c.classList.remove('active'));
    if (el) el.classList.add('active');
}

function initThemePicker() {
    const saved = localStorage.getItem('pb_theme') || 'light';
    const card  = document.querySelector(`.theme-card[data-theme="${saved}"]`);
    if (card) card.classList.add('active');

    // Also restore font size
    const fs = localStorage.getItem('pb_fontSize') || '15';
    document.documentElement.style.fontSize = fs + 'px';
    const range = document.getElementById('fontRange');
    const label = document.getElementById('fontSizeLabel');
    if (range) range.value = fs;
    if (label) label.textContent = fs + 'px';

    // Restore language
    const lang = localStorage.getItem('pb_lang') || 'en';
    const sel  = document.getElementById('langSelect');
    if (sel) sel.value = lang;
}

// Re-run when appearance section is opened
const _origShowSection = window.showSection;
window.showSection = function(id, link) {
    _origShowSection(id, link);
    if (id === 'appearance') initThemePicker();
};

/* ── Font size */
function setFontSize(val) {
    document.documentElement.style.fontSize = val + 'px';
    document.getElementById('fontSizeLabel').textContent = val + 'px';
}

/* ── Language  */
function saveLanguage(val) {
    localStorage.setItem('pb_lang', val);
    // Wire to your i18n system here if applicable
}

/* ── Save all appearance*/
function saveAppearance() {
    const fs = document.getElementById('fontRange').value;
    localStorage.setItem('pb_fontSize', fs);
    document.documentElement.style.fontSize = fs + 'px';

    const saved = document.getElementById('appearanceSaved');
    saved.style.display = 'inline';
    setTimeout(() => saved.style.display = 'none', 2500);
}

/* ── Danger zone modals ─ */
function openDeactivateModal() {
    document.getElementById('deactivateOverlay').classList.add('show');
}
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
    btn.disabled = true;
    btn.textContent = 'Please wait…';

    try {
        const res  = await fetch('settings.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `danger_action=${action}`
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

// Init on page load 
document.addEventListener('DOMContentLoaded', initThemePicker);
</script>