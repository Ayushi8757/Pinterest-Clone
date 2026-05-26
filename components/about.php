<?php include "header.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About Pinterest – Find and explore what you actually like</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@800;900&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --red: #e60023;
      --red-dark: #ad081b;
      --text: #111;
      --muted: #6b6b6b;
      --sbg: #f0eeeb;
      --nav-h: 60px;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body { font-family: "DM Sans", sans-serif; background: #fff; color: var(--text); overflow-x: hidden; }

    /* ── NAV ── */
    .anav {
      position: sticky; top: 0; z-index: 1000;
      height: var(--nav-h); background: #fff;
      display: flex; align-items: center;
      padding: 0 24px; justify-content: space-between;
    }
    .anav-logo {
      display: flex; align-items: center; gap: 8px;
      border: 1.5px solid #ddd; border-radius: 24px;
      padding: 7px 16px; text-decoration: none; color: var(--text);
      font-weight: 700; font-size: 0.88rem; background: #fff;
      transition: background 0.2s;
    }
    .anav-logo:hover { background: var(--sbg); }
    .anav-logo svg { width: 22px; height: 22px; fill: var(--red); }
    .anav-right { display: flex; gap: 10px; align-items: center; }
    .btn-alog {
      background: none; border: none; font-weight: 600; font-size: 0.9rem;
      cursor: pointer; font-family: "DM Sans", sans-serif; color: var(--text);
      padding: 8px 16px; border-radius: 24px; text-decoration: none;
      transition: background 0.2s;
    }
    .btn-alog:hover { background: var(--sbg); }
    .btn-asignup {
      background: var(--red); color: #fff; border: none; border-radius: 24px;
      padding: 10px 22px; font-weight: 700; font-size: 0.9rem; cursor: pointer;
      font-family: "DM Sans", sans-serif; text-decoration: none;
      transition: background 0.2s;
    }
    .btn-asignup:hover { background: var(--red-dark); }

    /* ── HERO FULLBLEED ── */
    .hero-fullbleed {
      position: relative; width: 100%; height: 72vh; min-height: 480px;
      overflow: hidden;
    }
    .hero-fullbleed img {
      width: 100%; height: 100%; object-fit: cover; display: block;
    }
    .hero-board-badge {
      position: absolute; left: 50%; bottom: 50%;
      transform: translate(-50%, 50%);
      background: #fff; border-radius: 20px;
      padding: 14px 20px; display: flex; align-items: center; gap: 14px;
      box-shadow: 0 4px 24px rgba(0,0,0,0.15);
      min-width: 260px;
    }
    .hero-board-badge img {
      width: 60px; height: 60px; border-radius: 10px; object-fit: cover;
      flex-shrink: 0;
    }
    .hero-board-badge .hbb-title { font-weight: 900; font-size: 1.15rem; }
    .hero-board-badge .hbb-meta { font-size: 0.82rem; color: var(--muted); margin-top: 2px; }

    /* ── MANIFESTO ── */
    .manifesto {
      padding: 80px 48px;
      text-align: center;
    }
    .manifesto-text {
      font-family: "Nunito", sans-serif; font-weight: 900;
      font-size: clamp(2rem, 4.5vw, 3.6rem);
      line-height: 1.18; max-width: 1000px; margin: 0 auto;
    }
    .manifesto-text .inline-el {
      display: inline-flex; align-items: center;
      vertical-align: middle; margin: 0 8px;
    }
    .search-pill {
      display: inline-flex; align-items: center; gap: 6px;
      border: 1.5px solid #ccc; border-radius: 20px;
      padding: 4px 14px; font-size: 0.7em; font-weight: 600;
      background: #fff; vertical-align: middle; margin: 0 6px;
      font-family: "DM Sans", sans-serif;
    }
    .search-pill i { font-size: 0.85em; color: var(--muted); }
    .pin-thumb {
      display: inline-block; width: 54px; height: 54px;
      border-radius: 10px; overflow: hidden; vertical-align: middle;
      margin: 0 4px; position: relative;
    }
    .pin-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .pin-thumb .save-icon {
      position: absolute; bottom: 3px; right: 3px;
      background: rgba(0,0,0,0.55); border-radius: 50%;
      width: 18px; height: 18px;
      display: flex; align-items: center; justify-content: center;
      font-size: 0.55rem; color: #fff;
    }

    /* ── FEATURE ROWS ── */
    .feat-section { padding: 80px 60px; }
    .feat-section.alt { background: #f9f9f9; }
    .feat-inner {
      max-width: 1080px; margin: 0 auto;
      display: flex; align-items: center; gap: 80px;
    }
    .feat-inner.rev { flex-direction: row-reverse; }
    .feat-txt { flex: 1; }
    .feat-txt h2 {
      font-family: "Nunito", sans-serif; font-weight: 900;
      font-size: clamp(1.8rem, 3vw, 2.6rem); margin-bottom: 18px; line-height: 1.15;
    }
    .feat-txt p { color: var(--muted); font-size: 0.97rem; line-height: 1.7; margin-bottom: 28px; max-width: 420px; }
    .btn-feat {
      display: inline-block; background: var(--text); color: #fff; border: none;
      border-radius: 24px; padding: 13px 28px; font-weight: 700; font-size: 0.93rem;
      cursor: pointer; text-decoration: none; font-family: "DM Sans", sans-serif;
      transition: background 0.2s;
    }
    .btn-feat:hover { background: #333; color: #fff; }
    .feat-visual { flex: 1; display: flex; justify-content: center; }

    /* Board card */
    .board-card {
      background: #fff; border-radius: 22px;
      box-shadow: 0 4px 32px rgba(0,0,0,0.1);
      overflow: hidden; max-width: 400px; width: 100%;
    }
    .board-card .bc-imgs {
      display: grid; grid-template-columns: 2fr 1fr; height: 240px; gap: 2px;
    }
    .board-card .bc-imgs img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .board-card .bc-imgs img:first-child { grid-row: 1 / -1; }
    .board-card .bc-meta {
      padding: 14px 16px; display: flex; align-items: center; justify-content: space-between;
    }
    .board-card .bc-title { font-weight: 700; font-size: 1rem; }
    .board-card .bc-count { font-size: 0.8rem; color: var(--muted); margin-top: 2px; }
    .board-card .bc-av {
      width: 36px; height: 36px; border-radius: 50%;
      object-fit: cover; border: 2px solid #fff;
    }

    /* Visual search mock */
    .vsearch-mock {
      max-width: 420px; width: 100%; border-radius: 20px;
      overflow: hidden; box-shadow: 0 4px 32px rgba(0,0,0,0.12);
    }
    .vsearch-main {
      position: relative; background: #1a5f8a;
      height: 320px; overflow: hidden;
    }
    .vsearch-main img { width: 100%; height: 100%; object-fit: cover; }
    .vsearch-overlay {
      position: absolute; inset: 0;
      display: flex; align-items: center; justify-content: center;
    }
    .vsearch-box {
      border: 3px solid rgba(255,255,255,0.8);
      border-radius: 12px; width: 160px; height: 160px;
      box-shadow: 0 0 0 9999px rgba(0,0,0,0.35);
    }
    .vsearch-icons {
      position: absolute; top: 14px; left: 0; right: 0;
      display: flex; justify-content: space-between; padding: 0 16px;
      color: #fff; font-size: 1.1rem;
    }
    .vsearch-bottom { background: #fff; padding: 16px; }
    .vsearch-bottom .vb-label { font-weight: 700; font-size: 0.92rem; margin-bottom: 10px; }
    .vsearch-bottom .vb-imgs { display: flex; gap: 6px; }
    .vsearch-bottom .vb-imgs img {
      flex: 1; height: 72px; object-fit: cover; border-radius: 8px;
    }

    /* Shopping mock */
    .shop-mock {
      max-width: 400px; width: 100%; border-radius: 20px;
      overflow: hidden; box-shadow: 0 4px 32px rgba(0,0,0,0.12);
      position: relative;
    }
    .shop-mock > img { width: 100%; height: 380px; object-fit: cover; display: block; }
    .price-tag {
      position: absolute; top: 36px; left: 20px;
      background: #fff; border-radius: 14px; padding: 10px 14px;
      display: flex; align-items: center; gap: 10px;
      box-shadow: 0 2px 14px rgba(0,0,0,0.12);
    }
    .price-tag img { width: 40px; height: 40px; border-radius: 8px; object-fit: cover; }
    .price-tag .pt-price { font-weight: 700; font-size: 0.95rem; }
    .price-tag .pt-name { font-size: 0.75rem; color: var(--muted); }
    .shop-now-btn {
      position: absolute; bottom: 0; left: 0; right: 0;
      background: var(--red); color: #fff; border: none;
      padding: 16px; font-weight: 700; font-size: 1rem;
      text-align: center; cursor: pointer; font-family: "DM Sans", sans-serif;
    }

    /* ── POSITIVE PLACE ── */
    .positive-section { padding: 80px 40px; background: var(--sbg); }
    .positive-section h2 {
      font-family: "Nunito", sans-serif; font-weight: 900;
      font-size: clamp(1.8rem, 3.5vw, 2.8rem);
      text-align: center; margin-bottom: 48px;
    }
    .pos-grid {
      display: grid; grid-template-columns: repeat(4, 1fr);
      gap: 16px; max-width: 1200px; margin: 0 auto;
    }
    .pos-card { background: #fff; border-radius: 20px; overflow: hidden; }
    .pos-card .pc-img { width: 100%; height: 340px; object-fit: cover; display: block; }
    .pos-card .pc-body { padding: 20px; }
    .pos-card .pc-body h4 { font-weight: 700; font-size: 1.05rem; margin-bottom: 10px; line-height: 1.25; }
    .pos-card .pc-body p { color: var(--muted); font-size: 0.85rem; line-height: 1.65; }
    .pos-card .pc-body a { color: var(--text); }

    /* ── APP DOWNLOAD ── */
    .app-section {
      background: #f5ede3; padding: 60px 40px;
      display: flex; align-items: center; justify-content: center;
      gap: 60px; position: relative; overflow: hidden; min-height: 360px;
    }
    .app-phones { display: flex; gap: -20px; align-items: flex-end; }
    .app-phones .phone-wrap {
      width: 200px; border-radius: 28px; overflow: hidden;
      box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    }
    .app-phones .phone-wrap img { width: 100%; display: block; }
    .app-phones .phone-wrap:last-child { margin-left: -30px; margin-bottom: -20px; }
    .app-txt { text-align: center; max-width: 400px; }
    .app-txt h2 {
      font-family: "Nunito", sans-serif; font-weight: 900;
      font-size: clamp(1.5rem, 3vw, 2.2rem); margin-bottom: 32px; line-height: 1.25;
    }
    .app-btns { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
    .btn-app {
      background: var(--text); color: #fff; border: none; border-radius: 24px;
      padding: 13px 28px; font-weight: 700; font-size: 0.93rem; cursor: pointer;
      text-decoration: none; font-family: "DM Sans", sans-serif;
      transition: background 0.2s;
    }
    .btn-app:hover { background: #333; color: #fff; }

    /* ── COMMUNITY ── */
    .community-section { padding: 70px 40px; text-align: center; }
    .community-section h2 {
      font-family: "Nunito", sans-serif; font-weight: 900;
      font-size: clamp(1.8rem, 3.5vw, 2.8rem); margin-bottom: 48px;
    }
    .comm-grid {
      display: grid; grid-template-columns: repeat(3, 1fr);
      gap: 40px; max-width: 960px; margin: 0 auto 48px; text-align: left;
    }
    .comm-card h4 { font-weight: 700; font-size: 1.1rem; margin-bottom: 10px; }
    .comm-card p { color: var(--muted); font-size: 0.88rem; line-height: 1.65; margin-bottom: 14px; }
    .comm-card a {
      font-weight: 700; font-size: 0.9rem; color: var(--text);
      text-decoration: none; display: flex; align-items: center; gap: 4px;
    }
    .comm-card a:hover { text-decoration: underline; }

    /* Footnotes accordion */
    .footnotes-section { padding: 0 40px; max-width: 1200px; margin: 0 auto; }
    .footnotes-toggle {
      width: 100%; text-align: left; background: none; border: none;
      border-top: 1px solid #ddd; padding: 18px 0;
      font-weight: 700; font-size: 0.92rem; cursor: pointer;
      display: flex; align-items: center; justify-content: space-between;
      font-family: "DM Sans", sans-serif; color: var(--text);
    }
    .footnotes-body { display: none; padding: 0 0 16px; color: var(--muted); font-size: 0.82rem; }
    .footnotes-body.open { display: block; }

    /* ── FOOTER ── */
    .a-footer { padding: 56px 80px 32px; border-top: 1px solid #e8e8e8; }
    .a-footer .footer-logo {
      font-size: 2rem; font-weight: 900; margin-bottom: 28px; display: block; color: var(--text);
      text-decoration: none;
    }
    .a-footer .lang-sel {
      display: inline-flex; align-items: center; gap: 8px;
      border: 1.5px solid #ddd; border-radius: 24px; padding: 7px 14px;
      font-size: 0.84rem; cursor: pointer; background: #fff; margin-bottom: 36px;
    }
    .af-grid {
      display: grid; grid-template-columns: repeat(3, 1fr);
      gap: 32px; max-width: 700px; margin-left: auto;
    }
    .af-col { margin-left: auto; text-align: left; }
    .af-col h6 { font-weight: 700; font-size: 0.82rem; margin-bottom: 14px; color: var(--text); }
    .af-col a { display: block; color: #555; font-size: 0.88rem; text-decoration: none; margin-bottom: 10px; transition: color 0.2s; }
    .af-col a:hover { color: var(--text); }
    .af-wrap { display: flex; gap: 40px; align-items: flex-start; }
    .af-left { flex: 0 0 260px; }
    .af-right { flex: 1; }

    /* Fade in */
    .fade-up { opacity: 0; transform: translateY(28px); transition: opacity 0.6s, transform 0.6s; }
    .fade-up.vis { opacity: 1; transform: translateY(0); }

    @media (max-width: 960px) {
      .feat-inner, .feat-inner.rev { flex-direction: column; gap: 36px; }
      .pos-grid { grid-template-columns: 1fr 1fr; }
      .comm-grid { grid-template-columns: 1fr; }
      .app-section { flex-direction: column; }
      .af-wrap { flex-direction: column; }
      .af-grid { margin-left: 0; grid-template-columns: 1fr 1fr; }
      .a-footer { padding: 36px 20px 20px; }
      .feat-section { padding: 60px 24px; }
      .manifesto { padding: 60px 20px; }
    }
    @media (max-width: 600px) {
      .pos-grid { grid-template-columns: 1fr; }
      .af-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

  <!-- ── NAV ── -->
  <nav class="anav">
    <a href="../landing.php" class="anav-logo">
      <svg viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>
      About &nbsp;<i class="bi bi-list" style="font-size:1rem;"></i>
    </a>
    <div class="anav-right">
      <a href="../landing.php" class="btn-alog">Log in</a>
      <a href="register.php" class="btn-asignup">Sign up</a>
    </div>
  </nav>

  <!-- ── HERO FULLBLEED ── -->
  <section class="hero-fullbleed">
    <img src="https://picsum.photos/seed/nails_about/1600/900" alt="Bold nail ideas" />
    <div class="hero-board-badge">
      <img src="https://picsum.photos/seed/nails_thumb/120/120" alt="" />
      <div>
        <div class="hbb-title">Bold nail ideas</div>
        <div class="hbb-meta">48 Pins · 2 sections</div>
      </div>
    </div>
  </section>

  <!-- ── MANIFESTO ── -->
  <section class="manifesto fade-up">
    <div class="manifesto-text">
      <svg viewBox="0 0 24 24" width="52" height="52" fill="#E60023" style="vertical-align:middle;margin-right:8px;"><path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>
      Pinterest is your space to find and
      <span class="search-pill"><i class="bi bi-search"></i> Search image <i class="bi bi-arrow-repeat"></i></span>
      explore what you actually like, not just what gets likes. Save
      <span class="pin-thumb"><img src="https://picsum.photos/seed/pin_fashion/108/108" alt="" /></span>
      Pins,
      <span class="pin-thumb"><img src="https://picsum.photos/seed/pin_boots/108/108" alt="" /><span class="save-icon"><i class="bi bi-tag-fill"></i></span></span>
      shop what you love and follow your taste wherever it takes you.
    </div>
  </section>

  <!-- ── FIND WHAT INSPIRES ── -->
  <section class="feat-section fade-up">
    <div class="feat-inner">
      <div class="feat-txt">
        <h2>Find what inspires you</h2>
        <p>Explore visual ideas (Pins) and get to know boards — collections of Pins that bring ideas together. You'll find a steady stream of fresh inspiration, from Featured boards handpicked by experts to AI-powered boards tailored to your taste.</p>
        <a href="register.php" class="btn-feat">Discover new ideas</a>
      </div>
      <div class="feat-visual">
        <div class="board-card">
          <div class="bc-imgs">
            <img src="https://picsum.photos/seed/heels_main/400/480" alt="" />
            <img src="https://picsum.photos/seed/heels2/200/240" alt="" />
            <img src="https://picsum.photos/seed/heels3/200/240" alt="" />
          </div>
          <div class="bc-meta">
            <div>
              <div class="bc-title">Party heels</div>
              <div class="bc-count">33 Pins · 2 sections</div>
            </div>
            <img class="bc-av" src="https://i.pravatar.cc/72?img=47" alt="" />
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ── TUNE YOUR TASTE ── -->
  <section class="feat-section alt fade-up">
    <div class="feat-inner rev">
      <div class="feat-txt">
        <h2>Tune your taste</h2>
        <p>See more of what you love and discover ideas that match your style — even when you don't have the words yet. Turn them into visual collages and save your favourite Pins to boards to plan your next project.</p>
        <a href="register.php" class="btn-feat">Try visual search</a>
      </div>
      <div class="feat-visual">
        <div class="vsearch-mock">
          <div class="vsearch-main">
            <img src="https://picsum.photos/seed/denim_jacket/420/320" alt="" />
            <div class="vsearch-icons">
              <span>✕</span><span>✂</span>
            </div>
            <div class="vsearch-overlay">
              <div class="vsearch-box"></div>
            </div>
          </div>
          <div class="vsearch-bottom">
            <div class="vb-label">Shop similar</div>
            <div class="vb-imgs">
              <img src="https://picsum.photos/seed/sim1/140/144" alt="" />
              <img src="https://picsum.photos/seed/sim2/140/144" alt="" />
              <img src="https://picsum.photos/seed/sim3/140/144" alt="" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ── BRING IDEAS TO LIFE ── -->
  <section class="feat-section fade-up">
    <div class="feat-inner">
      <div class="feat-txt">
        <h2>Bring your ideas to life</h2>
        <p>Save what you love and shop for it directly from your board. Turn the ideas that you discover into pieces for your home, your wardrobe and your everyday life.</p>
        <a href="register.php" class="btn-feat">Shop on Pinterest</a>
      </div>
      <div class="feat-visual">
        <div class="shop-mock">
          <img src="https://picsum.photos/seed/velvet_chair/400/420" alt="" />
          <div class="price-tag">
            <img src="https://picsum.photos/seed/chair_thumb/80/80" alt="" />
            <div>
              <div class="pt-price">£390</div>
              <div class="pt-name">Suede accent…</div>
            </div>
          </div>
          <button class="shop-now-btn">Shop now</button>
        </div>
      </div>
    </div>
  </section>

  <!-- ── POSITIVE PLACE ── -->
  <section class="positive-section fade-up">
    <h2>All in a more positive place online</h2>
    <div class="pos-grid">
      <div class="pos-card">
        <img class="pc-img" src="https://picsum.photos/seed/body_types/380/340" alt="Body types" />
        <div class="pc-body">
          <h4>Inspiration for every body</h4>
          <p>Find inspiration that reflects your body type with filters that let you discover relevant outfits and ideas that are true to you.</p>
        </div>
      </div>
      <div class="pos-card">
        <img class="pc-img" src="https://picsum.photos/seed/wellbeing/380/340" alt="Well-being" />
        <div class="pc-body">
          <h4>Well-being, online and offline</h4>
          <p>Through the Pinterest Impact Fund, we've invested £22.5M in 60+ <a href="#">non-profit organisations</a> to fuel emotional well-being in our communities.<sup>1</sup></p>
        </div>
      </div>
      <div class="pos-card">
        <img class="pc-img" src="https://picsum.photos/seed/teen_safety/380/340" alt="Teen safety" />
        <div class="pc-body">
          <h4>Teen safety tools</h4>
          <p>You and your family choose who can contact you or see your content on Pinterest with age verification and parental passcodes.</p>
        </div>
      </div>
      <div class="pos-card">
        <img class="pc-img" src="https://picsum.photos/seed/skin_search/380/340" alt="See yourself in search" />
        <div class="pc-body">
          <h4>See yourself in search</h4>
          <p>Customise your search results by skin tone range and hair type, so you can see how a lipstick or hat might look on you.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ── APP DOWNLOAD ── -->
  <section class="app-section fade-up">
    <div class="app-phones">
      <div class="phone-wrap">
        <img src="https://picsum.photos/seed/phone_app1/200/400" alt="" />
      </div>
      <div class="phone-wrap">
        <img src="https://picsum.photos/seed/phone_app2/200/400" alt="" />
      </div>
    </div>
    <div class="app-txt">
      <h2>Download the app to start creating a life you love.</h2>
      <div class="app-btns">
        <a href="#" class="btn-app">Download on iOS</a>
        <a href="#" class="btn-app">Download on Android</a>
      </div>
    </div>
  </section>

  <!-- ── JOIN COMMUNITY ── -->
  <section class="community-section fade-up">
    <h2>Join the Pinterest community</h2>
    <div class="comm-grid">
      <div class="comm-card">
        <h4>Advertise with us</h4>
        <p>Reach your audience from first scroll to final purchase with Pinterest ads. Create a free business account to tap into marketing tools.</p>
        <a href="business.php" target="_blank">Get started ↗</a>
      </div>
      <div class="comm-card">
        <h4>Create on Pinterest</h4>
        <p>Go from camera roll to can't-miss Pins with easy tips, tools and creative ideas designed to boost performance.</p>
        <a href="create-pin.php">Start creating ↗</a>
      </div>
      <div class="comm-card">
        <h4>Join our team</h4>
        <p>Join the team behind Pinterest and help people to turn their ideas into real-life plans, all in the most positive place online.</p>
        <a href="#">Explore jobs ↗</a>
      </div>
    </div>

    <!-- Footnotes -->
    <div class="footnotes-section">
      <button class="footnotes-toggle" onclick="this.nextElementSibling.classList.toggle('open')">
        Footnotes <span style="font-size:1.2rem;">+</span>
      </button>
      <div class="footnotes-body">
        <sup>1</sup> Pinterest Impact Fund data as of 2025. Non-profit organisations include mental health, body image, and youth well-being initiatives.
      </div>
    </div>
  </section>

  <!-- ── FOOTER ── -->
  <footer class="a-footer">
    <div class="af-wrap">
      <div class="af-left">
        <a href="../landing.php" class="footer-logo" style="font-family:'Nunito',sans-serif;">Pinterest</a>
        <div class="lang-sel">
          <i class="bi bi-globe"></i> English (UK) <i class="bi bi-chevron-down"></i>
        </div>
      </div>
      <div class="af-right">
        <div class="af-grid">
          <div class="af-col">
            <h6>Quick links</h6>
            <a href="../index.php">Visit Pinterest</a>
            <a href="#">Download on iOS</a>
            <a href="#">Download on Android</a>
            <a href="#">Help Centre</a>
          </div>
          <div class="af-col">
            <h6>Company</h6>
            <a href="#">Newsroom</a>
            <a href="#">Careers</a>
            <a href="#">Investors</a>
            <a href="#">Impact</a>
            <a href="#">Brand guidelines</a>
          </div>
          <div class="af-col">
            <h6>More from Pinterest</h6>
            <a href="business.php" target="_blank">Businesses</a>
            <a href="#">Creators</a>
            <a href="#">Developers</a>
            <a href="#">Designers</a>
          </div>
        </div>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const obs = new IntersectionObserver(entries =>
      entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('vis'); }),
      { threshold: 0.08 }
    );
    document.querySelectorAll('.fade-up').forEach(el => obs.observe(el));
  </script>
</body>
</html>
<?php include "footer.php"; ?>