<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: /pinterest_project_v5/Pinterest_Final/landing.php');
    exit;
}

$profileImg  = $_SESSION['profile_image'] ?? '';
$sessionName = $_SESSION['user_name'] ?? 'User';
$firstLetter = strtoupper(mb_substr($sessionName, 0, 1));
?>
<?php
include "config/database.php";

$user_id = $_SESSION['user_id'];

$pinsQuery = mysqli_query($conn, "
    SELECT p.*, u.name AS user_name, u.profile_image AS user_avatar,
           (SELECT COUNT(*) FROM followers WHERE following_user_id = p.user_id) AS follower_count
    FROM pins p
    LEFT JOIN users u ON p.user_id = u.user_id
    ORDER BY p.created_at DESC
");

if (!$pinsQuery) {
    die("Query failed: " . mysqli_error($conn));
}

$likedPins = [];
$likedQuery = mysqli_query($conn, "SELECT pin_id FROM pin_likes WHERE user_id = '$user_id'");
while ($row = mysqli_fetch_assoc($likedQuery)) {
    $likedPins[] = $row['pin_id'];
}

$savedPins = [];
$savedQuery = mysqli_query($conn, "SELECT pin_id FROM saved_pins WHERE user_id = '$user_id'");
while ($row = mysqli_fetch_assoc($savedQuery)) {
    $savedPins[] = $row['pin_id'];
}
?>
<?php include "components/header.php"; ?>
<?php require_once __DIR__ . "/components/app_shell.php"; ?>
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pinterest – Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@800;900&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.2/css/all.css">
  <link rel="stylesheet" href="css/home.css" />
  <link rel="stylesheet" href="css/app-shell.css" />
</head>
<body><?php render_app_shell([
    'scope' => 'root',
    'active' => 'home',
    'search_attributes' => 'id="searchInput" oninput="filterPins()"',
]); ?>

  <div class="main-content">
    <div class="cat-strip" id="catStrip">
      <button class="cat-chip on" onclick="filterCat('all', this)">All</button>
      <button class="cat-chip" onclick="filterCat('food', this)">🍳 Food</button>
      <button class="cat-chip" onclick="filterCat('travel', this)">✈️ Travel</button>
      <button class="cat-chip" onclick="filterCat('fashion', this)">👗 Fashion</button>
      <button class="cat-chip" onclick="filterCat('home', this)">🏡 Home Decor</button>
      <button class="cat-chip" onclick="filterCat('art', this)">🎨 Art</button>
      <button class="cat-chip" onclick="filterCat('fitness', this)">💪 Fitness</button>
      <button class="cat-chip" onclick="filterCat('tech', this)">💻 Tech</button>
      <button class="cat-chip" onclick="filterCat('nature', this)">🌿 Nature</button>
      <button class="cat-chip" onclick="filterCat('diy', this)">🔨 DIY</button>
      <button class="cat-chip" onclick="filterCat('beauty', this)">💄 Beauty</button>
      <button class="cat-chip" onclick="filterCat('animals', this)">🐾 Animals</button>
    </div>

    <div class="masonry" id="masonryGrid">
      <?php while ($pin = mysqli_fetch_assoc($pinsQuery)): ?>
      <?php
        $isLiked = in_array($pin['pin_id'], $likedPins);
        $isSaved = in_array($pin['pin_id'], $savedPins);
        $pinAuthor    = htmlspecialchars($pin['user_name'] ?? 'Unknown');
        $pinAvatar    = htmlspecialchars($pin['user_avatar'] ?? '');
        $pinFollowers = number_format($pin['follower_count'] ?? 0);
        $authorInitial = strtoupper(mb_substr($pinAuthor, 0, 1));
      ?>
      <div class="pin-card"
           data-title="<?= strtolower(htmlspecialchars($pin['title'])) ?>"
           data-desc="<?= strtolower(htmlspecialchars($pin['description'])) ?>"
           data-cat="<?= strtolower(htmlspecialchars($pin['category'])) ?>"
           data-id="<?= $pin['pin_id'] ?>"
           data-liked="<?= $isLiked ? '1' : '0' ?>"
           data-saved="<?= $isSaved ? '1' : '0' ?>"
           data-pin-title="<?= htmlspecialchars($pin['title']) ?>"
           data-pin-desc="<?= htmlspecialchars($pin['description']) ?>"
           data-pin-img="<?= htmlspecialchars($pin['image']) ?>"
           data-author="<?= $pinAuthor ?>"
           data-author-avatar="<?= $pinAvatar ?>"
           data-author-initial="<?= $authorInitial ?>"
           data-followers="<?= $pinFollowers ?>"
data-author-id="<?= $pin['user_id'] ?>"
onclick="openPin(this)">

        <img src="<?= $pin['image'] ? htmlspecialchars($pin['image']) : 'https://placehold.co/400x300?text=No+Image' ?>"
             style="height:280px; object-fit:cover; width:100%;"
             loading="lazy" alt="<?= htmlspecialchars($pin['title']) ?>">

        <div class="ov">
          <button class="save-btn <?= $isSaved ? 'saved-state' : '' ?>"
                  onclick="event.stopPropagation(); savePin(<?= $pin['pin_id'] ?>, this)">
            <?= $isSaved ? 'Saved' : 'Save' ?>
          </button>
          <div class="card-actions">
            <button onclick="event.stopPropagation(); toggleLike(<?= $pin['pin_id'] ?>, this)" title="Like">
              <i class="bi <?= $isLiked ? 'bi-heart-fill' : 'bi-heart' ?>"
                 style="<?= $isLiked ? 'color:#E60023;' : '' ?>"></i>
            </button>
            <button onclick="event.stopPropagation(); sharePin(<?= $pin['pin_id'] ?>)" title="Share">
              <i class="bi bi-share"></i>
            </button>
          </div>
        </div>

        <div class="pin-info">
          <div class="ptitle"><?= htmlspecialchars($pin['title']) ?></div>
          <div class="pauthor"><?= htmlspecialchars($pin['category']) ?></div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>

  <!-- PIN MODAL -->
  <div class="pm-overlay" id="pmOverlay" onclick="closePinMod(event)">
    <div class="pm-box">
      <button class="pm-close" onclick="closePinMod()">&#x2715;</button>
      <div class="pm-img">
        <img id="pmImg" src="" alt="" />
      </div>
      <div class="pm-detail">
        <div class="pd-top">
          <div class="pd-actions">
            <button id="modalLikeBtn" onclick="toggleModalLike()" title="Like">
              <i class="bi bi-heart"></i>
            </button>
            <button onclick="sharePin()" title="Share">
              <i class="bi bi-share"></i>
            </button>
            <button onclick="toggleMoreMenu(event)" title="More">
              <i class="bi bi-three-dots"></i>
            </button>
            <div id="moreMenu" class="more-menu">
              <button onclick="downloadPin()">Download image</button>
              <button onclick="seeMoreLikeThis()">See more like this</button>
              <button onclick="seeLessLikeThis()">See less like this</button>
              <button onclick="reportPin()">Report Pin</button>
              <button onclick="getEmbedCode()">Get Pin embed code</button>
            </div>
          </div>
          <button class="btn-save-big" id="modalSaveBtn" onclick="toggleModalSave()">Save</button>
        </div>
        <div class="pm-title" id="pmTitle"></div>
        <div class="pm-desc"  id="pmDesc"></div>
        <div class="pm-author">
          <div id="pmAvatarWrap"></div>
          <div>
            <div class="aname" id="pmAuthor"></div>
            <div class="afol"  id="pmFollowers"></div>
          </div>
          <button class="btn-follow" id="followBtn" onclick="toggleFollow(this)">Follow</button>
        </div>
        <hr style="border-color:#f0eeeb; margin-bottom:16px" />
        <div class="comments-section">
          <h6>Comments</h6>
          <div id="commentsList"></div>
          <div class="comment-input-row">
            <input type="text" id="commentInput" placeholder="Add a comment…"
                   onkeydown="if(event.key==='Enter') addComment()" />
            <button onclick="addComment()">Post</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Toast -->
  <div class="toast-area">
    <div id="toastEl" class="toast align-items-center border-0" role="alert">
      <div class="d-flex align-items-center gap-2 px-3 py-2">
        <div class="toast-body px-0" id="toastMsg"></div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>


    /* ── State ── */
    let curPin   = null;

    /* ── Open Pin Modal ── */
    function openPin(card) {
  curPin = {
    id:            card.dataset.id,
    title:         card.dataset.pinTitle,
    desc:          card.dataset.pinDesc,
    img:           card.dataset.pinImg,
    author:        card.dataset.author        || 'Unknown',
    authorAvatar:  card.dataset.authorAvatar  || '',
    authorInitial: card.dataset.authorInitial || '?',
    followers:     card.dataset.followers     || '0',
    authorId:      card.dataset.authorId      || null,
    liked:         card.dataset.liked === '1',
    saved:         card.dataset.saved  === '1'
  };

  document.getElementById("pmImg").src           = curPin.img || 'https://placehold.co/400x300?text=No+Image';
  document.getElementById("pmImg").alt           = curPin.title;
  document.getElementById("pmTitle").textContent = curPin.title;
  document.getElementById("pmDesc").textContent  = curPin.desc;

  /* Author avatar */
  const avatarWrap = document.getElementById("pmAvatarWrap");
  avatarWrap.innerHTML = curPin.authorAvatar
    ? `<img src="${curPin.authorAvatar}" class="author-avatar" alt="${curPin.author}" />`
    : `<div class="author-initials">${curPin.authorInitial}</div>`;

  document.getElementById("pmAuthor").textContent    = curPin.author;
  document.getElementById("pmFollowers").textContent = curPin.followers + ' followers';

  /* Sync LIKE state */
  syncModalLike(curPin.liked);

  /* Sync SAVE state */
  syncModalSave(curPin.saved);

  /* ── Follow button logic ── */
  const fb      = document.getElementById("followBtn");
  const myId    = <?php echo intval($_SESSION['user_id']); ?>;
  const authorId = curPin.authorId;

  // Hide follow button if viewing own pin
  if (!authorId || authorId == myId) {
    fb.style.display = 'none';
  } else {
    fb.style.display  = '';
    fb.textContent    = 'Follow';
    fb.classList.remove('following');

    // Check if already following this user
    fetch('components/get_follow_list.php?type=following')
      .then(r => r.json())
      .then(data => {
        if (data.success && data.list.length > 0) {
          const already = data.list.some(u => String(u.user_id) === String(authorId));
          if (already) {
            fb.textContent = 'Following';
            fb.classList.add('following');
          }
        }
      })
      .catch(() => {});
  }

  renderComments();
  document.getElementById("pmOverlay").classList.add("show");
  document.body.style.overflow = "hidden";
}

    /* ── Sync Like UI ── */
    function syncModalLike(isLiked) {
      const btn  = document.getElementById("modalLikeBtn");
      const icon = btn.querySelector("i");
      if (isLiked) {
        icon.className   = "bi bi-heart-fill";
        icon.style.color = "#e60023";
        btn.classList.add("liked");
      } else {
        icon.className   = "bi bi-heart";
        icon.style.color = "";
        btn.classList.remove("liked");
      }
    }

    /* ── Sync Save UI ── */
    function syncModalSave(isSaved) {
      const btn = document.getElementById("modalSaveBtn");
      btn.textContent = isSaved ? "Saved" : "Save";
      if (isSaved) btn.classList.add("saved-state");
      else btn.classList.remove("saved-state");
    }

    /* ── Close Modal ── */
    function closePinMod(e) {
      if (!e || e.target === document.getElementById("pmOverlay")) {
        document.getElementById("pmOverlay").classList.remove("show");
        document.body.style.overflow = "";
        document.getElementById("moreMenu").classList.remove("show");
      }
    }

    /* ── Toggle Like (modal button) ── */
    function toggleModalLike() {
      if (!curPin) return;
      toggleLike(curPin.id);
    }

    /* ── Toggle Like (main function) ── */
    function toggleLike(pinId) {
      fetch("components/like_pin.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "pin_id=" + pinId
      })
      .then(res => res.text())
      .then(data => {
        const isLiked = data.trim() === "liked";

        /* Update card data attribute */
        const card = document.querySelector(`.pin-card[data-id="${pinId}"]`);
        if (card) {
          card.dataset.liked = isLiked ? '1' : '0';
          const cardIcon = card.querySelector('.card-actions button[title="Like"] i');
          if (cardIcon) {
            cardIcon.className   = isLiked ? "bi bi-heart-fill" : "bi bi-heart";
            cardIcon.style.color = isLiked ? "#E60023" : "";
          }
        }

        /* Update modal if same pin open */
        if (curPin && curPin.id == pinId) {
          curPin.liked = isLiked;
          syncModalLike(isLiked);
        }
      })
      .catch(err => console.error("Like error:", err));
    }

    /* ── Toggle Save (modal button) ── */
    function toggleModalSave() {
      if (!curPin) return;
      savePin(curPin.id);
    }

    /* ── Save Pin (main function) ── */
    function savePin(pinId) {
      fetch("components/save_pin.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "pin_id=" + pinId
      })
      .then(res => res.text())
      .then(data => {
        const isSaved = data.trim() === "saved";

        /* Update card */
        const card = document.querySelector(`.pin-card[data-id="${pinId}"]`);
        if (card) {
          card.dataset.saved = isSaved ? '1' : '0';
          const csb = card.querySelector('.save-btn');
          if (csb) {
            csb.textContent = isSaved ? "Saved" : "Save";
            if (isSaved) csb.classList.add("saved-state");
            else csb.classList.remove("saved-state");
          }
        }

        /* Update modal if same pin */
        if (curPin && curPin.id == pinId) {
          curPin.saved = isSaved;
          syncModalSave(isSaved);
        }

        /* Toast */
        showToast(isSaved ? "Pin saved " : "Pin removed");
      })
      .catch(err => console.error("Save error:", err));
    }

    /* ── Share ── */
    function sharePin(pinId) {
      const url = window.location.href + (pinId ? "#pin-" + pinId : (curPin ? "#pin-" + curPin.id : ""));
      if (navigator.share) {
        navigator.share({ title: curPin?.title || "Pin", url });
      } else {
        navigator.clipboard.writeText(url);
        showToast("Link copied! 🔗");
      }
    }

    /* ── Follow ── */
    function toggleFollow(btn) {
  if (!curPin) return;

  // Get the pin owner's user_id from the card
  const card = document.querySelector(`.pin-card[data-id="${curPin.id}"]`);
  const targetId = card ? card.dataset.authorId : null;
  if (!targetId) { showToast("Cannot follow this user"); return; }

  const action = btn.textContent.trim() === 'Follow' ? 'follow' : 'unfollow';
  const fd = new FormData();
  fd.append('target_id', targetId);
  fd.append('action', action);

  fetch('components/follow.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        if (action === 'follow') {
          btn.textContent = 'Following';
          btn.classList.add('following');
          showToast('Following ' + curPin.author + ' 🎉');
        } else {
          btn.textContent = 'Follow';
          btn.classList.remove('following');
          showToast('Unfollowed ' + curPin.author);
        }
      } else {
        showToast('Error: ' + (data.message || 'Try again'));
      }
    })
    .catch(() => showToast('Network error. Try again.'));
}

    /* ── More menu ── */
    function toggleMoreMenu(e) {
      e.stopPropagation();
      document.getElementById("moreMenu").classList.toggle("show");
    }
    document.addEventListener("click", function(e) {
      if (!e.target.closest(".pd-actions"))
        document.getElementById("moreMenu").classList.remove("show");
    });
    function downloadPin() {
      if (!curPin) return;
      const a = document.createElement("a");
      a.href = curPin.img; 
      a.download = (curPin.title || "pin") + ".jpg";
      a.click();
      showToast("Downloading...");
      document.getElementById("moreMenu").classList.remove("show");
    }
    function reportPin()       { 
      showToast("Pin reported.");    
     document.getElementById("moreMenu").classList.remove("show"); 
    }
    function seeMoreLikeThis() { 
      showToast("Showing more similar pins");
       document.getElementById("moreMenu").classList.remove("show"); 
      }
    function seeLessLikeThis() { 
      showToast("Showing fewer similar pins");
      document.getElementById("moreMenu").classList.remove("show"); 
    }
    function getEmbedCode() {
      if (!curPin) return;
      navigator.clipboard.writeText(`<img src="${curPin.img}" alt="${curPin.title}">`);
      showToast("Embed code copied!");
      document.getElementById("moreMenu").classList.remove("show");
    }

// Pin modal khulne par real comments load karo
function renderComments() {
  if (!curPin) return;
  const list = document.getElementById("commentsList");
  list.innerHTML = '<p style="color:#999;font-size:0.85rem">Loading comments...</p>';

  fetch("components/get_comments.php?pin_id=" + curPin.id)
    .then(r => r.json())
    .then(data => {
      if (!data.success) { list.innerHTML = ''; return; }
      if (data.comments.length === 0) {
        list.innerHTML = '<p style="color:#999;font-size:0.85rem;padding:4px 0">No comments yet. Be the first! 💬</p>';
        return;
      }
      list.innerHTML = data.comments.map(c => {
        const initial = c.user_name ? c.user_name[0].toUpperCase() : '?';
        const avatar  = c.profile_image
          ? `<img src="${c.profile_image}" alt="${c.user_name}"
                  style="width:32px;height:32px;border-radius:50%;object-fit:cover;flex-shrink:0;" />`
          : `<div style="width:32px;height:32px;border-radius:50%;background:#e60023;color:#fff;
                          display:flex;align-items:center;justify-content:center;font-size:0.85rem;
                          font-weight:700;flex-shrink:0;">${initial}</div>`;
        

return `
  <div class="comment-item" style="margin-bottom:10px;">
    ${avatar}
    <div style="flex:1">
      <div style="font-size:0.75rem;font-weight:700;margin-bottom:2px;">${c.user_name}</div>
      <div class="comment-text">${c.comment}</div>
    </div>
    ${c.is_mine == 1 ? `<button onclick="deleteComment(${c.comment_id})" 
      style="background:none;border:none;cursor:pointer;color:#999;font-size:0.8rem;align-self:flex-start;padding:2px 6px;" 
      title="Delete">
        <svg xmlns="http://w3.org" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 6h18"></path>
          <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
          <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
          <line x1="10" y1="11" x2="10" y2="17"></line>
          <line x1="14" y1="11" x2="14" y2="17"></line>
        </svg>
      </button>` : ''}
  </div>`;
      }).join('');
    })
    .catch(() => { list.innerHTML = ''; });
}

// Comment post karo aur turant show karo
function addComment() {
  const inp = document.getElementById("commentInput");
  const text = inp.value.trim();
  if (!text || !curPin) return;

  const btn = document.querySelector(".comment-input-row button");
  btn.disabled = true;
  btn.textContent = "Posting...";

  const fd = new FormData();
  fd.append('pin_id',  curPin.id);
  fd.append('comment', text);

  fetch("components/add_comment.php", { method: "POST", body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        inp.value = '';
        renderComments(); // Reload all comments including new one
        showToast("Comment posted! ");
      } else {
        showToast("Error: " + (data.message || "Try again"));
      }
    })
    .catch(() => showToast("Network error. Try again."))
    .finally(() => {
      btn.disabled = false;
      btn.textContent = "Post";
    });
}
function deleteComment(commentId) {
  if (!confirm('Delete this comment?')) return;
  const fd = new FormData();
  fd.append('comment_id', commentId);
  fetch('components/delete_comment.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        renderComments();
        showToast('Comment deleted');
      } else {
        showToast('Error: ' + data.message);
      }
    });
}

    /* Search  */
    function filterPins() {
      const q = document.getElementById("searchInput").value.toLowerCase().trim();
      const cards = document.querySelectorAll(".pin-card");
      let found = false;
      cards.forEach(card => {
        const match = card.dataset.title.includes(q) || card.dataset.desc.includes(q) || card.dataset.cat.includes(q);
        card.style.display = match ? "" : "none";
        if (match) found = true;
      });
      const oldMsg = document.getElementById("noResultsMsg");
      if (oldMsg) oldMsg.remove();
      if (!found && q !== "") {
        const msg = document.createElement("div");
        msg.id = "noResultsMsg";
        msg.className = "no-results";
        msg.innerHTML = `<h3>No results found</h3><p>No pins found for "${q}"</p>`;
        document.getElementById("masonryGrid").appendChild(msg);
      }
    }

    function filterCat(cat, el) {
      document.querySelectorAll(".cat-chip").
      forEach(c => c.classList.remove("on"));
      el.classList.add("on");
      document.querySelectorAll(".pin-card").forEach(card => {
        card.style.display = (cat === "all" || card.dataset.cat === cat) ? "" : "none";
      });
    }

    /* ── Toast ── */
    function showToast(msg) {
      document.getElementById("toastMsg").textContent = msg;
      new bootstrap.Toast(document.getElementById("toastEl"), { delay: 2500 }).show();
    }

    /*  Avatar dropdown  */
    function toggleDropdown() { 
      document.getElementById("avatarDD").classList.toggle("show"); }
    document.addEventListener("click", (e) => {
      if (!e.target.closest(".right-side")) 
        document.getElementById("avatarDD").classList.remove("show");
    });

    /*  Escape closes modal  */
    document.addEventListener("keydown", (e) => { 
      if (e.key === "Escape") 
        closePinMod(); 
    });
   function checkNotifBadge() {
  fetch('components/get_notif_count.php')
    .then(r => r.json())
    .then(data => {
      const badge = document.getElementById('notifBadge');
      if (badge) badge.style.display = data.count > 0 ? 'block' : 'none';
    })
    .catch(() => {});
}
checkNotifBadge();
setInterval(checkNotifBadge, 30000);
</script>

<?php include "components/Message-panel.php"; ?>
</body>
<?php include "components/footer.php"; ?>


