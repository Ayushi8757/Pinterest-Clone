<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: ' . (strstr(__DIR__, 'components') ? '../' : '') . 'components/login.php');
    exit;
}

$root = rtrim(str_replace('components', '', __DIR__), '/\\') . DIRECTORY_SEPARATOR;

// Get board info from URL params (passed from explore.php featured cards)
$boardTag   = htmlspecialchars($_GET['tag']   ?? 'Featured');
$boardTitle = htmlspecialchars($_GET['title'] ?? 'Board');
$boardImg   = htmlspecialchars($_GET['img']   ?? '');

// Board descriptions keyed by tag
$descriptions = [
    'Mellow bliss'       => 'Your reminder to come back to the little things. Warm light, slow mornings, calming hobbies and a moment to pause.',
    'Stitched sentiments'=> 'Beautiful hand-stitched quotes and embroidery art that speak to the soul. A celebration of slow, intentional craft.',
    'Calming creations'  => 'Art as a form of therapy. Discover how creativity heals, grounds, and restores a sense of wonder.',
    'Trend edits'        => 'The freshest nail art looks, from minimalist to maximalist. Stay ahead of every tip trend.',
];
$boardDesc = $descriptions[$boardTag] ?? 'A curated collection of inspiring pins.';

// Pin count (random-ish based on tag)
$pinCounts = ['Mellow bliss' => 47, 'Stitched sentiments' => 32, 'Calming creations' => 28, 'Trend edits' => 61];
$pinCount  = $pinCounts[$boardTag] ?? 40;

// Board pins — different sets per tag
$pinSets = [
    'Mellow bliss' => [
        ['img' => 'https://images.unsplash.com/photo-1490750967868-88df5691cc5d?w=400&q=80', 'h' => 280, 'text' => ''],
        ['img' => 'https://images.unsplash.com/photo-1464226184884-fa280b87c399?w=400&q=80', 'h' => 220, 'text' => 'Morning light ritual'],
        ['img' => 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308?w=400&q=80', 'h' => 260, 'text' => ''],
        ['img' => 'https://images.unsplash.com/photo-1472214103451-9374bd1c798e?w=400&q=80', 'h' => 300, 'text' => 'Golden hour walks'],
        ['img' => 'https://images.unsplash.com/photo-1518495973542-4542c06a5843?w=400&q=80', 'h' => 240, 'text' => ''],
        ['img' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400&q=80', 'h' => 200, 'text' => 'Cosy corner'],
        ['img' => 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=400&q=80', 'h' => 260, 'text' => 'Soft life'],
        ['img' => 'https://images.unsplash.com/photo-1507842217343-583bb7270b66?w=400&q=80', 'h' => 220, 'text' => 'Today I choose calm'],
        ['img' => 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=400&q=80', 'h' => 250, 'text' => ''],
        ['img' => 'https://images.unsplash.com/photo-1519741497674-611481863552?w=400&q=80', 'h' => 280, 'text' => ''],
        ['img' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400&q=80', 'h' => 210, 'text' => 'Slow breakfast'],
        ['img' => 'https://images.unsplash.com/photo-1452860606245-08befc0ff44b?w=400&q=80', 'h' => 240, 'text' => ''],
    ],
    'Stitched sentiments' => [
        ['img' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&q=80', 'h' => 260, 'text' => 'Hand embroidered love'],
        ['img' => 'https://images.unsplash.com/photo-1604580864964-0462f5d5b1a8?w=400&q=80', 'h' => 220, 'text' => 'Making handmade gifts'],
        ['img' => 'https://images.unsplash.com/photo-1452587925148-ce544e77e70d?w=400&q=80', 'h' => 280, 'text' => ''],
        ['img' => 'https://images.unsplash.com/photo-1536599524557-5f784dd53282?w=400&q=80', 'h' => 240, 'text' => ''],
        ['img' => 'https://images.unsplash.com/photo-1490578474895-699cd4e2cf59?w=400&q=80', 'h' => 200, 'text' => 'Stitch by stitch'],
        ['img' => 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?w=400&q=80', 'h' => 260, 'text' => 'Art in every thread'],
        ['img' => 'https://images.unsplash.com/photo-1507842217343-583bb7270b66?w=400&q=80', 'h' => 220, 'text' => 'Quotes in thread'],
        ['img' => 'https://images.unsplash.com/photo-1526045612212-70caf35c14df?w=400&q=80', 'h' => 250, 'text' => 'Sweet stitches'],
        ['img' => 'https://images.unsplash.com/photo-1465146344425-f00d5f5c8f07?w=400&q=80', 'h' => 230, 'text' => ''],
        ['img' => 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308?w=400&q=80', 'h' => 270, 'text' => ''],
    ],
    'Calming creations' => [
        ['img' => 'https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=400&q=80', 'h' => 260, 'text' => 'Art is wellness'],
        ['img' => 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?w=400&q=80', 'h' => 240, 'text' => 'Painting as therapy'],
        ['img' => 'https://images.unsplash.com/photo-1472214103451-9374bd1c798e?w=400&q=80', 'h' => 280, 'text' => ''],
        ['img' => 'https://images.unsplash.com/photo-1518495973542-4542c06a5843?w=400&q=80', 'h' => 200, 'text' => 'Light and colour'],
        ['img' => 'https://images.unsplash.com/photo-1536599524557-5f784dd53282?w=400&q=80', 'h' => 250, 'text' => ''],
        ['img' => 'https://images.unsplash.com/photo-1455894127589-22f75500213a?w=400&q=80', 'h' => 220, 'text' => 'Creative calm'],
        ['img' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&q=80', 'h' => 260, 'text' => ''],
        ['img' => 'https://images.unsplash.com/photo-1465146344425-f00d5f5c8f07?w=400&q=80', 'h' => 230, 'text' => 'Nature as muse'],
        ['img' => 'https://images.unsplash.com/photo-1464347744102-11db6282a4a6?w=400&q=80', 'h' => 270, 'text' => ''],
        ['img' => 'https://images.unsplash.com/photo-1490750967868-88df5691cc5d?w=400&q=80', 'h' => 210, 'text' => 'Still life'],
    ],
    'Trend edits' => [
        ['img' => 'https://images.unsplash.com/photo-1604654894610-df63bc536371?w=400&q=80', 'h' => 260, 'text' => 'French tips reinvented'],
        ['img' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=400&q=80', 'h' => 240, 'text' => 'Bold glam'],
        ['img' => 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=400&q=80', 'h' => 200, 'text' => ''],
        ['img' => 'https://images.unsplash.com/photo-1490578474895-699cd4e2cf59?w=400&q=80', 'h' => 270, 'text' => 'Street style lookbook'],
        ['img' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=400&q=80', 'h' => 230, 'text' => 'Pastel season'],
        ['img' => 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308?w=400&q=80', 'h' => 250, 'text' => ''],
        ['img' => 'https://images.unsplash.com/photo-1604580864964-0462f5d5b1a8?w=400&q=80', 'h' => 220, 'text' => 'Nail art inspo'],
        ['img' => 'https://images.unsplash.com/photo-1490750967868-88df5691cc5d?w=400&q=80', 'h' => 260, 'text' => ''],
        ['img' => 'https://images.unsplash.com/photo-1536599524557-5f784dd53282?w=400&q=80', 'h' => 280, 'text' => 'Minimal chic'],
        ['img' => 'https://images.unsplash.com/photo-1465146344425-f00d5f5c8f07?w=400&q=80', 'h' => 210, 'text' => ''],
        ['img' => 'https://images.unsplash.com/photo-1472214103451-9374bd1c798e?w=400&q=80', 'h' => 240, 'text' => 'Colour block trend'],
        ['img' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&q=80', 'h' => 220, 'text' => ''],
    ],
];

$pins = $pinSets[$boardTag] ?? $pinSets['Mellow bliss'];
?>
<?php include $root . "components/header.php"; ?>
<?php require_once __DIR__ . "/app_shell.php"; ?>
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $boardTitle ?> – Pinterest</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@800;900&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="../css/expboard.css" />
  <link rel="stylesheet" href="../css/app-shell.css" />
</head>
<body><?php render_app_shell([
    'scope' => 'components',
    'active' => 'boards',
]); ?>

  <div class="main-content">

    <!-- Back link -->
    <a href="explore.php" class="back-link">
      <i class="bi bi-arrow-left"></i> Back to Explore
    </a>

    <!-- Board header -->
    <div class="board-header">
      <div class="board-tag"><?= $boardTag ?></div>
      <h1 class="board-title"><?= $boardTitle ?></h1>
      <div class="board-meta">
        <i class="bi bi-bookmark-fill" style="color:var(--red)"></i>
        Featured board &nbsp;·&nbsp; <?= $pinCount ?> Pins
      </div>
      <p class="board-desc"><?= $boardDesc ?></p>
      <div class="board-by">
        <svg viewBox="0 0 24 24">
          <path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/>
        </svg>
        <span>by Inspiration</span>
      </div>
      <div class="board-actions">
        <button class="btn-share" onclick="showToast('Link copied!')">
          <i class="bi bi-box-arrow-up"></i> Share
        </button>
        <button class="btn-more">
          <i class="bi bi-three-dots"></i>
        </button>
      </div>
    </div>

    <!-- Pin masonry -->
    <div class="masonry-wrap">
      <div class="masonry" id="pinGrid">
        <?php foreach ($pins as $pin): ?>
          <div class="pin-card">
            <img src="<?= htmlspecialchars($pin['img']) ?>" alt="pin" loading="lazy"
                 style="height:<?= (int)$pin['h'] ?>px; object-fit:cover;" />
            <div class="ov">
              <button class="save-btn" onclick="event.stopPropagation(); quickSave(this)">Save</button>
              <div class="card-actions">
                <button onclick="event.stopPropagation()"><i class="bi bi-heart"></i></button>
                <button onclick="event.stopPropagation()"><i class="bi bi-share"></i></button>
              </div>
            </div>
            <?php if ($pin['text']): ?>
              <div class="pin-info"><div class="ptitle"><?= htmlspecialchars($pin['text']) ?></div></div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
  <!-- /main-content -->

  <!-- Toast -->
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
      if (!e.target.closest('.right-side'))
        document.getElementById('avatarDD').classList.remove('show');
    });

    function showToast(msg) {
      document.getElementById('toastMsg').textContent = msg;
      new bootstrap.Toast(document.getElementById('toastEl'), { delay: 2200 }).show();
    }

    function quickSave(btn) {
      if (btn.classList.contains('saved')) {
        btn.textContent = 'Save';
        btn.classList.remove('saved');
        btn.style.background = '';
      } else {
        btn.textContent = 'Saved ✓';
        btn.classList.add('saved');
        btn.style.background = '#111';
        showToast('Saved! ✅');
      }
    }
  </script>

</body>
<?php include $root . "components/footer.php"; ?>


