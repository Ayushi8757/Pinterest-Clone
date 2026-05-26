<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: ../components/login.php');
    exit;
}

include "../config/database.php";

$uid = intval($_SESSION['user_id']);

// ── Fetch notifications using YOUR actual columns ──
// notification_id, user_id, message, notification_type, is_read, created_at
$notifsRes = mysqli_query($conn,
    "SELECT notification_id, user_id, message, notification_type, is_read, created_at
     FROM notifications
     WHERE user_id = $uid
     ORDER BY created_at DESC
     LIMIT 100"
);

$notifs = [];
if ($notifsRes) {
    while ($row = mysqli_fetch_assoc($notifsRes)) {
        $notifs[] = $row;
    }
}

$unreadCount = count(array_filter($notifs, fn($n) => !(bool)$n['is_read']));

// ── Time ago helper ──
function timeAgo($datetime) {
    $now  = new DateTime();
    $then = new DateTime($datetime);
    $diff = $now->diff($then);
    if ($diff->days == 0 && $diff->h == 0 && $diff->i < 1) return 'just now';
    if ($diff->days == 0 && $diff->h == 0) return $diff->i . 'm ago';
    if ($diff->days == 0) return $diff->h . 'h ago';
    if ($diff->days < 7)  return $diff->days . 'd ago';
    return $then->format('M j');
}

// Build JS-safe array
$notifsJson = [];
foreach ($notifs as $n) {
    $notifsJson[] = [
        'id'      => (int)$n['notification_id'],
        'type'    => $n['notification_type'],
        'message' => $n['message'],
        'unread'  => !(bool)$n['is_read'],
        'time'    => timeAgo($n['created_at']),
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Notifications – PinBoard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<?php require_once __DIR__ . "/app_shell.php"; ?>
  <link rel="stylesheet" href="../css/notifications.css" />
  <link rel="stylesheet" href="../css/app-shell.css" />
</head>
<body>

<?php render_app_shell([
    'scope' => 'components',
    'active' => 'notifications',
]); ?>

<!-- MAIN -->
<div class="main">

  <!-- Header row -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <span class="page-title">Notifications</span>
      <?php if ($unreadCount > 0): ?>
        <span class="new-badge" id="headerBadge"><?= $unreadCount ?> new</span>
      <?php endif; ?>
    </div>
    <?php if ($unreadCount > 0): ?>
      <button class="mark-all-btn" id="markAllBtn" onclick="markAll()">Mark all as read</button>
    <?php endif; ?>
  </div>

  <!-- Tabs -->
  <div class="tabs">
    <button class="active" onclick="switchTab('all',this)">
      All
      <?php if($unreadCount > 0): ?>
        <span class="tab-badge" id="allBadge"><?= $unreadCount ?></span>
      <?php endif; ?>
    </button>
    <button onclick="switchTab('follow',this)">Follows</button>
    <button onclick="switchTab('like',this)">Likes</button>
    <button onclick="switchTab('save',this)">Saves</button>
    <button onclick="switchTab('comment',this)">Comments</button>
  </div>

  <!-- List -->
  <div id="notifList"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const NOTIFS = <?= json_encode($notifsJson) ?>;
let currentTab = 'all';

// Icon & colour per type
const CFG = {
  follow:  { icon:'bi bi-person-fill-add',    bg:'#e8f4fd', color:'#0d6efd' },
  like:    { icon:'bi bi-heart-fill',          bg:'#fff0f5', color:'#E60023' },
  save:    { icon:'bi bi-bookmark-heart-fill', bg:'#fff5f5', color:'#c0392b' },
  comment: { icon:'bi bi-chat-heart-fill',     bg:'#f3f0ff', color:'#6610f2' },
};
const DEFAULT_CFG = { icon:'bi bi-bell-fill', bg:'#f0f0f0', color:'#555' };

function esc(s){ return s ? String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;') : ''; }

function renderNotifs(tab) {
  const list = tab === 'all' ? NOTIFS : NOTIFS.filter(n => n.type === tab);
  const el   = document.getElementById('notifList');

  if (!list.length) {
    const labels = { follow:'follows', like:'likes', save:'saves', comment:'comments', all:'notifications' };
    el.innerHTML = `
      <div class="empty-state">
        <i class="bi bi-bell-slash"></i>
        <h4>No ${labels[tab] || 'notifications'} yet</h4>
        <p>When someone ${tab === 'all' ? 'follows, likes, saves or comments' : tab + 's'}, it'll show up here.</p>
      </div>`;
    return;
  }

  let html = '';
  let lastDay = '';

  list.forEach(n => {
    const cfg = CFG[n.type] || DEFAULT_CFG;

    // Day grouping
    const isRecent = n.time.includes('ago') || n.time === 'just now';
    const dayLabel = isRecent ? 'Today' : n.time;
    if (dayLabel !== lastDay) {
      html += `<div class="day-label">${esc(dayLabel)}</div>`;
      lastDay = dayLabel;
    }

    html += `
      <div class="notif-item ${n.unread ? 'unread' : ''}" id="notif-${n.id}"
           onclick="markRead(${n.id})">
        <div class="notif-icon" style="background:${cfg.bg};color:${cfg.color}">
          <i class="${cfg.icon}"></i>
        </div>
        <div class="notif-body">
          <div class="notif-msg">${esc(n.message)}</div>
          <div class="notif-time"><i class="bi bi-clock"></i> ${esc(n.time)}</div>
        </div>
      </div>`;
  });

  el.innerHTML = html;
}

function switchTab(tab, btn) {
  currentTab = tab;
  document.querySelectorAll('.tabs button').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  renderNotifs(tab);
}

function markRead(id) {
  const item = document.getElementById('notif-' + id);
  if (item && item.classList.contains('unread')) {
    item.classList.remove('unread');
    const notif = NOTIFS.find(n => n.id === id);
    if (notif) notif.unread = false;
    fetch('mark_notif_read.php', {
      method:'POST',
      body: new URLSearchParams({ notif_id: id })
    });
    updateBadges();
  }
}

function markAll() {
  NOTIFS.forEach(n => n.unread = false);
  document.querySelectorAll('.notif-item.unread').forEach(el => el.classList.remove('unread'));
  fetch('mark_notif_read.php', { method:'POST', body: new URLSearchParams({}) });
  updateBadges(true);
}

function updateBadges(allRead) {
  const unread = NOTIFS.filter(n => n.unread).length;
  const hb = document.getElementById('headerBadge');
  const ab = document.getElementById('allBadge');
  const mb = document.getElementById('markAllBtn');
  if (hb) hb.textContent = unread > 0 ? unread + ' new' : '';
  if (hb && unread === 0) hb.style.display = 'none';
  if (ab) { ab.textContent = unread; if(unread===0) ab.style.display='none'; }
  if (mb && unread === 0) mb.style.display = 'none';
}

// Initial render
renderNotifs('all');

// Poll every 30s for new notifications
setInterval(() => {
  fetch('get_notif_count.php')
    .then(r => r.json())
    .then(data => { if (data.count > 0) location.reload(); });
}, 30000);
</script>

<?php include "footer.php"; ?>
</body>
</html>


