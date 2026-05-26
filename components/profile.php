<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: ../components/login.php');
    exit;
}

include "../config/database.php";

if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../uploads/profiles/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);
    $ext      = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
    $fileName = 'user_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
    $dest     = $uploadDir . $fileName;
    if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $dest)) {
        $_SESSION['profile_image'] = 'uploads/profiles/' . $fileName;
    }
    header('Location: profile.php');
    exit;
}

$user_id        = $_SESSION['user_id'];
$profileImg     = $_SESSION['profile_image'] ?? '';
$profileImgDisp = $profileImg ? '../' . $profileImg : '';
$profileName    = htmlspecialchars($_SESSION['user_name'] ?? 'User');
$firstLetter    = strtoupper(mb_substr($profileName, 0, 1));
$userEmail      = htmlspecialchars($_SESSION['user_email'] ?? '');
$userUsername   = htmlspecialchars($_SESSION['username'] ?? strtolower(str_replace(' ', '', $profileName)));
$userBio        = htmlspecialchars($_SESSION['user_bio'] ?? '');

$myPinsQuery = mysqli_query($conn, "
    SELECT p.*, GROUP_CONCAT(b.board_name SEPARATOR ', ') AS board_names
    FROM pins p
    LEFT JOIN board_pins bp ON p.pin_id = bp.pin_id
    LEFT JOIN boards b ON bp.board_id = b.board_id
    WHERE p.user_id = '$user_id'
    GROUP BY p.pin_id
    ORDER BY p.created_at DESC
");
if (!$myPinsQuery) $myPinsQuery = null;

$savedPinsQuery = mysqli_query($conn, "
    SELECT p.* FROM pins p
    INNER JOIN saved_pins sp ON p.pin_id = sp.pin_id
    WHERE sp.user_id = '$user_id'
    ORDER BY sp.created_at DESC
");
if (!$savedPinsQuery) $savedPinsQuery = null;

$likedPinsQuery = mysqli_query($conn, "
    SELECT p.* FROM pins p
    INNER JOIN pin_likes pl ON p.pin_id = pl.pin_id
    WHERE pl.user_id = '$user_id'
    ORDER BY p.created_at DESC
");
if (!$likedPinsQuery) $likedPinsQuery = null;

$pinCount       = $myPinsQuery ? mysqli_num_rows($myPinsQuery) : 0;
$followerRes    = mysqli_query($conn, "SELECT COUNT(*) as c FROM followers WHERE following_user_id = '$user_id'");
$followerCount  = ($followerRes) ? (mysqli_fetch_assoc($followerRes)['c'] ?? 0) : 0;
$followingRes   = mysqli_query($conn, "SELECT COUNT(*) as c FROM followers WHERE user_id = '$user_id'");
$followingCount = ($followingRes) ? (mysqli_fetch_assoc($followingRes)['c'] ?? 0) : 0;

$boardsQuery = mysqli_query($conn, "SELECT * FROM boards WHERE user_id='$user_id' ORDER BY created_at DESC");
$boardsArr = [];
if ($boardsQuery) {
    while ($b = mysqli_fetch_assoc($boardsQuery)) {
        $bid = $b['board_id'];
        $bImgsQuery = mysqli_query($conn, "
            SELECT p.image FROM pins p
            INNER JOIN board_pins bp ON p.pin_id = bp.pin_id
            WHERE bp.board_id='$bid' AND p.image != ''
            ORDER BY p.created_at DESC LIMIT 3
        ");
        $bImgs = [];
        
if ($bImgsQuery) while ($bi = mysqli_fetch_assoc($bImgsQuery)) {
    $img = $bi['image'];
    
    $bImgs[] = (str_starts_with($img, 'http') ? $img : '../' . $img);
}
        while (count($bImgs) < 3) $bImgs[] = 'https://placehold.co/300x300?text=+';
        $pinCountQ = mysqli_query($conn, "SELECT COUNT(*) as c FROM board_pins WHERE board_id='$bid'");
        $pinCountB = $pinCountQ ? (mysqli_fetch_assoc($pinCountQ)['c'] ?? 0) : 0;
        $b['imgs'] = $bImgs;
        $b['pin_count'] = $pinCountB;
        $boardsArr[] = $b;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pinterest – Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@800;900&family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

<?php require_once __DIR__ . "/app_shell.php"; ?>
  <link rel="stylesheet" href="../css/profile.css" />
  <link rel="stylesheet" href="../css/app-shell.css" />

  
</head>
<body>

<script>
  function switchTab(tab, btn) {
    document.querySelectorAll('.profile-tabs button').forEach(function(b) { b.classList.remove('active'); });
    document.querySelectorAll('.tab-content-section').forEach(function(s) { s.classList.remove('active'); });
    btn.classList.add('active');
    document.getElementById('tab-' + tab).classList.add('active');
  }
</script>
 
<?php render_app_shell([
    'scope' => 'components',
    'active' => 'boards',
]); ?>
 
<div class="profile-header">
  <div class="avatar-wrap">
    <form id="avatarForm" action="profile.php" method="POST" enctype="multipart/form-data" style="display:none;">
      <input type="file" id="avatarInput" name="profile_photo" accept="image/*"
             onchange="document.getElementById('avatarForm').submit()"/>
    </form>
    <?php if ($profileImgDisp): ?>
      <img src="<?= htmlspecialchars($profileImgDisp) ?>" alt="Avatar" id="avatarImg"/>
    <?php else: ?>
      <div id="avatarImg" class="profile-initials-avatar"><?= $firstLetter ?></div>
    <?php endif; ?>
    <button class="avatar-edit" onclick="document.getElementById('avatarInput').click()" title="Change photo">
      <i class="bi bi-camera-fill"></i>
    </button>
  </div>
 
  <div class="profile-name" contenteditable="false" id="profileName"><?= $profileName ?></div>
  <div class="profile-handle">
    @<?= $userUsername ?> ·
    <a href="mailto:<?= $userEmail ?>" style="color:var(--muted);text-decoration:none;"><?= $userEmail ?></a>
  </div>
  <div class="profile-bio" id="profileBio" contenteditable="false"><?= $userBio ?></div>
 
  <div class="profile-stats">
    <div class="stat">
      <div class="num"><?= $pinCount ?></div>
      <div class="lbl">Pins</div>
    </div>
    <div class="stat stat-clickable" onclick="openFollowModal('followers')">
      <div class="num" id="followerCount"><?= $followerCount ?></div>
      <div class="lbl">Followers</div>
    </div>
    <div class="stat stat-clickable" onclick="openFollowModal('following')">
      <div class="num" id="followingCount"><?= $followingCount ?></div>
      <div class="lbl">Following</div>
    </div>
  </div>
 
  <div class="profile-actions">
    <button class="btn-edit" onclick="window.location.href='edit_profile.php'">
      <i class="bi bi-pencil"></i> <span id="editBtnLabel">Edit profile</span>
    </button>
    <button class="btn btn-outline-secondary rounded-pill" onclick="shareProfile()">
      <i class="bi bi-share"></i> Share
    </button>
  </div>
</div>
 
<div class="profile-tabs">
  <button class="active" onclick="switchTab('boards', this)">Boards</button>
  <button onclick="switchTab('pins', this)">Pins</button>
  <button onclick="switchTab('saved', this)">Saved</button>
  <button onclick="switchTab('liked', this)">Liked</button>
</div>
 
<div class="tab-content-section active" id="tab-boards">
  <div class="empty-state" id="boardsEmptyState" style="display:none;">
    <div class="empty-icon"><i class="bi bi-pin-angle"></i></div>
    <h3>Organize your ideas</h3>
    <p>Create boards to organize your Pins your way.</p>
    <button class="btn-create-board" onclick="showBoardModal()">Create a board</button>
  </div>
  <div class="boards-grid" id="boardsGrid"></div>
</div>
 
<div class="tab-content-section" id="tab-pins">
  <?php
  if ($myPinsQuery) mysqli_data_seek($myPinsQuery, 0);
  $myPinsArr = [];
  if ($myPinsQuery) { while ($p = mysqli_fetch_assoc($myPinsQuery)) $myPinsArr[] = $p; }
  ?>
  <?php if (empty($myPinsArr)): ?>
    <div class="no-pins">
      <i class="bi bi-image"></i>
      <h4>No pins yet</h4>
      <p>Pins you create will appear here.</p>
    </div>
  <?php else: ?>
    <div class="masonry">
      <?php foreach ($myPinsArr as $p):
        $imgSrc = $p['image']
          ? (str_starts_with($p['image'], 'http') ? $p['image'] : '../' . $p['image'])
          : 'https://placehold.co/400x300?text=No+Image';
      ?>
        <div class="pin-card" id="pin-<?= $p['pin_id'] ?>">
          <img src="<?= htmlspecialchars($imgSrc) ?>"
               alt="<?= htmlspecialchars($p['title']) ?>"
               style="height:200px;object-fit:cover;">
          <div class="pin-info" style="display:flex;justify-content:space-between;align-items:center;">
            <div>
              <p class="title"><?= htmlspecialchars($p['title']) ?></p>
              <p class="pin-cat"><?= htmlspecialchars($p['category']) ?></p>
            </div>
            <button onclick="deletePin(<?= $p['pin_id'] ?>)"
                    style="background:none;border:none;color:#dc3545;cursor:pointer;font-size:1rem;padding:4px;"
                    title="Delete pin">
              <i class="bi bi-trash3"></i>
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
 
<div class="tab-content-section" id="tab-saved">
  <?php
  $savedArr = [];
  if ($savedPinsQuery) { while ($p = mysqli_fetch_assoc($savedPinsQuery)) $savedArr[] = $p; }
  ?>
  <?php if (empty($savedArr)): ?>
    <div class="no-pins">
      <i class="bi bi-bookmark"></i>
      <h4>No saved pins yet</h4>
      <p>Pins you save will appear here.</p>
    </div>
  <?php else: ?>
    <div class="masonry">
      <?php foreach ($savedArr as $p):
        $imgSrc = $p['image']
          ? (str_starts_with($p['image'], 'http') ? $p['image'] : '../' . $p['image'])
          : 'https://placehold.co/400x300?text=No+Image';
      ?>
        <div class="pin-card">
          <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($p['title']) ?>" style="height:200px;object-fit:cover;">
          <div class="pin-info">
            <p class="title"><?= htmlspecialchars($p['title']) ?></p>
            <p class="pin-cat"><?= htmlspecialchars($p['category']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
 
<div class="tab-content-section" id="tab-liked">
  <?php
  $likedArr = [];
  if ($likedPinsQuery) { while ($p = mysqli_fetch_assoc($likedPinsQuery)) $likedArr[] = $p; }
  ?>
  <?php if (empty($likedArr)): ?>
    <div class="no-pins">
      <i class="bi bi-heart"></i>
      <h4>No liked pins yet</h4>
      <p>Pins you like will appear here.</p>
    </div>
  <?php else: ?>
    <div class="masonry">
      <?php foreach ($likedArr as $p):
        $imgSrc = $p['image']
          ? (str_starts_with($p['image'], 'http') ? $p['image'] : '../' . $p['image'])
          : 'https://placehold.co/400x300?text=No+Image';
      ?>
        <div class="pin-card">
          <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($p['title']) ?>" style="height:200px;object-fit:cover;">
          <div class="pin-info">
            <p class="title"><?= htmlspecialchars($p['title']) ?></p>
            <p class="pin-cat"><?= htmlspecialchars($p['category']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
 
<div class="create-fab-wrap" id="createFabWrap">
  <button class="create-fab-btn" onclick="toggleCreateMenu(event)">Create</button>
  <div class="create-fab-menu" id="createFabMenu">
    <div class="create-fab-menu-item" onclick="window.location.href='create-pin.php'">Pin</div>
    <div class="create-fab-menu-item" onclick="openBoardFromMenu()">Board</div>
  </div>
</div>
 
<div class="modal-overlay" id="createBoardOverlay" onclick="closeBoardModalBackdrop(event)">
  <div class="create-board-modal">
    <button class="modal-close" onclick="hideBoardModal()"><i class="bi bi-x-lg"></i></button>
    <h2 class="modal-title">Create board</h2>
    <label class="form-label-custom" for="boardNameInput">Name</label>
    <input type="text" id="boardNameInput" class="form-input-custom" placeholder="Add a name" maxlength="50" oninput="updateCreateBtn()"/>
    <div class="form-hint">Keep it short and easy to remember.</div>
    <div class="privacy-toggle-wrap">
      <div class="privacy-info">
        <div class="privacy-title"><i class="bi bi-lock-fill" style="font-size:.85rem;margin-right:4px;"></i>Keep this board secret</div>
        <div class="privacy-desc">Only you and collaborators can see this board.</div>
      </div>
      <label class="toggle-switch">
        <input type="checkbox" id="boardPrivate"/>
        <span class="toggle-slider"></span>
      </label>
    </div>
    <button class="btn-submit-board" id="createBoardBtn" disabled onclick="submitCreateBoard()">Create</button>
  </div>
</div>
 
<div class="toast-area">
  <div id="toastEl" class="toast align-items-center text-bg-dark border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body" id="toastMsg">Done!</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>
 
<div class="follow-modal-overlay" id="followModalOverlay" onclick="closeFollowModalBackdrop(event)">
  <div class="follow-modal">
    <div class="follow-modal-header">
      <h3 id="followModalTitle">Following</h3>
      <button class="modal-close" onclick="closeFollowModal()">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <div class="follow-modal-body" id="followModalBody">
      <div class="follow-modal-empty">Loading...</div>
    </div>
  </div>
</div>
 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
 
<script>
  var boards = <?php echo json_encode(array_map(function($b){
    return ['title'=>$b['board_name'],'count'=>$b['pin_count'],'imgs'=>$b['imgs'],'id'=>$b['board_id']];
  }, $boardsArr)); ?>;
 
  function renderBoards() {
    var grid  = document.getElementById('boardsGrid');
    var empty = document.getElementById('boardsEmptyState');
    if (boards.length === 0) {
      empty.style.display = 'block';
      grid.innerHTML = '';
    } else {
      empty.style.display = 'none';
      grid.innerHTML = boards.map(function(b) {
        return '<div class="board-card" id="board-' + b.id + '">' +
          '<div onclick="window.location.href=\'board.php?board_id=' + b.id + '\'">' +
            '<div class="board-imgs">' +
              '<img src="' + b.imgs[0] + '" alt=""/>' +
              '<img src="' + b.imgs[1] + '" alt=""/>' +
              '<img src="' + b.imgs[2] + '" alt=""/>' +
            '</div>' +
            '<div class="board-title">' + b.title + '</div>' +
            '<div class="board-count">' + b.count + ' pins</div>' +
          '</div>' +
          '<div style="padding:0 12px 12px;display:flex;justify-content:flex-end;">' +
            '<button onclick="deleteBoard(' + b.id + ',\'' + b.title + '\')" ' +
              'style="background:none;border:none;color:#dc3545;font-size:.82rem;font-weight:600;cursor:pointer;padding:4px 8px;border-radius:8px;transition:background .2s;" ' +
              'onmouseover="this.style.background=\'#fff0f0\'" onmouseout="this.style.background=\'none\'">' +
              '<i class="bi bi-trash3"></i> Delete' +
            '</button>' +
          '</div>' +
        '</div>';
      }).join('');
    }
  }
  renderBoards();
 
  function toggleCreateMenu(e) {
    e.stopPropagation();
    document.getElementById('createFabMenu').classList.toggle('open');
  }
 
  document.addEventListener('click', function(e) {
    var wrap = document.getElementById('createFabWrap');
    if (wrap && !wrap.contains(e.target)) {
      document.getElementById('createFabMenu').classList.remove('open');
    }
  });
 
  function openBoardFromMenu() {
    document.getElementById('createFabMenu').classList.remove('open');
    showBoardModal();
  }
 
  function deleteBoard(boardId, boardName) {
    if (!confirm('Delete board "' + boardName + '"? The pins inside will not be deleted.')) return;
    var fd = new FormData();
    fd.append('board_id', boardId);
    fetch('delete_board.php', { method: 'POST', body: fd })
      .then(function(r){ return r.json(); })
      .then(function(data){
        if (data.success) {
          boards = boards.filter(function(b){ return b.id !== boardId; });
          renderBoards();
          showToast('Board deleted 🗑️');
        } else {
          showToast('Error: ' + (data.message || 'Could not delete board'));
        }
      })
      .catch(function(){ showToast('Network error. Try again.'); });
  }
 
  function deletePin(pinId) {
    if (!confirm('Delete this pin? This cannot be undone.')) return;
    var fd = new FormData();
    fd.append('pin_id', pinId);
    fetch('delete_pin.php', { method: 'POST', body: fd })
      .then(function(r){ return r.json(); })
      .then(function(data){
        if (data.success) {
          var card = document.getElementById('pin-' + pinId);
          if (card) card.remove();
          showToast('Pin deleted');
        } else {
          showToast('Error: ' + (data.message || 'Could not delete pin'));
        }
      })
      .catch(function(){ showToast('Network error. Try again.'); });
  }
 
  var editing = false;
  function toggleEdit() {
    editing = !editing;
    ['profileName', 'profileBio'].forEach(function(id) {
      document.getElementById(id).contentEditable = editing ? 'true' : 'false';
    });
    document.getElementById('editBtnLabel').textContent = editing ? 'Save profile' : 'Edit profile';
    if (editing) document.getElementById('profileName').focus();
  }
 
  function shareProfile() {
    navigator.clipboard.writeText(window.location.href);
    showToast('Profile link copied! 🔗');
  }
 
  function showBoardModal() {
    document.getElementById('createBoardOverlay').classList.add('show');
    setTimeout(function() { document.getElementById('boardNameInput').focus(); }, 200);
  }
 
  function hideBoardModal() {
    document.getElementById('createBoardOverlay').classList.remove('show');
    document.getElementById('boardNameInput').value = '';
    document.getElementById('boardPrivate').checked = false;
    updateCreateBtn();
  }
 
  function closeBoardModalBackdrop(e) {
    if (e.target === document.getElementById('createBoardOverlay')) hideBoardModal();
  }
 
  function updateCreateBtn() {
    document.getElementById('createBoardBtn').disabled =
      document.getElementById('boardNameInput').value.trim().length === 0;
  }
 
  function submitCreateBoard() {
    var name = document.getElementById('boardNameInput').value.trim();
    if (!name) return;
    var formData = new FormData();
    formData.append('board_name', name);
    formData.append('privacy', document.getElementById('boardPrivate').checked ? 'private' : 'public');
    fetch('create-board.php', { method: 'POST', body: formData })
      .then(function(r){ return r.json(); })
      .then(function(data){
        if (data.success) {
          boards.unshift({
            title: name,
            count: 0,
            id: data.board_id,
            imgs: ['https://placehold.co/300x300?text=+','https://placehold.co/150x150?text=+','https://placehold.co/150x150?text=+']
          });
          renderBoards();
          hideBoardModal();
          showToast('Board "' + name + '" created! ');
        } else {
          showToast('Error: ' + (data.message || 'Could not create board'));
        }
      })
      .catch(function(){ showToast('Network error. Try again.'); });
  }
 
  function showToast(msg) {
    document.getElementById('toastMsg').textContent = msg;
    new bootstrap.Toast(document.getElementById('toastEl'), { delay: 2500 }).show();
  }
 
  function openFollowModal(type) {
    document.getElementById('followModalTitle').textContent =
      type === 'following' ? 'Following' : 'Followers';
    document.getElementById('followModalBody').innerHTML =
      '<div class="follow-modal-empty">Loading...</div>';
    document.getElementById('followModalOverlay').classList.add('show');
 
    fetch('get_follow_list.php?type=' + type)
      .then(function(r){ return r.json(); })
      .then(function(data){
        if (!data.success || data.list.length === 0) {
          document.getElementById('followModalBody').innerHTML =
            '<div class="follow-modal-empty">' +
            (type === 'following' ? "You're not following anyone yet." : "No followers yet.") +
            '</div>';
          return;
        }
        document.getElementById('followModalBody').innerHTML = data.list.map(function(u) {
          var avatar = u.profile_image
            ? '<img class="follow-user-avatar" src="../' + u.profile_image + '" alt=""/>'
            : '<div class="follow-user-avatar">' + (u.name||'U')[0].toUpperCase() + '</div>';
 
          var btn = '';
          if (type === 'following') {
            btn = '<button class="btn-unfollow" onclick="doUnfollow(' + u.user_id + ', this)">Unfollow</button>';
          } else {
            if (u.i_follow_them == 1) {
              btn = '<button class="btn-unfollow" onclick="doUnfollow(' + u.user_id + ', this)">Following</button>';
            } else {
              btn = '<button class="btn-follow-back" onclick="doFollow(' + u.user_id + ', this)">Follow back</button>';
            }
          }
 
          return '<div class="follow-user-row" id="follow-row-' + u.user_id + '">' +
            avatar +
            '<div class="follow-user-info">' +
              '<div class="follow-user-name">' + (u.name || 'User') + '</div>' +
              '<div class="follow-user-handle">@' + (u.username || '') + '</div>' +
            '</div>' +
            btn +
          '</div>';
        }).join('');
      })
      .catch(function(){
        document.getElementById('followModalBody').innerHTML =
          '<div class="follow-modal-empty">Could not load list.</div>';
      });
  }
 
  function closeFollowModal() {
    document.getElementById('followModalOverlay').classList.remove('show');
  }
 
  function closeFollowModalBackdrop(e) {
    if (e.target === document.getElementById('followModalOverlay')) closeFollowModal();
  }
 
  function doUnfollow(targetId, btn) {
    var fd = new FormData();
    fd.append('target_id', targetId);
    fd.append('action', 'unfollow');
    fetch('follow.php', { method: 'POST', body: fd })
      .then(function(r){ return r.json(); })
      .then(function(data){
        if (data.success) {
          document.getElementById('followerCount').textContent  = data.follower_count;
          document.getElementById('followingCount').textContent = data.following_count;
          var row = document.getElementById('follow-row-' + targetId);
          if (row) row.remove();
          showToast('Unfollowed successfully');
        }
      });
  }
 
  function doFollow(targetId, btn) {
    var fd = new FormData();
    fd.append('target_id', targetId);
    fd.append('action', 'follow');
    fetch('follow.php', { method: 'POST', body: fd })
      .then(function(r){ return r.json(); })
      .then(function(data){
        if (data.success) {
          document.getElementById('followerCount').textContent  = data.follower_count;
          document.getElementById('followingCount').textContent = data.following_count;
          btn.className = 'btn-unfollow';
          btn.textContent = 'Following';
          btn.onclick = function(){ doUnfollow(targetId, btn); };
          showToast('Followed! ');
        }
      });
  }
 
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      hideBoardModal();
      closeFollowModal();
      document.getElementById('createFabMenu').classList.remove('open');
    }
  });
</script>
 
</body>
</html>