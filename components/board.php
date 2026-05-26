<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: ../components/login.php');
    exit;
}

include "../config/database.php";

$board_id = isset($_GET['board_id']) ? intval($_GET['board_id']) : 0;
if (!$board_id) {
    header('Location: profile.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch board details (only if it belongs to this user)
$boardRes = mysqli_query($conn, "SELECT * FROM boards WHERE board_id='$board_id' AND user_id='$user_id'");
if (!$boardRes || mysqli_num_rows($boardRes) === 0) {
    header('Location: profile.php');
    exit;
}
$board = mysqli_fetch_assoc($boardRes);

// Fetch all pins in this board via board_pins junction table
$pinsRes = mysqli_query($conn, "
    SELECT p.* FROM pins p
    INNER JOIN board_pins bp ON p.pin_id = bp.pin_id
    WHERE bp.board_id = '$board_id'
    ORDER BY p.created_at DESC
");
$pinsArr = [];
if ($pinsRes) {
    while ($p = mysqli_fetch_assoc($pinsRes)) {
        $pinsArr[] = $p;
    }
}

$pinCount    = count($pinsArr);
$boardName   = htmlspecialchars($board['board_name']);
$isPrivate   = ($board['privacy'] ?? 'public') === 'private';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PinBoard – <?= $boardName ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<?php require_once __DIR__ . "/app_shell.php"; ?>
  <link rel="stylesheet" href="../css/board.css" />
  <link rel="stylesheet" href="../css/app-shell.css" />
</head>
<body>

<?php render_app_shell([
    'scope' => 'components',
    'active' => 'boards',
]); ?>

<!-- BOARD HEADER -->
<div class="board-header">
  <h1><?= $boardName ?></h1>
  <div class="meta">
    <?= $pinCount ?> pin<?= $pinCount !== 1 ? 's' : '' ?> ·
    <?= $isPrivate ? '<i class="bi bi-lock-fill" style="font-size:.85rem;"></i> Private' : 'Public' ?>
  </div>
  <?php if (!empty($board['description'])): ?>
    <p style="color:#555;max-width:420px;margin:0 auto 20px;line-height:1.6;">
      <?= htmlspecialchars($board['description']) ?>
    </p>
  <?php endif; ?>
  <div class="board-actions">
    <button class="btn-outline-act" onclick="window.location.href='edit_board.php?board_id=<?= $board_id ?>'">
      <i class="bi bi-pencil"></i> Edit board
    </button>
    <button class="btn-red-act" onclick="window.location.href='create-pin.php?board_id=<?= $board_id ?>'">
      <i class="bi bi-plus-lg"></i> Add pin
    </button>
    <button class="btn-outline-act" onclick="shareBoard()">
      <i class="bi bi-share"></i>
    </button>
    <button class="btn-outline-act" id="moreBtn">
      <i class="bi bi-three-dots"></i>
    </button>
  </div>
</div>

<!-- PIN GRID -->
<?php if (empty($pinsArr)): ?>
  <div class="empty-state">
    <div class="empty-icon"><i class="bi bi-image"></i></div>
    <h3>No pins in this board yet</h3>
    <p>Start adding pins to fill up your board!</p>
    <button class="btn-red-act" style="margin:0 auto;"
            onclick="window.location.href='create-pin.php?board_id=<?= $board_id ?>'">
      <i class="bi bi-plus-lg"></i> Add your first pin
    </button>
  </div>
<?php else: ?>
  <div class="masonry" style="padding:0 120px 60px";>
    <?php foreach ($pinsArr as $p):
      $imgSrc = $p['image']
        ? (str_starts_with($p['image'], 'http') ? $p['image'] : '../' . $p['image'])
        : 'https://placehold.co/400x300?text=No+Image';
    ?>
      <div class="pin-card" id="pin-<?= $p['pin_id'] ?>">
        <img src="<?= htmlspecialchars($imgSrc) ?>"
             alt="<?= htmlspecialchars($p['title']) ?>"
             loading="lazy"/>
        <div class="overlay">
          <button class="save-btn">Save</button>
        </div>
        <div class="pin-info">
          <p class="title"><?= htmlspecialchars($p['title']) ?></p>
          <?php if (!empty($p['category'])): ?>
            <p class="pin-cat"><?= htmlspecialchars($p['category']) ?></p>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<!-- TOAST -->
<div style="position:fixed;bottom:24px;left:50%;transform:translateX(-50%);z-index:9999;" class="toast-area">
  <div id="toastEl" class="toast align-items-center text-bg-dark border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body" id="toastMsg">Done!</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function shareBoard() {
    navigator.clipboard.writeText(window.location.href);
    showToast('Board link copied! ');
  }
  function showToast(msg) {
    document.getElementById('toastMsg').textContent = msg;
    new bootstrap.Toast(document.getElementById('toastEl'), { delay: 2500 }).show();
  }
</script>

<?php include "footer.php"; ?>
</body>
</html>


