<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: /pinterest_project_v5/Pinterest_Final/landing.php');
    exit;
}

$root         = rtrim(str_replace('components', '', __DIR__), '/\\') . DIRECTORY_SEPARATOR;
$pfx          = (strpos(__DIR__, 'components') !== false) ? '../' : '';
$sessionName  = $_SESSION['user_name']  ?? 'User';
$sessionEmail = htmlspecialchars($_SESSION['user_email'] ?? '');
$sessionType  = ucfirst($_SESSION['account_type'] ?? 'Personal');
$firstLetter  = strtoupper(mb_substr(trim($sessionName), 0, 1));
$profileImg   = $_SESSION['profile_image'] ?? '';
if ($profileImg && !str_starts_with($profileImg, 'http')) {
    $profileImg = '../' . ltrim($profileImg, '/');
}

$featured = [
    ['tag' => 'Mellow bliss',       'title' => 'Soft life mood board',         'img' => 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=600&q=80'],
    ['tag' => 'Stitched sentiments', 'title' => 'Hand embroidered quotes',      'img' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80'],
    ['tag' => 'Calming creations',   'title' => 'Art is wellness',              'img' => 'https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=600&q=80'],
    ['tag' => 'Trend edits',         'title' => 'A fresh twist on French tips', 'img' => 'https://images.unsplash.com/photo-1604654894610-df63bc536371?w=600&q=80'],
];

$categories = [
    ['name' => 'Animals',        'img' => 'https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=400&q=80'],
    ['name' => 'Art',            'img' => 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?w=400&q=80'],
    ['name' => 'Beauty',         'img' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=400&q=80'],
    ['name' => 'Design',         'img' => 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=400&q=80'],
    ['name' => 'DIY And Crafts', 'img' => 'https://images.unsplash.com/photo-1452860606245-08befc0ff44b?w=400&q=80'],
    ['name' => 'Food And Drink', 'img' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400&q=80'],
    ['name' => 'Home Decor',     'img' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400&q=80'],
    ['name' => 'Mens Fashion',   'img' => 'https://images.unsplash.com/photo-1490578474895-699cd4e2cf59?w=400&q=80'],
    ['name' => 'Quotes',         'img' => 'https://images.unsplash.com/photo-1507842217343-583bb7270b66?w=400&q=80'],
    ['name' => 'Tattoos',        'img' => 'https://images.unsplash.com/photo-1590246814883-57c511e2a2b2?w=400&q=80'],
    ['name' => 'Travel',         'img' => 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=400&q=80'],
    ['name' => 'Weddings',       'img' => 'https://images.unsplash.com/photo-1519741497674-611481863552?w=400&q=80'],
    ['name' => 'Fitness',        'img' => 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=400&q=80'],
    ['name' => 'Architecture',   'img' => 'https://images.unsplash.com/photo-1486325212027-8081e485255e?w=400&q=80'],
    ['name' => 'Photography',    'img' => 'https://images.unsplash.com/photo-1452587925148-ce544e77e70d?w=400&q=80'],
];

$pins = [
    ['img' => 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308?w=300&q=80', 'duration' => '1:05', 'text' => '', 'h' => 280],
    ['img' => 'https://images.unsplash.com/photo-1464226184884-fa280b87c399?w=300&q=80', 'duration' => '',     'text' => '', 'h' => 220],
    ['img' => 'https://images.unsplash.com/photo-1490750967868-88df5691cc5d?w=300&q=80', 'duration' => '',     'text' => '', 'h' => 250],
    ['img' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300&q=80',   'duration' => '0:46', 'text' => '', 'h' => 210],
    ['img' => 'https://images.unsplash.com/photo-1565958011703-44f9829ba187?w=300&q=80', 'duration' => '',     'text' => 'Lemon blueberry cake with cream cheese frosting', 'h' => 200],
    ['img' => 'https://images.unsplash.com/photo-1472214103451-9374bd1c798e?w=300&q=80', 'duration' => '',     'text' => '', 'h' => 260],
    ['img' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=300&q=80', 'duration' => '1:00', 'text' => "Last minute Mother's Day gift DIY under \$10", 'h' => 230],
    ['img' => 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300&q=80',   'duration' => '',     'text' => '', 'h' => 190],
    ['img' => 'https://images.unsplash.com/photo-1455894127589-22f75500213a?w=300&q=80', 'duration' => '',     'text' => 'Marry me chicken pasta', 'h' => 220],
    ['img' => 'https://images.unsplash.com/photo-1464347744102-11db6282a4a6?w=300&q=80', 'duration' => '0:38', 'text' => '', 'h' => 240],
    ['img' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=300&q=80', 'duration' => '',     'text' => '', 'h' => 260],
    ['img' => 'https://images.unsplash.com/photo-1526045612212-70caf35c14df?w=300&q=80', 'duration' => '',     'text' => 'Sinful triple chocolate poke cake', 'h' => 200],
    ['img' => 'https://images.unsplash.com/photo-1536599524557-5f784dd53282?w=300&q=80', 'duration' => '',     'text' => '', 'h' => 280],
    ['img' => 'https://images.unsplash.com/photo-1604580864964-0462f5d5b1a8?w=300&q=80', 'duration' => '',     'text' => 'Making handmade gifts', 'h' => 220],
    ['img' => 'https://images.unsplash.com/photo-1518495973542-4542c06a5843?w=300&q=80', 'duration' => '',     'text' => '', 'h' => 250],
    ['img' => 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=300&q=80', 'duration' => '0:19', 'text' => '', 'h' => 210],
    ['img' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=300&q=80', 'duration' => '',     'text' => 'Bold glam makeup look', 'h' => 230],
    ['img' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=300&q=80',   'duration' => '',     'text' => '', 'h' => 200],
    ['img' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=300&q=80', 'duration' => '0:15', 'text' => '', 'h' => 190],
    ['img' => 'https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=300&q=80', 'duration' => '',     'text' => '', 'h' => 260],
];
?>
<?php include $root . "components/header.php"; ?>
<?php require_once __DIR__ . "/app_shell.php"; ?>
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pinterest – Explore</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@800;900&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../css/explore.css" />
  <link rel="stylesheet" href="../css/app-shell.css" />
</head>
<body>

<?php render_app_shell([
    'scope' => 'components',
    'active' => 'explore',
]); ?>

<div class="main-content">
  <div class="explore-header">
    <h1>Explore the best of Pinterest</h1>
  </div>

  <div class="featured-grid">
    <?php foreach (array_slice($featured, 0, 3) as $f): ?>
      <a href="Expboard.php?tag=<?= urlencode($f['tag']) ?>&title=<?= urlencode($f['title']) ?>&img=<?= urlencode($f['img']) ?>" style="text-decoration:none;display:block;">
        <div class="feat-card">
          <img src="<?= htmlspecialchars($f['img']) ?>" alt="<?= htmlspecialchars($f['title']) ?>" loading="lazy" />
          <div class="feat-overlay">
            <div class="feat-tag"><?= htmlspecialchars($f['tag']) ?></div>
            <div class="feat-title"><?= htmlspecialchars($f['title']) ?></div>
          </div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>

  <div class="featured-single">
    <a href="Expboard.php?tag=<?= urlencode($featured[3]['tag']) ?>&title=<?= urlencode($featured[3]['title']) ?>&img=<?= urlencode($featured[3]['img']) ?>" style="text-decoration:none;display:block;">
      <div class="feat-card">
        <img src="<?= htmlspecialchars($featured[3]['img']) ?>" alt="<?= htmlspecialchars($featured[3]['title']) ?>" loading="lazy" />
        <div class="feat-overlay">
          <div class="feat-tag"><?= htmlspecialchars($featured[3]['tag']) ?></div>
          <div class="feat-title"><?= htmlspecialchars($featured[3]['title']) ?></div>
        </div>
      </div>
    </a>
  </div>

  <div class="section-wrap">
    <h2 class="section-title">Browse by category</h2>
    <div class="cat-grid" id="catGrid">
      <?php foreach ($categories as $i => $cat): ?>
        <div class="cat-card <?= ($i >= 10) ? 'extra-cat' : '' ?>">
          <img src="<?= htmlspecialchars($cat['img']) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" loading="lazy" />
          <div class="cat-label"><?= htmlspecialchars($cat['name']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
    <button class="btn-see-more" id="seeMoreBtn" onclick="toggleCategories()">See more</button>
  </div>

  <div class="section-wrap masonry-wrap">
    <h2 class="section-title">What's new on Pinterest</h2>
    <div class="masonry" id="pinGrid">
      <?php foreach ($pins as $pin): ?>
        <div class="pin-card" data-type="<?= $pin['duration'] ? 'videos' : 'all' ?>">
          <?php if ($pin['duration']): ?>
            <span class="pin-duration"><?= htmlspecialchars($pin['duration']) ?></span>
          <?php endif; ?>
          <img src="<?= htmlspecialchars($pin['img']) ?>" alt="pin" loading="lazy" style="height:<?= (int)$pin['h'] ?>px;object-fit:cover;" />
          <div class="ov">
            <button class="save-btn" onclick="event.stopPropagation();quickSave(this)">Save</button>
            <div class="card-actions">
              <button onclick="event.stopPropagation()"><i class="bi bi-heart"></i></button>
              <button onclick="event.stopPropagation()"><i class="bi bi-share"></i></button>
            </div>
          </div>
          <button class="dots-btn" onclick="event.stopPropagation()"><i class="bi bi-three-dots"></i></button>
          <?php if ($pin['text']): ?>
            <div class="pin-info"><div class="ptitle"><?= htmlspecialchars($pin['text']) ?></div></div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-4">
      <button class="btn-see-more" id="loadMoreBtn" onclick="loadMorePins()">
        <i class="bi bi-arrow-clockwise me-1"></i> Load more
      </button>
    </div>
  </div>
</div>

<div class="toast-area">
  <div id="toastEl" class="toast align-items-center text-bg-dark border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body" id="toastMsg">Saved!</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function toggleDropdown() {
    document.getElementById('avatarDD').classList.toggle('show');
  }
  document.addEventListener('click', e => {
    if (!e.target.closest('.right-side')) document.getElementById('avatarDD').classList.remove('show');
  });

  function showToast(msg) {
    document.getElementById('toastMsg').textContent = msg;
    new bootstrap.Toast(document.getElementById('toastEl'), { delay: 2200 }).show();
  }

  function quickSave(btn) {
    if (btn.classList.contains('saved')) {
      btn.textContent = 'Save'; btn.classList.remove('saved'); btn.style.background = '';
    } else {
      btn.textContent = 'Saved ✓'; btn.classList.add('saved'); btn.style.background = '#111';
      showToast('Saved!');
    }
  }

  let catsOpen = false;
  function toggleCategories() {
    catsOpen = !catsOpen;
    document.querySelectorAll('.extra-cat').forEach(el => el.classList.toggle('visible', catsOpen));
    document.getElementById('seeMoreBtn').textContent = catsOpen ? 'See less' : 'See more';
  }

  const morePins = [
    { img: 'https://images.unsplash.com/photo-1519741497674-611481863552?w=300&q=80', duration: '', text: 'Dream wedding inspo', h: 240 },
    { img: 'https://images.unsplash.com/photo-1486325212027-8081e485255e?w=300&q=80', duration: '', text: '', h: 220 },
    { img: 'https://images.unsplash.com/photo-1452587925148-ce544e77e70d?w=300&q=80', duration: '', text: '', h: 260 },
    { img: 'https://images.unsplash.com/photo-1490578474895-699cd4e2cf59?w=300&q=80', duration: '0:30', text: 'Street style lookbook', h: 280 },
    { img: 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=300&q=80', duration: '', text: '', h: 200 },
  ];
  let loadRound = 0;
  function loadMorePins() {
    if (loadRound >= 3) {
      document.getElementById('loadMoreBtn').textContent = 'No more pins';
      document.getElementById('loadMoreBtn').disabled = true;
      return;
    }
    const grid = document.getElementById('pinGrid');
    morePins.forEach(p => {
      const div = document.createElement('div');
      div.className = 'pin-card';
      div.dataset.type = p.duration ? 'videos' : 'all';
      div.innerHTML = `
        ${p.duration ? `<span class="pin-duration">${p.duration}</span>` : ''}
        <img src="${p.img}" alt="pin" loading="lazy" style="height:${p.h}px;object-fit:cover;" />
        <div class="ov">
          <button class="save-btn" onclick="event.stopPropagation();quickSave(this)">Save</button>
          <div class="card-actions">
            <button onclick="event.stopPropagation()"><i class="bi bi-heart"></i></button>
            <button onclick="event.stopPropagation()"><i class="bi bi-share"></i></button>
          </div>
        </div>
        <button class="dots-btn" onclick="event.stopPropagation()"><i class="bi bi-three-dots"></i></button>
        ${p.text ? `<div class="pin-info"><div class="ptitle">${p.text}</div></div>` : ''}
      `;
      grid.appendChild(div);
    });
    loadRound++;
  }

  function checkNotifBadge() {
    fetch('get_notif_count.php')
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
<?php include $root . "components/Message-panel.php"; ?>
</body>
<?php include $root . "components/footer.php"; ?>


